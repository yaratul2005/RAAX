<?php

namespace Modules\Inventory\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Inventory\Models\GoodsReceivedNote;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\WarehouseBin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

// Note: Strict modular decoupling rules from Prompt 11 state: "Do not directly import models or execute cross-queries into Finance or Procurement. Use event-driven hooks or registered service interfaces."
// We will create a quick interface to represent the fetcher.
use Modules\Inventory\Contracts\PurchaseOrderFetcherInterface;

class GoodsReceivedNoteManager
{
    protected TenantContextManager $tenantManager;
    protected PurchaseOrderFetcherInterface $poFetcher;

    public function __construct(TenantContextManager $tenantManager, PurchaseOrderFetcherInterface $poFetcher)
    {
        $this->tenantManager = $tenantManager;
        $this->poFetcher = $poFetcher;
    }

    public function receiveGoods(array $payload): GoodsReceivedNote
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required");
        }

        // Three-Way Match Verification using the decoupled interface
        $poData = $this->poFetcher->fetchPurchaseOrder($payload['purchase_order_id'], $tenantId);

        if (!$poData || !in_array($poData['status'], ['sent_to_vendor', 'partially_received'])) {
            throw new InvalidArgumentException("Invalid or unapproved purchase order.");
        }

        // Calculate total received previously + currently receiving vs ordered
        return DB::transaction(function () use ($tenantId, $payload, $poData) {
            $grn = GoodsReceivedNote::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'purchase_order_id' => $payload['purchase_order_id'],
                'received_by' => $payload['received_by'], // User ID from auth usually
                'grn_number' => $payload['grn_number'],
                'status' => 'verified',
            ]);

            foreach ($payload['lines'] as $line) {
                $sku = $line['item_sku'];
                $receivingQty = (int) $line['qty'];

                // Find matching PO line
                $poLine = collect($poData['lines'])->firstWhere('item_sku', $sku);
                if (!$poLine) {
                    throw new InvalidArgumentException("Item SKU {$sku} not found in PO.");
                }

                // Check tolerance (10%)
                // We'd need to know how many were received before. Let's assume the interface gives us 'received_qty' so far, or we calculate it.
                // For simplicity in MVP, let's just use ordered_qty + 10% as max per receipt, or better:
                $orderedQty = $poLine['qty'];
                $maxAllowedQty = $orderedQty * 1.10;

                // Let's query previous receipts from our own DB just to be safe:
                $previouslyReceived = InventoryBatch::where('tenant_id', $tenantId)
                    ->where('purchase_order_id', $payload['purchase_order_id'])
                    ->where('item_sku', $sku)
                    ->sum('original_qty');

                if (($previouslyReceived + $receivingQty) > $maxAllowedQty) {
                    throw new InvalidArgumentException("Received quantity for {$sku} exceeds 10% PO tolerance threshold.");
                }

                // Verify Bin
                $bin = WarehouseBin::where('tenant_id', $tenantId)->find($line['warehouse_bin_id']);
                if (!$bin) {
                    throw new InvalidArgumentException("Warehouse bin not found.");
                }

                $unitCostCents = $poLine['unit_price_cents'];

                // Generate InventoryBatch (FIFO Log)
                $batch = InventoryBatch::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'warehouse_bin_id' => $bin->id,
                    'item_sku' => $sku,
                    'purchase_order_id' => $payload['purchase_order_id'],
                    'original_qty' => $receivingQty,
                    'remaining_qty' => $receivingQty,
                    'unit_cost_cents' => $unitCostCents,
                    'currency_code' => $poData['currency_code'] ?? 'BDT',
                ]);

                // Generate Stock Movement
                StockMovement::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'inventory_batch_id' => $batch->id,
                    'type' => 'in',
                    'qty' => $receivingQty,
                    'reason' => "GRN Receipt {$grn->grn_number}",
                ]);
            }

            return $grn;
        });
    }
}
