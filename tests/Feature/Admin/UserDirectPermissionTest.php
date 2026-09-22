<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserDirectPermissionTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $manager;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Grand Palace POS Bistro',
            'slug' => 'grand-palace-pos-bistro',
            'phone' => '0912345678',
            'address' => 'Yangon',
            'currency' => 'MMK',
            'is_active' => true,
        ]);

        $this->owner = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bistro Owner',
            'email' => 'owner@grandpalace.test',
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bistro Manager',
            'email' => 'manager@grandpalace.test',
        ]);
        $this->manager->assignRole('MANAGER');

        $this->staff = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Aung Staff',
            'email' => 'aung@grandpalace.test',
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_can_view_edit_employee_page_with_direct_permission_overrides(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.employees.edit', $this->staff));

        $response->assertOk();
        $response->assertSee('User-Level Direct Permission Overrides');
        $response->assertSee('apply-discounts');
        $response->assertSee('manage-inventory');
        $response->assertSee('manage-tables');
    }

    public function test_owner_can_grant_direct_permissions_to_staff_member(): void
    {
        // Initially staff only has base STAFF role permissions (cannot apply discounts or manage inventory)
        $this->assertFalse($this->staff->hasPermissionTo('apply-discounts'));
        $this->assertFalse($this->staff->hasPermissionTo('manage-inventory'));
        $this->assertCount(0, $this->staff->getDirectPermissions());

        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Staff (Promoted Direct)',
            'email' => 'aung@grandpalace.test',
            'role' => 'STAFF',
            'direct_permissions_override_submitted' => '1',
            'direct_permissions' => [
                'apply-discounts',
                'manage-inventory',
            ],
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $response->assertSessionHas('success');

        $this->staff->refresh();

        // Base role remains STAFF
        $this->assertTrue($this->staff->hasRole('STAFF'));

        // Direct permissions are assigned
        $this->assertTrue($this->staff->hasPermissionTo('apply-discounts'));
        $this->assertTrue($this->staff->hasPermissionTo('manage-inventory'));
        $this->assertTrue($this->staff->can('apply-discounts'));
        $this->assertTrue($this->staff->can('manage-inventory'));

        $directPerms = $this->staff->getDirectPermissions()->pluck('name')->all();
        $this->assertContains('apply-discounts', $directPerms);
        $this->assertContains('manage-inventory', $directPerms);
    }

    public function test_owner_can_revoke_direct_permissions_by_submitting_empty_or_partial_list(): void
    {
        // First grant direct permissions
        $this->staff->givePermissionTo(['apply-discounts', 'manage-tables']);
        $this->assertTrue($this->staff->hasPermissionTo('apply-discounts'));
        $this->assertTrue($this->staff->hasPermissionTo('manage-tables'));

        // Now update removing apply-discounts and keeping manage-tables
        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Staff',
            'email' => 'aung@grandpalace.test',
            'role' => 'STAFF',
            'direct_permissions_override_submitted' => '1',
            'direct_permissions' => [
                'manage-tables',
            ],
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->staff->refresh();

        $this->assertFalse($this->staff->hasPermissionTo('apply-discounts'));
        $this->assertTrue($this->staff->hasPermissionTo('manage-tables'));

        // Now revoke all direct permissions
        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Staff',
            'email' => 'aung@grandpalace.test',
            'role' => 'STAFF',
            'direct_permissions_override_submitted' => '1',
            // no direct_permissions key sent (like unchecking all checkboxes)
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->staff->refresh();

        $this->assertCount(0, $this->staff->getDirectPermissions());
        $this->assertFalse($this->staff->hasPermissionTo('manage-tables'));
    }

    public function test_manager_cannot_grant_direct_permissions_to_staff(): void
    {
        // Manager attempts to elevate staff with manage-inventory direct permission
        $response = $this->actingAs($this->manager)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Staff',
            'email' => 'aung@grandpalace.test',
            'role' => 'STAFF',
            'direct_permissions_override_submitted' => '1',
            'direct_permissions' => [
                'manage-inventory',
                'manage-promotions',
            ],
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->staff->refresh();

        // Direct permissions should NOT be granted because user is not OWNER
        $this->assertCount(0, $this->staff->getDirectPermissions());
        $this->assertFalse($this->staff->hasPermissionTo('manage-inventory'));
    }

    public function test_invalid_permission_names_are_rejected(): void
    {
        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Staff',
            'email' => 'aung@grandpalace.test',
            'role' => 'STAFF',
            'direct_permissions_override_submitted' => '1',
            'direct_permissions' => [
                'non-existent-hacky-permission',
            ],
        ]);

        $response->assertSessionHasErrors(['direct_permissions.0']);
    }

    public function test_owner_and_manager_edit_screens_do_not_show_direct_permission_overrides(): void
    {
        // Manager edit screen should not have direct permissions section
        $responseManager = $this->actingAs($this->owner)->get(route('admin.employees.edit', $this->manager));
        $responseManager->assertOk();
        $responseManager->assertDontSee('User-Level Direct Permission Overrides');

        // Owner edit screen should not have direct permissions section
        $responseOwner = $this->actingAs($this->owner)->get(route('admin.employees.edit', $this->owner));
        $responseOwner->assertOk();
        $responseOwner->assertDontSee('User-Level Direct Permission Overrides');
    }

    public function test_direct_permissions_are_cleared_if_employee_is_promoted_to_manager(): void
    {
        // First give staff direct permission
        $this->staff->givePermissionTo('apply-discounts');
        $this->assertCount(1, $this->staff->getDirectPermissions());

        // Promote staff to Manager
        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Ko Aung Promoted Manager',
            'email' => 'aung@grandpalace.test',
            'role' => 'MANAGER',
            'direct_permissions_override_submitted' => '1',
            'direct_permissions' => ['apply-discounts'],
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->staff->refresh();

        $this->assertTrue($this->staff->hasRole('MANAGER'));
        // Direct permissions should be cleared because role is MANAGER
        $this->assertCount(0, $this->staff->getDirectPermissions());
    }
}
