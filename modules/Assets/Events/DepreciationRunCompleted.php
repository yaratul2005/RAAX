<?php

namespace Modules\Assets\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepreciationRunCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $periodMonth;
    public int $totalDepreciationCents;
    public string $tenantId;

    public function __construct(string $periodMonth, int $totalDepreciationCents, string $tenantId)
    {
        $this->periodMonth = $periodMonth;
        $this->totalDepreciationCents = $totalDepreciationCents;
        $this->tenantId = $tenantId;
    }
}
