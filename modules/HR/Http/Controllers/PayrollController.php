<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\HR\Http\Requests\CreateSalaryProfileRequest;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Services\PayrollEngine;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    protected PayrollEngine $payrollEngine;
    protected TenantContextManager $tenantManager;

    public function __construct(PayrollEngine $payrollEngine, TenantContextManager $tenantManager)
    {
        $this->payrollEngine = $payrollEngine;
        $this->tenantManager = $tenantManager;
    }

    public function storeSalaryProfile(CreateSalaryProfileRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        // Use updateOrCreate to allow updating an existing profile
        $salary = EmployeeSalary::updateOrCreate(
            ['tenant_id' => $tenantId, 'employee_id' => $request->input('employee_id')],
            array_merge($request->validated(), ['id' => Str::uuid()->toString()])
        );

        return response()->json([
            'success' => true,
            'data' => $salary
        ], 201);
    }

    public function generateBatch(Request $request): JsonResponse
    {
        $billingMonth = $request->input('billing_month'); // YYYY-MM

        try {
            $payslips = $this->payrollEngine->generateBatch($billingMonth);
            return response()->json([
                'success' => true,
                'data' => $payslips
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function pay(string $payslipId): JsonResponse
    {
        try {
            $payslip = $this->payrollEngine->processPayment($payslipId);
            return response()->json([
                'success' => true,
                'data' => $payslip
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
