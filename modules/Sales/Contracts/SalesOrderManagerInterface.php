<?php

namespace Modules\Sales\Contracts;

interface SalesOrderManagerInterface
{
    /**
     * Creates a draft Sales Order from EDI payload.
     */
    public function createDraftOrder(string $customerId, string $orderNumber, array $lines, string $tenantId): \Modules\Sales\Models\SalesOrder;
}
