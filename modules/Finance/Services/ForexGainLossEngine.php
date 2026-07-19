<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Finance\Models\CurrencyExchangeRate;
use Modules\Finance\Models\FinanceInvoice;

class ForexGainLossEngine
{
    protected TenantContextManager $tenantManager;
    protected PostingEngine $postingEngine;

    public function __construct(TenantContextManager $tenantManager, PostingEngine $postingEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->postingEngine = $postingEngine;
    }

    public function calculateRealizedGainLoss(FinanceInvoice $invoice, int $paymentAmountCents, string $paymentDate): int
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        // If invoice is natively in BDT, no forex logic
        if ($invoice->currency_code === 'BDT') {
            return 0;
        }

        return DB::transaction(function () use ($tenantId, $invoice, $paymentAmountCents, $paymentDate) {
            $currency = $invoice->currency_code;

            // R_invoice
            $rateInvoice = CurrencyExchangeRate::where('tenant_id', $tenantId)
                ->where('from_currency', $currency)
                ->where('effective_date', '<=', $invoice->issue_date)
                ->orderBy('effective_date', 'desc')
                ->first();

            if (!$rateInvoice) {
                throw new InvalidArgumentException("Exchange rate for {$currency} not found on or before invoice issue date.");
            }

            // R_payment
            $ratePayment = CurrencyExchangeRate::where('tenant_id', $tenantId)
                ->where('from_currency', $currency)
                ->where('effective_date', '<=', $paymentDate)
                ->orderBy('effective_date', 'desc')
                ->first();

            if (!$ratePayment) {
                throw new InvalidArgumentException("Exchange rate for {$currency} not found on or before payment date.");
            }

            // Calculations
            // Base Value_invoice = (Payment Amount Cents * R_invoice) / 10000
            $baseValueInvoice = (int) round(($paymentAmountCents * $rateInvoice->rate_basis_points) / 10000);

            // Base Value_payment = (Payment Amount Cents * R_payment) / 10000
            $baseValuePayment = (int) round(($paymentAmountCents * $ratePayment->rate_basis_points) / 10000);

            // Realized Gain/Loss Cents
            $realizedGainLossCents = $baseValuePayment - $baseValueInvoice;

            if ($realizedGainLossCents !== 0) {
                // Post GL Entry
                // If it's AP (we are paying):
                // A negative variance means we paid less BDT than expected -> Gain
                // A positive variance means we paid more BDT than expected -> Loss
                // For AR (we are receiving):
                // A negative variance means we received less BDT than expected -> Loss
                // A positive variance means we received more BDT than expected -> Gain

                // Account Codes (Hardcoded for MVP)
                // 7001: Forex Gain/Loss (Income/Expense type account)
                $lines = [];

                if ($invoice->type === 'AP') {
                    if ($realizedGainLossCents < 0) {
                        // Gain (Credit)
                        $lines[] = ['account_code' => '7001', 'debit_cents' => 0, 'credit_cents' => abs($realizedGainLossCents)];
                        // Debit AP
                        $lines[] = ['account_code' => '2001', 'debit_cents' => abs($realizedGainLossCents), 'credit_cents' => 0];
                    } else {
                        // Loss (Debit)
                        $lines[] = ['account_code' => '7001', 'debit_cents' => abs($realizedGainLossCents), 'credit_cents' => 0];
                        // Credit AP
                        $lines[] = ['account_code' => '2001', 'debit_cents' => 0, 'credit_cents' => abs($realizedGainLossCents)];
                    }
                } else {
                    if ($realizedGainLossCents > 0) {
                        // Gain (Credit)
                        $lines[] = ['account_code' => '7001', 'debit_cents' => 0, 'credit_cents' => abs($realizedGainLossCents)];
                        // Debit AR
                        $lines[] = ['account_code' => '1002', 'debit_cents' => abs($realizedGainLossCents), 'credit_cents' => 0];
                    } else {
                        // Loss (Debit)
                        $lines[] = ['account_code' => '7001', 'debit_cents' => abs($realizedGainLossCents), 'credit_cents' => 0];
                        // Credit AR
                        $lines[] = ['account_code' => '1002', 'debit_cents' => 0, 'credit_cents' => abs($realizedGainLossCents)];
                    }
                }

                $this->postingEngine->postJournal([
                    'date' => $paymentDate,
                    'reference' => 'FXR-' . $invoice->invoice_number,
                    'description' => "Realized Forex variance for {$invoice->invoice_number}",
                    'lines' => $lines
                ]);
            }

            return $realizedGainLossCents;
        });
    }
}
