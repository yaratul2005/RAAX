<?php

namespace Modules\Manufacturing\Services\MRP;

use Illuminate\Support\Str;
use Modules\Manufacturing\Contracts\MrpProcurementReleaserInterface;
use Modules\Manufacturing\Models\MrpPlannedOrder;
use Modules\Procurement\Models\PurchaseRequest;

class DefaultMrpProcurementReleaser implements MrpProcurementReleaserInterface
{
    public function createPurchaseRequest(string $itemSku, int $qty, string $tenantId): void
    {
        PurchaseRequest::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'requester_id' => Str::uuid()->toString(),
            'requested_by' => Str::uuid()->toString(),
            'total_estimated_cost_cents' => 0,
            'status' => 'submitted',
            'notes' => "MRP Requisition for {$itemSku} (Qty: {$qty})",
        ]);
    }
}
