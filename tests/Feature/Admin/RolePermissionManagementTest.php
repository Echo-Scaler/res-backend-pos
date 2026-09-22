<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private Restaurant $otherRestaurant;

    private User $owner;

    private User $manager;

    private User $cashier;

    private User $staff;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Rangoon Spice Kitchen',
            'slug' => 'rangoon-spice-kitchen',
            'is_active' => true,
        ]);

        $this->otherRestaurant = Restaurant::create([
            'name' => 'Mandalay Royal Tea',
            'slug' => 'mandalay-royal-tea',
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

        $this->otherUser = User::create([
            'restaurant_id' => $this->otherRestaurant->id,
            'name' => 'Other Branch Staff',
            'email' => 'other@mandalay.com',
            'password' => Hash::make('password123'),
        ]);
        $this->otherUser->assignRole('STAFF');
    }

    public function test_owner_can_access_roles_permissions_workspace(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.roles.permissions'));

        $response->assertStatus(200);
        $response->assertSee('Roles & Permissions');
        $response->assertSee('Team Role Assignment');
        $response->assertSee('Permissions Matrix (RBAC)');
        $response->assertSee('Restaurant Owner');
        $response->assertSee('Operations Manager');
        $response->assertSee('Counter Cashier');
        $response->assertSee('Dining Waiter');
        $response->assertSee('pos-checkout');
        $response->assertSee('manage-menu');
    }

    public function test_manager_cashier_and_staff_are_forbidden_from_roles_workspace(): void
    {
        $this->actingAs($this->manager)->get(route('admin.roles.permissions'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('admin.roles.permissions'))->assertStatus(403);
        $this->actingAs($this->staff)->get(route('admin.roles.permissions'))->assertStatus(403);
    }

    public function test_owner_can_update_employee_role_via_ajax(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.updateRole'), [
            'user_id' => $this->staff->id,
            'role' => 'CASHIER',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'id' => $this->staff->id,
                'role' => 'CASHIER',
            ],
        ]);

        $this->assertTrue($this->staff->fresh()->hasRole('CASHIER'));
        $this->assertFalse($this->staff->fresh()->hasRole('STAFF'));
    }

    public function test_owner_cannot_demote_themselves(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.updateRole'), [
            'user_id' => $this->owner->id,
            'role' => 'MANAGER',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
        $this->assertTrue($this->owner->fresh()->hasRole('OWNER'));
    }

    public function test_cannot_update_role_to_non_existent_role(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.updateRole'), [
            'user_id' => $this->staff->id,
            'role' => 'SUPER_ADMIN_INVALID',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
    }

    public function test_cannot_update_role_of_employee_from_different_restaurant(): void
    {
        $response = $this->actingAs($this->owner)->postJson(route('admin.roles.permissions.updateRole'), [
            'user_id' => $this->otherUser->id,
            'role' => 'CASHIER',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
    }
}
