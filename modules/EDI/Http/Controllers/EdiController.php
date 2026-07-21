<?php

namespace Modules\EDI\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\EDI\Http\Requests\Inbound850Request;
use Modules\EDI\Services\EdiProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdiController extends Controller
{
    protected EdiProcessor $ediProcessor;

    public function __construct(EdiProcessor $ediProcessor)
    {
        $this->ediProcessor = $ediProcessor;
    }

    public function inbound(Inbound850Request $request): JsonResponse
    {
        $partner = $request->attributes->get('edi_partner');

        try {
            $order = $this->ediProcessor->processInbound850($partner, $request->input('payload'));

            return response()->json([
                'success' => true,
                'message' => 'ANSI X12 850 (Purchase Order) parsed and draft sales order created successfully.',
                'data' => [
                    'sales_order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function outbound(string $orderId, Request $request): JsonResponse
    {
        $partner = $request->attributes->get('edi_partner');

        try {
            $payload = $this->ediProcessor->generateOutbound810($partner, $orderId);

            return response()->json([
                'success' => true,
                'data' => $payload
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
