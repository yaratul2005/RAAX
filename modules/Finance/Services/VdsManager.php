<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Models\VdsCertificate;
use Modules\Finance\Services\PostingEngine;
use Illuminate\Support\Str;

class VdsManager
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function issueVdsCertificate(string $invoiceId, int $withheldAmountCents, string $depositDate): VdsCertificate
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        return DB::transaction(function () use ($tenantId, $invoiceId, $withheldAmountCents, $depositDate) {
            $invoice = FinanceInvoice::where('tenant_id', $tenantId)->lockForUpdate()->find($invoiceId);

            if (!$invoice) {
                throw new InvalidArgumentException("Invoice not found.");
            }

            // In our system, the amount_cents is total, but we didn't explicitly store VAT in the FinanceInvoice table.
            // The prompt says: "Block certificate generation if the withheld amount exceeds the total invoice VAT amount."
            // Assuming for MVP VAT is exactly 15% of the total, or it exceeds outstanding.
            $assumedVatCents = (int) round($invoice->amount_cents * (15 / 115)); // Assuming amount_cents includes 15% VAT

            if ($withheldAmountCents > $assumedVatCents) {
                throw new InvalidArgumentException("Withheld amount exceeds total invoice VAT amount.");
            }

            if ($withheldAmountCents > $invoice->outstanding_balance) {
                throw new InvalidArgumentException("Withheld amount exceeds invoice outstanding balance.");
            }

            // Create certificate
            $certificateNumber = 'VDS-' . Str::upper(Str::random(6));

            $certificate = VdsCertificate::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'finance_invoice_id' => $invoice->id,
                'certificate_number' => $certificateNumber,
                'withheld_amount_cents' => $withheldAmountCents,
                'deposit_date' => $depositDate,
                'status' => 'issued',
            ]);

            // We treat the VDS as a partial payment reducing the AP liability to the supplier
            $invoice->update([
                'paid_cents' => $invoice->paid_cents + $withheldAmountCents,
                'status' => ($invoice->paid_cents + $withheldAmountCents) >= $invoice->amount_cents ? 'paid' : 'partially_paid',
            ]);

            // Create ledger entry
            // Debit: Supplier Accounts Payable (2001)
            // Credit: VDS Payable (2005)
            $this->postingEngine->postJournal([
                'date' => now()->toDateString(),
                'reference' => 'VDS-' . $certificateNumber,
                'description' => "VDS Certificate issued for Invoice {$invoice->invoice_number}",
                'lines' => [
                    ['account_code' => '2001', 'debit_cents' => $withheldAmountCents, 'credit_cents' => 0],
                    ['account_code' => '2005', 'debit_cents' => 0, 'credit_cents' => $withheldAmountCents],
                ]
            ]);

            return $certificate;
        });
    }
}
