<?php

namespace Modules\EDI\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\EDI\Models\EdiLog;
use Modules\EDI\Models\EdiPartner;
use Modules\Sales\Contracts\SalesOrderManagerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\Customer;

class EdiProcessor
{
    protected TenantContextManager $tenantManager;
    protected SalesOrderManagerInterface $salesOrderManager;

    public function __construct(TenantContextManager $tenantManager, SalesOrderManagerInterface $salesOrderManager)
    {
        $this->tenantManager = $tenantManager;
        $this->salesOrderManager = $salesOrderManager;
    }

    public function processInbound850(EdiPartner $partner, array $payload): SalesOrder
    {
        $tenantId = $this->tenantManager->getTenantId();

        try {
            // Simplified 850 extraction
            // e.g. payload contains 'customer_bin', 'po_number', 'items' => [['sku', 'qty', 'price']]
            if (!isset($payload['customer_bin']) || !isset($payload['po_number']) || !isset($payload['items'])) {
                throw new InvalidArgumentException("Invalid ANSI X12 850 JSON equivalent payload.");
            }

            // Resolve Customer
            $customer = Customer::where('tenant_id', $tenantId)->where('bin', $payload['customer_bin'])->first();
            if (!$customer) {
                // We'll create a dummy one for testing if not found, or just throw error.
                // Throw error is safer. But wait, what if they don't have BIN? We'll search by name or BIN.
                $customer = Customer::where('tenant_id', $tenantId)->where('name', $payload['customer_name'] ?? 'Unknown EDI Customer')->first();
                if (!$customer) {
                    throw new InvalidArgumentException("Customer not registered in Sales module.");
                }
            }

            // Map Lines
            $lines = [];
            foreach ($payload['items'] as $item) {
                $lines[] = [
                    'item_sku' => $item['sku'],
                    'qty' => (int) $item['qty'],
                    'unit_price_cents' => (int) $item['price_cents'],
                ];
            }

            $order = $this->salesOrderManager->createDraftOrder(
                $customer->id,
                $payload['po_number'], // use their PO as our Order Number
                $lines,
                $tenantId
            );

            // Log
            EdiLog::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'edi_partner_id' => $partner->id,
                'direction' => 'inbound',
                'document_type' => '850_purchase_order',
                'payload' => json_encode($payload),
                'status' => 'success',
                'processed_at' => now(),
            ]);

            return $order;

        } catch (\Exception $e) {
            EdiLog::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'edi_partner_id' => $partner->id,
                'direction' => 'inbound',
                'document_type' => '850_purchase_order',
                'payload' => json_encode($payload),
                'status' => 'failed',
            ]);
            throw $e;
        }
    }

    public function generateOutbound810(EdiPartner $partner, string $orderId): array
    {
        $tenantId = $this->tenantManager->getTenantId();
        $order = SalesOrder::where('tenant_id', $tenantId)->with(['lines', 'customer'])->find($orderId);

        if (!$order) {
            throw new InvalidArgumentException("Sales Order not found.");
        }

        // Generate 810 payload
        $payload = [
            'document_type' => '810',
            'invoice_number' => 'INV-' . $order->order_number,
            'customer_bin' => $order->customer->bin,
            'issue_date' => now()->toDateString(),
            'subtotal_cents' => $order->subtotal_cents,
            'tax_cents' => $order->tax_cents,
            'grand_total_cents' => $order->grand_total_cents,
            'currency_code' => 'BDT', // Fixed per constraints
            'lines' => $order->lines->map(function ($line) {
                return [
                    'sku' => $line->item_sku,
                    'qty' => $line->qty,
                    'unit_price_cents' => $line->unit_price_cents,
                    'total_cents' => $line->total_cents,
                ];
            })->toArray()
        ];

        EdiLog::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'edi_partner_id' => $partner->id,
            'direction' => 'outbound',
            'document_type' => '810_invoice',
            'payload' => json_encode($payload),
            'status' => 'success',
            'processed_at' => now(),
        ]);

        return $payload;
    }
}
