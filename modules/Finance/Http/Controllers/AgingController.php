<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\CreateInvoiceRequest;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Services\AgingAnalysisService;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AgingController extends Controller
{
    protected AgingAnalysisService $agingService;
    protected TenantContextManager $tenantManager;

    public function __construct(AgingAnalysisService $agingService, TenantContextManager $tenantManager)
    {
        $this->agingService = $agingService;
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $invoice = FinanceInvoice::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'unpaid',
                'paid_cents' => 0,
                'currency_code' => $request->input('currency_code', 'USD'),
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $invoice
        ], 201);
    }

    public function agingAp(Request $request): JsonResponse
    {
        $evaluationDate = $request->query('evaluation_date');
        $schedule = $this->agingService->getAgingSchedule('AP', $evaluationDate);

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function agingAr(Request $request): JsonResponse
    {
        $evaluationDate = $request->query('evaluation_date');
        $schedule = $this->agingService->getAgingSchedule('AR', $evaluationDate);

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }
}
