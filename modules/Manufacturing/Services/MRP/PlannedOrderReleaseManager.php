<?php

namespace Modules\Manufacturing\Services\MRP;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Manufacturing\Contracts\MrpProcurementReleaserInterface;
use Modules\Manufacturing\Models\MrpPlannedOrder;
use Modules\Manufacturing\Models\MrpRun;

class PlannedOrderReleaseManager
{
    protected TenantContextManager $tenantManager;
    protected MrpProcurementReleaserInterface $procurementReleaser;

    public function __construct(TenantContextManager $tenantManager, MrpProcurementReleaserInterface $procurementReleaser)
    {
        $this->tenantManager = $tenantManager;
        $this->procurementReleaser = $procurementReleaser;
    }

    public function releasePlannedOrders(string $mrpRunId): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $mrpRunId) {
            $run = MrpRun::where('tenant_id', $tenantId)->find($mrpRunId);

            if (!$run || $run->status !== 'completed') {
                throw new InvalidArgumentException("Valid completed MRP run not found.");
            }

            $orders = MrpPlannedOrder::where('tenant_id', $tenantId)
                ->where('mrp_run_id', $mrpRunId)
                ->where('status', 'pending')
                ->where('order_recommendation_type', 'purchase_requisition')
                ->lockForUpdate()
                ->get();

            foreach ($orders as $order) {
                // Call Procurement interface
                $this->procurementReleaser->createPurchaseRequest($order->item_sku, $order->net_requirement_qty, $tenantId);

                // Transition status
                $order->update(['status' => 'released']);
            }
        });
    }
}
