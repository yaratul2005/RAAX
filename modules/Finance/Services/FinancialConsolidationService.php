<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\Finance\Models\LedgerAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FinancialConsolidationService
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function generateConsolidatedTrialBalance(array $branchUuids, string $startDate, string $endDate): array
    {
        $user = Auth::user();

        // Security check: Must have consolidated-reporting permission
        $hasPermission = $user && $user->roles()->whereHas('permissions', function ($query) {
            $query->where('slug', 'consolidated-reporting');
        })->exists();

        if (!$hasPermission) {
            throw new \Illuminate\Auth\Access\AuthorizationException("User lacks authorization for consolidated reporting.");
        }

        // Store original context to restore later
        $originalTenantId = $this->tenantManager->getTenantId();

        try {
            $consolidatedBalances = [];
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($branchUuids as $tenantId) {
                // Switch tenant context dynamically
                $this->tenantManager->setTenantId($tenantId);

                $accounts = LedgerAccount::where('tenant_id', $tenantId)->get();

                foreach ($accounts as $account) {
                    $code = $account->account_code ?? $account->code;
                    $name = $account->account_name ?? $account->name;
                    $type = $account->account_type ?? $account->type;

                    if (!isset($consolidatedBalances[$code])) {
                        $consolidatedBalances[$code] = [
                            'code' => $code,
                            'name' => $name, // Assuming accounts share names across branches
                            'type' => $type,
                            'balance_cents' => 0,
                            'debit_cents' => 0,
                            'credit_cents' => 0,
                        ];
                    }

                    // For real MVP, we'd sum journal entry lines inside date range
                    $lines = DB::table('journal_entry_lines')
                        ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                        ->where('journal_entries.tenant_id', $tenantId)
                        ->where('journal_entry_lines.tenant_id', $tenantId)
                        ->where('journal_entry_lines.account_id', $account->id)
                        ->whereBetween('journal_entries.date', [$startDate, $endDate])
                        ->select(
                            DB::raw('SUM(debit_cents) as total_debit'),
                            DB::raw('SUM(credit_cents) as total_credit')
                        )->first();

                    $debit = (int) $lines->total_debit;
                    $credit = (int) $lines->total_credit;

                    $consolidatedBalances[$code]['debit_cents'] += $debit;
                    $consolidatedBalances[$code]['credit_cents'] += $credit;

                    if (in_array($type, ['asset', 'expense'])) {
                        $consolidatedBalances[$code]['balance_cents'] += ($debit - $credit);
                    } else {
                        $consolidatedBalances[$code]['balance_cents'] += ($credit - $debit);
                    }

                    $totalDebit += $debit;
                    $totalCredit += $credit;
                }
            }

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_debit_cents' => $totalDebit,
                'total_credit_cents' => $totalCredit,
                'accounts' => array_values($consolidatedBalances),
            ];

        } finally {
            // Restore original context securely
            if ($originalTenantId) {
                $this->tenantManager->setTenantId($originalTenantId);
            } else {
                $this->tenantManager->clearTenantId();
            }
        }
    }
}
