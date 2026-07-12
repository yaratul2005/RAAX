<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Modules\HR\Http\Requests\CreateEmployeeRequest;
use Modules\HR\Models\Employee;

class EmployeeController extends Controller
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $data = $request->validated();
        $data['id'] = Str::uuid()->toString();
        $data['tenant_id'] = $tenantId;

        $employee = Employee::create($data);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ], 201);
    }

    public function index(): JsonResponse
    {
        $employees = Employee::all();

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::find($id);
        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $employee->delete();

        return response()->json(['success' => true]);
    }
}
