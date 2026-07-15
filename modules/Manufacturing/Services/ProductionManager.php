<?php

namespace Modules\Manufacturing\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Inventory\Contracts\FIFOValuationEngineInterface;
use Modules\Manufacturing\Models\ProductionWorkOrder;

class ProductionManager
{
    protected TenantContextManager $tenantManager;
    protected FIFOValuationEngineInterface $fifoEngine;

    public function __construct(TenantContextManager $tenantManager, FIFOValuationEngineInterface $fifoEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->fifoEngine = $fifoEngine;
    }

    public function completeWorkOrder(string $workOrderId, string $targetWarehouseBinId): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $workOrderId, $targetWarehouseBinId) {
            $workOrder = ProductionWorkOrder::where('tenant_id', $tenantId)
                ->with('billOfMaterials.items')
                ->lockForUpdate()
                ->find($workOrderId);

            if (!$workOrder) {
                throw new InvalidArgumentException("Work order not found.");
            }

            if ($workOrder->status === 'completed') {
                throw new InvalidArgumentException("Work order is already completed.");
            }

            $bom = $workOrder->billOfMaterials;
            $totalRawMaterialCOGS = 0;

            // 1. Raw Material Depletion
            foreach ($bom->items as $item) {
                // Formula: BOM Qty Required * Work Order Qty to Produce * (1 + Wastage / 10000)
                $qtyToConsumeBase = $item->qty_required * $workOrder->qty_to_produce;
                // Add wastage
                $wastageMultiplier = 1 + ($item->wastage_allowance_percentage_cents / 10000);
                $totalConsumptionQty = (int) ceil($qtyToConsumeBase * $wastageMultiplier);

                // Deplete FIFO and accumulate cost
                $cogs = $this->fifoEngine->calculateStockOutCost(
                    $item->raw_item_sku,
                    $totalConsumptionQty,
                    "Consumed for WO: {$workOrder->work_order_number}"
                );

                $totalRawMaterialCOGS += $cogs;
            }

            // 2. Finished Goods Valuation
            // Formula: (Total Raw Material COGS + Total Overhead Cost) / Work Order Qty to Produce
            $totalProductionCost = $totalRawMaterialCOGS + $workOrder->total_overhead_cost_cents;
            $finishedUnitCostCents = (int) round($totalProductionCost / $workOrder->qty_to_produce);

            // 3. Add to inventory
            $this->fifoEngine->addInboundStock(
                $bom->finished_item_sku,
                $workOrder->qty_to_produce,
                $finishedUnitCostCents,
                $targetWarehouseBinId,
                "Produced from WO: {$workOrder->work_order_number}"
            );

            // Update Work Order
            $workOrder->update(['status' => 'completed']);
        });
    }
}
