<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
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
            'name' => 'Golden Palace Restaurant',
            'slug' => 'golden-palace',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'U Hla - Owner',
            'email' => 'owner@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ko Kyaw - Manager',
            'email' => 'manager@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Ma Su - Cashier',
            'email' => 'cashier@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mg Zaw - Waiter',
            'email' => 'staff@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.employees.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_cashier_and_staff_are_forbidden_from_employee_management(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.employees.index'));
        $response->assertForbidden();

        $response = $this->actingAs($this->staff)->get(route('admin.employees.index'));
        $response->assertForbidden();
    }

    public function test_owner_can_view_employee_list_with_all_roles(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.employees.index'));

        $response->assertOk();
        $response->assertViewIs('admin.employees.index');
        $response->assertSee('Employee Management');
        $response->assertSee('U Hla - Owner');
        $response->assertSee('Ko Kyaw - Manager');
        $response->assertSee('Ma Su - Cashier');
        $response->assertSee('Mg Zaw - Waiter');
    }

    public function test_owner_can_create_manager_with_pin_code(): void
    {
        $response = $this->actingAs($this->owner)->post(route('admin.employees.store'), [
            'name' => 'Aung Aung',
            'email' => 'aungaung@goldenpalace.com',
            'phone' => '0912345678',
            'role' => 'MANAGER',
            'password' => 'secret123',
            'pin_code' => '1234',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $response->assertSessionHas('success');

        $newEmployee = User::where('email', 'aungaung@goldenpalace.com')->first();
        $this->assertNotNull($newEmployee);
        $this->assertEquals('Aung Aung', $newEmployee->name);
        $this->assertEquals($this->restaurant->id, $newEmployee->restaurant_id);
        $this->assertTrue($newEmployee->hasRole('MANAGER'));
        $this->assertTrue(Hash::check('1234', $newEmployee->pin_code));
    }

    public function test_manager_can_create_cashier_and_dining_staff(): void
    {
        $response = $this->actingAs($this->manager)->post(route('admin.employees.store'), [
            'name' => 'Thidar',
            'email' => 'thidar@goldenpalace.com',
            'phone' => '0998765432',
            'role' => 'CASHIER',
            'password' => 'secret123',
            'pin_code' => '5678',
        ]);

        $response->assertRedirect(route('admin.employees.index'));

        $thidar = User::where('email', 'thidar@goldenpalace.com')->first();
        $this->assertNotNull($thidar);
        $this->assertTrue($thidar->hasRole('CASHIER'));
    }

    public function test_manager_cannot_create_a_manager_or_owner(): void
    {
        $response = $this->actingAs($this->manager)->post(route('admin.employees.store'), [
            'name' => 'Unauthorized Manager',
            'email' => 'unauth@goldenpalace.com',
            'role' => 'MANAGER',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'unauth@goldenpalace.com']);
    }

    public function test_manager_cannot_edit_or_delete_owner_or_another_manager(): void
    {
        // Manager cannot edit Owner
        $response = $this->actingAs($this->manager)->get(route('admin.employees.edit', $this->owner));
        $response->assertForbidden();

        // Manager cannot edit another Manager
        $anotherManager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Second Manager',
            'email' => 'manager2@goldenpalace.com',
            'password' => Hash::make('password123'),
        ]);
        $anotherManager->assignRole('MANAGER');

        $response = $this->actingAs($this->manager)->get(route('admin.employees.edit', $anotherManager));
        $response->assertForbidden();

        // Manager cannot delete another Manager
        $response = $this->actingAs($this->manager)->delete(route('admin.employees.destroy', $anotherManager));
        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $anotherManager->id]);
    }

    public function test_owner_can_update_employee_details_and_role(): void
    {
        $response = $this->actingAs($this->owner)->put(route('admin.employees.update', $this->staff), [
            'name' => 'Mg Zaw Promoted',
            'email' => 'mgzaw.promoted@goldenpalace.com',
            'phone' => '0944445555',
            'role' => 'CASHIER',
            'pin_code' => '9999',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertEquals('Mg Zaw Promoted', $this->staff->name);
        $this->assertEquals('mgzaw.promoted@goldenpalace.com', $this->staff->email);
        $this->assertTrue($this->staff->hasRole('CASHIER'));
        $this->assertTrue(Hash::check('9999', $this->staff->pin_code));
    }

    public function test_user_cannot_delete_themselves_or_owner_account(): void
    {
        // Owner cannot delete themselves
        $response = $this->actingAs($this->owner)->delete(route('admin.employees.destroy', $this->owner));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->owner->id]);

        // Manager cannot delete Owner
        $response = $this->actingAs($this->manager)->delete(route('admin.employees.destroy', $this->owner));
        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $this->owner->id]);
    }

    public function test_owner_can_delete_staff_member(): void
    {
        $staffId = $this->staff->id;
        $response = $this->actingAs($this->owner)->delete(route('admin.employees.destroy', $this->staff));

        $response->assertRedirect(route('admin.employees.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $staffId]);
    }

    public function test_employee_from_another_restaurant_cannot_be_accessed(): void
    {
        $otherRestaurant = Restaurant::create([
            'name' => 'Other Restaurant',
            'slug' => 'other-restaurant',
            'is_active' => true,
        ]);

        $otherEmployee = User::create([
            'restaurant_id' => $otherRestaurant->id,
            'name' => 'Other Staff',
            'email' => 'other@other.com',
            'password' => Hash::make('password123'),
        ]);
        $otherEmployee->assignRole('STAFF');

        $response = $this->actingAs($this->owner)->get(route('admin.employees.edit', $otherEmployee));
        $response->assertNotFound();

        $response = $this->actingAs($this->owner)->delete(route('admin.employees.destroy', $otherEmployee));
        $response->assertNotFound();
    }
}
