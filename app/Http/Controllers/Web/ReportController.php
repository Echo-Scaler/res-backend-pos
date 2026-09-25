<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\ExportReportRequest;
use App\Http\Requests\Admin\Report\ReportFilterRequest;
use App\Http\Resources\ReportAnalyticsResource;
use App\Services\ReportAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportAnalyticsService $reportService
    ) {}

    /**
     * Display the Enterprise Reports & Analytics workspace.
     */
    public function index(ReportFilterRequest $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $period = $request->input('period', 'today');
        $tab = $request->input('tab', 'overview');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$start, $end, $periodLabel] = $this->reportService->parseDateRange($period, $startDate, $endDate);

        // Core data
        $kpis = $this->reportService->getKpiSummary($restaurant, $start, $end);
        $todayOverview = $this->reportService->getTodayOverview($restaurant);
        $dailyBreakdown = $this->reportService->getDailyBreakdown($restaurant, $start, $end);
        $salesTrends = $this->reportService->getSalesTrends($restaurant);
        $salesByCategory = $this->reportService->getSalesByCategory($restaurant, $start, $end);
        $foodCostCogs = $this->reportService->getFoodCostAndCogs($restaurant);
        $pnl = $this->reportService->getProfitAndLossSummary($restaurant, $period);
        $alcohol = $this->reportService->getAlcoholSalesReport($restaurant);
        $tablePerf = $this->reportService->getTablePerformance($restaurant);
        $orderTypes = $this->reportService->getOrderTypeReport($restaurant);
        $voidAnalysis = $this->reportService->getCancellationVoidAnalysis($restaurant);
        $promotions = $this->reportService->getPromotionAnalytics($restaurant);
        $expenses = $this->reportService->getExpenseReport($restaurant);
        $comparisons = $this->reportService->getSalesComparison($restaurant);
        $forecast = $this->reportService->getSalesForecast($restaurant);
        $auditLogs = $this->reportService->getAuditLogs($restaurant);

        return view('admin.reports.index', [
            'user' => $user,
            'restaurant' => $restaurant,
            'activeTab' => $tab,
            'activePeriod' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'kpis' => $kpis,
            'todayOverview' => $todayOverview,
            'dailyBreakdown' => $dailyBreakdown,
            'salesTrends' => $salesTrends,
            'salesByCategory' => $salesByCategory,
            'foodCostCogs' => $foodCostCogs,
            'pnl' => $pnl,
            'alcohol' => $alcohol,
            'tablePerf' => $tablePerf,
            'orderTypes' => $orderTypes,
            'voidAnalysis' => $voidAnalysis,
            'promotions' => $promotions,
            'expenses' => $expenses,
            'comparisons' => $comparisons,
            'forecast' => $forecast,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Asynchronous JSON endpoint for instant AJAX tab/period switching.
     */
    public function apiData(ReportFilterRequest $request): JsonResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$start, $end, $periodLabel] = $this->reportService->parseDateRange($period, $startDate, $endDate);

        $payload = [
            'period_label' => $periodLabel,
            'kpis' => $this->reportService->getKpiSummary($restaurant, $start, $end),
            'today_overview' => $this->reportService->getTodayOverview($restaurant),
            'daily_breakdown' => $this->reportService->getDailyBreakdown($restaurant, $start, $end),
            'sales_by_category' => $this->reportService->getSalesByCategory($restaurant, $start, $end),
            'sales_trends' => $this->reportService->getSalesTrends($restaurant),
            'order_types' => $this->reportService->getOrderTypeReport($restaurant),
            'sales_comparison' => $this->reportService->getSalesComparison($restaurant),
        ];

        return response()->json([
            'status' => 'success',
            'data' => new ReportAnalyticsResource($payload),
        ]);
    }

    /**
     * Stream CSV download of the selected report sector breakdown.
     */
    public function exportCsv(ExportReportRequest $request): StreamedResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $tab = $request->input('tab', 'overview');
        $period = $request->input('period', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return $this->reportService->exportReportCsv(
            restaurant: $restaurant,
            tab: $tab,
            period: $period,
            startDate: $startDate,
            endDate: $endDate,
            user: $user
        );
    }
}
