<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Finance\Http\Requests\UploadStatementRequest;
use Modules\Finance\Models\BankStatement;
use Modules\Finance\Models\BankStatementLine;
use Modules\Finance\Services\MT940Parser;
use Modules\Finance\Services\BankReconciliationManager;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankReconciliationController extends Controller
{
    protected TenantContextManager $tenantManager;
    protected MT940Parser $parser;
    protected BankReconciliationManager $reconciliationManager;

    public function __construct(TenantContextManager $tenantManager, MT940Parser $parser, BankReconciliationManager $reconciliationManager)
    {
        $this->tenantManager = $tenantManager;
        $this->parser = $parser;
        $this->reconciliationManager = $reconciliationManager;
    }

    public function upload(UploadStatementRequest $request): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        try {
            $parsedData = $this->parser->parseStatement($request->input('mt940_content'));

            $statement = DB::transaction(function () use ($tenantId, $request, $parsedData) {
                $statement = BankStatement::create([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'bank_name' => $request->input('bank_name'),
                    'account_number' => $parsedData['account_number'],
                    'statement_date' => $parsedData['statement_date'],
                    'opening_balance_cents' => $parsedData['opening_balance_cents'],
                    'closing_balance_cents' => $parsedData['closing_balance_cents'],
                    'status' => 'draft',
                ]);

                foreach ($parsedData['transactions'] as $txn) {
                    BankStatementLine::create([
                        'id' => Str::uuid()->toString(),
                        'tenant_id' => $tenantId,
                        'bank_statement_id' => $statement->id,
                        'transaction_date' => $txn['transaction_date'],
                        'reference' => $txn['reference'],
                        'amount_cents' => $txn['amount_cents'],
                    ]);
                }

                return $statement->load('lines');
            });

            return response()->json([
                'success' => true,
                'data' => $statement
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function reconcile(string $statementId): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $statement = BankStatement::where('tenant_id', $tenantId)->find($statementId);
        if (!$statement) {
            return response()->json(['success' => false, 'message' => 'Statement not found.'], 404);
        }

        $this->reconciliationManager->reconcileStatement($statement);

        return response()->json([
            'success' => true,
            'data' => $statement->fresh('lines')
        ]);
    }

    public function unmatched(string $statementId): JsonResponse
    {
        $tenantId = $this->tenantManager->getTenantId();

        $statement = BankStatement::where('tenant_id', $tenantId)->find($statementId);
        if (!$statement) {
            return response()->json(['success' => false, 'message' => 'Statement not found.'], 404);
        }

        $unmatchedLines = $statement->lines()->where('is_reconciled', false)->get();

        return response()->json([
            'success' => true,
            'data' => $unmatchedLines
        ]);
    }
}
