<?php

namespace App\Services\Integrations;

use Modules\Inventory\Contracts\PurchaseOrderFetcherInterface;
use Modules\Procurement\Models\PurchaseOrder;

class ProcurementToInventoryFetcher implements PurchaseOrderFetcherInterface
{
    public function fetchPurchaseOrder(string $poId, string $tenantId): ?array
    {
        $po = PurchaseOrder::where('tenant_id', $tenantId)->with('lines')->find($poId);

        if (!$po) {
            return null;
        }

        return [
            'id' => $po->id,
            'status' => $po->status,
            'currency_code' => $po->currency_code,
            'lines' => $po->lines->map(function ($line) {
                return [
                    'item_sku' => $line->item_sku,
                    'qty' => $line->qty,
                    'unit_price_cents' => $line->unit_price_cents,
                ];
            })->toArray()
        ];
    }
}
