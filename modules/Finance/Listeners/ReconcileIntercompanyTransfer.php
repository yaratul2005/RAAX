<?php

namespace Modules\Finance\Listeners;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Services\PostingEngine;
use Modules\Inventory\Events\IntercompanyTransferCompleted;

class ReconcileIntercompanyTransfer implements ShouldQueue
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function handle(IntercompanyTransferCompleted $event): void
    {
        $transfer = $event->transfer;
        $amount = $transfer->total_cost_cents;

        $sourceTenant = $transfer->tenant_id;
        $destTenant = $transfer->destination_tenant_id;
        $ref = 'ICT-' . $transfer->transfer_number;

        // Note: For MVP we hardcode simple generic account codes.
        // Intercompany Receivable (Branch B) from Source view: e.g. 1005
        // Inventory Asset: 1003
        // Intercompany Payable (Branch A) from Dest view: e.g. 2005

        // Source Branch (Seller) Journal
        $this->tenantManager->setTenantId($sourceTenant);
        $this->postingEngine->postJournal([
            'date' => now()->toDateString(),
            'reference' => $ref,
            'description' => "Intercompany transfer outbound to {$destTenant}",
            'lines' => [
                ['account_code' => '1005', 'debit_cents' => $amount, 'credit_cents' => 0],   // Debit IC Receivable
                ['account_code' => '1003', 'debit_cents' => 0, 'credit_cents' => $amount],   // Credit Inventory
            ]
        ]);
        $this->tenantManager->clearTenantId();

        // Destination Branch (Buyer) Journal
        $this->tenantManager->setTenantId($destTenant);
        $this->postingEngine->postJournal([
            'date' => now()->toDateString(),
            'reference' => $ref,
            'description' => "Intercompany transfer inbound from {$sourceTenant}",
            'lines' => [
                ['account_code' => '1003', 'debit_cents' => $amount, 'credit_cents' => 0],   // Debit Inventory
                ['account_code' => '2005', 'debit_cents' => 0, 'credit_cents' => $amount],   // Credit IC Payable
            ]
        ]);
        $this->tenantManager->clearTenantId();
    }
}
