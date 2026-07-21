<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\GoodsReceivedNoteController;
use Modules\Inventory\Http\Controllers\InventoryValuationController;

Route::middleware(['api', 'auth', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/inventory/grn', [GoodsReceivedNoteController::class, 'store']);
    Route::get('/inventory/valuation/{sku}', [InventoryValuationController::class, 'valuation']);
    Route::post('/inventory/transfers', [\Modules\Inventory\Http\Controllers\IntercompanyTransferController::class, 'store']);
    Route::post('/inventory/transfers/{transfer}/ship', [\Modules\Inventory\Http\Controllers\IntercompanyTransferController::class, 'ship']);
    Route::post('/inventory/transfers/{transfer}/receive', [\Modules\Inventory\Http\Controllers\IntercompanyTransferController::class, 'receive']);
});
