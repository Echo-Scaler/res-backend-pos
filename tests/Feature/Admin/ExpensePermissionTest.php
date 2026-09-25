<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpensePermissionTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private User $manager;

    private User $staff;

    private Expense $expense;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Riverside Grill',
            'slug' => 'riverside-grill',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Owner Person',
            'email' => 'owner@riverside.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Manager Person',
            'email' => 'manager@riverside.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->staff = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Staff Person',
            'email' => 'staff@riverside.com',
            'password' => Hash::make('password123'),
        ]);
        $this->staff->assignRole('STAFF');

        $cat = ExpenseCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Cleaning',
            'is_active' => true,
        ]);

        $this->expense = Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-PERM-01',
            'category_id' => $cat->id,
            'title' => 'Mop and Detergent Refill',
            'amount' => 12000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_PENDING_APPROVAL,
            'created_by' => $this->staff->id,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.expenses.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_owner_has_access_to_all_expense_actions(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.expenses.index'));
        $response->assertStatus(200);

        $approveResponse = $this->actingAs($this->owner)->post(route('admin.expenses.approve', $this->expense));
        $approveResponse->assertRedirect();
        $this->assertEquals(Expense::STATUS_APPROVED, $this->expense->fresh()->status);
    }

    public function test_manager_has_access_to_approve_and_pay(): void
    {
        $response = $this->actingAs($this->manager)->get(route('admin.expenses.index'));
        $response->assertStatus(200);

        $approveResponse = $this->actingAs($this->manager)->post(route('admin.expenses.approve', $this->expense));
        $approveResponse->assertRedirect();
        $this->assertEquals(Expense::STATUS_APPROVED, $this->expense->fresh()->status);
    }

    public function test_staff_cannot_access_manager_admin_expense_panel(): void
    {
        // Admin routes are guarded by role:OWNER|MANAGER
        $response = $this->actingAs($this->staff)->get(route('admin.expenses.index'));
        $response->assertStatus(403);
    }
}
