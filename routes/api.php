<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ModuleRegistryController;
use App\Services\LicenseManagerService;
use App\Services\DocNumberingEngine;
use App\Services\ApprovalWorkflowEngine;
use Illuminate\Http\Request;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1/system')->group(function () {
    Route::get('/modules', [ModuleRegistryController::class, 'index']);

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
