<?php

namespace Modules\Notifications\Listeners;

use Modules\HR\Events\AttendanceLogged;
use Modules\Notifications\Models\Notification;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Str;

class SendCheckInNotification
{
    public function handle(AttendanceLogged $event): void
    {
        $log = $event->attendanceLog;
        $employee = $log->employee; // Note: Ensure employee relationship is eager loaded or available

        $notification = Notification::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $log->tenant_id,
            'user_id' => null, // Or resolve user ID if needed
            'type' => 'email',
            'recipient' => $employee->email,
            'subject' => 'Check-In Confirmation',
            'body' => "You have successfully checked in at {$log->check_in}. Status: {$log->status}.",
            'status' => 'queued',
        ]);

        SendNotificationJob::dispatch($notification)->onQueue('notifications');
    }
}
