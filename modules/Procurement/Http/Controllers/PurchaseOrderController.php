<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Http\Requests\CreatePurchaseOrderRequest;
use Modules\Procurement\Services\ProcurementManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    protected ProcurementManager $procurementManager;

    public function __construct(ProcurementManager $procurementManager)
    {
        $this->procurementManager = $procurementManager;
    }

    public function store(CreatePurchaseOrderRequest $request): JsonResponse
    {
        try {
            $po = $this->procurementManager->createPurchaseOrder($request->validated());

            return response()->json([
                'success' => true,
                'data' => $po->load('lines')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function approve(string $poId, Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $po = $this->procurementManager->approvePurchaseOrder($poId, $user);

            return response()->json([
                'success' => true,
                'data' => $po
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
