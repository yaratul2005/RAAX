<?php

namespace Modules\HR\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\HR\Events\SalaryPaymentApproved;
use Modules\HR\Models\AttendanceLog;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\PayrollPayslip;
use Modules\HR\Models\Shift;
use Modules\HR\Services\NBRTaxCalculator;
use Tests\TestCase;

class PayrollEngineTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected Department $dept;
    protected Designation $desig;
    protected Shift $shift;
    protected Employee $employeeMale;
    protected Employee $employeeFemale;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        $this->tenantManager->setTenantId($this->tenantA);

        $this->dept = Department::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'IT', 'code' => 'IT-1']);
        $this->desig = Designation::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'title' => 'Dev', 'grade' => 1]);
        $this->shift = Shift::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Morning', 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'grace_period_minutes' => 15]);

        // Male Employee (BDT 50,000 gross/month => 600,000 annual)
        // Tax-free: 375,000. Next 300,000 at 10%. Taxable = 225,000 @ 10% = 22,500. Monthly = 1,875 BDT = 187500 cents
        $this->employeeMale = Employee::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'first_name' => 'John', 'last_name' => 'Doe',
            'email' => 'm@t.com', 'department_id' => $this->dept->id, 'designation_id' => $this->desig->id, 'joining_date' => '2020-01-01', 'gender' => 'male'
        ]);
        EmployeeSalary::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'employee_id' => $this->employeeMale->id,
            'basic_salary_cents' => 3000000, 'house_rent_cents' => 1000000, 'medical_allowance_cents' => 500000, 'transport_allowance_cents' => 500000 // 5,000,000 cents (BDT 50,000)
        ]);

        // Female Employee (BDT 50,000 gross/month => 600,000 annual)
        // Tax-free: 425,000. Taxable = 175,000 @ 10% = 17,500. Monthly = 1,458 BDT (approx) = 145833 cents
        $this->employeeFemale = Employee::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'first_name' => 'Jane', 'last_name' => 'Doe',
            'email' => 'f@t.com', 'department_id' => $this->dept->id, 'designation_id' => $this->desig->id, 'joining_date' => '2020-01-01', 'gender' => 'female'
        ]);
        EmployeeSalary::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'employee_id' => $this->employeeFemale->id,
            'basic_salary_cents' => 3000000, 'house_rent_cents' => 1000000, 'medical_allowance_cents' => 500000, 'transport_allowance_cents' => 500000
        ]);
    }

    public function test_tax_calculator_progressive_slabs(): void
    {
        $calculator = new NBRTaxCalculator();

        // Male: 600,000 BDT = 60,000,000 cents
        $maleTax = $calculator->calculateMonthlyWithholding(60000000, 'male');
        $this->assertEquals(187500, $maleTax); // BDT 1,875

        // Female: 600,000 BDT = 60,000,000 cents
        $femaleTax = $calculator->calculateMonthlyWithholding(60000000, 'female');
        $this->assertEquals(145833, $femaleTax); // approx BDT 1,458.33
    }

    public function test_payroll_generation_calculates_correct_payslips(): void
    {
        // Add some late attendance for male employee
        AttendanceLog::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'employee_id' => $this->employeeMale->id, 'shift_id' => $this->shift->id,
            'date' => '2026-07-10', 'status' => 'Late', 'late_minutes' => 30
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/hr/payroll/generate', ['billing_month' => '2026-07'], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payroll_payslips', [
            'employee_id' => $this->employeeMale->id,
            'billing_month' => '2026-07',
            'gross_salary_cents' => 5000000,
            'late_deductions_cents' => 50000, // 1 late day * BDT 500
            'withholding_tax_cents' => 187500, // calculated from test_tax_calculator
            'net_salary_cents' => 5000000 - 50000 - 187500,
        ]);

        $this->assertDatabaseHas('payroll_payslips', [
            'employee_id' => $this->employeeFemale->id,
            'late_deductions_cents' => 0,
            'withholding_tax_cents' => 145833,
            'net_salary_cents' => 5000000 - 145833,
        ]);
    }

    public function test_paying_payslip_dispatches_event(): void
    {
        Event::fake();

        $payslip = PayrollPayslip::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'employee_id' => $this->employeeMale->id,
            'billing_month' => '2026-07', 'gross_salary_cents' => 5000000, 'net_salary_cents' => 5000000, 'status' => 'approved'
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/hr/payroll/{$payslip->id}/pay", [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payroll_payslips', [
            'id' => $payslip->id,
            'status' => 'paid'
        ]);

        Event::assertDispatched(SalaryPaymentApproved::class, function ($e) use ($payslip) {
            return $e->payslip->id === $payslip->id;
        });
    }

    public function test_tenant_isolation_on_payroll_data(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $payslipA = PayrollPayslip::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'employee_id' => $this->employeeMale->id,
            'billing_month' => '2026-07', 'gross_salary_cents' => 5000, 'net_salary_cents' => 5000
        ]);

        $this->tenantManager->setTenantId($this->tenantB);

        // Tenant B shouldn't see Tenant A's payslip
        $this->assertCount(0, PayrollPayslip::all());

        // And trying to pay it should fail
        $response = $this->actingAs($this->user)->postJson("/api/v1/hr/payroll/{$payslipA->id}/pay", [], [
            'X-Tenant-ID' => $this->tenantB,
        ]);
        $response->assertStatus(422);
    }
}
