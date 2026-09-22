<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OwnerDashboardMetricsService;
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

        // Base statistics
        $stats = [
            'total_users' => $restaurant ? $restaurant->users()->count() : 1,
            'status' => $restaurant && $restaurant->is_active ? 'Active' : 'Inactive',
        ];

        // Comprehensive 10 Owner KPI metrics
        $metrics = $restaurant ? $metricsService->getMetrics($restaurant) : [];

        return view('admin.dashboard', compact('user', 'restaurant', 'stats', 'metrics'));
    }
}
