<?php

namespace Modules\Manufacturing\Contracts;

use Modules\Manufacturing\Models\MrpPlannedOrder;

interface MrpProcurementReleaserInterface
{
    public function createPurchaseRequest(string $itemSku, int $qty, string $tenantId): void;
}
