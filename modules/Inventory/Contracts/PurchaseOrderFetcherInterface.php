<?php

namespace Modules\Inventory\Contracts;

interface PurchaseOrderFetcherInterface
{
    /**
     * Fetches PO data as a plain array to prevent model coupling.
     * Expected array structure:
     * [
     *    'id' => '...',
     *    'status' => 'sent_to_vendor',
     *    'currency_code' => 'BDT',
     *    'lines' => [
     *        ['item_sku' => '...', 'qty' => 10, 'unit_price_cents' => 500],
     *    ]
     * ]
     */
    public function fetchPurchaseOrder(string $poId, string $tenantId): ?array;
}
