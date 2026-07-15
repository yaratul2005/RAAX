<?php

namespace Modules\Manufacturing\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Inventory\Contracts\FIFOValuationEngineInterface;
use Modules\Manufacturing\Models\BillOfMaterials;
use Modules\Manufacturing\Models\Mushak43Declaration;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Mushak43Generator
{
    protected TenantContextManager $tenantManager;
    protected FIFOValuationEngineInterface $fifoEngine;

    public function __construct(TenantContextManager $tenantManager, FIFOValuationEngineInterface $fifoEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->fifoEngine = $fifoEngine;
    }

    public function generateDeclaration(string $bomId, int $overheadCents, int $profitCents): Mushak43Declaration
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        $bom = BillOfMaterials::where('tenant_id', $tenantId)->with('items')->find($bomId);
        if (!$bom) {
            throw new InvalidArgumentException("BOM not found.");
        }

        // Compute cost-basis for 1 unit of finished goods
        $declaredCostBaseCents = 0;

        foreach ($bom->items as $item) {
            // Get current average unit cost from inventory for this raw material
            $avgUnitCost = $this->fifoEngine->getAverageUnitCost($item->raw_item_sku);

            // Calculate consumption for 1 unit including wastage
            $wastageMultiplier = 1 + ($item->wastage_allowance_percentage_cents / 10000);
            $qtyNeeded = ceil($item->qty_required * $wastageMultiplier);

            $declaredCostBaseCents += (int) ($qtyNeeded * $avgUnitCost);
        }

        // Formula: Declared Sale Price = FIFO Cost Base + Overhead Cents + Profit Cents
        $declaredSalePriceCents = $declaredCostBaseCents + $overheadCents + $profitCents;

        $declaration = Mushak43Declaration::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'bill_of_materials_id' => $bom->id,
            'declaration_number' => 'M43-' . date('Ymd') . '-' . Str::random(4),
            'declared_cost_base_cents' => $declaredCostBaseCents,
            'declared_overhead_cents' => $overheadCents,
            'declared_profit_cents' => $profitCents,
            'declared_sale_price_cents' => $declaredSalePriceCents,
            'declared_at' => now()->toDateString(),
        ]);

        return $declaration;
    }
}
