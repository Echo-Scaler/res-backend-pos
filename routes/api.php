<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseApiController;
use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

// Public Endpoints
Route::post('/setup', [AuthController::class, 'setup']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Endpoints (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // POS Reports & Analytics
    Route::prefix('v1/reports')->group(function () {
        Route::get('/analytics', [ReportApiController::class, 'analytics']);
        Route::get('/cogs', [ReportApiController::class, 'cogs']);
        Route::get('/profit-loss', [ReportApiController::class, 'profitLoss']);
        Route::get('/table-performance', [ReportApiController::class, 'tablePerformance']);
        Route::get('/voids', [ReportApiController::class, 'voids']);
    });

    // POS Expense Management
    Route::prefix('v1/expenses')->group(function () {
        Route::get('/', [ExpenseApiController::class, 'index']);
        Route::post('/', [ExpenseApiController::class, 'store']);
        Route::get('/reports/summary', [ExpenseApiController::class, 'summary']);
        Route::get('/reports/by-category', [ExpenseApiController::class, 'byCategory']);
        Route::get('/reports/budget-vs-actual', [ExpenseApiController::class, 'budgetVsActual']);
        Route::get('/reports/forecast', [ExpenseApiController::class, 'forecast']);
        Route::get('/categories', [ExpenseApiController::class, 'categories']);
        Route::get('/vendors', [ExpenseApiController::class, 'vendors']);
        Route::get('/{expense}', [ExpenseApiController::class, 'show']);
        Route::put('/{expense}', [ExpenseApiController::class, 'update']);
        Route::post('/{expense}/submit', [ExpenseApiController::class, 'submit']);
        Route::post('/{expense}/approve', [ExpenseApiController::class, 'approve']);
        Route::post('/{expense}/reject', [ExpenseApiController::class, 'reject']);
        Route::post('/{expense}/pay', [ExpenseApiController::class, 'pay']);
        Route::post('/{expense}/void', [ExpenseApiController::class, 'void']);
    });
});
