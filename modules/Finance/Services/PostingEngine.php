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
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'entry_date' => $payload['entry_date'],
                'date' => $payload['entry_date'],
                'reference' => $payload['reference'] ?? null,
                'description' => $payload['description'] ?? null,
                'amount' => $totalDebit,
                'currency_code' => $payload['currency_code'],
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $line['ledger_account_id'],
                    'ledger_account_id' => $line['ledger_account_id'],
                    'debit_amount' => (int) ($line['debit_amount'] ?? 0),
                    'credit_amount' => (int) ($line['credit_amount'] ?? 0),
                    'debit_cents' => (int) ($line['debit_amount'] ?? 0),
                    'credit_cents' => (int) ($line['credit_amount'] ?? 0),
                ]);
            }

            return $journalEntry;
        });
    }

    public function postJournal(array $payload): JournalEntry
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException('Tenant context required');
        }

        // Check if date is in closed fiscal year
        $date = $payload['date'] ?? $payload['entry_date'] ?? date('Y-m-d');
        $closedFy = FiscalYear::where('tenant_id', $tenantId)
            ->where('status', 'closed')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($closedFy) {
            throw new InvalidArgumentException('Cannot post journal entry to a closed fiscal year.');
        }

        $lines = $payload['lines'] ?? [];
        $totalDebit = 0;
        $totalCredit = 0;
        $mappedLines = [];

        foreach ($lines as $line) {
            $debit = (int) ($line['debit_cents'] ?? $line['debit_amount'] ?? 0);
            $credit = (int) ($line['credit_cents'] ?? $line['credit_amount'] ?? 0);
            $totalDebit += $debit;
            $totalCredit += $credit;

            $accountCode = $line['account_code'] ?? null;
            $accountId = $line['ledger_account_id'] ?? $line['account_id'] ?? null;

            if (!$accountId && $accountCode) {
                $acc = \Modules\Finance\Models\LedgerAccount::where('tenant_id', $tenantId)
                    ->where('account_code', $accountCode)
                    ->first();
                $accountId = $acc?->id;
            }

            $mappedLines[] = [
                'ledger_account_id' => $accountId,
                'debit_cents' => $debit,
                'credit_cents' => $credit,
                'debit_amount' => $debit,
                'credit_amount' => $credit,
            ];
        }

        if ($totalDebit !== $totalCredit) {
            throw new InvalidArgumentException("Unbalanced journal entry.");
        }

        $journalEntry = DB::transaction(function () use ($payload, $mappedLines, $tenantId, $totalDebit, $date) {
            $entry = JournalEntry::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'entry_date' => $date,
                'date' => $date,
                'reference' => $payload['reference'] ?? null,
                'description' => $payload['description'] ?? null,
                'amount' => $totalDebit,
                'currency_code' => $payload['currency_code'] ?? 'BDT',
            ]);

            foreach ($mappedLines as $line) {
                JournalEntryLine::create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['ledger_account_id'],
                    'ledger_account_id' => $line['ledger_account_id'],
                    'debit_cents' => $line['debit_cents'],
                    'credit_cents' => $line['credit_cents'],
                    'debit_amount' => $line['debit_amount'],
                    'credit_amount' => $line['credit_amount'],
                ]);
            }

            return $entry;
        });

        $this->chainEngine->appendEntry($journalEntry);

        return $journalEntry;
    }
}
