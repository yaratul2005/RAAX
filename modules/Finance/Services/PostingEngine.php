<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\FiscalYear;
use Illuminate\Support\Carbon;

class PostingEngine
{
    protected TenantContextManager $tenantManager;
    protected LedgerChainEngine $chainEngine;

    public function __construct(TenantContextManager $tenantManager, LedgerChainEngine $chainEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->chainEngine = $chainEngine;
    }

    /**
     * Post a new journal entry with balanced lines.
     *
     * @throws InvalidArgumentException
     */
    /**
     * @param  array<string, mixed>  $payload
     */
    public function post(array $payload): JournalEntry
    {
        $tenantId = $this->tenantManager->getTenantId();

        if (! $tenantId) {
            throw new InvalidArgumentException('Tenant context is required for posting journals.');
        }

        $lines = $payload['lines'] ?? [];
        if (empty($lines)) {
            throw new InvalidArgumentException('Journal must contain at least one line.');
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += (int) ($line['debit_amount'] ?? 0);
            $totalCredit += (int) ($line['credit_amount'] ?? 0);
        }

        if ($totalDebit !== $totalCredit) {
            throw new InvalidArgumentException("Journal is unbalanced. Total Debits ({$totalDebit}) do not equal Total Credits ({$totalCredit}).");
        }

        if ($totalDebit <= 0) {
            throw new InvalidArgumentException('Journal must have a non-zero balance.');
        }

        return DB::transaction(function () use ($payload, $lines, $tenantId, $totalDebit) {
            // Re-assert tenant ID inside transaction just to be safe
            if (config('database.default') !== 'sqlite') {
                DB::statement("SET LOCAL app.current_tenant_id = '{$tenantId}'");
            }

            $journalEntry = JournalEntry::create([
                'tenant_id' => $tenantId,
                'entry_date' => $payload['entry_date'],
                'reference' => $payload['reference'] ?? null,
                'description' => $payload['description'] ?? null,
                'amount' => $totalDebit,
                'currency_code' => $payload['currency_code'],
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'tenant_id' => $tenantId,
                    'journal_entry_id' => $journalEntry->id,
                    'ledger_account_id' => $line['ledger_account_id'],
                    'debit_amount' => (int) ($line['debit_amount'] ?? 0),
                    'credit_amount' => (int) ($line['credit_amount'] ?? 0),
                ]);
            }

            return $journalEntry;
        });
    }
}
