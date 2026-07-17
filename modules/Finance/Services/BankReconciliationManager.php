<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Finance\Models\BankStatement;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankReconciliationManager
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function reconcileStatement(BankStatement $statement): void
    {
        $tenantId = $this->tenantManager->getTenantId();

        DB::transaction(function () use ($tenantId, $statement) {
            $lines = $statement->lines()->where('is_reconciled', false)->get();

            $allMatched = true;

            foreach ($lines as $line) {
                // Incoming Credit (+ cents) matches AR invoices. Outgoing Debit (- cents) matches AP.
                $type = $line->amount_cents > 0 ? 'AR' : 'AP';
                $absAmount = abs($line->amount_cents);

                // Find matching invoice within 3 days.
                // For simplicity in MVP, we match exactly the outstanding balance.
                $minDate = $line->transaction_date->copy()->subDays(3);
                $maxDate = $line->transaction_date->copy()->addDays(3);

                $matchingInvoices = FinanceInvoice::where('tenant_id', $tenantId)
                    ->where('type', $type)
                    ->where('status', '!=', 'paid')
                    ->whereBetween('due_date', [$minDate, $maxDate])
                    ->get()
                    ->filter(function ($inv) use ($absAmount) {
                        return $inv->outstanding_balance === $absAmount;
                    });

                if ($matchingInvoices->count() === 1) {
                    $invoice = $matchingInvoices->first();

                    // Mark invoice as paid
                    $invoice->update([
                        'paid_cents' => $invoice->amount_cents,
                        'status' => 'paid',
                    ]);

                    // Mark line as reconciled
                    $line->update(['is_reconciled' => true]);

                    // Post clearing journal
                    // Assuming we have basic account codes predefined for Cash and Receivables/Payables
                    $debitAccount = $type === 'AR' ? '1001' : '2001'; // 1001: Cash, 2001: AP
                    $creditAccount = $type === 'AR' ? '1002' : '1001'; // 1002: AR, 1001: Cash

                    $this->postingEngine->postJournal([
                        'date' => $line->transaction_date->toDateString(),
                        'reference' => 'RECON-' . $line->reference,
                        'description' => "Auto-reconciled payment for {$invoice->invoice_number}",
                        'lines' => [
                            ['account_code' => $debitAccount, 'debit_cents' => $absAmount, 'credit_cents' => 0],
                            ['account_code' => $creditAccount, 'debit_cents' => 0, 'credit_cents' => $absAmount],
                        ]
                    ]);
                } else {
                    $allMatched = false;
                }
            }

            if ($allMatched) {
                $statement->update(['status' => 'reconciled']);
            }
        });
    }
}
