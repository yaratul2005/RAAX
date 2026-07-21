<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Finance\Contracts\BudgetManagerInterface;
use Modules\Finance\Models\BudgetLine;
use Modules\Finance\Models\EncumbranceLedger;

class InsufficientBudgetException extends \Exception {}

class BudgetManager implements BudgetManagerInterface
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function checkFunds(string $accountId, int $requestedAmountCents): bool
    {
        $tenantId = $this->tenantManager->getTenantId();

        // 1. Get Allocated Budget
        // For simplicity, we just aggregate all active budget lines for this account
        $allocatedCents = BudgetLine::where('tenant_id', $tenantId)
            ->where('chart_of_accounts_id', $accountId)
            ->whereHas('budget', function ($q) {
                $q->where('is_active', true);
            })
            ->sum('allocated_amount_cents');

        // 2. Get Actual Expenditures (Journal Entries)
        // Expenditures normally sit on debit side for expense accounts
        $expenditures = DB::table('journal_entry_lines')
            ->where('tenant_id', $tenantId)
            ->where('account_id', $accountId)
            ->selectRaw('SUM(debit_cents) - SUM(credit_cents) as actual_cents') // Simplified view
            ->first();
        $actualCents = $expenditures->actual_cents ?? 0;

        // 3. Get Active Encumbrances
        $encumberedCents = EncumbranceLedger::where('tenant_id', $tenantId)
            ->where('chart_of_accounts_id', $accountId)
            ->where('status', 'active')
            ->selectRaw('SUM(encumbered_amount_cents - relieved_amount_cents) as active_encumbrance')
            ->first();
        $activeEncumbrance = $encumberedCents->active_encumbrance ?? 0;

        // Funds Available = Allocated - Actual - Active Encumbrances
        $fundsAvailable = $allocatedCents - $actualCents - $activeEncumbrance;

        return $requestedAmountCents <= $fundsAvailable;
    }

    public function encumberFunds(string $accountId, string $sourceType, string $sourceId, int $amountCents): EncumbranceLedger
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        return DB::transaction(function () use ($tenantId, $accountId, $sourceType, $sourceId, $amountCents) {
            if (!$this->checkFunds($accountId, $amountCents)) {
                throw new InsufficientBudgetException("Requested amount exceeds available budget funds.");
            }

            return EncumbranceLedger::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'chart_of_accounts_id' => $accountId,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'encumbered_amount_cents' => $amountCents,
                'status' => 'active',
            ]);
        });
    }

    public function relieveFunds(string $sourceType, string $sourceId, int $amountToRelieveCents): void
    {
        $tenantId = $this->tenantManager->getTenantId();

        DB::transaction(function () use ($tenantId, $sourceType, $sourceId, $amountToRelieveCents) {
            $ledger = EncumbranceLedger::where('tenant_id', $tenantId)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$ledger) {
                return; // Nothing to relieve or already relieved
            }

            $newRelieved = $ledger->relieved_amount_cents + $amountToRelieveCents;
            $status = ($newRelieved >= $ledger->encumbered_amount_cents) ? 'relieved' : 'active';

            $ledger->update([
                'relieved_amount_cents' => $newRelieved,
                'status' => $status
            ]);
        });
    }
}
