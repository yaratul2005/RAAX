<?php

namespace Modules\Manufacturing\Services\MRP;

use App\Services\Tenant\TenantContextManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Manufacturing\Contracts\MrpDemandFetcherInterface;
use Modules\Manufacturing\Contracts\MrpInventoryFetcherInterface;
use Modules\Manufacturing\Models\BillOfMaterials;
use Modules\Manufacturing\Models\MrpPlannedOrder;
use Modules\Manufacturing\Models\MrpRun;

class MRPRunEngine
{
    protected TenantContextManager $tenantManager;
    protected MrpDemandFetcherInterface $demandFetcher;
    protected MrpInventoryFetcherInterface $inventoryFetcher;

    public function __construct(
        TenantContextManager $tenantManager,
        MrpDemandFetcherInterface $demandFetcher,
        MrpInventoryFetcherInterface $inventoryFetcher
    ) {
        $this->tenantManager = $tenantManager;
        $this->demandFetcher = $demandFetcher;
        $this->inventoryFetcher = $inventoryFetcher;
    }

    public function executeJitRun(): MrpRun
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        return DB::transaction(function () use ($tenantId) {
            $run = MrpRun::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'run_number' => 'MRP-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
                'status' => 'processing',
            ]);

            // 1. Get Gross Demand (Finished Goods)
            $demands = $this->demandFetcher->getGrossDemand($tenantId);

            // 2. Expand BOMs
            $rawMaterialRequirements = []; // SKU => ['qty' => X, 'due_date' => Y]

            foreach ($demands as $demand) {
                // Find BOM for the finished item
                $bom = BillOfMaterials::where('tenant_id', $tenantId)
                    ->where('finished_item_sku', $demand['item_sku'])
                    ->where('is_active', true)
                    ->with('items')
                    ->first();

                if ($bom) {
                    // Explode BOM
                    foreach ($bom->items as $item) {
                        $sku = $item->raw_item_sku;
                        // Qty Required * Gross Demand * (1 + Wastage / 10000)
                        $wastageMultiplier = 1 + ($item->wastage_allowance_percentage_cents / 10000);
                        $qtyNeeded = (int) ceil($item->qty_required * $demand['qty'] * $wastageMultiplier);

                        if (!isset($rawMaterialRequirements[$sku])) {
                            $rawMaterialRequirements[$sku] = [
                                'qty' => 0,
                                'due_date' => clone Carbon::parse($demand['due_date'])
                            ];
                        }

                        $rawMaterialRequirements[$sku]['qty'] += $qtyNeeded;
                        // For simplicity, take the earliest due date if multiple demands hit the same raw material
                        $currentDue = $rawMaterialRequirements[$sku]['due_date'];
                        $newDue = Carbon::parse($demand['due_date']);
                        if ($newDue->lt($currentDue)) {
                            $rawMaterialRequirements[$sku]['due_date'] = $newDue;
                        }
                    }
                }
            }

            // 3. Calculate Net Requirements & Lead-Time Offsetting
            foreach ($rawMaterialRequirements as $sku => $req) {
                $grossDemand = $req['qty'];
                $safetyStock = $this->inventoryFetcher->getSafetyStockThreshold($sku, $tenantId);
                $onHand = $this->inventoryFetcher->getOnHandStock($sku, $tenantId);
                $inTransit = $this->inventoryFetcher->getInTransitStock($sku, $tenantId);

                // Formula: Net = max(0, Gross + Safety - OnHand - InTransit)
                $netRequirement = max(0, $grossDemand + $safetyStock - $onHand - $inTransit);

                if ($netRequirement > 0) {
                    $leadTimeDays = $this->inventoryFetcher->getLeadTimeDays($sku, $tenantId);
                    $deliveryDate = $req['due_date'];
                    // Planned Order Date = Planned Delivery Date - Lead Time Days
                    $orderDate = $deliveryDate->copy()->subDays($leadTimeDays);

                    MrpPlannedOrder::create([
                        'id' => Str::uuid()->toString(),
                        'tenant_id' => $tenantId,
                        'mrp_run_id' => $run->id,
                        'item_sku' => $sku,
                        'gross_requirement_qty' => $grossDemand,
                        'net_requirement_qty' => $netRequirement,
                        'safety_stock_threshold' => $safetyStock,
                        'lead_time_days' => $leadTimeDays,
                        'order_recommendation_type' => 'purchase_requisition', // Assume raw materials are purchased
                        'planned_order_date' => $orderDate->toDateString(),
                        'planned_delivery_date' => $deliveryDate->toDateString(),
                        'status' => 'pending',
                    ]);
                }
            }

            $run->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $run;
        });
    }
}
