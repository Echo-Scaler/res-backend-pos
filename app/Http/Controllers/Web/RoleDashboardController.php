<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

        $stats = [
            'total_staff' => $restaurant ? $restaurant->users()->whereHas('roles', function ($q) {
                $q->whereIn('name', ['CASHIER', 'STAFF']);
            })->count() : 0,
            'floor_status' => 'Active Shifts Running',
            'pending_voids' => 0,
            'tables_count' => 24,
        ];

        return view('admin.roles.manager', compact('user', 'restaurant', 'stats'));
    }

    /**
     * Display the Cashier Register & Checkout Dashboard.
     */
    public function cashierIndex(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $register = [
            'status' => 'OPEN',
            'terminal_id' => 'POS-REG-01',
            'opening_balance' => '150,000 MMK',
            'current_sales' => '485,000 MMK',
            'transactions_count' => 18,
        ];

        return view('admin.roles.cashier', compact('user', 'restaurant', 'register'));
    }

    /**
     * Display the Waiter / Floor Staff Dashboard.
     */
    public function staffIndex(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $floor = [
            'assigned_zone' => 'Zone A (Main Dining)',
            'active_tables' => 6,
            'ready_pickup_orders' => 2,
            'table_list' => [
                ['number' => 'T-01', 'status' => 'occupied', 'guests' => 4, 'time' => '35m ago'],
                ['number' => 'T-02', 'status' => 'vacant', 'guests' => 0, 'time' => 'Available'],
                ['number' => 'T-03', 'status' => 'billing', 'guests' => 2, 'time' => '50m ago'],
                ['number' => 'T-04', 'status' => 'occupied', 'guests' => 6, 'time' => '15m ago'],
                ['number' => 'T-05', 'status' => 'vacant', 'guests' => 0, 'time' => 'Available'],
                ['number' => 'T-06', 'status' => 'occupied', 'guests' => 2, 'time' => '5m ago'],
            ],
        ];

        return view('admin.roles.staff', compact('user', 'restaurant', 'floor'));
    }
}
