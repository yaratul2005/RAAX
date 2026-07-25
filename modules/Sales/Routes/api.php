<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\CustomerController;
use Modules\Sales\Http\Controllers\SalesOrderController;

Route::middleware(['api', 'tenant'])->prefix('api/v1')->group(function () {
    Route::get('/sales/customers', [CustomerController::class, 'index']);
    Route::post('/sales/customers', [CustomerController::class, 'store']);
    Route::get('/sales/orders', [SalesOrderController::class, 'index']);
    Route::post('/sales/orders', [SalesOrderController::class, 'store']);
    Route::post('/sales/orders/{order}/confirm', [SalesOrderController::class, 'confirm']);
});
