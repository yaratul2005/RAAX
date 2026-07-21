<?php

namespace Modules\Manufacturing\Services\MRP;

use Modules\Manufacturing\Contracts\MrpDemandFetcherInterface;
use Modules\Sales\Models\SalesOrder;

class SalesOrderDemandFetcher implements MrpDemandFetcherInterface
{
    public function getGrossDemand(string $tenantId): array
    {
        $orders = SalesOrder::where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->with('lines')
            ->get();

        $demands = [];
        foreach ($orders as $order) {
            foreach ($order->lines as $line) {
                $demands[] = [
                    'item_sku' => $line->item_sku,
                    'qty' => $line->qty,
                    'due_date' => now()->addDays(7)->toDateString(),
                ];
            }
        }

        return $demands;
    }
}
