<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Expense\ApproveExpenseRequest;
use App\Http\Requests\Admin\Expense\PayExpenseRequest;
use App\Http\Requests\Admin\Expense\RejectExpenseRequest;
use App\Http\Requests\Admin\Expense\StoreBudgetRequest;
use App\Http\Requests\Admin\Expense\StoreCategoryRequest;
use App\Http\Requests\Admin\Expense\StoreExpenseRequest;
use App\Http\Requests\Admin\Expense\StoreRecurringRequest;
use App\Http\Requests\Admin\Expense\StoreVendorRequest;
use App\Http\Requests\Admin\Expense\UpdateCategoryRequest;
use App\Http\Requests\Admin\Expense\UpdateExpenseRequest;
use App\Http\Requests\Admin\Expense\UpdateVendorRequest;
use App\Http\Requests\Admin\Expense\VoidExpenseRequest;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\RecurringExpense;
use App\Models\Restaurant;
use App\Models\Vendor;
use App\Services\ExpenseReportService;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService,
        protected ExpenseReportService $reportService
    ) {}

    /**
     * Display a listing of expenses with KPI summary, search, and multi-filters.
     */
    public function index(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);

        $query = Expense::with(['categoryRelation', 'vendor', 'creator', 'approver', 'payer'])
            ->where('restaurant_id', $restaurant->id);

        // Search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('expense_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('vendor', fn ($vq) => $vq->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Vendor filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }

        // Payment Method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Due status filter
        if ($request->input('due_status') === 'overdue') {
            $query->overdue();
        }

        // Date range
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('expense_date', [$request->input('from'), $request->input('to')]);
        }

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $summary = $this->reportService->getSummary(
            $restaurant,
            $request->input('from'),
            $request->input('to')
        );

        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();
        $vendors = Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return view('admin.expenses.index', compact('expenses', 'summary', 'categories', 'vendors'));
    }

    /**
     * Show form for creating a new expense.
     */
    public function create(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();
        $vendors = Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return view('admin.expenses.create', compact('categories', 'vendors'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $expense = $this->expenseService->createExpense(
            $request->validated(),
            $request->user(),
            $request->file('receipt')
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense #'.$expense->expense_number.' created successfully.');
    }

    /**
     * Display detailed expense information with lifecycle, cash/bank transactions, and audit logs.
     */
    public function show(Request $request, Expense $expense): View
    {
        $this->authorizeExpense($request, $expense);

        $expense->load([
            'categoryRelation',
            'vendor',
            'creator',
            'approver',
            'rejecter',
            'payer',
            'voider',
            'attachments.uploader',
            'transactions.processor',
        ]);

        $auditLogs = AuditLog::where('restaurant_id', $expense->restaurant_id)
            ->where(function ($q) use ($expense) {
                $q->where('resource_id', $expense->id)
                    ->orWhere('resource_type', 'like', "%#{$expense->expense_number}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.expenses.show', compact('expense', 'auditLogs'));
    }

    /**
     * Show form for editing an expense.
     */
    public function edit(Request $request, Expense $expense): View|RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Paid expenses cannot be edited. Please void if needed.');
        }

        if ($expense->isVoid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Voided expenses cannot be edited.');
        }

        $restaurant = $this->getRestaurant($request);
        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();
        $vendors = Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return view('admin.expenses.edit', compact('expense', 'categories', 'vendors'));
    }

    /**
     * Update an existing expense.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $this->expenseService->updateExpense(
            $expense,
            $request->validated(),
            $request->user(),
            $request->file('receipt')
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Submit an expense for approval.
     */
    public function submit(Request $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $this->expenseService->submitForApproval($expense, $request->user());

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense #'.$expense->expense_number.' submitted for manager review.');
    }

    /**
     * Approve an expense.
     */
    public function approve(ApproveExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $this->expenseService->approveExpense(
            $expense,
            $request->user(),
            $request->validated()['notes'] ?? null
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense approved. Ready for payment disbursement.');
    }

    /**
     * Reject an expense.
     */
    public function reject(RejectExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $this->expenseService->rejectExpense(
            $expense,
            $request->user(),
            $request->validated()['reason']
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('warning', 'Expense rejected with note provided to staff.');
    }

    /**
     * Record payment for an approved expense (Cash Drawer / Bank Ledger Integration).
     */
    public function pay(PayExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $transaction = $this->expenseService->processPayment(
            $expense,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Payment recorded. Financial ledger entry #'.$transaction->id.' created.');
    }

    /**
     * Void an expense transaction.
     */
    public function void(VoidExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeExpense($request, $expense);

        $this->expenseService->voidExpense(
            $expense,
            $request->user(),
            $request->validated()['reason']
        );

        return redirect()->route('admin.expenses.show', $expense)
            ->with('warning', 'Expense #'.$expense->expense_number.' has been voided.');
    }

    /**
     * Categories management view.
     */
    public function categories(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $categories = ExpenseCategory::withCount('expenses')
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_categories' => ExpenseCategory::where('restaurant_id', $restaurant->id)->count(),
            'active_categories' => ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->count(),
            'gl_mapped' => ExpenseCategory::where('restaurant_id', $restaurant->id)->whereNotNull('gl_account_code')->count(),
            'total_expenses' => Expense::where('restaurant_id', $restaurant->id)->whereNotNull('category_id')->count(),
        ];

        return view('admin.expenses.categories', compact('categories', 'stats'));
    }

    /**
     * Store new expense category.
     */
    public function storeCategory(StoreCategoryRequest $request): RedirectResponse
    {
        $restaurant = $this->getRestaurant($request);

        ExpenseCategory::create(array_merge($request->validated(), [
            'restaurant_id' => $restaurant->id,
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.expenses.categories')
            ->with('success', 'Expense category created successfully.');
    }

    /**
     * Update expense category.
     */
    public function updateCategory(UpdateCategoryRequest $request, ExpenseCategory $category): RedirectResponse
    {
        $category->update(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.expenses.categories')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Vendors management view.
     */
    public function vendors(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $vendors = Vendor::withCount('expenses')
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_vendors' => Vendor::where('restaurant_id', $restaurant->id)->count(),
            'active_suppliers' => Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->count(),
            'total_invoices' => Expense::where('restaurant_id', $restaurant->id)->whereNotNull('vendor_id')->count(),
            'avg_credit_days' => round(Vendor::where('restaurant_id', $restaurant->id)->avg('payment_terms_days') ?: 30),
        ];

        return view('admin.expenses.vendors', compact('vendors', 'stats'));
    }

    /**
     * Store new vendor.
     */
    public function storeVendor(StoreVendorRequest $request): RedirectResponse
    {
        $restaurant = $this->getRestaurant($request);

        Vendor::create(array_merge($request->validated(), [
            'restaurant_id' => $restaurant->id,
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.expenses.vendors')
            ->with('success', 'Vendor registered successfully.');
    }

    /**
     * Update vendor.
     */
    public function updateVendor(UpdateVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update(array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.expenses.vendors')
            ->with('success', 'Vendor information updated.');
    }

    /**
     * Budgets & Budget vs Actual view.
     */
    public function budgets(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $budgetComparison = $this->reportService->getBudgetVsActual($restaurant, $year, $month);
        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return view('admin.expenses.budgets', compact('budgetComparison', 'categories', 'year', 'month'));
    }

    /**
     * Store or update budget limit.
     */
    public function storeBudget(StoreBudgetRequest $request): RedirectResponse
    {
        $restaurant = $this->getRestaurant($request);

        ExpenseBudget::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => $request->input('category_id'),
                'fiscal_year' => $request->input('fiscal_year'),
                'period_type' => $request->input('period_type'),
                'period_month' => $request->input('period_month'),
            ],
            [
                'budget_amount' => $request->input('budget_amount'),
                'alert_threshold_percent' => $request->input('alert_threshold_percent', 80),
                'notes' => $request->input('notes'),
            ]
        );

        return redirect()->route('admin.expenses.budgets')
            ->with('success', 'Monthly category budget updated.');
    }

    /**
     * Recurring expense templates view.
     */
    public function recurring(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $templates = RecurringExpense::with(['category', 'vendor'])
            ->where('restaurant_id', $restaurant->id)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'active_schedules' => RecurringExpense::where('restaurant_id', $restaurant->id)->where('is_active', true)->count(),
            'monthly_commitment' => RecurringExpense::where('restaurant_id', $restaurant->id)->where('is_active', true)->sum('amount'),
            'auto_submit_count' => RecurringExpense::where('restaurant_id', $restaurant->id)->where('auto_submit', true)->count(),
            'total_templates' => RecurringExpense::where('restaurant_id', $restaurant->id)->count(),
        ];

        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();
        $vendors = Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return view('admin.expenses.recurring', compact('templates', 'stats', 'categories', 'vendors'));
    }

    /**
     * Store recurring expense schedule.
     */
    public function storeRecurring(StoreRecurringRequest $request): RedirectResponse
    {
        $restaurant = $this->getRestaurant($request);

        RecurringExpense::create(array_merge($request->validated(), [
            'restaurant_id' => $restaurant->id,
            'created_by' => $request->user()->id,
            'auto_submit' => $request->boolean('auto_submit'),
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.expenses.recurring')
            ->with('success', 'Recurring schedule registered successfully.');
    }

    /**
     * Manually trigger recurring expense generator.
     */
    public function triggerRecurring(Request $request): RedirectResponse
    {
        $restaurant = $this->getRestaurant($request);
        $count = $this->expenseService->generateRecurringExpenses($restaurant);

        return redirect()->route('admin.expenses.recurring')
            ->with('success', "Generated {$count} scheduled expense(s) due today.");
    }

    /**
     * Visual Expense Reports & Analytics view.
     */
    public function reports(Request $request): View
    {
        $restaurant = $this->getRestaurant($request);
        $from = $request->input('from');
        $to = $request->input('to');

        $summary = $this->reportService->getSummary($restaurant, $from, $to);
        $byCategory = $this->reportService->getByCategory($restaurant, $from, $to);
        $byEmployee = $this->reportService->getByEmployee($restaurant, $from, $to);
        $byVendor = $this->reportService->getByVendor($restaurant, $from, $to);
        $byPaymentMethod = $this->reportService->getByPaymentMethod($restaurant, $from, $to);
        $monthlyComparison = $this->reportService->getMonthlyComparison($restaurant, 6);
        $alerts = $this->reportService->getExpenseAlerts($restaurant);
        $forecast = $this->reportService->getExpenseForecast($restaurant);
        $journal = $this->reportService->getAccountingJournalEntries($restaurant, $from, $to);

        return view('admin.expenses.reports', compact(
            'summary',
            'byCategory',
            'byEmployee',
            'byVendor',
            'byPaymentMethod',
            'monthlyComparison',
            'alerts',
            'forecast',
            'journal',
            'from',
            'to'
        ));
    }

    /**
     * Export filtered expenses to CSV conforming to MMK Rule #6.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $restaurant = $this->getRestaurant($request);

        return $this->reportService->exportCsv($restaurant, $request->all());
    }

    /**
     * Retrieve the active restaurant.
     */
    protected function getRestaurant(Request $request): Restaurant
    {
        return $request->user()->restaurant ?? Restaurant::firstOrFail();
    }

    /**
     * Ensure the expense belongs to the active restaurant.
     */
    protected function authorizeExpense(Request $request, Expense $expense): void
    {
        $restaurant = $this->getRestaurant($request);
        if ($expense->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to expense record.');
        }
    }
}
