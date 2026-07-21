<?php

namespace Modules\Manufacturing\Services\MRP;

use Modules\Manufacturing\Contracts\MrpInventoryFetcherInterface;

class DefaultMrpInventoryFetcher implements MrpInventoryFetcherInterface
{
    public function getSafetyStockThreshold(string $sku, string $tenantId): int
    {
        return 100;
    }

    public function getOnHandStock(string $sku, string $tenantId): int
    {
        return 0;
    }

    public function getInTransitStock(string $sku, string $tenantId): int
    {
        return 0;
    }

    public function getLeadTimeDays(string $sku, string $tenantId): int
    {
        return 7;
    }
}
