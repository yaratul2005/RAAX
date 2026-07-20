<?php

namespace App\Recorders;

use Illuminate\Queue\Events\JobProcessing;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Carbon\Carbon;

class HorizonLatencyRecorder
{
    use Ignores;

    protected Pulse $pulse;

    public function __construct(Pulse $pulse)
    {
        $this->pulse = $pulse;
    }

    public function record(JobProcessing $event): void
    {
        // Job payload contains 'pushedAt' if it's from Laravel queued jobs
        $payload = $event->job->payload();

        if (isset($payload['pushedAt'])) {
            $pushedAt = Carbon::createFromTimestamp($payload['pushedAt']);
            $processingAt = now();

            $latencyMs = (int) $pushedAt->diffInMilliseconds($processingAt);

            // Record custom metric in Pulse using integer ms
            $this->pulse->record(
                'queue_latency',
                $event->job->getQueue(),
                $latencyMs,
                $processingAt
            )->avg()->max();
        }
    }
}
