<?php

use App\Http\Controllers\Api\AuthController;
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
});
