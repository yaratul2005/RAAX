<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\HR\Http\Requests\CreateShiftRequest;
use Modules\HR\Models\Shift;
use Modules\HR\Services\ShiftManager;

class ShiftController extends Controller
{
    protected ShiftManager $shiftManager;

    public function __construct(ShiftManager $shiftManager)
    {
        $this->shiftManager = $shiftManager;
    }

    public function store(CreateShiftRequest $request): JsonResponse
    {
        try {
            $shift = $this->shiftManager->createShift($request->validated());

            return response()->json([
                'success' => true,
                'data' => $shift,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function index(): JsonResponse
    {
        $shifts = Shift::all();

        return response()->json([
            'success' => true,
            'data' => $shifts,
        ]);
    }
}
