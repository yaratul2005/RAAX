<?php

namespace Modules\Assets\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Assets\Events\DepreciationRunCompleted;
use Modules\Assets\Models\DepreciationLog;
use Modules\Assets\Models\FixedAsset;

class DepreciationEngine
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function runMonthlyDepreciation(string $targetMonth): void
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $targetMonth)) {
            throw new InvalidArgumentException("Invalid target month format. Use YYYY-MM.");
        }

        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        DB::transaction(function () use ($tenantId, $targetMonth) {
            // Check if this month has already been run
            $existingRun = DepreciationLog::where('tenant_id', $tenantId)
                ->where('period_month', $targetMonth)
                ->exists();

            if ($existingRun) {
                throw new InvalidArgumentException("Depreciation for {$targetMonth} has already been run.");
            }

            $assets = FixedAsset::where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            $totalDepreciationForPeriod = 0;

            foreach ($assets as $asset) {
                // Get latest log to find current book value
                $latestLog = DepreciationLog::where('fixed_asset_id', $asset->id)
                    ->orderBy('period_month', 'desc')
                    ->first();

                $currentBookValueCents = $latestLog ? $latestLog->book_value_cents : $asset->acquisition_cost_cents;
                $accumulatedDepreciationCents = $latestLog ? $latestLog->accumulated_depreciation_cents : 0;

                if ($currentBookValueCents <= $asset->salvage_value_cents) {
                    $asset->update(['status' => 'fully_depreciated']);
                    continue;
                }

                $depreciationCents = 0;

                if ($asset->depreciation_method === 'straight_line') {
                    $depreciationCents = (int) round(($asset->acquisition_cost_cents - $asset->salvage_value_cents) / $asset->lifespan_months);
                } elseif ($asset->depreciation_method === 'reducing_balance') {
                    // basis is e.g. 1500 for 15.00%. So multiplier is 1500 / 10000 = 0.15
                    $rate = $asset->depreciation_rate_basis_cents / 10000;
                    $depreciationCents = (int) round($currentBookValueCents * $rate);
                }

                // Ensure we don't depreciate below salvage value
                $maxAllowedDepreciation = max(0, $currentBookValueCents - $asset->salvage_value_cents);
                if ($depreciationCents > $maxAllowedDepreciation) {
                    $depreciationCents = $maxAllowedDepreciation;
                }

                if ($depreciationCents <= 0) {
                    continue;
                }

                $newBookValueCents = $currentBookValueCents - $depreciationCents;
                $newAccumulatedCents = $accumulatedDepreciationCents + $depreciationCents;

                DepreciationLog::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'fixed_asset_id' => $asset->id,
                    'period_month' => $targetMonth,
                    'depreciation_amount_cents' => $depreciationCents,
                    'accumulated_depreciation_cents' => $newAccumulatedCents,
                    'book_value_cents' => $newBookValueCents,
                ]);

                if ($newBookValueCents <= $asset->salvage_value_cents) {
                    $asset->update(['status' => 'fully_depreciated']);
                }

                $totalDepreciationForPeriod += $depreciationCents;
            }

            if ($totalDepreciationForPeriod > 0) {
                event(new DepreciationRunCompleted($targetMonth, $totalDepreciationForPeriod, $tenantId));
            }
        });
    }
}
