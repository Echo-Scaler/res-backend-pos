<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\ToggleRolePermissionRequest;
use App\Http\Requests\Admin\Roles\UpdateUserRoleRequest;
use App\Http\Resources\UserRoleResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    /**
     * Display the Roles & Permissions workspace.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        // Fetch all restaurant staff members with their assigned roles and direct permissions
        $employees = User::query()
            ->where('restaurant_id', $currentUser->restaurant_id)
            ->with(['roles', 'permissions'])
            ->orderBy('name')
            ->get();

        // Categorize into sections
        $groupedUsers = [
            'OWNER' => $employees->filter(fn (User $u) => $u->hasRole('OWNER')),
            'MANAGER' => $employees->filter(fn (User $u) => $u->hasRole('MANAGER')),
            'CASHIER' => $employees->filter(fn (User $u) => $u->hasRole('CASHIER')),
            'STAFF' => $employees->filter(fn (User $u) => $u->hasRole('STAFF') || $u->roles->isEmpty()),
        ];

        // System roles & fine-grained Spatie permissions
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        // Categorized permission groups for RBAC matrix
        $permissionGroups = [
            'POS & Ordering Operations' => [
                'pos-checkout' => 'Counter POS Register, Cash Drawer & Billing',
                'take-orders' => 'Tableside Ordering, KDS routing & Guest Service',
                'apply-discounts' => 'Apply Authorized Discounts & Promotional Coupons at POS Checkout',
            ],
            'Dining, Menu & Inventory Operations' => [
                'manage-menu' => 'Create & Edit Food Categories, Dishes & Pricing Modifiers',
                'manage-tables' => 'Floor Plan, Seating Zones & QR Ordering Codes',
                'manage-inventory' => 'Track Kitchen Stock Levels, Deductions & Low-Stock Alerts',
            ],
            'Marketing & Special Promotions' => [
                'manage-promotions' => 'Create & Configure Percentage or Fixed Coupons & Special Offers',
            ],
            'Staff & Account Delegation' => [
                'manage-staff' => 'Recruit & Supervise Cashiers and Dining Waiters',
                'manage-managers' => 'Appoint & Delegate Operations Managers (Owner Only)',
            ],
            'Financials & Executive Governance' => [
                'view-financial-reports' => 'Profit/Loss Analytics, Daily Revenue & Shift Reconciliations',
                'manage-restaurant-settings' => 'Tax, Service Charge, Hardware & Store Profile',
                'delete-restaurant' => 'Permanent Restaurant Purge & Master Reset (Owner Only)',
            ],
        ];

        // Build role to permission lookup map: [role_name => [perm_1, perm_2]]
        $rolePermissionsMap = [];
        foreach ($roles as $role) {
            $rolePermissionsMap[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        $stats = [
            'total_users' => $employees->count(),
            'owner_count' => $groupedUsers['OWNER']->count(),
            'manager_count' => $groupedUsers['MANAGER']->count(),
            'cashier_count' => $groupedUsers['CASHIER']->count(),
            'staff_count' => $groupedUsers['STAFF']->count(),
            'total_permissions' => $permissions->count(),
        ];

        return view('admin.roles.permissions_index', [
            'restaurant' => $restaurant,
            'currentUser' => $currentUser,
            'employees' => $employees,
            'groupedUsers' => $groupedUsers,
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionGroups' => $permissionGroups,
            'rolePermissionsMap' => $rolePermissionsMap,
            'stats' => $stats,
        ]);
    }

    /**
     * Update an employee's assigned role.
     */
    public function updateRole(UpdateUserRoleRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $targetUser = User::where('restaurant_id', $currentUser->restaurant_id)
            ->findOrFail($validated['user_id']);

        $newRole = $validated['role'];
        $targetUser->syncRoles([$newRole]);

        $message = "Role for {$targetUser->name} successfully updated to {$newRole}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => new UserRoleResource($targetUser->fresh(['roles', 'permissions'])),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Dynamically grant or revoke a fine-grained permission for a specific role.
     */
    public function togglePermission(ToggleRolePermissionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $roleName = $validated['role'];
        $permissionName = $validated['permission'];

        $role = Role::findByName($roleName, 'web');

        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
            $granted = false;
            $message = "Revoked '{$permissionName}' from role {$roleName}.";
        } else {
            $role->givePermissionTo($permissionName);
            $granted = true;
            $message = "Granted '{$permissionName}' to role {$roleName}.";
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'role' => $roleName,
            'permission' => $permissionName,
            'granted' => $granted,
            'message' => $message,
        ]);
    }
}
