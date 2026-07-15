<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\BOMController;
use Modules\Manufacturing\Http\Controllers\WorkOrderController;

Route::middleware(['api', 'auth:sanctum', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/manufacturing/bom', [BOMController::class, 'store']);
    Route::post('/manufacturing/work-orders', [WorkOrderController::class, 'store']);
    Route::post('/manufacturing/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);
    Route::post('/manufacturing/mushak-4-3', [WorkOrderController::class, 'generateMushak']);
});
