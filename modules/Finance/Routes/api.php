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
    Route::post('/finance/bank/statements', [\Modules\Finance\Http\Controllers\BankReconciliationController::class, 'upload']);
    Route::post('/finance/bank/statements/{statement}/reconcile', [\Modules\Finance\Http\Controllers\BankReconciliationController::class, 'reconcile']);
    Route::get('/finance/bank/statements/{statement}/unmatched', [\Modules\Finance\Http\Controllers\BankReconciliationController::class, 'unmatched']);
    Route::post('/finance/fiscal-years', [\Modules\Finance\Http\Controllers\ConsolidatedReportingController::class, 'storeFiscalYear']);
    Route::post('/finance/fiscal-years/{fiscalYear}/close', [\Modules\Finance\Http\Controllers\ConsolidatedReportingController::class, 'closeFiscalYear']);
    Route::get('/finance/reports/consolidated-trial-balance', [\Modules\Finance\Http\Controllers\ConsolidatedReportingController::class, 'consolidatedTrialBalance']);
    Route::post('/finance/vat/deposits', [\Modules\Finance\Http\Controllers\VATComplianceController::class, 'storeDeposit']);
    Route::get('/finance/vat/returns/{period}', [\Modules\Finance\Http\Controllers\VATComplianceController::class, 'previewReturn']);
    Route::post('/finance/vat/returns/{period}/submit', [\Modules\Finance\Http\Controllers\VATComplianceController::class, 'submitReturn']);
    Route::post('/finance/vat/vds', [\Modules\Finance\Http\Controllers\VATAdjustmentController::class, 'issueVds']);
    Route::post('/finance/vat/credit-notes', [\Modules\Finance\Http\Controllers\VATAdjustmentController::class, 'processCreditNote']);
    Route::post('/finance/vat/debit-notes', [\Modules\Finance\Http\Controllers\VATAdjustmentController::class, 'processDebitNote']);
    Route::post('/finance/forex/rates', [\Modules\Finance\Http\Controllers\MultiCurrencyController::class, 'storeRate']);
    Route::post('/finance/forex/revalue', [\Modules\Finance\Http\Controllers\MultiCurrencyController::class, 'revalue']);
    Route::get('/finance/forex/analysis', [\Modules\Finance\Http\Controllers\MultiCurrencyController::class, 'analysis']);
});
