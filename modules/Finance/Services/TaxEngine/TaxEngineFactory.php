<?php

namespace Modules\Finance\Services\TaxEngine;

use Modules\Finance\Models\TaxJurisdiction;
use InvalidArgumentException;

class TaxEngineFactory
{
    public static function resolve(TaxJurisdiction $jurisdiction): TaxEngineInterface
    {
        return match (strtoupper($jurisdiction->country_code)) {
            'BD' => new BangladeshVatDriver(),
            'IN' => new IndiaGstDriver(),
            'EU' => new EuropeVatDriver(), // Fallback for Europe generic example
            default => new BangladeshVatDriver(), // Default fallback
        };
    }
}
