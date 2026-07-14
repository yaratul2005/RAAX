<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Http\Requests\CreateVendorRequest;
use Modules\Procurement\Models\Vendor;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateVendorRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $vendor = Vendor::create(array_merge(
            $request->validated(),
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'status' => 'active',
            ]
        ));

        return response()->json([
            'success' => true,
            'data' => $vendor
        ], 201);
    }
}
