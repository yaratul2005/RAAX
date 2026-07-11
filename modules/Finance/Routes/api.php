<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\ChartOfAccountsController;
use Modules\Finance\Http\Controllers\JournalController;
use Modules\Finance\Http\Controllers\TrialBalanceController;

Route::middleware('tenant')->group(function () {
    Route::post('/finance/journals', [JournalController::class, 'post']);
    Route::post('/finance/accounts', [ChartOfAccountsController::class, 'store']);
    Route::get('/finance/accounts', [ChartOfAccountsController::class, 'index']);
    Route::get('/finance/trial-balance', [TrialBalanceController::class, 'generate']);
});
