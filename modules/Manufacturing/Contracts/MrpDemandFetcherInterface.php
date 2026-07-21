<?php

namespace Modules\Manufacturing\Contracts;

interface MrpDemandFetcherInterface
{
    /**
     * @return array<int, array{item_sku: string, qty: int, due_date: string}>
     */
    public function getGrossDemand(string $tenantId): array;
}
