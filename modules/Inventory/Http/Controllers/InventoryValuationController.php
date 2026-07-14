<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Services\FIFOValuationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryValuationController extends Controller
{
    protected FIFOValuationEngine $fifoEngine;

    public function __construct(FIFOValuationEngine $fifoEngine)
    {
        $this->fifoEngine = $fifoEngine;
    }

    public function valuation(string $sku, Request $request): JsonResponse
    {
        // For standard valuation viewing, we sum the remaining qty * unit cost.
        // The FIFO Engine is for stock OUT calculation. Let's provide a generic valuation query.
        $tenantId = app(\App\Services\Tenant\TenantContextManager::class)->getTenantId();

        $batches = \Modules\Inventory\Models\InventoryBatch::where('tenant_id', $tenantId)
            ->where('item_sku', $sku)
            ->where('remaining_qty', '>', 0)
            ->get();

        $totalQty = $batches->sum('remaining_qty');
        $totalValueCents = $batches->reduce(function ($carry, $batch) {
            return $carry + ($batch->remaining_qty * $batch->unit_cost_cents);
        }, 0);

        return response()->json([
            'success' => true,
            'data' => [
                'sku' => $sku,
                'available_qty' => $totalQty,
                'total_valuation_cents' => $totalValueCents,
            ]
        ]);
    }
}
