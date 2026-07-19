<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\CreateDepositRequest;
use Modules\Finance\Models\TreasuryDeposit;
use Modules\Finance\Services\Mushak91Engine;
use Modules\Finance\Services\VATReconciliationManager;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VATComplianceController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected Mushak91Engine $mushakEngine;
    protected VATReconciliationManager $reconciliationManager;

    public function __construct(
        TenantContextManager $tenantManager,
        Mushak91Engine $mushakEngine,
        VATReconciliationManager $reconciliationManager
    ) {
        $this->tenantManager = $tenantManager;
        $this->mushakEngine = $mushakEngine;
        $this->reconciliationManager = $reconciliationManager;
    }

    public function storeDeposit(CreateDepositRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $deposit = TreasuryDeposit::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'cleared',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $deposit
        ], 201);
    }

    public function previewReturn(string $period): JsonResponse
    {
        try {
            $return = $this->mushakEngine->compileMonthlyReturn($period);
            return response()->json([
                'success' => true,
                'data' => $return
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function submitReturn(string $period, Request $request): JsonResponse
    {
        $request->validate(['treasury_deposit_id' => 'required|uuid']);

        try {
            // Ensure return is compiled
            $return = $this->mushakEngine->compileMonthlyReturn($period);

            // Reconcile and submit
            $this->reconciliationManager->submitReturnAndClearLiability($return->id, $request->input('treasury_deposit_id'));

            return response()->json([
                'success' => true,
                'message' => 'Mushak 9.1 Return submitted and VAT liability cleared.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
