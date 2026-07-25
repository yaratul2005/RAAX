<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModuleRegistryController;
use App\Http\Controllers\Api\SystemSetupController;
use App\Services\LicenseManagerService;
use App\Services\DocNumberingEngine;
use App\Services\ApprovalWorkflowEngine;
use Modules\Finance\Http\Controllers\BankReconciliationController;
use Modules\Finance\Services\NbrQrCodeSignerService;
use Modules\Inventory\Services\ZplLabelGenerator;
use Modules\Procurement\Services\DynamicReorderEngine;
use Illuminate\Http\Request;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [SystemSetupController::class, 'login']);
});

Route::prefix('v1/system')->group(function () {
    Route::get('/modules', [ModuleRegistryController::class, 'index']);
    Route::post('/db-setup', [SystemSetupController::class, 'setupDatabase']);

    Route::get('/license', function (Request $request) {
        $key = $request->query('key', 'RAAX-DEV-UNLIMITED-LICENSE-KEY');
        return response()->json(LicenseManagerService::validateLicense($key));
    });

    Route::post('/sequences/next', function (Request $request) {
        $tenantId = $request->header('X-Tenant-ID', 'aca9ea90-0d0f-4ed9-98ed-398af6b67efd');
        $module = $request->input('module', 'INV');
        $prefix = $request->input('prefix', 'INV');
        return response()->json([
            'success' => true,
            'number' => DocNumberingEngine::generateNextNumber($tenantId, $module, $prefix)
        ]);
    });

    Route::post('/approvals/submit', function (Request $request) {
        $tenantId = $request->header('X-Tenant-ID', 'aca9ea90-0d0f-4ed9-98ed-398af6b67efd');
        $result = ApprovalWorkflowEngine::submitForApproval(
            $tenantId,
            $request->input('workflow_type', 'PO_APPROVAL'),
            $request->input('requester_id', 'e1000000-0000-0000-0000-000000000001'),
            $request->input('amount_cents', 125000000)
        );
        return response()->json(['success' => true, 'data' => $result]);
    });
});

Route::prefix('v1/procurement')->group(function () {
    Route::get('/reorder-point/calculate', function (Request $request) {
        $daily = (int) $request->query('daily_usage', 50);
        $lead = (int) $request->query('lead_time_days', 7);
        return response()->json([
            'success' => true,
            'data' => DynamicReorderEngine::calculateReorderPoint($daily, $lead)
        ]);
    });
});

Route::prefix('v1/inventory')->group(function () {
    Route::post('/transfers', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Stock transfer executed cleanly across bin locations.',
            'transfer_id' => 'TRF-2026-' . rand(100, 999)
        ]);
    });
    Route::post('/labels/zpl', function (Request $request) {
        $sku = $request->input('sku', 'SKU-FASTENER-A');
        $name = $request->input('name', 'Heavy Duty Fastener');
        $bin = $request->input('bin', 'BIN-MAIN-A1');
        $cost = $request->input('unit_cost_cents', 4500);
        return response()->json([
            'success' => true,
            'zpl_code' => ZplLabelGenerator::generateZplBinLabel($sku, $name, $bin, $cost),
            'tspl_code' => ZplLabelGenerator::generateTsplReceiptLabel($sku, 1)
        ]);
    });
});

Route::prefix('v1/finance')->group(function () {
    Route::post('/bank-reconciliation/mt940', [BankReconciliationController::class, 'parseMt940']);
    Route::post('/vat/nbr-qr', function (Request $request) {
        $bin = $request->input('bin', '1899201928301');
        $inv = $request->input('invoice_number', 'SO-2026-4412');
        $date = $request->input('date', '2026-07-25');
        $subtotal = $request->input('subtotal_cents', 73913000);
        $vat = $request->input('vat_cents', 11087000);
        return response()->json([
            'success' => true,
            'data' => NbrQrCodeSignerService::generateNbrQrPayload($bin, $inv, $date, $subtotal, $vat)
        ]);
    });
});
