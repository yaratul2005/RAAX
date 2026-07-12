<?php

namespace Modules\HR\Services;

use App\Services\Tenant\TenantContextManager;
use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\HR\Models\Shift;

class ShiftManager
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createShift(array $data): Shift
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (! $tenantId) {
            throw new InvalidArgumentException('Tenant context required');
        }

        $this->validateNoOverlap($tenantId, (string) $data['start_time'], (string) $data['end_time']);

        $data['id'] = Str::uuid()->toString();
        $data['tenant_id'] = $tenantId;

        return Shift::create($data);
    }

    protected function validateNoOverlap(string $tenantId, string $startTime, string $endTime): void
    {
        $start = Carbon::parse($startTime)->format('H:i:s');
        $end = Carbon::parse($endTime)->format('H:i:s');

        $overlapping = Shift::where('tenant_id', $tenantId)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                        ->where('end_time', '>', $start)
                        ->whereRaw('start_time < end_time');
                })->orWhere(function ($q) use ($start, $end) {
                    $q->whereRaw('start_time > end_time')
                        ->where(function ($sub) use ($start, $end) {
                            $sub->where('start_time', '<', $end)
                                ->orWhere('end_time', '>', $start);
                        });
                });
            })
            ->exists();

        if ($overlapping) {
            throw new InvalidArgumentException('The shift times overlap with an existing shift.');
        }
    }
}
