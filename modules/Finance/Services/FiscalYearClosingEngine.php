<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Finance\Models\FiscalYear;
use Modules\Finance\Models\LedgerAccount;
use Modules\Finance\Models\RetainedEarningsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FiscalYearClosingEngine
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function closeFiscalYear(string $fiscalYearId, string $retainedEarningsAccountId): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $fiscalYearId, $retainedEarningsAccountId) {
            $fiscalYear = FiscalYear::where('tenant_id', $tenantId)->lockForUpdate()->find($fiscalYearId);
            if (!$fiscalYear) {
                throw new InvalidArgumentException("Fiscal Year not found.");
            }

            if ($fiscalYear->status === 'closed') {
                throw new InvalidArgumentException("Fiscal Year is already closed.");
            }

            $retainedEarningsAccount = LedgerAccount::where('tenant_id', $tenantId)->find($retainedEarningsAccountId);
            $reType = $retainedEarningsAccount->account_type ?? $retainedEarningsAccount->type;
            if (!$retainedEarningsAccount || $reType !== 'equity') {
                throw new InvalidArgumentException("Invalid retained earnings account. Must be of type 'equity'.");
            }

            $revenueAccounts = LedgerAccount::where('tenant_id', $tenantId)->where(function($q){ $q->where('account_type', 'revenue')->orWhere('type', 'revenue'); })->get();
            $expenseAccounts = LedgerAccount::where('tenant_id', $tenantId)->where(function($q){ $q->where('account_type', 'expense')->orWhere('type', 'expense'); })->get();

            $totalRevenueCents = 0;
            $totalExpenseCents = 0;

            $journalLines = [];

            // Sum up revenues (Credit balance normally)
            foreach ($revenueAccounts as $account) {
                $code = $account->account_code ?? $account->code;
                $lines = DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_entries.tenant_id', $tenantId)
                    ->where('journal_entry_lines.tenant_id', $tenantId)
                    ->where('journal_entry_lines.account_id', $account->id)
                    ->whereBetween('journal_entries.date', [$fiscalYear->start_date, $fiscalYear->end_date])
                    ->select(
                        DB::raw('SUM(debit_cents) as total_debit'),
                        DB::raw('SUM(credit_cents) as total_credit')
                    )->first();

                $balance = ((int) $lines->total_credit) - ((int) $lines->total_debit);

                if ($balance > 0) {
                    $totalRevenueCents += $balance;
                    $journalLines[] = ['account_code' => $code, 'debit_cents' => $balance, 'credit_cents' => 0]; // Debit to close
                } elseif ($balance < 0) {
                    $totalRevenueCents += $balance;
                    $journalLines[] = ['account_code' => $code, 'debit_cents' => 0, 'credit_cents' => abs($balance)]; // Credit to close abnormal
                }
            }

            // Sum up expenses (Debit balance normally)
            foreach ($expenseAccounts as $account) {
                $code = $account->account_code ?? $account->code;
                $lines = DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_entries.tenant_id', $tenantId)
                    ->where('journal_entry_lines.tenant_id', $tenantId)
                    ->where('journal_entry_lines.account_id', $account->id)
                    ->whereBetween('journal_entries.date', [$fiscalYear->start_date, $fiscalYear->end_date])
                    ->select(
                        DB::raw('SUM(debit_cents) as total_debit'),
                        DB::raw('SUM(credit_cents) as total_credit')
                    )->first();

                $balance = ((int) $lines->total_debit) - ((int) $lines->total_credit);

                if ($balance > 0) {
                    $totalExpenseCents += $balance;
                    $journalLines[] = ['account_code' => $code, 'debit_cents' => 0, 'credit_cents' => $balance]; // Credit to close
                } elseif ($balance < 0) {
                    $totalExpenseCents += $balance;
                    $journalLines[] = ['account_code' => $code, 'debit_cents' => abs($balance), 'credit_cents' => 0]; // Debit to close abnormal
                }
            }

            $netIncomeCents = $totalRevenueCents - $totalExpenseCents;
            $reCode = $retainedEarningsAccount->account_code ?? $retainedEarningsAccount->code;

            // Post Net Income to Retained Earnings
            if ($netIncomeCents > 0) {
                // Profit -> Credit RE
                $journalLines[] = ['account_code' => $reCode, 'debit_cents' => 0, 'credit_cents' => $netIncomeCents];
            } elseif ($netIncomeCents < 0) {
                // Loss -> Debit RE
                $journalLines[] = ['account_code' => $reCode, 'debit_cents' => abs($netIncomeCents), 'credit_cents' => 0];
            }

            // Update fiscal year status before posting so the validation doesn't block the closing entry itself (or we bypass it)
            // Wait, the PostingEngine blocks if the date is within a CLOSED year. We haven't closed it yet!
            // Status is currently active/closing. We are good to post.

            $journalEntry = null;

            if (count($journalLines) > 0) {
                // We need to ensure debits and credits match (they mathematically do because Revenue - Expense = Net Income, so Revenue (Dr) - Expense (Cr) - Net Income (Cr) = 0 => Revenue = Expense + Net Income => Dr = Cr
                $journalEntry = $this->postingEngine->postJournal([
                    'date' => $fiscalYear->end_date->toDateString(), // Post on the last day
                    'reference' => 'FY-CLOSE-' . $fiscalYear->name,
                    'description' => "Year End Closing for " . $fiscalYear->name,
                    'lines' => $journalLines
                ]);
            }

            // Create Log
            RetainedEarningsLog::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'fiscal_year_id' => $fiscalYear->id,
                'closing_net_income_cents' => $netIncomeCents,
                'retained_earnings_account_id' => $retainedEarningsAccount->id,
                'journal_entry_id' => $journalEntry ? $journalEntry->id : Str::uuid()->toString(), // Null not allowed so fallback but ideally never null
            ]);

            // Close the year
            $fiscalYear->update([
                'status' => 'closed',
                'closed_at' => now()
            ]);
        });
    }
}
