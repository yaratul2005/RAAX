<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Finance\Http\Requests\CreateAccountRequest;
use Modules\Finance\Models\ChartOfAccounts;
use Modules\Finance\Services\ChartOfAccountsService;

class ChartOfAccountsController extends Controller
{
    protected ChartOfAccountsService $coaService;

    public function __construct(ChartOfAccountsService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function store(CreateAccountRequest $request): JsonResponse
    {
        $account = $this->coaService->createAccount($request->validated());

        return response()->json([
            'success' => true,
            'data' => $account,
        ], 201);
    }

    public function index(): JsonResponse
    {
        // RLS will ensure we only get accounts for the current tenant.
        // We'll load the hierarchy for display
        $accounts = ChartOfAccounts::with('children')->whereNull('parent_id')->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ], 200);
    }
}
