<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Manufacturing\Http\Requests\CreateBOMRequest;
use Modules\Manufacturing\Models\BillOfMaterials;
use Modules\Manufacturing\Models\BOMItem;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BOMController extends Controller
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateBOMRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $bom = DB::transaction(function () use ($request, $tenantId) {
            $user = Auth::user();
            $payload = $request->validated();

            $bom = BillOfMaterials::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'finished_item_sku' => $payload['finished_item_sku'],
                'name' => $payload['name'],
                'created_by' => $user?->id,
            ]);

            foreach ($payload['items'] as $item) {
                BOMItem::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'bill_of_materials_id' => $bom->id,
                    'raw_item_sku' => $item['raw_item_sku'],
                    'qty_required' => $item['qty_required'],
                    'wastage_allowance_percentage_cents' => $item['wastage_allowance_percentage_cents'] ?? 0,
                ]);
            }

            return $bom->load('items');
        });

        return response()->json([
            'success' => true,
            'data' => $bom
        ], 201);
    }
}
