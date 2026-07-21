<?php

use Illuminate\Support\Facades\Route;
use Modules\EDI\Http\Controllers\EdiController;

// Apply the generic rate limiting explicitly and use our edi.auth middleware
Route::middleware(['api', 'edi.auth', 'throttle:60,1'])->prefix('api/v1')->group(function () {
    Route::post('/edi/inbound', [EdiController::class, 'inbound']);
    Route::get('/edi/outbound/{orderId}', [EdiController::class, 'outbound']);
});
