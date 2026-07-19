<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Finance\Models\Mushak91Return;
use Modules\Finance\Models\TreasuryDeposit;
use Modules\Finance\Services\PostingEngine;

class VATReconciliationManager
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function submitReturnAndClearLiability(string $returnId, string $depositId): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $returnId, $depositId) {
            $return = Mushak91Return::where('tenant_id', $tenantId)->lockForUpdate()->find($returnId);
            if (!$return) {
                throw new InvalidArgumentException("VAT Return not found.");
            }

            if ($return->status === 'submitted') {
                throw new InvalidArgumentException("This return has already been submitted.");
            }

            $deposit = TreasuryDeposit::where('tenant_id', $tenantId)->lockForUpdate()->find($depositId);
            if (!$deposit) {
                throw new InvalidArgumentException("Treasury Deposit not found.");
            }

            if ($deposit->status !== 'cleared') {
                throw new InvalidArgumentException("Treasury deposit must be cleared before submission.");
            }

            // Verify the treasury deposit covers the net VAT payable (if positive)
            if ($return->net_tax_payable_cents > 0) {
                if ($deposit->amount_cents < $return->net_tax_payable_cents) {
                    throw new InvalidArgumentException("Treasury deposit amount does not cover the net VAT payable.");
                }
            }

            // Link them
            $return->update([
                'treasury_deposit_id' => $deposit->id,
                'status' => 'submitted'
            ]);

            // Trigger PostingEngine to clear VAT liability
            // For MVP, we'll use predefined codes:
            // 2002: Output VAT (Liability - Credit Normal)
            // 1004: Input VAT (Asset - Debit Normal)
            // 1001: Cash/Bank

            $outputTax = $return->total_output_tax_cents;
            $inputTax = $return->total_input_tax_cents;
            $depositAmount = $deposit->amount_cents;

            $lines = [];

            // Debit Output VAT to clear
            if ($outputTax > 0) {
                $lines[] = ['account_code' => '2002', 'debit_cents' => $outputTax, 'credit_cents' => 0];
            }

            // Credit Input VAT to clear
            if ($inputTax > 0) {
                $lines[] = ['account_code' => '1004', 'debit_cents' => 0, 'credit_cents' => $inputTax];
            }

            // Credit Cash for the deposit amount paid to government
            if ($depositAmount > 0) {
                $lines[] = ['account_code' => '1001', 'debit_cents' => 0, 'credit_cents' => $depositAmount];
            }

            // If the deposit > net payable, it acts as an advance (e.g. overpaid).
            // For simplicity, we assume exact match or the remainder is carried over.
            // Let's ensure the journal is balanced:
            // Debit = OutputTax
            // Credit = InputTax + Deposit
            // If they don't match, we need a discrepancy account (e.g. VAT Receivable/Payable Rollover).
            // Let's create an exact balance logic:
            $totalDebit = collect($lines)->sum('debit_cents');
            $totalCredit = collect($lines)->sum('credit_cents');
            $diff = $totalDebit - $totalCredit;

            if ($diff > 0) {
                // We owe more credit (e.g. we had a rebate carry forward)
                // Credit VAT Receivable/Carry-forward: 1006
                $lines[] = ['account_code' => '1006', 'debit_cents' => 0, 'credit_cents' => $diff];
            } elseif ($diff < 0) {
                // We owe more debit (e.g. deposit was less than payable due to past carry-forward)
                // Debit VAT Payable/Carry-forward: 2006
                $lines[] = ['account_code' => '2006', 'debit_cents' => abs($diff), 'credit_cents' => 0];
            }

            $this->postingEngine->postJournal([
                'date' => now()->toDateString(),
                'reference' => 'TR6-' . $deposit->challan_number,
                'description' => "VAT Clearing for " . $return->tax_period,
                'lines' => $lines
            ]);
        });
    }
}
