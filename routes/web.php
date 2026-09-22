<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\RoleDashboardController;
use Illuminate\Support\Facades\Route;

// Root route redirects to role dashboard if authenticated, or login
Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('home');

// Authentication Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// 1. OWNER Executive Portal & Back-Office
Route::middleware(['auth', 'role:OWNER'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

// Employee Management (Accessible by OWNER & MANAGER with role-based policies)
Route::middleware(['auth', 'role:OWNER|MANAGER'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class);
});

// 2. MANAGER Operations & Floor Management Portal
Route::middleware(['auth', 'role:OWNER|MANAGER'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('manager.dashboard');
    });
    Route::get('/dashboard', [RoleDashboardController::class, 'managerIndex'])->name('dashboard');
});

// 3. CASHIER POS Register & Checkout Counter Portal
Route::middleware(['auth', 'role:OWNER|MANAGER|CASHIER'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('cashier.dashboard');
    });
    Route::get('/dashboard', [RoleDashboardController::class, 'cashierIndex'])->name('dashboard');
});

// 4. STAFF / Waiter Floor Ordering Portal
Route::middleware(['auth', 'role:OWNER|MANAGER|STAFF'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('staff.dashboard');
    });
    Route::get('/dashboard', [RoleDashboardController::class, 'staffIndex'])->name('dashboard');
});
