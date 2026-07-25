<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\VendorController;
use Modules\Procurement\Http\Controllers\PurchaseOrderController;

Route::middleware(['api', 'tenant'])->prefix('api/v1')->group(function () {
    Route::get('/procurement/vendors', [VendorController::class, 'index']);
    Route::post('/procurement/vendors', [VendorController::class, 'store']);
    Route::get('/procurement/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::post('/procurement/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::post('/procurement/purchase-orders/{po}/approve', [PurchaseOrderController::class, 'approve']);
});
