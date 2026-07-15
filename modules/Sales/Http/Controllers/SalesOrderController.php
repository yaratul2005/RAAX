<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sales\Http\Requests\CreateOrderRequest;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;
use Modules\Sales\Services\SalesOrderManager;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesOrderController extends Controller
{
    protected SalesOrderManager $salesOrderManager;
    protected TenantContextManager $tenantManager;

    public function __construct(SalesOrderManager $salesOrderManager, TenantContextManager $tenantManager)
    {
        $this->salesOrderManager = $salesOrderManager;
        $this->tenantManager = $tenantManager;
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();
        $payload = $request->validated();

        $order = DB::transaction(function () use ($tenantId, $payload) {
            $subtotalCents = 0;
            $linesData = [];

            foreach ($payload['lines'] as $line) {
                $qty = (int) $line['qty'];
                $unitPrice = (int) $line['unit_price_cents'];
                $lineTotal = $qty * $unitPrice;
                $subtotalCents += $lineTotal;

                $linesData[] = [
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'item_sku' => $line['item_sku'],
                    'qty' => $qty,
                    'unit_price_cents' => $unitPrice,
                    'total_cents' => $lineTotal,
                ];
            }

            // VAT 15% is standard as per prompt formulas
            $vatCents = (int) round($subtotalCents * 0.15);
            $grandTotalCents = $subtotalCents + $vatCents;

            $order = SalesOrder::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'customer_id' => $payload['customer_id'],
                'order_number' => $payload['order_number'],
                'subtotal_cents' => $subtotalCents,
                'tax_cents' => $vatCents,
                'grand_total_cents' => $grandTotalCents,
                'status' => 'draft',
            ]);

            foreach ($linesData as $lineData) {
                $lineData['sales_order_id'] = $order->id;
                SalesOrderLine::create($lineData);
            }

            return $order->load('lines');
        });

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }

    public function confirm(string $orderId): JsonResponse
    {
        try {
            $this->salesOrderManager->confirmOrder($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed, stock depleted, and Mushak generated.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
