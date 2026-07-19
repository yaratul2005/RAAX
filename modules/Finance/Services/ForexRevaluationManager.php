<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Finance\Models\CurrencyExchangeRate;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Models\ForexRevaluationLog;

class ForexRevaluationManager
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function runMonthEndRevaluation(string $targetMonth, string $targetCurrency): void
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $targetMonth)) {
            throw new InvalidArgumentException("Invalid month format. Use YYYY-MM.");
        }

        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        if ($targetCurrency === 'BDT') {
            throw new InvalidArgumentException("Cannot revalue base currency.");
        }

        DB::transaction(function () use ($tenantId, $targetMonth, $targetCurrency) {
            $endOfMonth = Carbon::createFromFormat('Y-m', $targetMonth)->endOfMonth()->toDateString();
            $firstOfNextMonth = Carbon::createFromFormat('Y-m', $targetMonth)->endOfMonth()->addDay()->toDateString();

            $monthEndRate = CurrencyExchangeRate::where('tenant_id', $tenantId)
                ->where('from_currency', $targetCurrency)
                ->where('effective_date', '<=', $endOfMonth)
                ->orderBy('effective_date', 'desc')
                ->first();

            if (!$monthEndRate) {
                throw new InvalidArgumentException("No exchange rate found for {$targetCurrency} on or before {$endOfMonth}.");
            }

            // Get open invoices in this currency
            $invoices = FinanceInvoice::where('tenant_id', $tenantId)
                ->where('currency_code', $targetCurrency)
                ->where('status', '!=', 'paid')
                ->where('issue_date', '<=', $endOfMonth)
                ->get();

            $totalUnrealizedGainLossCents = 0;

            foreach ($invoices as $invoice) {
                $rateInvoice = CurrencyExchangeRate::where('tenant_id', $tenantId)
                    ->where('from_currency', $targetCurrency)
                    ->where('effective_date', '<=', $invoice->issue_date)
                    ->orderBy('effective_date', 'desc')
                    ->first();

                if (!$rateInvoice) continue;

                $outstandingForeign = $invoice->outstanding_balance;

                $baseValueInvoice = (int) round(($outstandingForeign * $rateInvoice->rate_basis_points) / 10000);
                $baseValueMonthEnd = (int) round(($outstandingForeign * $monthEndRate->rate_basis_points) / 10000);

                // Variance
                $variance = $baseValueMonthEnd - $baseValueInvoice;

                // For AP: Negative variance = Gain (owe less BDT), Positive = Loss
                // For AR: Positive variance = Gain (receive more BDT), Negative = Loss
                if ($invoice->type === 'AP') {
                    $totalUnrealizedGainLossCents -= $variance; // Invert so positive is gain
                } else {
                    $totalUnrealizedGainLossCents += $variance;
                }
            }

            if ($totalUnrealizedGainLossCents !== 0) {
                // Post revaluation entry
                $lines = [];
                if ($totalUnrealizedGainLossCents > 0) {
                    // Net Gain
                    $lines[] = ['account_code' => '7001', 'debit_cents' => 0, 'credit_cents' => $totalUnrealizedGainLossCents];
                    $lines[] = ['account_code' => '1006', 'debit_cents' => $totalUnrealizedGainLossCents, 'credit_cents' => 0]; // 1006 Unrealized FX
                } else {
                    // Net Loss
                    $lines[] = ['account_code' => '7001', 'debit_cents' => abs($totalUnrealizedGainLossCents), 'credit_cents' => 0];
                    $lines[] = ['account_code' => '1006', 'debit_cents' => 0, 'credit_cents' => abs($totalUnrealizedGainLossCents)];
                }

                $journalEntry = $this->postingEngine->postJournal([
                    'date' => $endOfMonth,
                    'reference' => "FX-REVAL-{$targetCurrency}-{$targetMonth}",
                    'description' => "Unrealized FX Revaluation for {$targetCurrency} in {$targetMonth}",
                    'lines' => $lines
                ]);

                // Create reversing entry on the 1st of next month
                $reversedLines = array_map(function ($line) {
                    return [
                        'account_code' => $line['account_code'],
                        'debit_cents' => $line['credit_cents'],
                        'credit_cents' => $line['debit_cents']
                    ];
                }, $lines);

                $this->postingEngine->postJournal([
                    'date' => $firstOfNextMonth,
                    'reference' => "FX-REV-REV-{$targetCurrency}-{$targetMonth}",
                    'description' => "Reversal of FX Revaluation for {$targetCurrency} in {$targetMonth}",
                    'lines' => $reversedLines
                ]);

                ForexRevaluationLog::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'revaluation_month' => $targetMonth,
                    'unrealized_gain_loss_cents' => $totalUnrealizedGainLossCents,
                    'journal_entry_id' => $journalEntry->id,
                ]);
            }
        });
    }
}
