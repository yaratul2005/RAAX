<?php

namespace Modules\HR\Services;

use App\Services\Tenant\TenantContextManager;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\PayrollPayslip;
use Modules\HR\Models\AttendanceLog;
use Modules\HR\Events\SalaryPaymentApproved;
use Illuminate\Support\Str;
use Carbon\Carbon;
use InvalidArgumentException;

class PayrollEngine
{
    protected TenantContextManager $tenantManager;
    protected NBRTaxCalculator $taxCalculator;

    public function __construct(TenantContextManager $tenantManager, NBRTaxCalculator $taxCalculator)
    {
        $this->tenantManager = $tenantManager;
        $this->taxCalculator = $taxCalculator;
    }

    public function generateBatch(string $billingMonth): array
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required");
        }

        // Validate billing_month format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $billingMonth)) {
            throw new InvalidArgumentException("Invalid billing month format. Use YYYY-MM.");
        }

        $startDate = Carbon::createFromFormat('Y-m', $billingMonth)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $salaries = EmployeeSalary::where('tenant_id', $tenantId)->with('employee')->get();
        $payslips = [];

        foreach ($salaries as $salary) {
            $employee = $salary->employee;
            if (!$employee) continue;

            // Skip if already generated
            if (PayrollPayslip::where('employee_id', $employee->id)->where('billing_month', $billingMonth)->exists()) {
                continue;
            }

            // Calculate unpaids and lates from attendance
            // For simplicity in MVP: Assume we just deduct late minutes mapped to a flat fine.
            // Let's say BDT 500 (50000 cents) for every late day recorded.
            $lateDaysCount = AttendanceLog::where('employee_id', $employee->id)
                ->where('status', 'Late')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();

            $lateDeductionsCents = $lateDaysCount * 50000;

            // Calculate Absent days
            $absentDaysCount = AttendanceLog::where('employee_id', $employee->id)
                ->where('status', 'Absent')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();

            // Calculate gross
            $monthlyGrossCents = $salary->gross_salary_cents;
            $annualGrossCents = $monthlyGrossCents * 12;

            // Calculate tax
            $monthlyTaxCents = $this->taxCalculator->calculateMonthlyWithholding($annualGrossCents, $employee->gender ?? 'male');

            // Calculate net
            // Base deduction for absent days: (Gross / Days in month) * absent days
            $dailyRate = (int) round($monthlyGrossCents / $startDate->daysInMonth);
            $unpaidDeductionCents = $dailyRate * $absentDaysCount;

            $netSalaryCents = $monthlyGrossCents - $lateDeductionsCents - $unpaidDeductionCents - $monthlyTaxCents;
            // Prevent negative net salary
            $netSalaryCents = max(0, $netSalaryCents);

            $payslip = PayrollPayslip::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'employee_id' => $employee->id,
                'billing_month' => $billingMonth,
                'gross_salary_cents' => $monthlyGrossCents,
                'unpaid_days' => $absentDaysCount,
                'late_deductions_cents' => $lateDeductionsCents,
                'withholding_tax_cents' => $monthlyTaxCents,
                'net_salary_cents' => $netSalaryCents,
                'status' => 'draft',
            ]);

            $payslips[] = $payslip;
        }

        return $payslips;
    }

    public function processPayment(string $payslipId): PayrollPayslip
    {
        $tenantId = $this->tenantManager->getTenantId();

        $payslip = PayrollPayslip::where('tenant_id', $tenantId)->find($payslipId);
        if (!$payslip) {
            throw new InvalidArgumentException("Payslip not found.");
        }

        if ($payslip->status === 'paid') {
            throw new InvalidArgumentException("Payslip is already paid.");
        }

        $payslip->update(['status' => 'paid']);

        event(new SalaryPaymentApproved($payslip));

        return $payslip;
    }
}
