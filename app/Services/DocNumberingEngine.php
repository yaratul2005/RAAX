<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocNumberingEngine
{
    /**
     * Generate next sequential document number based on configurable format patterns.
     * E.g. pattern: "{PREFIX}-{YYYY}-{00000}"
     */
    public static function generateNextNumber(string $tenantId, string $moduleType, string $prefix = 'DOC'): string
    {
        $year = date('Y');
        $sequenceKey = "seq_{$moduleType}_{$tenantId}_{$year}";
        
        $counter = DB::table('system_sequences')->where('key', $sequenceKey)->value('counter') ?? 0;
        $counter++;

        DB::table('system_sequences')->updateOrInsert(
            ['key' => $sequenceKey],
            ['counter' => $counter, 'tenant_id' => $tenantId, 'updated_at' => now()]
        );

        $padded = str_pad($counter, 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$padded}";
    }
}
