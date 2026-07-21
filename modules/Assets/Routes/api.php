<?php

use Illuminate\Support\Facades\Route;
use Modules\Assets\Http\Controllers\AssetController;

Route::middleware(['api', 'auth', 'tenant'])->prefix('api/v1')->group(function () {
    Route::post('/assets', [AssetController::class, 'store']);
    Route::post('/assets/depreciate', [AssetController::class, 'depreciate']);
    Route::get('/assets/{asset}/history', [AssetController::class, 'history']);
});
