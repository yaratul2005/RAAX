<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\HR\Http\Requests\CheckInRequest;
use Modules\HR\Http\Requests\CheckOutRequest;
use Modules\HR\Services\AttendanceLogger;

class AttendanceController extends Controller
{
    protected AttendanceLogger $logger;

    public function __construct(AttendanceLogger $logger)
    {
        $this->logger = $logger;
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        try {
            $log = $this->logger->checkIn(
                (string) $request->input('employee_id'),
                (string) $request->input('shift_id'),
                (string) $request->input('check_in_time')
            );

            return response()->json([
                'success' => true,
                'data' => $log,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        try {
            $log = $this->logger->checkOut(
                (string) $request->input('employee_id'),
                (string) $request->input('check_out_time')
            );

            return response()->json([
                'success' => true,
                'data' => $log,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
