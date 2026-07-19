<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Finance\Models\CreditNote;
use Modules\Finance\Models\DebitNote;
use Modules\Finance\Services\PostingEngine;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Sales\Models\SalesOrder;

class AdjustmentManager
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function applyCreditNote(string $salesOrderId, int $returnedAmountCents, string $originalInvoiceNumber): CreditNote
    {
        $tenantId = $this->tenantManager->getTenantId();

        return DB::transaction(function () use ($tenantId, $salesOrderId, $returnedAmountCents, $originalInvoiceNumber) {
            $order = SalesOrder::where('tenant_id', $tenantId)->lockForUpdate()->find($salesOrderId);
            if (!$order) {
                throw new InvalidArgumentException("Sales Order not found.");
            }

            // VAT is 15% standard
            $adjustedVatCents = (int) round($returnedAmountCents * 0.15);
            $totalReturnCents = $returnedAmountCents + $adjustedVatCents;

            if ($totalReturnCents > $order->grand_total_cents) {
                throw new InvalidArgumentException("Returned amount exceeds order grand total.");
            }

            $noteNumber = 'CN-' . Str::upper(Str::random(6));

            $creditNote = CreditNote::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'sales_order_id' => $order->id,
                'note_number' => $noteNumber,
                'original_tax_invoice_number' => $originalInvoiceNumber,
                'returned_amount_cents' => $returnedAmountCents,
                'adjusted_vat_cents' => $adjustedVatCents,
                'status' => 'applied',
            ]);

            // Adjust Customer Balance
            $customer = $order->customer;
            $customer->update([
                'outstanding_balance_cents' => max(0, $customer->outstanding_balance_cents - $totalReturnCents)
            ]);

            // Post Ledger
            // Debit: Output VAT (2002) - reducing liability
            // Debit: Sales Returns (4002)
            // Credit: Accounts Receivable (1002)
            $this->postingEngine->postJournal([
                'date' => now()->toDateString(),
                'reference' => $noteNumber,
                'description' => "Credit Note applied to {$originalInvoiceNumber}",
                'lines' => [
                    ['account_code' => '2002', 'debit_cents' => $adjustedVatCents, 'credit_cents' => 0],
                    ['account_code' => '4002', 'debit_cents' => $returnedAmountCents, 'credit_cents' => 0],
                    ['account_code' => '1002', 'debit_cents' => 0, 'credit_cents' => $totalReturnCents],
                ]
            ]);

            return $creditNote;
        });
    }

    public function applyDebitNote(string $purchaseOrderId, int $returnedAmountCents, string $originalInvoiceNumber): DebitNote
    {
        $tenantId = $this->tenantManager->getTenantId();

        return DB::transaction(function () use ($tenantId, $purchaseOrderId, $returnedAmountCents, $originalInvoiceNumber) {
            $order = PurchaseOrder::where('tenant_id', $tenantId)->lockForUpdate()->find($purchaseOrderId);
            if (!$order) {
                throw new InvalidArgumentException("Purchase Order not found.");
            }

            // VAT is 15% standard
            $adjustedVatCents = (int) round($returnedAmountCents * 0.15);
            $totalReturnCents = $returnedAmountCents + $adjustedVatCents;

            if ($totalReturnCents > $order->total_amount_cents) {
                // In our model total_amount_cents is the PO value.
                // We'll trust the prompt limits.
                throw new InvalidArgumentException("Returned amount exceeds order total.");
            }

            $noteNumber = 'DN-' . Str::upper(Str::random(6));

            $debitNote = DebitNote::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'purchase_order_id' => $order->id,
                'note_number' => $noteNumber,
                'original_purchase_invoice_number' => $originalInvoiceNumber,
                'returned_amount_cents' => $returnedAmountCents,
                'adjusted_vat_cents' => $adjustedVatCents,
                'status' => 'applied',
            ]);

            // Post Ledger
            // Debit: Accounts Payable (2001) - reducing liability
            // Credit: Input VAT (1004) - reducing rebate claim
            // Credit: Purchase Returns (5002)
            $this->postingEngine->postJournal([
                'date' => now()->toDateString(),
                'reference' => $noteNumber,
                'description' => "Debit Note applied to {$originalInvoiceNumber}",
                'lines' => [
                    ['account_code' => '2001', 'debit_cents' => $totalReturnCents, 'credit_cents' => 0],
                    ['account_code' => '1004', 'debit_cents' => 0, 'credit_cents' => $adjustedVatCents],
                    ['account_code' => '5002', 'debit_cents' => 0, 'credit_cents' => $returnedAmountCents],
                ]
            ]);

            return $debitNote;
        });
    }
}
