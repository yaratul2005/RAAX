<?php

namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\HR\Events\AttendanceLogged;
use Modules\Notifications\Listeners\SendCheckInNotification;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            AttendanceLogged::class,
            SendCheckInNotification::class
        );
    }
}
