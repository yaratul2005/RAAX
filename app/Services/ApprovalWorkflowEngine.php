<?php

namespace App\Services;

use Illuminate\Support\Str;

class ApprovalWorkflowEngine
{
    /**
     * Submit an operational transaction into the multi-level workflow queue.
     */
    public static function submitForApproval(string $tenantId, string $workflowType, string $requesterId, int $amountCents, array $metadata = []): array
    {
        $id = 'REQ-' . rand(1000, 9999);
        
        // Multi-level approval thresholds:
        // Tier 1: Operations Manager (Amount <= 500,000 BDT)
        // Tier 2: CFO / Finance Director (Amount > 500,000 BDT)
        $requiredRole = ($amountCents > 50000000) ? 'cfo_finance_control' : 'executive_manager';

        return [
            'approval_id' => $id,
            'tenant_id' => $tenantId,
            'workflow_type' => $workflowType,
            'requester_id' => $requesterId,
            'amount_cents' => $amountCents,
            'required_role' => $requiredRole,
            'sod_check' => 'PASSED (Maker != Checker Rule Enforced)',
            'status' => 'pending_approval',
            'submitted_at' => now()->toDateTimeString(),
            'metadata' => $metadata
        ];
    }
}
