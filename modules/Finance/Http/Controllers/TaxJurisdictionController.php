<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\StoreTaxJurisdictionRequest;
use Modules\Finance\Http\Requests\StoreTaxRuleRequest;
use Modules\Finance\Models\TaxJurisdiction;
use Modules\Finance\Models\TaxRateRule;
use Modules\Finance\Services\TaxEngine\TaxEngineFactory;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxJurisdictionController extends Controller
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function storeJurisdiction(StoreTaxJurisdictionRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $jurisdiction = TaxJurisdiction::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'is_active' => true,
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $jurisdiction
        ], 201);
    }

    public function storeRule(StoreTaxRuleRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        // Ensure jurisdiction belongs to tenant
        $jurisdiction = TaxJurisdiction::where('tenant_id', $tenantId)->find($request->input('tax_jurisdiction_id'));
        if (!$jurisdiction) {
            return response()->json(['success' => false, 'message' => 'Jurisdiction not found'], 404);
        }

        $rule = TaxRateRule::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $rule
        ], 201);
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'tax_jurisdiction_id' => 'required|uuid',
            'base_amount_cents' => 'required|integer|min:0',
            'is_inter_state' => 'boolean', // specific to India GST context
        ]);

        $tenantId = $this->tenantManager->getTenantId();

        $jurisdiction = TaxJurisdiction::where('tenant_id', $tenantId)->find($request->input('tax_jurisdiction_id'));
        if (!$jurisdiction) {
            return response()->json(['success' => false, 'message' => 'Jurisdiction not found'], 404);
        }

        // Fetch the active standard rule for this jurisdiction for MVP
        $rule = TaxRateRule::where('tenant_id', $tenantId)
            ->where('tax_jurisdiction_id', $jurisdiction->id)
            ->where('type', 'standard')
            ->where('effective_from', '<=', now()->toDateString())
            ->orderBy('effective_from', 'desc')
            ->first();

        if (!$rule) {
            return response()->json(['success' => false, 'message' => 'No active standard tax rule found for this jurisdiction'], 422);
        }

        $engine = TaxEngineFactory::resolve($jurisdiction);
        $taxLines = $engine->calculateTax(
            $request->input('base_amount_cents'),
            $rule,
            ['is_inter_state' => $request->input('is_inter_state', false)]
        );

        return response()->json([
            'success' => true,
            'data' => $taxLines
        ]);
    }
}
