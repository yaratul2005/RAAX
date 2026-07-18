<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Inventory\Http\Requests\CreateTransferRequest;
use Modules\Inventory\Models\IntercompanyTransfer;
use Modules\Inventory\Models\IntercompanyTransferLine;
use Modules\Inventory\Services\IntercompanyTransferManager;

class IntercompanyTransferController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected IntercompanyTransferManager $transferManager;

    public function __construct(TenantContextManager $tenantManager, IntercompanyTransferManager $transferManager)
    {
        $this->tenantManager = $tenantManager;
        $this->transferManager = $transferManager;
    }

    public function store(CreateTransferRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();
        $payload = $request->validated();

        $transfer = \Illuminate\Support\Facades\DB::transaction(function () use ($tenantId, $payload) {
            $transfer = IntercompanyTransfer::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'destination_tenant_id' => $payload['destination_tenant_id'],
                'transfer_number' => $payload['transfer_number'],
                'status' => 'draft',
            ]);

            foreach ($payload['lines'] as $line) {
                IntercompanyTransferLine::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'intercompany_transfer_id' => $transfer->id,
                    'item_sku' => $line['item_sku'],
                    'qty' => $line['qty'],
                ]);
            }

            return $transfer->load('lines');
        });

        return response()->json([
            'success' => true,
            'data' => $transfer
        ], 201);
    }

    public function ship(string $transferId, Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_number' => 'required|string',
            'driver_name' => 'required|string',
        ]);

        try {
            $challan = $this->transferManager->shipTransfer(
                $transferId,
                $request->input('vehicle_number'),
                $request->input('driver_name')
            );

            return response()->json([
                'success' => true,
                'data' => $challan,
                'message' => 'Transfer shipped and Mushak 6.5 generated.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function receive(string $transferId, Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_bin_id' => 'required|uuid',
        ]);

        try {
            $this->transferManager->receiveTransfer($transferId, $request->input('warehouse_bin_id'));

            return response()->json([
                'success' => true,
                'message' => 'Transfer received and automated reconciliation triggered.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
