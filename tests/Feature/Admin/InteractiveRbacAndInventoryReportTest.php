<?php

namespace Tests\Feature\Admin;

use App\Models\InventoryItem;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InteractiveRbacAndInventoryReportTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $manager;

    private User $cashier;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Rangoon Spice Kitchen',
            'slug' => 'rangoon-spice-kitchen',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Restaurant Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Operations Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Counter Cashier',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Dining Waiter',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_can_grant_and_revoke_role_permission_via_ajax(): void
    {
        $managerRole = Role::findByName('MANAGER', 'web');

        // Initially Manager does not have 'pos-checkout'
        $this->assertFalse($managerRole->hasPermissionTo('pos-checkout'));

        // 1. Grant pos-checkout to MANAGER
        $grantResponse = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.togglePermission'), [
            'role' => 'MANAGER',
            'permission' => 'pos-checkout',
        ]);

        $grantResponse->assertStatus(200);
        $grantResponse->assertJson([
            'success' => true,
            'granted' => true,
            'role' => 'MANAGER',
            'permission' => 'pos-checkout',
        ]);

        $this->assertTrue($managerRole->fresh()->hasPermissionTo('pos-checkout'));

        // 2. Revoke pos-checkout from MANAGER
        $revokeResponse = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.togglePermission'), [
            'role' => 'MANAGER',
            'permission' => 'pos-checkout',
        ]);

        $revokeResponse->assertStatus(200);
        $revokeResponse->assertJson([
            'success' => true,
            'granted' => false,
            'role' => 'MANAGER',
            'permission' => 'pos-checkout',
        ]);

        $this->assertFalse($managerRole->fresh()->hasPermissionTo('pos-checkout'));
    }

    public function test_owner_permissions_cannot_be_revoked_via_toggle(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.togglePermission'), [
            'role' => 'OWNER',
            'permission' => 'manage-menu',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
    }

    public function test_manager_cannot_toggle_role_permissions(): void
    {
        $response = $this->actingAs($this->manager)->postJson(route('admin.roles.permissions.togglePermission'), [
            'role' => 'CASHIER',
            'permission' => 'manage-menu',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_and_manager_can_view_inventory_remaining_report(): void
    {
        InventoryItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Basmati Long Grain Rice',
            'sku' => 'RAW-RICE-02',
            'unit' => 'kg',
            'current_stock' => 12.5,
            'min_stock_alert' => 20.0,
            'unit_cost' => 5000,
        ]);

        // Owner check
        $ownerResponse = $this->actingAs($this->owner)->get(route('admin.inventory.report'));
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee('Inventory Stock Remaining Report');
        $ownerResponse->assertSee('Basmati Long Grain Rice');
        $ownerResponse->assertSee('12.50');
        $ownerResponse->assertSee('Low Stock Warning');

        // Manager check
        $managerResponse = $this->actingAs($this->manager)->get(route('admin.inventory.report'));
        $managerResponse->assertStatus(200);
        $managerResponse->assertSee('Inventory Stock Remaining Report');
    }

    public function test_cashier_and_staff_cannot_view_inventory_report(): void
    {
        $this->actingAs($this->cashier)->get(route('admin.inventory.report'))->assertStatus(403);
        $this->actingAs($this->staff)->get(route('admin.inventory.report'))->assertStatus(403);
    }

    public function test_can_export_inventory_report_as_csv(): void
    {
        InventoryItem::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Pure Sesame Oil',
            'sku' => 'RAW-OIL-01',
            'unit' => 'liter',
            'current_stock' => 8.0,
            'min_stock_alert' => 3.0,
            'unit_cost' => 12000,
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.inventory.report', ['export' => 'csv']));

        $response->assertStatus(200);
        $this->assertTrue(str_contains((string) $response->headers->get('content-type'), 'text/csv'));
    }
}
