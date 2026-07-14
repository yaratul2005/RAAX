<?php

namespace Modules\HR\Services;

class NBRTaxCalculator
{
    /**
     * Calculate monthly withholding tax based on AY 2026-27 (FY 2025-26) slabs.
     * All values are in integer cents.
     */
    public function calculateMonthlyWithholding(int $annualGrossCents, string $gender = 'male'): int
    {
        $taxFreeThresholdCents = 37500000; // General threshold: BDT 375,000

        if (strtolower($gender) === 'female') {
            $taxFreeThresholdCents = 42500000; // Female/Senior threshold: BDT 425,000
        }

        $slabs = [
            ['limit' => $taxFreeThresholdCents, 'rate' => 0.00],
            ['limit' => 30000000, 'rate' => 0.10], // Next 300,000
            ['limit' => 40000000, 'rate' => 0.15], // Next 400,000
            ['limit' => 50000000, 'rate' => 0.20], // Next 500,000
            ['limit' => 200000000, 'rate' => 0.25], // Next 2,000,000
            ['limit' => PHP_INT_MAX, 'rate' => 0.30], // Remaining
        ];

        $remainingIncome = $annualGrossCents;
        $totalTaxCents = 0;

        foreach ($slabs as $slab) {
            if ($remainingIncome <= 0) {
                break;
            }

            $taxableInThisSlab = min($remainingIncome, $slab['limit']);
            $taxForThisSlab = (int) round($taxableInThisSlab * $slab['rate']);

            $totalTaxCents += $taxForThisSlab;
            $remainingIncome -= $taxableInThisSlab;
        }

        // Return monthly tax rounded to nearest cent
        return (int) round($totalTaxCents / 12);
    }
}
