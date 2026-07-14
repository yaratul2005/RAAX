<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\VendorController;
use Modules\Procurement\Http\Controllers\PurchaseOrderController;

Route::middleware(['api', 'auth:sanctum', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/procurement/vendors', [VendorController::class, 'store']);
    Route::post('/procurement/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::post('/procurement/purchase-orders/{po}/approve', [PurchaseOrderController::class, 'approve']);
});
