<?php

namespace Modules\HR\Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;
use Modules\HR\Models\Employee;
use Modules\HR\Services\ShiftManager;
use Tests\TestCase;

class EmployeeDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;

    protected string $tenantA;

    protected string $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
    }

    public function test_employee_can_be_registered_and_soft_deleted(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $dept = Department::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'name' => 'IT',
            'code' => 'IT-01',
        ]);

        $desig = Designation::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'title' => 'Software Engineer',
            'grade' => 1,
        ]);

        $employee = Employee::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2024-01-01',
        ]);

        $this->assertDatabaseHas('employees', ['email' => 'john@example.com', 'deleted_at' => null]);

        $employee->delete();

        $this->assertSoftDeleted('employees', ['email' => 'john@example.com']);
    }

    public function test_overlapping_shift_configurations_fail_validation(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $shiftManager = app(ShiftManager::class);

        $shiftManager->createShift([
            'name' => 'Morning Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'grace_period_minutes' => 15,
        ]);

        $this->assertDatabaseHas('shifts', ['name' => 'Morning Shift']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The shift times overlap with an existing shift.');

        $shiftManager->createShift([
            'name' => 'Overlapping Shift',
            'start_time' => '12:00:00',
            'end_time' => '20:00:00',
            'grace_period_minutes' => 15,
        ]);
    }

    public function test_shift_manager_allows_non_overlapping_shifts(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $shiftManager = app(ShiftManager::class);

        $shiftManager->createShift([
            'name' => 'Morning Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'grace_period_minutes' => 15,
        ]);

        $shiftManager->createShift([
            'name' => 'Evening Shift',
            'start_time' => '17:00:00',
            'end_time' => '01:00:00',
            'grace_period_minutes' => 15,
        ]);

        $this->assertDatabaseHas('shifts', ['name' => 'Evening Shift']);
    }

    public function test_tenant_data_isolation_for_employees(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantA);
        $deptA = Department::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'name' => 'HR',
            'code' => 'HR-01',
        ]);
        $desigA = Designation::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'title' => 'Manager',
            'grade' => 5,
        ]);
        Employee::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@tenantA.com',
            'department_id' => $deptA->id,
            'designation_id' => $desigA->id,
            'joining_date' => '2024-01-01',
        ]);

        $this->assertCount(1, Employee::all());
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $this->assertCount(0, Employee::all());

        $deptB = Department::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantB,
            'name' => 'Sales',
            'code' => 'SL-01',
        ]);
        $desigB = Designation::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantB,
            'title' => 'Executive',
            'grade' => 2,
        ]);
        Employee::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantB,
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'email' => 'bob@tenantB.com',
            'department_id' => $deptB->id,
            'designation_id' => $desigB->id,
            'joining_date' => '2024-01-01',
        ]);

        $this->assertCount(1, Employee::all());

        /** @var Employee $firstEmployee */
        $firstEmployee = Employee::first();

        $this->assertEquals('Bob', $firstEmployee->first_name);

        $this->tenantManager->clearTenantId();
    }
}
