<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccessApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Manual Access API endpoints (sebagai backup jika TomatoPHP tidak working)
    Route::apiResource('accesses', AccessApiController::class);
});

// Mobile Sync API
Route::post('/sync/push', [SyncController::class, 'push'])->middleware('auth:sanctum');
Route::get('/sync/pull', [SyncController::class, 'pull'])->middleware('auth:sanctum');
Route::post('/mobile/sync', [SyncController::class, 'mobileSync'])->middleware('auth:sanctum');

// Tomato API routes akan otomatis ter-generate dengan middleware auth:sanctum