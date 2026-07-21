<?php

namespace Modules\Finance\Services;

use Carbon\Carbon;
use InvalidArgumentException;

class MT940Parser
{
    /**
     * Parses an MT940 string into a structured array.
     */
    public function parseStatement(string $fileContent): array
    {
        $lines = explode("\n", str_replace("\r", "", $fileContent));

        $accountNumber = null;
        $openingBalance = null;
        $closingBalance = null;
        $transactions = [];
        $statementDate = null;

        $currentTransaction = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (str_starts_with($line, ':25:')) {
                $accountNumber = substr($line, 4);
            } elseif (str_starts_with($line, ':60F:')) {
                // :60F:C240101BDT1000,50
                // Format: D/C (1), Date (6), Currency (3), Amount
                $val = substr($line, 5);
                $dc = substr($val, 0, 1);
                $dateStr = substr($val, 1, 6); // YYMMDD

                // For simplicity assuming opening date is the statement date
                if (!$statementDate) {
                    $statementDate = Carbon::createFromFormat('ymd', $dateStr)->toDateString();
                }

                if (preg_match('/[A-Z]{3}([0-9,.]+)/', $val, $m)) {
                    $amountStr = str_replace(',', '.', $m[1]);
                    $cents = (int) round((float) $amountStr * 100);
                    $openingBalance = ($dc === 'C') ? $cents : -$cents;
                }
            } elseif (str_starts_with($line, ':61:')) {
                // :61:2401050105CR150,00NMSCNONREF
                $valDateStr = substr($line, 4, 6);

                if (preg_match('/(?P<dc>CR|DR|RC|RD|C|D)(?P<amount>[0-9]+(?:,[0-9]+)?)(?P<rest>.*)/', $line, $m)) {
                    $dc = $m['dc'];
                    $amountStr = str_replace(',', '.', $m['amount']);
                    $cents = (int) round((float) $amountStr * 100);
                    $amountCents = (str_starts_with($dc, 'C')) ? $cents : -$cents;

                    $transactions[] = [
                        'transaction_date' => Carbon::createFromFormat('ymd', $valDateStr)->toDateString(),
                        'amount_cents' => $amountCents,
                        'reference' => trim($m['rest']),
                    ];
                }
            } elseif (str_starts_with($line, ':62F:')) {
                // Similar to 60F
                $val = substr($line, 5);
                $dc = substr($val, 0, 1);
                if (preg_match('/[A-Z]{3}([0-9,.]+)/', $val, $m)) {
                    $amountStr = str_replace(',', '.', $m[1]);
                    $cents = (int) round((float) $amountStr * 100);
                    $closingBalance = ($dc === 'C') ? $cents : -$cents;
                }
            }
        }

        if ($accountNumber === null || $openingBalance === null || $closingBalance === null) {
            throw new InvalidArgumentException("Invalid MT940 format: missing mandatory tags.");
        }

        // Mathematical Validation
        $calculatedClosing = $openingBalance;
        foreach ($transactions as $txn) {
            $calculatedClosing += $txn['amount_cents'];
        }

        if ($calculatedClosing !== $closingBalance) {
            throw new InvalidArgumentException("Mathematical validation failed: computed closing balance ($calculatedClosing) does not match statement closing balance ($closingBalance).");
        }

        return [
            'account_number' => $accountNumber,
            'statement_date' => $statementDate,
            'opening_balance_cents' => $openingBalance,
            'closing_balance_cents' => $closingBalance,
            'transactions' => $transactions,
        ];
    }
}
