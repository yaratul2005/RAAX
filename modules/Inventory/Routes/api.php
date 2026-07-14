<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\GoodsReceivedNoteController;
use Modules\Inventory\Http\Controllers\InventoryValuationController;

Route::middleware(['api', 'auth:sanctum', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/inventory/grn', [GoodsReceivedNoteController::class, 'store']);
    Route::get('/inventory/valuation/{sku}', [InventoryValuationController::class, 'valuation']);
});
