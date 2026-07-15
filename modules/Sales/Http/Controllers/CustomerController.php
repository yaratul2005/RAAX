<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sales\Http\Requests\CreateCustomerRequest;
use Modules\Sales\Models\Customer;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $customer = Customer::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'outstanding_balance_cents' => 0,
                'status' => 'active',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $customer
        ], 201);
    }
}
