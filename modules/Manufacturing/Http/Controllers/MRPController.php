<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Manufacturing\Http\Requests\InitiateMrpRunRequest;
use Modules\Manufacturing\Services\MRP\MRPRunEngine;
use Modules\Manufacturing\Services\MRP\PlannedOrderReleaseManager;
use Modules\Manufacturing\Models\MrpRun;
use Modules\Manufacturing\Models\MrpPlannedOrder;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MRPController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected MRPRunEngine $mrpEngine;
    protected PlannedOrderReleaseManager $releaseManager;

    public function __construct(
        TenantContextManager $tenantManager,
        MRPRunEngine $mrpEngine,
        PlannedOrderReleaseManager $releaseManager
    ) {
        $this->tenantManager = $tenantManager;
        $this->mrpEngine = $mrpEngine;
        $this->releaseManager = $releaseManager;
    }

    public function run(InitiateMrpRunRequest $request): JsonResponse
    {
        try {
            $run = $this->mrpEngine->executeJitRun();
            return response()->json([
                'success' => true,
                'data' => $run
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function orders(string $mrpRunId): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $run = MrpRun::where('tenant_id', $tenantId)->find($mrpRunId);
        if (!$run) {
            return response()->json(['success' => false, 'message' => 'MRP Run not found'], 404);
        }

        $orders = MrpPlannedOrder::where('tenant_id', $tenantId)
            ->where('mrp_run_id', $run->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function release(string $mrpRunId, Request $request): JsonResponse
    {
        try {
            $this->releaseManager->releasePlannedOrders($mrpRunId);
            return response()->json([
                'success' => true,
                'message' => 'Planned orders successfully released into active requisitions.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
