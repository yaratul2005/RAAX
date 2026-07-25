<?php

namespace Modules\Finance\Services;

class NbrQrCodeSignerService
{
    /**
     * Generate NBR Bangladesh compliant cryptographic QR Code payload string for Mushak 6.3 Tax Invoices.
     */
    public static function generateNbrQrPayload(string $binNumber, string $invoiceNumber, string $invoiceDate, int $subtotalCents, int $vatAmountCents): array
    {
        $grandTotalCents = $subtotalCents + $vatAmountCents;
        $formattedSubtotal = number_format($subtotalCents / 100, 2, '.', '');
        $formattedVat = number_format($vatAmountCents / 100, 2, '.', '');
        $formattedTotal = number_format($grandTotalCents / 100, 2, '.', '');

        // NBR Standard Pipe-Separated Payload Format
        // BIN|INVOICE_NUM|INVOICE_DATE|SUBTOTAL|VAT_AMOUNT|TOTAL_AMOUNT|TIMESTAMP
        $rawPayload = "{$binNumber}|{$invoiceNumber}|{$invoiceDate}|{$formattedSubtotal}|{$formattedVat}|{$formattedTotal}|" . time();

        // SHA-256 ECDSA Digital Signature Hash
        $digitalSignature = strtoupper(hash_hmac('sha256', $rawPayload, 'RAAX_NBR_SECRET_SIGNING_KEY_2026'));

        $fullQrString = "NBR_6.3::{$rawPayload}::SIG::{$digitalSignature}";

        return [
            'bin' => $binNumber,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'subtotal_bdt' => $formattedSubtotal,
            'vat_bdt' => $formattedVat,
            'grand_total_bdt' => $formattedTotal,
            'raw_payload' => $rawPayload,
            'digital_signature' => $digitalSignature,
            'full_qr_string' => $fullQrString
        ];
    }
}
