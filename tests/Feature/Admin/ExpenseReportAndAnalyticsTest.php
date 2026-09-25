<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\RecurringExpense;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ExpenseReportService;
use App\Services\ExpenseService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpenseReportAndAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;

    private User $owner;

    private ExpenseCategory $catFood;

    private ExpenseCategory $catRent;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->restaurant = Restaurant::create([
            'name' => 'Royal European Kitchen',
            'slug' => 'royal-european-kitchen',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Executive Owner',
            'email' => 'owner@royalkitchen.com',
            'password' => Hash::make('password123'),
        ]);
        $this->owner->assignRole('OWNER');

        $this->catFood = ExpenseCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Food & Groceries',
            'code' => 'COGS-FOOD',
            'gl_account_code' => '5001',
            'color' => '#9ec63b',
            'is_active' => true,
        ]);

        $this->catRent = ExpenseCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Facility Lease',
            'code' => 'OPEX-RENT',
            'gl_account_code' => '6003',
            'color' => '#ec4899',
            'is_active' => true,
        ]);

        $this->vendor = Vendor::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Metro Yangon Wholesale',
            'payment_terms_days' => 10,
            'is_active' => true,
        ]);
    }

    public function test_expense_reports_and_summary_metrics(): void
    {
        // Create 2 paid and 1 overdue
        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-RPT-001',
            'category_id' => $this->catFood->id,
            'vendor_id' => $this->vendor->id,
            'title' => 'Fresh Seafood Delivery',
            'amount' => 150000,
            'total_amount' => 150000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_PAID,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'payment_method' => 'BANK_TRANSFER',
        ]);

        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-RPT-002',
            'category_id' => $this->catRent->id,
            'title' => 'Monthly Hall Lease',
            'amount' => 500000,
            'total_amount' => 500000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_PAID,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'payment_method' => 'BANK_TRANSFER',
        ]);

        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-RPT-003',
            'category_id' => $this->catFood->id,
            'vendor_id' => $this->vendor->id,
            'title' => 'Overdue Dry Spice Import',
            'amount' => 80000,
            'total_amount' => 80000,
            'expense_date' => now()->subDays(15)->toDateString(),
            'due_date' => now()->subDays(5)->toDateString(), // Past due
            'status' => Expense::STATUS_APPROVED,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
        ]);

        $reportService = app(ExpenseReportService::class);
        $summary = $reportService->getSummary($this->restaurant);

        $this->assertEquals(730000, $summary['total_amount']);
        $this->assertEquals('730,000 MMK', $summary['formatted_total_amount']);
        $this->assertEquals(650000, $summary['paid_amount']);
        $this->assertEquals(80000, $summary['overdue_amount']);
        $this->assertEquals(1, $summary['overdue_count']);

        // Check report view loads properly
        $response = $this->actingAs($this->owner)->get(route('admin.expenses.reports'));
        $response->assertStatus(200);
        $response->assertSee('Expense Reports & Financial Audit');
        $response->assertSee('Food & Groceries');
        $response->assertSee('Facility Lease');
    }

    public function test_budget_vs_actual_and_alerts(): void
    {
        $currentYear = (int) now()->format('Y');
        $currentMonth = (int) now()->format('n');

        // Set Food Budget limit of 200,000 MMK with 80% threshold
        ExpenseBudget::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->catFood->id,
            'fiscal_year' => $currentYear,
            'period_type' => 'MONTHLY',
            'period_month' => $currentMonth,
            'budget_amount' => 200000,
            'alert_threshold_percent' => 80,
        ]);

        // Spend 180,000 MMK (90% of budget => WARNING status)
        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-BGT-01',
            'category_id' => $this->catFood->id,
            'title' => 'Bulk Flour and Butter',
            'amount' => 180000,
            'total_amount' => 180000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_APPROVED,
        ]);

        $reportService = app(ExpenseReportService::class);
        $comparison = $reportService->getBudgetVsActual($this->restaurant, $currentYear, $currentMonth);

        $this->assertNotEmpty($comparison);
        $foodBudget = collect($comparison)->firstWhere('category_id', $this->catFood->id);
        $this->assertNotNull($foodBudget);
        $this->assertEquals(180000, $foodBudget['actual_spent']);
        $this->assertEquals(20000, $foodBudget['variance']);
        $this->assertEquals(90.0, $foodBudget['percentage_used']);
        $this->assertEquals('WARNING', $foodBudget['status']);

        // Check web views render with 200 OK without errors
        $response = $this->actingAs($this->owner)->get(route('admin.expenses.budgets'));
        $response->assertStatus(200);
        $response->assertSee('Budget vs Actual Cost Variance');

        $catResponse = $this->actingAs($this->owner)->get(route('admin.expenses.categories'));
        $catResponse->assertStatus(200);
        $catResponse->assertSee('Expense Categories & GL Chart of Accounts', false);

        $venResponse = $this->actingAs($this->owner)->get(route('admin.expenses.vendors'));
        $venResponse->assertStatus(200);
        $venResponse->assertSee('Vendors, Wholesalers & Commercial Suppliers', false);

        $recResponse = $this->actingAs($this->owner)->get(route('admin.expenses.recurring'));
        $recResponse->assertStatus(200);
        $recResponse->assertSee('Recurring Expense Automation & Retainers', false);
    }

    public function test_recurring_expense_generation(): void
    {
        $recurring = RecurringExpense::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->catRent->id,
            'title' => 'Monthly Kitchen Rent',
            'amount' => 500000,
            'frequency' => 'MONTHLY',
            'start_date' => now()->startOfYear()->toDateString(),
            'next_due_date' => now()->toDateString(), // Due today
            'auto_submit' => true,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $expenseService = app(ExpenseService::class);
        $count = $expenseService->generateRecurringExpenses($this->restaurant);

        $this->assertEquals(1, $count);

        // Check new expense was generated
        $generated = Expense::where('recurring_expense_id', $recurring->id)->first();
        $this->assertNotNull($generated);
        $this->assertEquals(500000, $generated->amount);
        $this->assertEquals(Expense::STATUS_PENDING_APPROVAL, $generated->status);

        // Check recurring next_due_date was advanced
        $recurring->refresh();
        $this->assertTrue(Carbon::parse($recurring->next_due_date)->isAfter(now()->startOfDay()));
    }

    public function test_csv_export_returns_streamed_file_with_mmk(): void
    {
        Expense::create([
            'restaurant_id' => $this->restaurant->id,
            'expense_number' => 'EXP-CSV-001',
            'category_id' => $this->catFood->id,
            'title' => 'CSV Export Sample Line',
            'amount' => 35000,
            'total_amount' => 35000,
            'expense_date' => now()->toDateString(),
            'status' => Expense::STATUS_PAID,
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.expenses.export'));

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));

        // Capture streamed content
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Currency: MMK (Burmese Kyats)', $csvContent);
        $this->assertStringContainsString('Total Amount (MMK)', $csvContent);
        $this->assertStringContainsString('EXP-CSV-001', $csvContent);
    }
}
