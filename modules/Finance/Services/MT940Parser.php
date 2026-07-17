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

                $amountStr = substr($val, 10);
                $amountStr = str_replace(',', '.', $amountStr); // some MT940 use comma for decimal
                $cents = (int) round((float) $amountStr * 100);

                $openingBalance = ($dc === 'C') ? $cents : -$cents;
            } elseif (str_starts_with($line, ':61:')) {
                // :61:2401050105CR150,00NMSCNONREF
                // Format: ValDate (6), EntryDate (4 or missing), D/C (1 or 2), Amount, TransType (4), Ref
                $valDateStr = substr($line, 4, 6);

                // Find D/C and extract
                // Can be C, D, RC, RD
                preg_match('/(C|D|RC|RD)([0-9,.]+)/', substr($line, 10), $matches);
                if (count($matches) >= 3) {
                    $dc = $matches[1];
                    $amountStr = str_replace(',', '.', $matches[2]);
                    $cents = (int) round((float) $amountStr * 100);
                    $amountCents = (str_starts_with($dc, 'C')) ? $cents : -$cents;

                    // Extract reference roughly after amount
                    $remaining = substr($line, strpos($line, $matches[2]) + strlen($matches[2]));
                    $reference = trim($remaining);

                    $transactions[] = [
                        'transaction_date' => Carbon::createFromFormat('ymd', $valDateStr)->toDateString(),
                        'amount_cents' => $amountCents,
                        'reference' => $reference,
                    ];
                }
            } elseif (str_starts_with($line, ':62F:')) {
                // Similar to 60F
                $val = substr($line, 5);
                $dc = substr($val, 0, 1);
                $amountStr = substr($val, 10);
                $amountStr = str_replace(',', '.', $amountStr);
                $cents = (int) round((float) $amountStr * 100);

                $closingBalance = ($dc === 'C') ? $cents : -$cents;
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
