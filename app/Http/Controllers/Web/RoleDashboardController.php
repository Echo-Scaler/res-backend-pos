<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CashDrawerSession;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    /**
     * Display the Manager Operations Dashboard.
     */
    public function managerIndex(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $tablesCount = DiningTable::where('restaurant_id', $restaurant->id)->count();
        $activeTables = DiningTable::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['OCCUPIED', 'BILLING'])
            ->count();

        $activeShifts = CashDrawerSession::where('restaurant_id', $restaurant->id)
            ->where('status', 'OPEN')
            ->count();

        $todaySales = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->where('status', 'COMPLETED')
            ->sum('total_amount');

        $pendingKitchen = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->whereIn('kitchen_status', ['PENDING_COOK', 'RECEIVED', 'PREPARING'])
            ->count();

        $stats = [
            'total_staff' => $restaurant ? $restaurant->users()->whereHas('roles', function ($q) {
                $q->whereIn('name', ['CASHIER', 'STAFF']);
            })->count() : 0,
            'floor_status' => $activeShifts > 0 ? "{$activeShifts} Shifts Active" : 'Shift Closed',
            'tables_count' => $tablesCount,
            'active_tables' => $activeTables,
            'today_sales' => number_format($todaySales, 0).' MMK',
            'pending_kitchen' => $pendingKitchen,
            'pending_voids' => 0,
        ];

        return view('admin.roles.manager', compact('user', 'restaurant', 'stats'));
    }

    /**
     * Display the Cashier Register & Checkout Dashboard.
     */
    public function cashierIndex(Request $request)
    {
        return app(CashierCheckoutController::class)->index($request);
    }

    /**
     * Display the Waiter / Floor Staff Dashboard.
     */
    public function staffIndex(Request $request)
    {
        return app(StaffFloorController::class)->index($request);
    }
}
