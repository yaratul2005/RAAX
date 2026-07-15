<?php

namespace Modules\Inventory\Contracts;

interface FIFOValuationEngineInterface
{
    /**
     * Consumes stock chronologically (FIFO) and returns total COGS in cents.
     */
    public function calculateStockOutCost(string $sku, int $qtyToReduce, string $reason = 'Stock Out'): int;
}
