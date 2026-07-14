<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Http\Requests\CreateGRNRequest;
use Modules\Inventory\Services\GoodsReceivedNoteManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GoodsReceivedNoteController extends Controller
{
    protected GoodsReceivedNoteManager $grnManager;

    public function __construct(GoodsReceivedNoteManager $grnManager)
    {
        $this->grnManager = $grnManager;
    }

    public function store(CreateGRNRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $payload = array_merge($request->validated(), ['received_by' => $user->id]);
            $grn = $this->grnManager->receiveGoods($payload);

            return response()->json([
                'success' => true,
                'data' => $grn
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
