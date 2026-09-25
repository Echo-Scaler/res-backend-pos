<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\ReportFilterRequest;
use App\Http\Resources\CogsReportResource;
use App\Http\Resources\ProfitLossResource;
use App\Http\Resources\ReportAnalyticsResource;
use App\Http\Resources\TablePerformanceResource;
use App\Http\Resources\VoidAnalysisResource;
use App\Services\ReportAnalyticsService;
use Illuminate\Http\JsonResponse;

class ReportApiController extends Controller
{
    public function __construct(
        protected ReportAnalyticsService $reportService
    ) {}

    /**
     * Get primary executive analytics.
     */
    public function analytics(ReportFilterRequest $request): JsonResponse
    {
        $restaurant = $request->user()->restaurant;
        [$start, $end, $periodLabel] = $this->reportService->parseDateRange(
            $request->input('period', 'today'),
            $request->input('start_date'),
            $request->input('end_date')
        );

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
     * Get Food Cost & COGS Recipe Profitability statement.
     */
    public function cogs(ReportFilterRequest $request): JsonResponse
    {
        $restaurant = $request->user()->restaurant;
        $cogs = $this->reportService->getFoodCostAndCogs($restaurant);

        return response()->json([
            'status' => 'success',
            'data' => new CogsReportResource($cogs),
        ]);
    }

    /**
     * Get Profit & Loss (P&L) Summary.
     */
    public function profitLoss(ReportFilterRequest $request): JsonResponse
    {
        $restaurant = $request->user()->restaurant;
        $pnl = $this->reportService->getProfitAndLossSummary($restaurant, $request->input('period', 'this_month'));

        return response()->json([
            'status' => 'success',
            'data' => new ProfitLossResource($pnl),
        ]);
    }

    /**
     * Get Table & Dine-in Performance metrics.
     */
    public function tablePerformance(ReportFilterRequest $request): JsonResponse
    {
        $restaurant = $request->user()->restaurant;
        $tablePerf = $this->reportService->getTablePerformance($restaurant);

        return response()->json([
            'status' => 'success',
            'data' => new TablePerformanceResource($tablePerf),
        ]);
    }

    /**
     * Get Cancellations and Voids Audit Analysis.
     */
    public function voids(ReportFilterRequest $request): JsonResponse
    {
        $restaurant = $request->user()->restaurant;
        $voids = $this->reportService->getCancellationVoidAnalysis($restaurant);

        return response()->json([
            'status' => 'success',
            'data' => new VoidAnalysisResource($voids),
        ]);
    }
}
