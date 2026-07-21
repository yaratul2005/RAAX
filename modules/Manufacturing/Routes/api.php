<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\BOMController;
use Modules\Manufacturing\Http\Controllers\WorkOrderController;

Route::middleware(['api', 'auth', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/manufacturing/bom', [BOMController::class, 'store']);
    Route::post('/manufacturing/work-orders', [WorkOrderController::class, 'store']);
    Route::post('/manufacturing/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);
    Route::post('/manufacturing/mushak-4-3', [WorkOrderController::class, 'generateMushak']);
    Route::post('/manufacturing/mrp/run', [\Modules\Manufacturing\Http\Controllers\MRPController::class, 'run']);
    Route::get('/manufacturing/mrp/runs/{mrpRun}/orders', [\Modules\Manufacturing\Http\Controllers\MRPController::class, 'orders']);
    Route::post('/manufacturing/mrp/runs/{mrpRun}/release', [\Modules\Manufacturing\Http\Controllers\MRPController::class, 'release']);
});
