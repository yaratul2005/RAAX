<?php

namespace Modules\Finance\Services\TaxEngine;

use Modules\Finance\Models\TaxRateRule;

class BangladeshVatDriver implements TaxEngineInterface
{
    public function calculateTax(int $baseAmountCents, TaxRateRule $rule, array $context = []): array
    {
        // Bangladesh applies standard VAT without splitting
        $vatCents = (int) round(($baseAmountCents * $rule->rate_basis_points) / 10000);

        return [
            [
                'name' => 'VAT (' . ($rule->rate_basis_points / 100) . '%)',
                'amount_cents' => $vatCents,
            ]
        ];
    }
}
