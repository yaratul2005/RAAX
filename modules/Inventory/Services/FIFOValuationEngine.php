<?php

namespace Modules\Inventory\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Inventory\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FIFOValuationEngine
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Consumes stock chronologically (FIFO) and returns total COGS in cents.
     */
    public function calculateStockOutCost(string $sku, int $qtyToReduce, string $reason = 'Stock Out'): int
    {
        if ($qtyToReduce <= 0) {
            throw new InvalidArgumentException("Quantity to reduce must be greater than 0.");
        }

        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        return DB::transaction(function () use ($tenantId, $sku, $qtyToReduce, $reason) {
            // Lock rows for update to prevent concurrent race conditions
            $batches = InventoryBatch::where('tenant_id', $tenantId)
                ->where('item_sku', $sku)
                ->where('remaining_qty', '>', 0)
                ->orderBy('created_at', 'asc') // Oldest first (FIFO)
                ->lockForUpdate()
                ->get();

            $totalAvailable = $batches->sum('remaining_qty');

            if ($totalAvailable < $qtyToReduce) {
                throw new InvalidArgumentException("Insufficient stock available for SKU {$sku}. Required: {$qtyToReduce}, Available: {$totalAvailable}.");
            }

            $unfulfilledQty = $qtyToReduce;
            $totalCogsCents = 0;

            foreach ($batches as $batch) {
                if ($unfulfilledQty <= 0) {
                    break; // Order fulfilled
                }

                $qtyFromThisBatch = min($batch->remaining_qty, $unfulfilledQty);
                $batchCogs = $qtyFromThisBatch * $batch->unit_cost_cents;

                // Update Batch
                $batch->remaining_qty -= $qtyFromThisBatch;
                $batch->save();

                // Log Movement
                StockMovement::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'inventory_batch_id' => $batch->id,
                    'type' => 'out',
                    'qty' => $qtyFromThisBatch,
                    'reason' => $reason,
                ]);

                $totalCogsCents += $batchCogs;
                $unfulfilledQty -= $qtyFromThisBatch;
            }

            return $totalCogsCents;
        });
    }
}
