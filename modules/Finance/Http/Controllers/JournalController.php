<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Finance\Http\Requests\PostJournalRequest;
use Modules\Finance\Services\PostingEngine;

class JournalController extends Controller
{
    protected PostingEngine $postingEngine;

    public function __construct(PostingEngine $postingEngine)
    {
        $this->postingEngine = $postingEngine;
    }

    public function post(PostJournalRequest $request): JsonResponse
    {
        try {
            $journalEntry = $this->postingEngine->post($request->validated());

            return response()->json([
                'success' => true,
                'data' => $journalEntry->load('lines'),
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
