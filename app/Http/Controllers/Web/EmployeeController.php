<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Employee\StoreEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees belonging to the restaurant.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        $query = User::query()
            ->where('restaurant_id', $currentUser->restaurant_id)
            ->with('roles');

        // Optional filter by role
        if ($role = $request->input('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Optional search by name, email, or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return view('admin.employees.index', [
            'employees' => $employees,
            'currentUser' => $currentUser,
            'restaurant' => $restaurant,
            'selectedRole' => $role,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(Request $request): View
    {
        $currentUser = $request->user();
        $allowedRoles = $this->getAllowedRolesForCreation($currentUser);

        return view('admin.employees.create', [
            'allowedRoles' => $allowedRoles,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $employee = User::create([
            'restaurant_id' => $currentUser->restaurant_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'pin_code' => $validated['pin_code'] ?? null,
        ]);

        $employee->assignRole($validated['role']);

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$employee->name}' successfully added as {$validated['role']}.");
    }

    /**
     * Show the form for editing an existing employee.
     */
    public function edit(Request $request, User $employee): View
    {
        $currentUser = $request->user();
        $this->authorizeAccessToEmployee($currentUser, $employee);

        $allowedRoles = $this->getAllowedRolesForEditing($currentUser, $employee);

        $allPermissions = Permission::all();
        $directPermissions = $employee->getDirectPermissions()->pluck('name')->toArray();
        $rolePermissions = $employee->getPermissionsViaRoles()->pluck('name')->toArray();

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

        return view('admin.employees.edit', [
            'employee' => $employee,
            'allowedRoles' => $allowedRoles,
            'currentUser' => $currentUser,
            'allPermissions' => $allPermissions,
            'directPermissions' => $directPermissions,
            'rolePermissions' => $rolePermissions,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(UpdateEmployeeRequest $request, User $employee): RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        if (array_key_exists('pin_code', $validated)) {
            $updateData['pin_code'] = ! empty($validated['pin_code']) ? $validated['pin_code'] : null;
        }

        $employee->update($updateData);

        // Sync role
        $employee->syncRoles([$validated['role']]);

        // Sync direct user-level permissions (Applicable ONLY to CASHIER and STAFF)
        if ($currentUser->hasRole('OWNER')) {
            if (in_array($validated['role'], ['CASHIER', 'STAFF'])) {
                if ($request->has('direct_permissions_override_submitted') || array_key_exists('direct_permissions', $validated)) {
                    $employee->syncPermissions($validated['direct_permissions'] ?? []);
                }
            } else {
                // Clear any individual direct overrides if role is OWNER or MANAGER
                $employee->syncPermissions([]);
            }
        }

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$employee->name}' details updated successfully.");
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Request $request, User $employee): RedirectResponse
    {
        $currentUser = $request->user();
        $this->authorizeAccessToEmployee($currentUser, $employee);

        // Cannot delete self
        if ($currentUser->id === $employee->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Cannot delete Owner
        if ($employee->hasRole('OWNER')) {
            abort(403, 'The Restaurant Owner account cannot be deleted.');
        }

        // Manager cannot delete another Manager
        if ($currentUser->hasRole('MANAGER') && $employee->hasRole('MANAGER')) {
            abort(403, 'Managers cannot delete other manager accounts.');
        }

        $name = $employee->name;
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$name}' has been successfully removed.");
    }

    /**
     * Ensure current user belongs to the same restaurant and respects role boundaries.
     */
    private function authorizeAccessToEmployee(User $currentUser, User $employee): void
    {
        if ($employee->restaurant_id !== $currentUser->restaurant_id) {
            abort(404);
        }

        // Managers cannot view/edit Owner accounts, nor other Managers
        if ($currentUser->hasRole('MANAGER')) {
            if ($employee->hasRole('OWNER') || ($employee->hasRole('MANAGER') && $employee->id !== $currentUser->id)) {
                abort(403, 'Managers can only manage Cashiers and Dining Staff members.');
            }
        }
    }

    /**
     * Determine which roles can be assigned by the current user when creating an employee.
     *
     * @return array<string>
     */
    private function getAllowedRolesForCreation(User $currentUser): array
    {
        if ($currentUser->hasRole('OWNER')) {
            return ['MANAGER', 'CASHIER', 'STAFF'];
        }

        if ($currentUser->hasRole('MANAGER')) {
            return ['CASHIER', 'STAFF'];
        }

        abort(403, 'Unauthorized to create employees.');
    }

    /**
     * Determine which roles can be assigned by the current user when editing an employee.
     *
     * @return array<string>
     */
    private function getAllowedRolesForEditing(User $currentUser, User $employee): array
    {
        if ($currentUser->hasRole('OWNER')) {
            return $employee->hasRole('OWNER') ? ['OWNER'] : ['MANAGER', 'CASHIER', 'STAFF'];
        }

        if ($currentUser->hasRole('MANAGER')) {
            return ['CASHIER', 'STAFF'];
        }

        abort(403, 'Unauthorized to edit employees.');
    }
}
