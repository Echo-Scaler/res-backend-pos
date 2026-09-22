<?php

use App\Http\Controllers\Web\AdminAuthController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AdminModuleController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\PromotionController;
use App\Http\Controllers\Web\RoleDashboardController;
use App\Http\Controllers\Web\RolePermissionController;
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

    // 14 Owner Modules
    Route::get('/settings/restaurant', [AdminModuleController::class, 'show'])->defaults('module', 'restaurant-settings')->name('settings.restaurant');
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions');
    Route::post('/roles-permissions/update-role', [RolePermissionController::class, 'updateRole'])->name('roles.permissions.updateRole');
    Route::post('/roles-permissions/toggle-permission', [RolePermissionController::class, 'togglePermission'])->name('roles.permissions.togglePermission');
    Route::get('/tables', [AdminModuleController::class, 'show'])->defaults('module', 'tables')->name('tables.index');
    Route::get('/orders', [AdminModuleController::class, 'show'])->defaults('module', 'orders')->name('orders.index');
    Route::get('/payments', [AdminModuleController::class, 'show'])->defaults('module', 'payments')->name('payments.index');
    Route::get('/customers', [AdminModuleController::class, 'show'])->defaults('module', 'customers')->name('customers.index');
    Route::get('/reports', [AdminModuleController::class, 'show'])->defaults('module', 'reports')->name('reports.index');
    Route::get('/expenses', [AdminModuleController::class, 'show'])->defaults('module', 'expenses')->name('expenses.index');
    Route::get('/settings/tax', [AdminModuleController::class, 'show'])->defaults('module', 'tax-settings')->name('settings.tax');
    Route::get('/settings/business', [AdminModuleController::class, 'show'])->defaults('module', 'business-settings')->name('settings.business');
    Route::get('/audit-logs', [AdminModuleController::class, 'show'])->defaults('module', 'audit-logs')->name('audit.logs');
    Route::get('/account/security', [AdminModuleController::class, 'show'])->defaults('module', 'account-security')->name('account.security');
});

// Operations Management: Accessible by OWNER & MANAGER
Route::middleware(['auth', 'role:OWNER|MANAGER'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class);

    // Menu & Products
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu/categories', [MenuController::class, 'storeCategory'])->name('menu.categories.store');
    Route::post('/menu/products', [MenuController::class, 'storeProduct'])->name('menu.products.store');
    Route::put('/menu/products/{product}', [MenuController::class, 'updateProduct'])->name('menu.products.update');
    Route::post('/menu/products/{product}/toggle', [MenuController::class, 'toggleProductAvailability'])->name('menu.products.toggle');
    Route::delete('/menu/products/{product}', [MenuController::class, 'destroyProduct'])->name('menu.products.destroy');

    // Inventory Stocks, Thresholds & Reports
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/report', [InventoryController::class, 'report'])->name('inventory.report');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Promotions & Coupons
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::post('/promotions/{promotion}/toggle', [PromotionController::class, 'toggleActive'])->name('promotions.toggle');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
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
