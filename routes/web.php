<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AdminModuleController;
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

    // 17 Owner Modules
    Route::get('/settings/restaurant', [AdminModuleController::class, 'show'])->defaults('module', 'restaurant-settings')->name('settings.restaurant');
    Route::get('/roles-permissions', [AdminModuleController::class, 'show'])->defaults('module', 'roles-permissions')->name('roles.permissions');
    Route::get('/menu', [AdminModuleController::class, 'show'])->defaults('module', 'menu')->name('menu.index');
    Route::get('/inventory', [AdminModuleController::class, 'show'])->defaults('module', 'inventory')->name('inventory.index');
    Route::get('/tables', [AdminModuleController::class, 'show'])->defaults('module', 'tables')->name('tables.index');
    Route::get('/orders', [AdminModuleController::class, 'show'])->defaults('module', 'orders')->name('orders.index');
    Route::get('/payments', [AdminModuleController::class, 'show'])->defaults('module', 'payments')->name('payments.index');
    Route::get('/customers', [AdminModuleController::class, 'show'])->defaults('module', 'customers')->name('customers.index');
    Route::get('/promotions', [AdminModuleController::class, 'show'])->defaults('module', 'promotions')->name('promotions.index');
    Route::get('/reports', [AdminModuleController::class, 'show'])->defaults('module', 'reports')->name('reports.index');
    Route::get('/expenses', [AdminModuleController::class, 'show'])->defaults('module', 'expenses')->name('expenses.index');
    Route::get('/settings/tax', [AdminModuleController::class, 'show'])->defaults('module', 'tax-settings')->name('settings.tax');
    Route::get('/settings/business', [AdminModuleController::class, 'show'])->defaults('module', 'business-settings')->name('settings.business');
    Route::get('/audit-logs', [AdminModuleController::class, 'show'])->defaults('module', 'audit-logs')->name('audit.logs');
    Route::get('/account/security', [AdminModuleController::class, 'show'])->defaults('module', 'account-security')->name('account.security');
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
