<?php

namespace Modules\Sales\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Sales\Models\Mushak63Invoice;
use Modules\Sales\Models\SalesOrder;
use Illuminate\Support\Str;

class MushakGenerator
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function generateMushak(SalesOrder $order): Mushak63Invoice
    {
        $tenantId = $this->tenantManager->getTenantId();

        // Calculate standard VAT (15%)
        $subtotalCents = $order->subtotal_cents;
        $vatCents = (int) round($subtotalCents * 0.15);
        $totalPayableCents = $subtotalCents + $vatCents;

        // Flag for high value audit (exceeds BDT 200,000 = 20,000,000 cents)
        $isHighValueAudit = $totalPayableCents > 20000000;

        $customer = $order->customer;

        $mushak = Mushak63Invoice::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'sales_order_id' => $order->id,
            'challan_number' => 'CH-' . $order->order_number,
            'issue_date' => now()->toDateString(),
            'buyer_name' => $customer->name,
            'buyer_bin' => $customer->bin,
            'seller_name' => 'RAAX ERP (Tenant ' . substr($tenantId, 0, 8) . ')',
            'seller_bin' => '123456789', // Example seller BIN
            'subtotal_cents' => $subtotalCents,
            'vat_cents' => $vatCents,
            'total_payable_cents' => $totalPayableCents,
            'is_high_value_audit' => $isHighValueAudit,
        ]);

        return $mushak;
    }
}
