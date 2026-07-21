<?php

namespace Modules\Finance\Services\TaxEngine;

use Modules\Finance\Models\TaxRateRule;

interface TaxEngineInterface
{
    /**
     * Calculates tax and returns breakdown lines.
     * Return array structure: [['name' => 'CGST', 'amount_cents' => 900], ...]
     */
    public function calculateTax(int $baseAmountCents, TaxRateRule $rule, array $context = []): array;
}
