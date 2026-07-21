<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\CustomerController;
use Modules\Sales\Http\Controllers\SalesOrderController;

Route::middleware(['api', 'auth', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/sales/customers', [CustomerController::class, 'store']);
    Route::post('/sales/orders', [SalesOrderController::class, 'store']);
    Route::post('/sales/orders/{order}/confirm', [SalesOrderController::class, 'confirm']);
});
