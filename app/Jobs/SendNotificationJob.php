<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Notifications\Models\Notification;
use App\Services\Tenant\TenantContextManager;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Notification $notification;
    public string $tenantId;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->tenantId = $notification->tenant_id;
    }

    public function handle(TenantContextManager $tenantManager): void
    {
        $previousTenantId = $tenantManager->getTenantId();
        // Re-establish tenant context for background job
        $tenantManager->setTenantId($this->tenantId);

        // Simulated sending logic
        // In a real app, integrate Mail/SMS providers here

        $this->notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        if ($previousTenantId) {
            $tenantManager->setTenantId($previousTenantId);
        } else {
            $tenantManager->clearTenantId();
        }
    }
}
