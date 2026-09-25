<?php

namespace Tests\Feature\Api;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseApiTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private ExpenseCategory $category;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'API Bistro',
            'slug' => 'api-bistro',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'API Owner',
            'email' => 'apiowner@bistro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->category = ExpenseCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Beverage & Bar',
            'code' => 'COGS-BEV',
            'color' => '#06b6d4',
            'is_active' => true,
        ]);

        $this->vendor = Vendor::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Wine & Spirits Ltd',
            'is_active' => true,
        ]);
    }

    public function test_api_can_list_and_create_expense(): void
    {
        Sanctum::actingAs($this->owner);

        // 1. Create via API
        $payload = [
            'title' => 'Imported Red Wine Cases',
            'category_id' => $this->category->id,
            'vendor_id' => $this->vendor->id,
            'amount' => 125000,
            'tax_amount' => 6250,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'BANK_TRANSFER',
        ];

        $postResponse = $this->postJson('/api/v1/expenses', $payload);

        $postResponse->assertStatus(201)
            ->assertJsonPath('message', 'Expense created successfully.')
            ->assertJsonPath('data.title', 'Imported Red Wine Cases')
            ->assertJsonPath('data.amount', 125000)
            ->assertJsonPath('data.currency', 'MMK')
            ->assertJsonPath('data.formatted_amount', '125,000 MMK')
            ->assertJsonPath('data.status', 'DRAFT');

        $expenseId = $postResponse->json('data.id');

        // 2. List via API
        $getResponse = $this->getJson('/api/v1/expenses');
        $getResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'expense_number', 'title', 'amount', 'formatted_amount', 'currency', 'status'],
                ],
            ]);

        // 3. Submit
        $submitResponse = $this->postJson("/api/v1/expenses/{$expenseId}/submit");
        $submitResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'PENDING_APPROVAL');

        // 4. Approve
        $approveResponse = $this->postJson("/api/v1/expenses/{$expenseId}/approve", [
            'notes' => 'API approval test',
        ]);
        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'APPROVED');

        // 5. Pay
        $payResponse = $this->postJson("/api/v1/expenses/{$expenseId}/pay", [
            'payment_method' => 'BANK_TRANSFER',
            'payment_reference' => 'API-TXN-101',
            'account_or_drawer_name' => 'Corporate CB Bank Account',
        ]);
        $payResponse->assertStatus(200)
            ->assertJsonPath('message', 'Payment processed successfully.')
            ->assertJsonPath('expense.status', 'PAID')
            ->assertJsonPath('transaction.transaction_type', 'BANK_ACCOUNT');

        // 6. Void
        $voidResponse = $this->postJson("/api/v1/expenses/{$expenseId}/void", [
            'reason' => 'Duplicate supplier entry via API',
        ]);
        $voidResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'VOID');
    }

    public function test_api_analytical_reports(): void
    {
        Sanctum::actingAs($this->owner);

        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'title' => 'Soda & Tonic Cans',
            'amount' => 40000,
            'total_amount' => 40000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_PAID,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'payment_method' => 'CASH',
        ]);

        // 1. Summary
        $summaryResponse = $this->getJson('/api/v1/expenses/reports/summary');
        $summaryResponse->assertStatus(200)
            ->assertJsonPath('data.currency', 'MMK')
            ->assertJsonPath('data.total_amount', 40000);

        // 2. By Category
        $catResponse = $this->getJson('/api/v1/expenses/reports/by-category');
        $catResponse->assertStatus(200)
            ->assertJsonStructure(['data' => [['category_name', 'amount', 'percentage']]]);

        // 3. Forecast
        $forecastResponse = $this->getJson('/api/v1/expenses/reports/forecast');
        $forecastResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['next_month_label', 'projected_total']]);
    }
}
