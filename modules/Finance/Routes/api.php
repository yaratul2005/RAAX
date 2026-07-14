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
    Route::post('/finance/invoices', [\Modules\Finance\Http\Controllers\AgingController::class, 'store']);
    Route::get('/finance/aging/ap', [\Modules\Finance\Http\Controllers\AgingController::class, 'agingAp']);
    Route::get('/finance/aging/ar', [\Modules\Finance\Http\Controllers\AgingController::class, 'agingAr']);
});
