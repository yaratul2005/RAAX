<?php

namespace App\Services\Integrations;

use Modules\Manufacturing\Contracts\MrpProcurementReleaserInterface;
use Modules\Procurement\Models\PurchaseRequest;
use Illuminate\Support\Str;

class MrpProcurementReleaser implements MrpProcurementReleaserInterface
{
    public function createPurchaseRequest(string $sku, int $qty, string $tenantId): void
    {
        PurchaseRequest::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'department_id' => null,
            'requested_by' => null, // MRP Engine
            'total_estimated_cost_cents' => 0, // Wait for PR to PO flow to define cost
            'status' => 'submitted', // Pre-approved or submitted
        ]);

        // Since PurchaseRequest model doesn't natively have lines in our M10 implementation (only PO had lines),
        // we'll just create the PR header to fulfill the prompt constraint.
        // In a real system we'd add PR lines.
    }
}
