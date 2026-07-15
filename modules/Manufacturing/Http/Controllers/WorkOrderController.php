<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Manufacturing\Http\Requests\CreateWorkOrderRequest;
use Modules\Manufacturing\Models\ProductionWorkOrder;
use Modules\Manufacturing\Services\ProductionManager;
use Modules\Manufacturing\Services\Mushak43Generator;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkOrderController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected ProductionManager $productionManager;
    protected Mushak43Generator $mushakGenerator;

    public function __construct(TenantContextManager $tenantManager, ProductionManager $productionManager, Mushak43Generator $mushakGenerator)
    {
        $this->tenantManager = $tenantManager;
        $this->productionManager = $productionManager;
        $this->mushakGenerator = $mushakGenerator;
    }

    public function store(CreateWorkOrderRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $wo = ProductionWorkOrder::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'draft',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $wo
        ], 201);
    }

    public function complete(string $workOrderId, Request $request): JsonResponse
    {
        $request->validate(['warehouse_bin_id' => 'required|uuid']);

        try {
            $this->productionManager->completeWorkOrder($workOrderId, $request->input('warehouse_bin_id'));

            return response()->json([
                'success' => true,
                'message' => 'Work order completed, raw materials depleted, and finished goods stocked.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function generateMushak(Request $request): JsonResponse
    {
        $request->validate([
            'bill_of_materials_id' => 'required|uuid',
            'overhead_cents' => 'required|integer|min:0',
            'profit_cents' => 'required|integer|min:0',
        ]);

        try {
            $declaration = $this->mushakGenerator->generateDeclaration(
                $request->input('bill_of_materials_id'),
                $request->input('overhead_cents'),
                $request->input('profit_cents')
            );

            return response()->json([
                'success' => true,
                'data' => $declaration
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
