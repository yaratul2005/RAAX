<?php

namespace Modules\HR\Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\HR\Models\AttendanceLog;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;
use Modules\HR\Services\AttendanceLogger;
use Tests\TestCase;

class AttendanceLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;

    protected AttendanceLogger $logger;

    protected string $tenantA;

    protected string $tenantB;

    protected Employee $employeeA;

    protected Employee $employeeB;

    protected Shift $shiftA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->logger = app(AttendanceLogger::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();

        // Setup Tenant A
        $this->tenantManager->setTenantId($this->tenantA);
        $dept = Department::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'IT', 'code' => 'IT-1']);
        $desig = Designation::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'title' => 'Dev', 'grade' => 1]);
        $this->employeeA = Employee::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'first_name' => 'John', 'last_name' => 'Doe',
            'email' => 'a@t.com', 'department_id' => $dept->id, 'designation_id' => $desig->id, 'joining_date' => '2020-01-01',
        ]);
        $this->shiftA = Shift::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'Morning',
            'start_time' => '09:00:00', 'end_time' => '17:00:00', 'grace_period_minutes' => 15,
        ]);
        $this->tenantManager->clearTenantId();

        // Setup Tenant B
        $this->tenantManager->setTenantId($this->tenantB);
        $deptB = Department::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantB, 'name' => 'IT', 'code' => 'IT-1']);
        $desigB = Designation::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantB, 'title' => 'Dev', 'grade' => 1]);
        $this->employeeB = Employee::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantB, 'first_name' => 'Bob', 'last_name' => 'Smith',
            'email' => 'b@t.com', 'department_id' => $deptB->id, 'designation_id' => $desigB->id, 'joining_date' => '2020-01-01',
        ]);
        $this->tenantManager->clearTenantId();
    }

    public function test_standard_check_in_within_grace_period_is_present(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $log = $this->logger->checkIn($this->employeeA->id, $this->shiftA->id, '2024-01-01 09:10:00');

        $this->assertEquals('Present', $log->status);
        $this->assertEquals(0, $log->late_minutes);
    }

    public function test_check_in_past_grace_period_is_late(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $log = $this->logger->checkIn($this->employeeA->id, $this->shiftA->id, '2024-01-01 09:20:00');

        $this->assertEquals('Late', $log->status);
        $this->assertEquals(20, $log->late_minutes);
    }

    public function test_check_out_calculates_worked_minutes(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $this->logger->checkIn($this->employeeA->id, $this->shiftA->id, '2024-01-01 09:00:00');
        $log = $this->logger->checkOut($this->employeeA->id, '2024-01-01 17:00:00');

        $this->assertEquals(480, $log->worked_minutes);
    }

    public function test_tenant_data_isolation_for_attendance_logs(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantA);
        $this->logger->checkIn($this->employeeA->id, $this->shiftA->id, '2024-01-01 09:00:00');
        $this->assertCount(1, AttendanceLog::all());
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $this->assertCount(0, AttendanceLog::all());
    }
}
