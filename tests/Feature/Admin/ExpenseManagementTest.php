<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private Restaurant $otherRestaurant;

    private User $owner;

    private User $manager;

    private User $cashier;

    private ExpenseCategory $cogsCategory;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'European Bistro & Grill',
            'slug' => 'european-bistro',
            'is_active' => true,
        ]);

        $this->otherRestaurant = Restaurant::create([
            'name' => 'Yangon Riverside Cafe',
            'slug' => 'yangon-riverside',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Bistro Owner',
            'email' => 'owner@bistro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Operations Manager',
            'email' => 'manager@bistro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->manager->assignRole('MANAGER');

        $this->cashier = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Floor Cashier',
            'email' => 'cashier@bistro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->cashier->assignRole('CASHIER');

        $this->cogsCategory = ExpenseCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Fresh Food Ingredients (COGS)',
            'code' => 'COGS-FOOD',
            'gl_account_code' => '5001',
            'color' => '#9ec63b',
            'is_active' => true,
        ]);

        $this->vendor = Vendor::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Metro Wholesale Meat Supplier',
            'contact_person' => 'U San',
            'phone' => '+959111222333',
            'payment_terms_days' => 15,
            'is_active' => true,
        ]);
    }

    public function test_can_view_expense_index_with_kpi_cards(): void
    {
        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-202609-0001',
            'category_id' => $this->cogsCategory->id,
            'vendor_id' => $this->vendor->id,
            'title' => 'Weekly Beef and Poultry Restock',
            'amount' => 120000,
            'tax_amount' => 0,
            'total_amount' => 120000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_DRAFT,
            'created_by' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.expenses.index'));

        $response->assertStatus(200);
        $response->assertSee('Expense Management');
        $response->assertSee('Weekly Beef and Poultry Restock');
        $response->assertSee('120,000 MMK');
        $response->assertSee('DRAFT');
    }

    public function test_can_create_expense_with_receipt_upload(): void
    {
        Storage::fake('public');

        $receipt = UploadedFile::fake()->create('market_voucher.jpg', 500, 'image/jpeg');

        $payload = [
            'title' => 'Emergency Kitchen Gas Tank Refill',
            'category_id' => $this->cogsCategory->id,
            'vendor_id' => $this->vendor->id,
            'amount' => 65000,
            'tax_amount' => 3250,
            'expense_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'payment_method' => 'CASH',
            'reason' => 'Kitchen gas depleted during lunch service rush',
            'receipt' => $receipt,
            'submit_now' => '1',
        ];

        $response = $this->actingAs($this->manager)->post(route('admin.expenses.store'), $payload);

        $response->assertRedirect();

        $expense = Expense::where('title', 'Emergency Kitchen Gas Tank Refill')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(65000, $expense->amount);
        $this->assertEquals(3250, $expense->tax_amount);
        $this->assertEquals(68250, $expense->total_amount);
        $this->assertEquals(Expense::STATUS_PENDING_APPROVAL, $expense->status);
        $this->assertNotNull($expense->receipt_path);

        Storage::disk('public')->assertExists($expense->receipt_path);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'EXPENSE_CREATED',
            'resource_id' => $expense->id,
        ]);
    }

    public function test_complete_expense_lifecycle_workflow(): void
    {
        // 1. Create as DRAFT
        $expense = Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-202609-0099',
            'category_id' => $this->cogsCategory->id,
            'vendor_id' => $this->vendor->id,
            'title' => 'Fresh Vegetables Market Run',
            'amount' => 45000,
            'tax_amount' => 0,
            'total_amount' => 45000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_DRAFT,
            'created_by' => $this->manager->id,
        ]);

        $this->assertTrue($expense->isDraft());

        // 2. Submit for Approval
        $submitResponse = $this->actingAs($this->manager)->post(route('admin.expenses.submit', $expense));
        $submitResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_PENDING_APPROVAL, $expense->status);

        // 3. Manager/Owner Rejects with Reason
        $rejectResponse = $this->actingAs($this->owner)->post(route('admin.expenses.reject', $expense), [
            'reason' => 'Vendor invoice tax breakdown missing. Please resubmit with invoice details.',
        ]);
        $rejectResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_REJECTED, $expense->status);
        $this->assertEquals('Vendor invoice tax breakdown missing. Please resubmit with invoice details.', $expense->rejection_reason);

        // 4. Update and Re-submit
        $updateResponse = $this->actingAs($this->manager)->put(route('admin.expenses.update', $expense), [
            'title' => 'Fresh Vegetables Market Run (Tax Verified)',
            'amount' => 45000,
            'expense_date' => now()->toDateString(),
            'submit_now' => '1',
        ]);
        $updateResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_PENDING_APPROVAL, $expense->status);

        // 5. Owner Approves
        $approveResponse = $this->actingAs($this->owner)->post(route('admin.expenses.approve', $expense), [
            'notes' => 'Invoice verified with kitchen head chef.',
        ]);
        $approveResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_APPROVED, $expense->status);
        $this->assertEquals($this->owner->id, $expense->approved_by);

        // 6. Record Cash Payment (Integrates with Cash Drawer)
        $payResponse = $this->actingAs($this->manager)->post(route('admin.expenses.pay', $expense), [
            'payment_method' => 'CASH',
            'account_or_drawer_name' => 'Main POS Cash Register Drawer #1',
            'payment_reference' => 'CASH-VOUCHER-084',
            'payment_date' => now()->toDateString(),
        ]);
        $payResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_PAID, $expense->status);
        $this->assertEquals(Expense::PAYMENT_STATUS_PAID, $expense->payment_status);
        $this->assertEquals('CASH', $expense->payment_method);

        // Verify Expense Transaction created
        $this->assertDatabaseHas('expense_transactions', [
            'expense_id' => $expense->id,
            'transaction_type' => 'CASH_DRAWER',
            'amount' => 45000,
            'status' => 'SUCCESS',
        ]);

        // 7. Void Expense with Reason
        $voidResponse = $this->actingAs($this->owner)->post(route('admin.expenses.void', $expense), [
            'reason' => 'Vendor issued duplicate voucher, disbursement reversed.',
        ]);
        $voidResponse->assertRedirect();
        $expense->refresh();
        $this->assertEquals(Expense::STATUS_VOID, $expense->status);
        $this->assertEquals(Expense::PAYMENT_STATUS_VOID, $expense->payment_status);

        // Check transaction was reversed
        $txn = ExpenseTransaction::where('expense_id', $expense->id)->first();
        $this->assertEquals('REVERSED', $txn->status);

        // Check Audit Log recorded void
        $this->assertDatabaseHas('audit_logs', [
            'restaurant_id' => $this->restaurant->id,
            'action' => 'EXPENSE_VOIDED',
            'resource_id' => $expense->id,
        ]);
    }

    public function test_multi_branch_isolation(): void
    {
        $otherExpense = Expense::create([
            'restaurant_id' => $this->otherRestaurant->id,
            'expense_number' => 'EXP-OTHER-001',
            'title' => 'Other Branch Air Conditioning Service',
            'amount' => 80000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_APPROVED,
        ]);

        // Accessing other branch expense returns 403 Forbidden
        $response = $this->actingAs($this->owner)->get(route('admin.expenses.show', $otherExpense));
        $response->assertStatus(403);
    }
}
