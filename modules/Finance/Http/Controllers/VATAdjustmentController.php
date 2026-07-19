<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Finance\Http\Requests\CreateCreditNoteRequest;
use Modules\Finance\Http\Requests\CreateDebitNoteRequest;
use Modules\Finance\Http\Requests\CreateVdsRequest;
use Modules\Finance\Services\AdjustmentManager;
use Modules\Finance\Services\VdsManager;

class VATAdjustmentController extends Controller
{
    protected VdsManager $vdsManager;
    protected AdjustmentManager $adjustmentManager;

    public function __construct(VdsManager $vdsManager, AdjustmentManager $adjustmentManager)
    {
        $this->vdsManager = $vdsManager;
        $this->adjustmentManager = $adjustmentManager;
    }

    public function issueVds(CreateVdsRequest $request): JsonResponse
    {
        try {
            $vds = $this->vdsManager->issueVdsCertificate(
                $request->input('finance_invoice_id'),
                $request->input('withheld_amount_cents'),
                $request->input('deposit_date')
            );

            return response()->json([
                'success' => true,
                'data' => $vds,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function processCreditNote(CreateCreditNoteRequest $request): JsonResponse
    {
        try {
            $cn = $this->adjustmentManager->applyCreditNote(
                $request->input('sales_order_id'),
                $request->input('returned_amount_cents'),
                $request->input('original_tax_invoice_number')
            );

            return response()->json([
                'success' => true,
                'data' => $cn,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function processDebitNote(CreateDebitNoteRequest $request): JsonResponse
    {
        try {
            $dn = $this->adjustmentManager->applyDebitNote(
                $request->input('purchase_order_id'),
                $request->input('returned_amount_cents'),
                $request->input('original_purchase_invoice_number')
            );

            return response()->json([
                'success' => true,
                'data' => $dn,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
