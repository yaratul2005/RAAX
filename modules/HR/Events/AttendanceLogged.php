<?php

namespace Modules\HR\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\AttendanceLog;

class AttendanceLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AttendanceLog $attendanceLog;

    public function __construct(AttendanceLog $attendanceLog)
    {
        $this->attendanceLog = $attendanceLog;
    }
}
