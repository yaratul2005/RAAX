<?php

namespace Modules\Sales\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Inventory\Contracts\FIFOValuationEngineInterface;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SalesOrderManager
{
    protected TenantContextManager $tenantManager;
    protected FIFOValuationEngineInterface $fifoEngine;
    protected MushakGenerator $mushakGenerator;

    public function __construct(TenantContextManager $tenantManager, FIFOValuationEngineInterface $fifoEngine, MushakGenerator $mushakGenerator)
    {
        $this->tenantManager = $tenantManager;
        $this->fifoEngine = $fifoEngine;
        $this->mushakGenerator = $mushakGenerator;
    }

    public function confirmOrder(string $orderId): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $orderId) {
            $order = SalesOrder::where('tenant_id', $tenantId)
                ->with(['customer', 'lines'])
                ->lockForUpdate()
                ->find($orderId);

            if (!$order) {
                throw new InvalidArgumentException("Sales Order not found.");
            }

            if ($order->status !== 'draft') {
                throw new InvalidArgumentException("Only draft orders can be confirmed.");
            }

            $customer = $order->customer;

            // Credit Limit Verification
            // Outstanding Balance + Grand Total > Credit Limit
            $exposureCents = $customer->outstanding_balance_cents + $order->grand_total_cents;

            if ($exposureCents > $customer->credit_limit_cents) {
                throw new InvalidArgumentException("Order confirmation rejected. Credit exposure exceeds the customer's approved credit limit.");
            }

            // Update customer outstanding balance
            $customer->outstanding_balance_cents += $order->grand_total_cents;
            $customer->save();

            // FIFO Stock Depletion
            $totalCogsCents = 0;
            foreach ($order->lines as $line) {
                $cogs = $this->fifoEngine->calculateStockOutCost(
                    $line->item_sku,
                    $line->qty,
                    "Sales Order {$order->order_number}"
                );
                $totalCogsCents += $cogs;
            }

            $order->update(['status' => 'confirmed']);

            // Auto-generate Mushak 6.3
            $this->mushakGenerator->generateMushak($order);
        });
    }
}
