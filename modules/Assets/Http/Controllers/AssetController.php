<?php

namespace Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Assets\Http\Requests\CreateAssetRequest;
use Modules\Assets\Models\DepreciationLog;
use Modules\Assets\Models\FixedAsset;
use Modules\Assets\Services\DepreciationEngine;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected DepreciationEngine $engine;

    public function __construct(TenantContextManager $tenantManager, DepreciationEngine $engine)
    {
        $this->tenantManager = $tenantManager;
        $this->engine = $engine;
    }

    public function store(CreateAssetRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $asset = FixedAsset::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $asset
        ], 201);
    }

    public function depreciate(Request $request): JsonResponse
    {
        $request->validate(['target_month' => 'required|date_format:Y-m']);

        try {
            $this->engine->runMonthlyDepreciation($request->input('target_month'));
            return response()->json([
                'success' => true,
                'message' => 'Depreciation run successfully.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function history(string $assetId): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $asset = FixedAsset::where('tenant_id', $tenantId)->find($assetId);
        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Asset not found.'], 404);
        }

        $logs = DepreciationLog::where('fixed_asset_id', $asset->id)
            ->orderBy('period_month', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
