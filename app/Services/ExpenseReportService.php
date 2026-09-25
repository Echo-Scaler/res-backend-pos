<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\Models\RecurringExpense;
use App\Models\Restaurant;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseReportService
{
    /**
     * 1. Expense Summary KPIs.
     *
     * @return array<string, mixed>
     */
    public function getSummary(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $allExpenses = $query->get();

        $totalAmount = (int) $allExpenses->sum('total_amount');
        $paidExpenses = $allExpenses->where('status', Expense::STATUS_PAID);
        $paidAmount = (int) $paidExpenses->sum('total_amount');

        $pendingApprovalExpenses = $allExpenses->where('status', Expense::STATUS_PENDING_APPROVAL);
        $pendingApprovalAmount = (int) $pendingApprovalExpenses->sum('total_amount');

        $today = now()->toDateString();
        $overdueExpenses = $allExpenses->filter(function (Expense $exp) use ($today) {
            return $exp->due_date && $exp->due_date->toDateString() < $today && ! $exp->isPaid();
        });
        $overdueAmount = (int) $overdueExpenses->sum('total_amount');

        $draftCount = $allExpenses->where('status', Expense::STATUS_DRAFT)->count();

        // Calculate daily average
        $dayCount = ($from && $to)
            ? max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1)
            : max(1, now()->day);

        $dailyAverage = (int) round($totalAmount / $dayCount);

        return [
            'total_amount' => $totalAmount,
            'formatted_total_amount' => number_format($totalAmount, 0).' MMK',
            'paid_amount' => $paidAmount,
            'formatted_paid_amount' => number_format($paidAmount, 0).' MMK',
            'pending_approval_amount' => $pendingApprovalAmount,
            'formatted_pending_approval_amount' => number_format($pendingApprovalAmount, 0).' MMK',
            'overdue_amount' => $overdueAmount,
            'formatted_overdue_amount' => number_format($overdueAmount, 0).' MMK',
            'total_count' => $allExpenses->count(),
            'paid_count' => $paidExpenses->count(),
            'pending_count' => $pendingApprovalExpenses->count(),
            'overdue_count' => $overdueExpenses->count(),
            'draft_count' => $draftCount,
            'daily_average' => $dailyAverage,
            'formatted_daily_average' => number_format($dailyAverage, 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
        ];
    }

    /**
     * 2. Expense Breakdown by Category.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByCategory(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::with('categoryRelation')
            ->where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->get();
        $totalSum = max(1, (int) $expenses->sum('total_amount'));

        $grouped = $expenses->groupBy(function (Expense $exp) {
            return $exp->categoryRelation ? $exp->categoryRelation->name : ($exp->category ?? 'Uncategorized');
        });

        $result = [];
        foreach ($grouped as $catName => $items) {
            $amount = (int) $items->sum('total_amount');
            $first = $items->first();
            $color = $first->categoryRelation?->color ?? '#9ec63b';
            $glCode = $first->categoryRelation?->gl_account_code ?? '5000';

            $result[] = [
                'category_name' => $catName,
                'gl_account_code' => $glCode,
                'color' => $color,
                'amount' => $amount,
                'formatted_amount' => number_format($amount, 0).' MMK',
                'percentage' => round(($amount / $totalSum) * 100, 1),
                'count' => $items->count(),
            ];
        }

        usort($result, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        return $result;
    }

    /**
     * 3. Expense Breakdown by Employee.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByEmployee(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::with('creator')
            ->where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->get();
        $grouped = $expenses->groupBy(fn (Expense $exp) => $exp->creator?->name ?? 'System Admin');

        $result = [];
        foreach ($grouped as $empName => $items) {
            $amount = (int) $items->sum('total_amount');
            $result[] = [
                'employee_name' => $empName,
                'total_amount' => $amount,
                'formatted_amount' => number_format($amount, 0).' MMK',
                'count' => $items->count(),
                'approved_count' => $items->where('status', Expense::STATUS_APPROVED)->count() + $items->where('status', Expense::STATUS_PAID)->count(),
            ];
        }

        usort($result, fn ($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return $result;
    }

    /**
     * 4. Expense Breakdown by Vendor.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByVendor(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::with('vendor')
            ->where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->get();
        $grouped = $expenses->groupBy(fn (Expense $exp) => $exp->vendor?->name ?? 'Direct Local Market / Petty Cash');

        $result = [];
        foreach ($grouped as $vendorName => $items) {
            $totalAmount = (int) $items->sum('total_amount');
            $paidAmount = (int) $items->where('status', Expense::STATUS_PAID)->sum('total_amount');
            $unpaidAmount = $totalAmount - $paidAmount;

            $result[] = [
                'vendor_name' => $vendorName,
                'total_amount' => $totalAmount,
                'formatted_total_amount' => number_format($totalAmount, 0).' MMK',
                'paid_amount' => $paidAmount,
                'formatted_paid_amount' => number_format($paidAmount, 0).' MMK',
                'unpaid_amount' => $unpaidAmount,
                'formatted_unpaid_amount' => number_format($unpaidAmount, 0).' MMK',
                'count' => $items->count(),
            ];
        }

        usort($result, fn ($a, $b) => $b['total_amount'] <=> $a['total_amount']);

        return $result;
    }

    /**
     * 5. Expense Breakdown by Payment Method.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByPaymentMethod(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::where('restaurant_id', $restaurant->id)
            ->where('status', Expense::STATUS_PAID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->get();
        $totalPaid = max(1, (int) $expenses->sum('total_amount'));

        $grouped = $expenses->groupBy(fn (Expense $exp) => $exp->payment_method ?: 'UNSPECIFIED');

        $result = [];
        foreach ($grouped as $method => $items) {
            $amount = (int) $items->sum('total_amount');
            $result[] = [
                'payment_method' => $method,
                'amount' => $amount,
                'formatted_amount' => number_format($amount, 0).' MMK',
                'percentage' => round(($amount / $totalPaid) * 100, 1),
                'count' => $items->count(),
            ];
        }

        usort($result, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        return $result;
    }

    /**
     * 6. Monthly Comparison (Last N Months).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMonthlyComparison(Restaurant $restaurant, int $months = 6): array
    {
        $result = [];
        $previousAmount = 0;

        for ($i = $months - 1; $i >= 0; $i--) {
            $targetMonth = now()->subMonths($i);
            $startDate = $targetMonth->copy()->startOfMonth()->toDateString();
            $endDate = $targetMonth->copy()->endOfMonth()->toDateString();

            $amount = (int) Expense::where('restaurant_id', $restaurant->id)
                ->where('status', '!=', Expense::STATUS_VOID)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('total_amount');

            $growthRate = 0.0;
            if ($previousAmount > 0) {
                $growthRate = round((($amount - $previousAmount) / $previousAmount) * 100, 1);
            }

            $result[] = [
                'month_label' => $targetMonth->format('M Y'),
                'year' => (int) $targetMonth->format('Y'),
                'month' => (int) $targetMonth->format('n'),
                'amount' => $amount,
                'formatted_amount' => number_format($amount, 0).' MMK',
                'growth_rate' => $growthRate,
                'growth_rate_formatted' => ($growthRate >= 0 ? '+' : '').$growthRate.'%',
            ];

            $previousAmount = $amount;
        }

        return $result;
    }

    /**
     * 7. Budget vs Actual Comparison.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBudgetVsActual(Restaurant $restaurant, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $budgets = ExpenseBudget::with('category')
            ->where('restaurant_id', $restaurant->id)
            ->where('fiscal_year', $year)
            ->where('period_type', 'MONTHLY')
            ->where('period_month', $month)
            ->get();

        $actualExpenses = Expense::where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(total_amount) as total_spent, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id')
            ->toArray();

        $result = [];

        foreach ($budgets as $budget) {
            $categoryId = $budget->category_id;
            $budgetAmount = (int) $budget->budget_amount;
            $actualSpent = (int) ($actualExpenses[$categoryId] ?? 0);
            $variance = $budgetAmount - $actualSpent;
            $percentageUsed = $budgetAmount > 0 ? round(($actualSpent / $budgetAmount) * 100, 1) : 0;

            $status = 'NORMAL';
            if ($percentageUsed >= 100) {
                $status = 'EXCEEDED';
            } elseif ($percentageUsed >= $budget->alert_threshold_percent) {
                $status = 'WARNING';
            }

            $result[] = [
                'category_id' => $categoryId,
                'category_name' => $budget->category?->name ?? 'Unknown',
                'budget_amount' => $budgetAmount,
                'formatted_budget' => number_format($budgetAmount, 0).' MMK',
                'actual_spent' => $actualSpent,
                'formatted_actual' => number_format($actualSpent, 0).' MMK',
                'variance' => $variance,
                'formatted_variance' => number_format(abs($variance), 0).' MMK'.($variance >= 0 ? ' Under' : ' Over'),
                'percentage_used' => $percentageUsed,
                'alert_threshold' => $budget->alert_threshold_percent,
                'status' => $status,
                'color' => $budget->category?->color ?? '#9ec63b',
            ];
        }

        return $result;
    }

    /**
     * 8. Expense Alerts (Overdue Bills + Budget Overruns).
     *
     * @return array<string, mixed>
     */
    public function getExpenseAlerts(Restaurant $restaurant): array
    {
        $today = now()->toDateString();

        // 1. Overdue unpaid expenses
        $overdueExpenses = Expense::with(['categoryRelation', 'vendor'])
            ->where('restaurant_id', $restaurant->id)
            ->whereNotIn('status', [Expense::STATUS_PAID, Expense::STATUS_VOID])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function (Expense $exp) {
                return [
                    'id' => $exp->id,
                    'expense_number' => $exp->expense_number,
                    'title' => $exp->title,
                    'vendor' => $exp->vendor?->name ?? 'Direct',
                    'amount' => $exp->total_amount ?: $exp->amount,
                    'formatted_amount' => number_format($exp->total_amount ?: $exp->amount, 0).' MMK',
                    'due_date' => $exp->due_date?->format('Y-m-d'),
                    'days_overdue' => now()->diffInDays($exp->due_date),
                ];
            });

        // 2. Budget Overruns
        $budgetItems = $this->getBudgetVsActual($restaurant);
        $overrunBudgets = array_values(array_filter($budgetItems, fn ($item) => in_array($item['status'], ['WARNING', 'EXCEEDED'], true)));

        return [
            'overdue_count' => count($overdueExpenses),
            'overdue_expenses' => $overdueExpenses,
            'budget_alert_count' => count($overrunBudgets),
            'budget_alerts' => $overrunBudgets,
            'total_alerts' => count($overdueExpenses) + count($overrunBudgets),
        ];
    }

    /**
     * 9. Expense Forecast for next month.
     *
     * @return array<string, mixed>
     */
    public function getExpenseForecast(Restaurant $restaurant): array
    {
        // Calculate recurring monthly commitments
        $recurringTotal = (int) RecurringExpense::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->where('frequency', 'MONTHLY')
            ->sum('amount');

        // Past 3 months historical average
        $past3MonthsTotal = (int) Expense::where('restaurant_id', $restaurant->id)
            ->where('status', '!=', Expense::STATUS_VOID)
            ->whereBetween('expense_date', [
                now()->subMonths(3)->startOfMonth()->toDateString(),
                now()->subMonth()->endOfMonth()->toDateString(),
            ])
            ->sum('total_amount');

        $historicalMonthlyAverage = (int) round($past3MonthsTotal / 3);

        // Projected forecast
        $projectedTotal = max($recurringTotal, $historicalMonthlyAverage);

        return [
            'next_month_label' => now()->addMonth()->format('F Y'),
            'recurring_commitments' => $recurringTotal,
            'formatted_recurring_commitments' => number_format($recurringTotal, 0).' MMK',
            'historical_monthly_average' => $historicalMonthlyAverage,
            'formatted_historical_monthly_average' => number_format($historicalMonthlyAverage, 0).' MMK',
            'projected_total' => $projectedTotal,
            'formatted_projected_total' => number_format($projectedTotal, 0).' MMK',
            'confidence_level' => 'HIGH (Based on recurring contracts and 90-day moving average)',
        ];
    }

    /**
     * 10. Accounting Integration (Chart of Accounts / Double-entry Journal stub).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAccountingJournalEntries(Restaurant $restaurant, ?string $from = null, ?string $to = null): array
    {
        $query = Expense::with(['categoryRelation', 'vendor'])
            ->where('restaurant_id', $restaurant->id)
            ->where('status', Expense::STATUS_PAID);

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->orderBy('expense_date', 'asc')->get();

        $journal = [];
        foreach ($expenses as $exp) {
            $debitGlCode = $exp->categoryRelation?->gl_account_code ?? '5001';
            $debitAccountName = $exp->categoryRelation?->name ?? 'Operating Expense';

            $creditGlCode = $exp->payment_method === 'CASH' ? '1001 (Cash Drawer)' : '1002 (Bank Account)';
            $amount = $exp->total_amount ?: $exp->amount;

            $journal[] = [
                'date' => $exp->payment_date ? $exp->payment_date->format('Y-m-d') : $exp->expense_date->format('Y-m-d'),
                'reference' => $exp->expense_number,
                'description' => $exp->title.' - '.$exp->vendor?->name,
                'debit_account' => $debitGlCode.' - '.$debitAccountName,
                'debit_amount' => $amount,
                'formatted_debit' => number_format($amount, 0).' MMK',
                'credit_account' => $creditGlCode,
                'credit_amount' => $amount,
                'formatted_credit' => number_format($amount, 0).' MMK',
            ];
        }

        return $journal;
    }

    /**
     * 11. Streamed CSV Export conforming to MMK Rule #6.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(Restaurant $restaurant, array $filters = []): StreamedResponse
    {
        $query = Expense::with(['categoryRelation', 'vendor', 'creator', 'approver', 'payer'])
            ->where('restaurant_id', $restaurant->id);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }
        if (! empty($filters['from']) && ! empty($filters['to'])) {
            $query->whereBetween('expense_date', [$filters['from'], $filters['to']]);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $filename = 'expense_report_'.now()->format('Ymd_His').'.csv';

        return new StreamedResponse(function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header metadata
            fputcsv($handle, ['Restaurant Expense Audit Report (European POS Standards)']);
            fputcsv($handle, ['Currency: MMK (Burmese Kyats)']);
            fputcsv($handle, ['Generated At: '.now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            // Column Headers
            fputcsv($handle, [
                'Expense #',
                'Expense Date',
                'Due Date',
                'Title',
                'Category',
                'Vendor',
                'Net Amount (MMK)',
                'Tax Amount (MMK)',
                'Total Amount (MMK)',
                'Status',
                'Payment Status',
                'Payment Method',
                'Payment Reference',
                'Paid Date',
                'Created By',
                'Approved By',
                'Notes',
            ]);

            foreach ($expenses as $exp) {
                fputcsv($handle, [
                    $exp->expense_number,
                    $exp->expense_date?->format('Y-m-d'),
                    $exp->due_date?->format('Y-m-d'),
                    $exp->title,
                    $exp->category_display_name,
                    $exp->vendor?->name ?? 'Direct Local',
                    $exp->amount,
                    $exp->tax_amount,
                    $exp->total_amount ?: $exp->amount,
                    $exp->status,
                    $exp->payment_status,
                    $exp->payment_method ?? 'N/A',
                    $exp->payment_reference ?? 'N/A',
                    $exp->payment_date?->format('Y-m-d') ?? 'N/A',
                    $exp->creator?->name ?? 'N/A',
                    $exp->approver?->name ?? 'N/A',
                    $exp->notes,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
