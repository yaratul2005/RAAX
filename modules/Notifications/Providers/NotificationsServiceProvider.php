<?php

namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\HR\Events\AttendanceLogged;
use Modules\Notifications\Listeners\SendCheckInNotification;

class NotificationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Event::listen(
            AttendanceLogged::class,
            SendCheckInNotification::class
        );
    }
}
