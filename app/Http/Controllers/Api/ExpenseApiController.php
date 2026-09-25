<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Expense\ApproveExpenseRequest;
use App\Http\Requests\Admin\Expense\PayExpenseRequest;
use App\Http\Requests\Admin\Expense\RejectExpenseRequest;
use App\Http\Requests\Admin\Expense\StoreExpenseRequest;
use App\Http\Requests\Admin\Expense\UpdateExpenseRequest;
use App\Http\Requests\Admin\Expense\VoidExpenseRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\ExpenseTransactionResource;
use App\Http\Resources\VendorResource;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Restaurant;
use App\Models\Vendor;
use App\Services\ExpenseReportService;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseApiController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService,
        protected ExpenseReportService $reportService
    ) {}

    /**
     * List expenses.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $restaurant = $this->getRestaurant($request);

        $query = Expense::with(['categoryRelation', 'vendor', 'creator', 'approver', 'payer'])
            ->where('restaurant_id', $restaurant->id);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('expense_date', [$request->input('from'), $request->input('to')]);
        }

        $expenses = $query->orderBy('expense_date', 'desc')
            ->paginate($request->integer('per_page', 10));

        return ExpenseResource::collection($expenses);
    }

    /**
     * Store new expense.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->createExpense(
            $request->validated(),
            $request->user(),
            $request->file('receipt')
        );

        return response()->json([
            'message' => 'Expense created successfully.',
            'data' => new ExpenseResource($expense),
        ], 201);
    }

    /**
     * Show expense details.
     */
    public function show(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $expense->load(['categoryRelation', 'vendor', 'creator', 'approver', 'rejecter', 'payer', 'voider', 'attachments', 'transactions']);

        return response()->json([
            'data' => new ExpenseResource($expense),
        ]);
    }

    /**
     * Update expense.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $updated = $this->expenseService->updateExpense(
            $expense,
            $request->validated(),
            $request->user(),
            $request->file('receipt')
        );

        return response()->json([
            'message' => 'Expense updated successfully.',
            'data' => new ExpenseResource($updated),
        ]);
    }

    /**
     * Submit expense for approval.
     */
    public function submit(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $submitted = $this->expenseService->submitForApproval($expense, $request->user());

        return response()->json([
            'message' => 'Expense submitted for approval.',
            'data' => new ExpenseResource($submitted),
        ]);
    }

    /**
     * Approve expense.
     */
    public function approve(ApproveExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $approved = $this->expenseService->approveExpense(
            $expense,
            $request->user(),
            $request->validated()['notes'] ?? null
        );

        return response()->json([
            'message' => 'Expense approved successfully.',
            'data' => new ExpenseResource($approved),
        ]);
    }

    /**
     * Reject expense.
     */
    public function reject(RejectExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $rejected = $this->expenseService->rejectExpense(
            $expense,
            $request->user(),
            $request->validated()['reason']
        );

        return response()->json([
            'message' => 'Expense rejected.',
            'data' => new ExpenseResource($rejected),
        ]);
    }

    /**
     * Process payment for expense.
     */
    public function pay(PayExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $transaction = $this->expenseService->processPayment(
            $expense,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Payment processed successfully.',
            'expense' => new ExpenseResource($expense->fresh()),
            'transaction' => new ExpenseTransactionResource($transaction),
        ]);
    }

    /**
     * Void expense.
     */
    public function void(VoidExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $voided = $this->expenseService->voidExpense(
            $expense,
            $request->user(),
            $request->validated()['reason']
        );

        return response()->json([
            'message' => 'Expense voided.',
            'data' => new ExpenseResource($voided),
        ]);
    }

    /**
     * Expense Categories list.
     */
    public function categories(Request $request): AnonymousResourceCollection
    {
        $restaurant = $this->getRestaurant($request);
        $categories = ExpenseCategory::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return ExpenseCategoryResource::collection($categories);
    }

    /**
     * Vendors list.
     */
    public function vendors(Request $request): AnonymousResourceCollection
    {
        $restaurant = $this->getRestaurant($request);
        $vendors = Vendor::where('restaurant_id', $restaurant->id)->where('is_active', true)->get();

        return VendorResource::collection($vendors);
    }

    /**
     * Analytical Summary API.
     */
    public function summary(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        $summary = $this->reportService->getSummary(
            $restaurant,
            $request->input('from'),
            $request->input('to')
        );

        return response()->json(['data' => $summary]);
    }

    /**
     * Category Breakdown API.
     */
    public function byCategory(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        $data = $this->reportService->getByCategory(
            $restaurant,
            $request->input('from'),
            $request->input('to')
        );

        return response()->json(['data' => $data]);
    }

    /**
     * Budget vs Actual API.
     */
    public function budgetVsActual(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $data = $this->reportService->getBudgetVsActual($restaurant, $year, $month);

        return response()->json(['data' => $data]);
    }

    /**
     * Expense Forecast API.
     */
    public function forecast(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        $data = $this->reportService->getExpenseForecast($restaurant);

        return response()->json(['data' => $data]);
    }

    protected function getRestaurant(Request $request): Restaurant
    {
        return $request->user()->restaurant ?? Restaurant::firstOrFail();
    }

    protected function authorizeExpense(Request $request, Expense $expense): void
    {
        $restaurant = $this->getRestaurant($request);
        if ($expense->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to expense.');
        }
    }
}
