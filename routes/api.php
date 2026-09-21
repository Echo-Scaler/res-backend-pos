<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Public Endpoints
Route::post('/setup', [AuthController::class, 'setup']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Endpoints (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
