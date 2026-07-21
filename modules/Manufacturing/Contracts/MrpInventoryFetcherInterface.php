<?php

namespace Modules\Manufacturing\Contracts;

interface MrpInventoryFetcherInterface
{
    public function getSafetyStockThreshold(string $sku, string $tenantId): int;
    public function getOnHandStock(string $sku, string $tenantId): int;
    public function getInTransitStock(string $sku, string $tenantId): int;
    public function getLeadTimeDays(string $sku, string $tenantId): int;
}
