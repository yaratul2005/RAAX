<?php

namespace App\Services\Integrations;

use Modules\Manufacturing\Contracts\MrpInventoryFetcherInterface;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Procurement\Models\PurchaseOrderLine;

class MrpInventoryFetcher implements MrpInventoryFetcherInterface
{
    public function getOnHandStock(string $sku, string $tenantId): int
    {
        return (int) InventoryBatch::where('tenant_id', $tenantId)
            ->where('item_sku', $sku)
            ->sum('remaining_qty');
    }

    public function getSafetyStockThreshold(string $sku, string $tenantId): int
    {
        // For MVP, hardcode or assume a base rule, e.g. 100 units.
        return 100;
    }

    public function getInTransitStock(string $sku, string $tenantId): int
    {
        // Items in approved POs but not yet fully received
        return (int) PurchaseOrderLine::where('tenant_id', $tenantId)
            ->where('item_sku', $sku)
            ->whereHas('purchaseOrder', function ($q) {
                $q->whereIn('status', ['sent_to_vendor', 'partially_received']);
            })
            ->sum('qty'); // Simplified, in reality would deduct received GRN qty
    }

    public function getLeadTimeDays(string $sku, string $tenantId): int
    {
        return 14; // Default 14 days lead time for MVP
    }
}
