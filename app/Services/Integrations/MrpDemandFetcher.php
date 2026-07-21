<?php

namespace App\Services\Integrations;

use Modules\Manufacturing\Contracts\MrpDemandFetcherInterface;
use Modules\Sales\Models\SalesOrderLine;

class MrpDemandFetcher implements MrpDemandFetcherInterface
{
    public function getGrossDemand(string $tenantId): array
    {
        // Fetch from SalesOrders that are confirmed but not shipped
        $lines = SalesOrderLine::where('tenant_id', $tenantId)
            ->whereHas('order', function ($q) {
                $q->where('status', 'confirmed');
            })->get();

        $demand = [];
        foreach ($lines as $line) {
            $demand[] = [
                'item_sku' => $line->item_sku,
                'qty' => $line->qty,
                // If SalesOrder had due date, we'd use it. For now, need ASAP (e.g. today)
                'due_date' => now()->toDateString()
            ];
        }

        return $demand;
    }
}
