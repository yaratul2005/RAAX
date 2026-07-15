<?php

namespace Modules\Inventory\Contracts;

interface FIFOValuationEngineInterface
{
    /**
     * Consumes stock chronologically (FIFO) and returns total COGS in cents.
     */
    public function calculateStockOutCost(string $sku, int $qtyToReduce, string $reason = 'Stock Out'): int;

    /**
     * Add inbound stock batch for finished goods.
     */
    public function addInboundStock(string $sku, int $qty, int $unitCostCents, string $warehouseBinId, string $reason = 'Inbound Stock'): void;

    /**
     * Get the average unit cost of available inventory batches for a given SKU.
     */
    public function getAverageUnitCost(string $sku): int;
}
