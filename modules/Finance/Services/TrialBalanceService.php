<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use InvalidArgumentException;
use Modules\Finance\Models\ChartOfAccounts;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\LedgerAccount;

class TrialBalanceService
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Generate Trial Balance for a given date range.
     *
     * @return array<string, mixed>
     */
    public function generate(string $startDate, string $endDate): array
    {
        $tenantId = $this->tenantManager->getTenantId();

        if (! $tenantId) {
            throw new InvalidArgumentException('Tenant context is required for Trial Balance.');
        }

        // We will calculate balances per LedgerAccount, as JournalEntryLine is currently linked to LedgerAccount.
        // If ChartOfAccounts is meant to replace LedgerAccount, I will group by ledger_account_id.
        $accounts = LedgerAccount::where('tenant_id', $tenantId)->get();

        $trialBalance = [];
        $totalDebitBalance = 0;
        $totalCreditBalance = 0;

        foreach ($accounts as $account) {
            // Opening Balance: Sum of entries before start_date
            $openingDebits = JournalEntryLine::where('ledger_account_id', $account->id)
                ->whereHas('journalEntry', function ($query) use ($startDate) {
                    $query->where('entry_date', '<', $startDate);
                })
                ->sum('debit_amount');

            $openingCredits = JournalEntryLine::where('ledger_account_id', $account->id)
                ->whereHas('journalEntry', function ($query) use ($startDate) {
                    $query->where('entry_date', '<', $startDate);
                })
                ->sum('credit_amount');

            $openingBalance = $openingDebits - $openingCredits;

            // Period Debits: Sum of all debit entries within the date range
            $periodDebits = JournalEntryLine::where('ledger_account_id', $account->id)
                ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('entry_date', [$startDate, $endDate]);
                })
                ->sum('debit_amount');

            // Period Credits: Sum of all credit entries within the date range
            $periodCredits = JournalEntryLine::where('ledger_account_id', $account->id)
                ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('entry_date', [$startDate, $endDate]);
                })
                ->sum('credit_amount');

            // Closing Balance = Opening Balance + Period Debits - Period Credits
            $closingBalance = $openingBalance + $periodDebits - $periodCredits;

            // Mathematical Verification: normal balances
            // Typically Assets/Expenses have debit normal balance (positive)
            // Liabilities/Equity/Revenue have credit normal balance (negative openingBalance)
            // We just keep it simple: sum of positive closing balances should equal sum of negative closing balances (absolute)
            // Wait, the prompt says: "sum of all closing balances for debit-normal accounts equals the sum of all closing balances for credit-normal accounts"
            $isDebitNormal = in_array($account->account_type, ['Asset', 'Expense']);

            // Adjust closing balance representation
            if ($isDebitNormal) {
                $totalDebitBalance += $closingBalance;
            } else {
                // credit normal account, so closing balance would typically be negative (Credits > Debits)
                // credit balance = -closingBalance
                $totalCreditBalance += -$closingBalance;
            }

            $trialBalance[] = [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'opening_balance' => $openingBalance,
                'period_debits' => $periodDebits,
                'period_credits' => $periodCredits,
                'closing_balance' => $closingBalance,
            ];
        }

        // The assertion to ensure the ledger is balanced
        if ($totalDebitBalance !== $totalCreditBalance) {
            throw new \LogicException("Trial Balance is unbalanced. Debits: {$totalDebitBalance}, Credits: {$totalCreditBalance}");
        }

        return [
            'accounts' => $trialBalance,
            'total_debits' => $totalDebitBalance,
            'total_credits' => $totalCreditBalance,
        ];
    }
}
