<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleBasedDashboardRoutingTest extends TestCase
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
            'name' => 'POS Cashier',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Floor Waiter',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_owner_login_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'owner@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->owner);
    }

    public function test_manager_login_redirects_to_manager_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'manager@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($this->manager);
    }

    public function test_cashier_login_redirects_to_cashier_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'cashier@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cashier.dashboard'));
        $this->assertAuthenticatedAs($this->cashier);
    }

    public function test_staff_login_redirects_to_staff_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'staff@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($this->staff);
    }

    public function test_authenticated_manager_visiting_login_page_redirects_to_manager_dashboard(): void
    {
        $response = $this->actingAs($this->manager)->get('/admin/login');

        $response->assertRedirect(route('manager.dashboard'));
    }

    public function test_authenticated_cashier_visiting_home_redirects_to_cashier_dashboard(): void
    {
        $response = $this->actingAs($this->cashier)->get('/');

        $response->assertRedirect(route('cashier.dashboard'));
    }

    public function test_manager_can_access_manager_portal_and_sees_manager_view(): void
    {
        $response = $this->actingAs($this->manager)->get('/manager/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Manager Operations Portal');
        $response->assertSee('Daily floor supervision');
        $response->assertSee('Operational Controls');
    }

    public function test_cashier_can_access_cashier_portal_and_sees_cashier_view(): void
    {
        $response = $this->actingAs($this->cashier)->get('/cashier/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Cashier Register');
        $response->assertSee('Tables Awaiting Payment');
        $response->assertSee('Quick Pay Channels');
    }

    public function test_staff_can_access_staff_portal_and_sees_staff_view(): void
    {
        $response = $this->actingAs($this->staff)->get('/staff/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Floor Waiter');
        $response->assertSee('Dining Floor Tables');
    }

    public function test_cashier_cannot_access_owner_or_manager_portal(): void
    {
        $ownerPortal = $this->actingAs($this->cashier)->get('/admin/dashboard');
        $ownerPortal->assertStatus(403);

        $managerPortal = $this->actingAs($this->cashier)->get('/manager/dashboard');
        $managerPortal->assertStatus(403);
    }

    public function test_staff_cannot_access_owner_manager_or_cashier_portal(): void
    {
        $ownerPortal = $this->actingAs($this->staff)->get('/admin/dashboard');
        $ownerPortal->assertStatus(403);

        $managerPortal = $this->actingAs($this->staff)->get('/manager/dashboard');
        $managerPortal->assertStatus(403);

        $cashierPortal = $this->actingAs($this->staff)->get('/cashier/dashboard');
        $cashierPortal->assertStatus(403);
    }

    public function test_manager_cannot_access_owner_portal(): void
    {
        $ownerPortal = $this->actingAs($this->manager)->get('/admin/dashboard');
        $ownerPortal->assertStatus(403);
    }

    public function test_owner_can_access_all_portals_for_supervision(): void
    {
        $this->actingAs($this->owner)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($this->owner)->get('/manager/dashboard')->assertStatus(200);
        $this->actingAs($this->owner)->get('/cashier/dashboard')->assertStatus(200);
        $this->actingAs($this->owner)->get('/staff/dashboard')->assertStatus(200);
    }
}
