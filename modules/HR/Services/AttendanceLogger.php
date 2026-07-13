<?php

namespace Modules\HR\Services;

use App\Services\Tenant\TenantContextManager;
use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\HR\Models\AttendanceLog;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;

class AttendanceLogger
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function checkIn(string $employeeId, string $shiftId, string $checkInTimeStr): AttendanceLog
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (! $tenantId) {
            throw new InvalidArgumentException('Tenant context required');
        }

        /** @var Employee|null $employee */
        $employee = Employee::where('tenant_id', $tenantId)->find($employeeId);
        if (! $employee) {
            throw new InvalidArgumentException('Employee not found.');
        }

        /** @var Shift|null $shift */
        $shift = Shift::where('tenant_id', $tenantId)->find($shiftId);
        if (! $shift) {
            throw new InvalidArgumentException('Shift not found.');
        }

        $checkInTime = Carbon::parse($checkInTimeStr);
        $date = $checkInTime->toDateString();

        /** @var AttendanceLog|null $existingLog */
        $existingLog = AttendanceLog::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();

        if ($existingLog) {
            throw new InvalidArgumentException('Already checked in today.');
        }

        $shiftStartTime = Carbon::parse($date.' '.$shift->start_time);

        $arrivalOffset = $shiftStartTime->diffInMinutes($checkInTime, false);

        $lateMinutes = 0;
        $status = 'Present';

        if ($arrivalOffset > 0) {
            if ($arrivalOffset > $shift->grace_period_minutes) {
                $status = 'Late';
                $lateMinutes = (int) $arrivalOffset;
            }
        }

        return AttendanceLog::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'shift_id' => $shiftId,
            'date' => $date,
            'check_in' => $checkInTime,
            'late_minutes' => $lateMinutes,
            'status' => $status,
        ]);
    }

    public function checkOut(string $employeeId, string $checkOutTimeStr): AttendanceLog
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (! $tenantId) {
            throw new InvalidArgumentException('Tenant context required');
        }

        $checkOutTime = Carbon::parse($checkOutTimeStr);
        $date = $checkOutTime->toDateString();

        /** @var AttendanceLog|null $log */
        $log = AttendanceLog::where('employee_id', $employeeId)
            ->where('tenant_id', $tenantId)
            ->where('date', $date)
            ->first();

        if (! $log) {
            throw new InvalidArgumentException('No check-in record found for today.');
        }

        if (! $log->check_in) {
            throw new InvalidArgumentException('Check-in time is missing.');
        }

        $checkInTime = Carbon::parse($log->check_in);
        $workedMinutes = (int) $checkInTime->diffInMinutes($checkOutTime);

        $log->update([
            'check_out' => $checkOutTime,
            'worked_minutes' => $workedMinutes,
        ]);

        return $log;
    }
}
