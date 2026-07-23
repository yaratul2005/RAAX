<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModuleRegistryController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1/system')->group(function () {
    Route::get('/modules', [ModuleRegistryController::class, 'index']);
});
