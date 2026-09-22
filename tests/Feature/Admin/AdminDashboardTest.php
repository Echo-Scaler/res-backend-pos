<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
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

        // Seed roles and fine-grained permissions
        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Rangoon Spice Kitchen',
            'slug' => 'rangoon-spice-kitchen',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Restaurant Owner',
            'email' => 'admin@example.com',
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
            'name' => 'Waiter Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Restaurant Admin');
        $response->assertSee('Sign In to Dashboard');
    }

    public function test_owner_can_login_with_valid_credentials_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->owner);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_owner_can_view_dashboard_with_full_control(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Rangoon Spice Kitchen');
        $response->assertSee('Restaurant Owner');
        $response->assertSee('Owner Authority Active');
        $response->assertSee('Unrestricted (100%)');
        $response->assertSee('Full Control (Owner Only)');
    }

    public function test_manager_cashier_and_staff_are_denied_access_to_owner_dashboard(): void
    {
        $managerResponse = $this->actingAs($this->manager)->get('/admin/dashboard');
        $managerResponse->assertStatus(403);

        $cashierResponse = $this->actingAs($this->cashier)->get('/admin/dashboard');
        $cashierResponse->assertStatus(403);

        $staffResponse = $this->actingAs($this->staff)->get('/admin/dashboard');
        $staffResponse->assertStatus(403);
    }

    public function test_manager_has_staff_permission_but_cannot_manage_managers_or_settings(): void
    {
        $this->assertTrue($this->manager->hasPermissionTo('manage-staff'));
        $this->assertTrue($this->manager->hasPermissionTo('manage-menu'));
        $this->assertFalse($this->manager->hasPermissionTo('manage-managers'));
        $this->assertFalse($this->manager->hasPermissionTo('manage-restaurant-settings'));
        $this->assertFalse($this->manager->hasPermissionTo('delete-restaurant'));
    }

    public function test_owner_can_logout_from_admin_portal(): void
    {
        $response = $this->actingAs($this->owner)->post('/admin/logout');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}
