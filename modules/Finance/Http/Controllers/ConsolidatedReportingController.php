<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\CloseYearRequest;
use Modules\Finance\Models\FiscalYear;
use Modules\Finance\Services\FinancialConsolidationService;
use Modules\Finance\Services\FiscalYearClosingEngine;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsolidatedReportingController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected FinancialConsolidationService $consolidationService;
    protected FiscalYearClosingEngine $closingEngine;

    public function __construct(
        TenantContextManager $tenantManager,
        FinancialConsolidationService $consolidationService,
        FiscalYearClosingEngine $closingEngine
    ) {
        $this->tenantManager = $tenantManager;
        $this->consolidationService = $consolidationService;
        $this->closingEngine = $closingEngine;
    }

    public function storeFiscalYear(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $tenantId = $this->tenantManager->getTenantId();

        $fy = FiscalYear::create(array_merge(
            $request->only(['name', 'start_date', 'end_date']),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $fy
        ], 201);
    }

    public function closeFiscalYear(string $fiscalYearId, CloseYearRequest $request): JsonResponse
    {
        try {
            $this->closingEngine->closeFiscalYear($fiscalYearId, $request->input('retained_earnings_account_id'));

            return response()->json([
                'success' => true,
                'message' => 'Fiscal Year closed successfully and net income posted to retained earnings.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function consolidatedTrialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'branch_uuids' => 'required|array',
            'branch_uuids.*' => 'uuid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $report = $this->consolidationService->generateConsolidatedTrialBalance(
                $request->input('branch_uuids'),
                $request->input('start_date'),
                $request->input('end_date')
            );

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }
}
