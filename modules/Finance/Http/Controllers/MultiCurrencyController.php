<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\ExecuteRevaluationRequest;
use Modules\Finance\Http\Requests\StoreExchangeRateRequest;
use Modules\Finance\Models\CurrencyExchangeRate;
use Modules\Finance\Services\ForexRevaluationManager;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MultiCurrencyController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected ForexRevaluationManager $revaluationManager;

    public function __construct(TenantContextManager $tenantManager, ForexRevaluationManager $revaluationManager)
    {
        $this->tenantManager = $tenantManager;
        $this->revaluationManager = $revaluationManager;
    }

    public function storeRate(StoreExchangeRateRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $rate = CurrencyExchangeRate::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'from_currency' => $request->input('from_currency'),
                'to_currency' => $request->input('to_currency', 'BDT'),
                'effective_date' => $request->input('effective_date')
            ],
            [
                'id' => Str::uuid()->toString(),
                'rate_basis_points' => $request->input('rate_basis_points'),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $rate
        ], 201);
    }

    public function revalue(ExecuteRevaluationRequest $request): JsonResponse
    {
        try {
            $this->revaluationManager->runMonthEndRevaluation(
                $request->input('target_month'),
                $request->input('target_currency')
            );

            return response()->json([
                'success' => true,
                'message' => 'Forex revaluation executed and journals posted successfully.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function analysis(Request $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        // MVP basic exposure report: count of open invoices and total foreign amount per currency
        $exposure = \Modules\Finance\Models\FinanceInvoice::where('tenant_id', $tenantId)
            ->where('currency_code', '!=', 'BDT')
            ->where('status', '!=', 'paid')
            ->selectRaw('currency_code, type, sum(amount_cents - paid_cents) as total_exposure_cents, count(id) as invoice_count')
            ->groupBy('currency_code', 'type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exposure
        ]);
    }
}
