<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

// Public: register/login
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);

    Route::apiResource('transactions', TransactionController::class)->only(['index', 'store', 'show']);

    // Reporting
    Route::get('report', [ReportController::class, 'index']);
    Route::get('report/sales-range', [ReportController::class, 'salesByRange']);
    Route::post('logout', [AuthController::class, 'logout']);
});
