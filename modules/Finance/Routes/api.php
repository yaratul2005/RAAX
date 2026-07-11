<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\JournalController;

Route::middleware('tenant')->group(function () {
    Route::post('/finance/journals', [JournalController::class, 'post']);
});
