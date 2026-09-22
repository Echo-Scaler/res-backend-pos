<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OwnerDashboardMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Management Dashboard.
     */
    public function index(Request $request, OwnerDashboardMetricsService $metricsService): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $selectedDate = $request->query('date', now()->format('Y-m-d'));
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Throwable $e) {
            $carbonDate = now();
            $selectedDate = $carbonDate->format('Y-m-d');
        }

        $dateLabel = $carbonDate->isToday()
            ? $carbonDate->format('d M Y').' (Today)'
            : $carbonDate->format('d M Y');

        // Base statistics
        $stats = [
            'total_users' => $restaurant ? $restaurant->users()->count() : 1,
            'status' => $restaurant && $restaurant->is_active ? 'Active' : 'Inactive',
        ];

        // Comprehensive 10 Owner KPI metrics
        $metrics = $restaurant ? $metricsService->getMetrics($restaurant) : [];

        return view('admin.dashboard', compact('user', 'restaurant', 'stats', 'metrics', 'selectedDate', 'dateLabel', 'carbonDate'));
    }
}
