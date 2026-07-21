<?php

namespace Modules\Finance\Services\TaxEngine;

use Modules\Finance\Models\TaxRateRule;

class EuropeVatDriver implements TaxEngineInterface
{
    public function calculateTax(int $baseAmountCents, TaxRateRule $rule, array $context = []): array
    {
        // European VAT is generally destination-based but acts similarly to a standard percentage applied
        $vatCents = (int) round(($baseAmountCents * $rule->rate_basis_points) / 10000);

        return [
            [
                'name' => 'EU VAT',
                'amount_cents' => $vatCents,
            ]
        ];
    }
}
