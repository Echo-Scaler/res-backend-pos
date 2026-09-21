<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Management Dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        // Statistics or summary items for the restaurant
        $stats = [
            'total_users' => $restaurant ? $restaurant->users()->count() : 1,
            'status' => $restaurant && $restaurant->is_active ? 'Active' : 'Inactive',
        ];

        return view('admin.dashboard', compact('user', 'restaurant', 'stats'));
    }
}
