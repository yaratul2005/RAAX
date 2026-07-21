<?php

namespace Modules\Finance\Services\TaxEngine;

use Modules\Finance\Models\TaxRateRule;

class IndiaGstDriver implements TaxEngineInterface
{
    public function calculateTax(int $baseAmountCents, TaxRateRule $rule, array $context = []): array
    {
        // Needs state context to determine intra vs inter state
        // Default to intra-state (CGST + SGST split) if context not provided for safety/demonstration
        $isInterState = $context['is_inter_state'] ?? false;

        $totalTaxCents = (int) round(($baseAmountCents * $rule->rate_basis_points) / 10000);

        if ($isInterState) {
            return [
                [
                    'name' => 'IGST',
                    'amount_cents' => $totalTaxCents,
                ]
            ];
        }

        // Intra-state split 50/50
        $halfTax = (int) round($totalTaxCents / 2);

        // Handle odd cents to ensure total matches exactly
        $remainder = $totalTaxCents - ($halfTax * 2);

        return [
            [
                'name' => 'CGST',
                'amount_cents' => $halfTax + $remainder, // Attach remainder to central
            ],
            [
                'name' => 'SGST',
                'amount_cents' => $halfTax,
            ]
        ];
    }
}
