<?php

namespace Modules\Procurement\Services;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderLine;
use Modules\Procurement\Models\Vendor;
use Modules\Finance\Contracts\BudgetManagerInterface;

class ProcurementManager
{
    protected TenantContextManager $tenantManager;
    protected BudgetManagerInterface $budgetManager;

    public function __construct(TenantContextManager $tenantManager, BudgetManagerInterface $budgetManager)
    {
        $this->tenantManager = $tenantManager;
        $this->budgetManager = $budgetManager;
    }

    /**
     * Create a Purchase Order and its lines in an ACID transaction.
     */
    public function createPurchaseOrder(array $payload): PurchaseOrder
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required");
        }

        // Validate vendor
        $vendor = Vendor::where('tenant_id', $tenantId)->find($payload['vendor_id']);
        if (!$vendor || $vendor->status !== 'active') {
            throw new InvalidArgumentException("Invalid or inactive vendor.");
        }

        return DB::transaction(function () use ($tenantId, $payload) {
            $totalAmountCents = 0;
            $linesData = [];

            // Pre-calculate line totals and overall PO total
            foreach ($payload['lines'] as $line) {
                $qty = (int) $line['qty'];
                $unitPrice = (int) $line['unit_price_cents'];

                if ($qty <= 0 || $unitPrice < 0) {
                    throw new InvalidArgumentException("Invalid quantity or unit price.");
                }

                $lineTotal = $qty * $unitPrice;
                $totalAmountCents += $lineTotal;

                $linesData[] = [
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'item_sku' => $line['item_sku'],
                    'qty' => $qty,
                    'unit_price_cents' => $unitPrice,
                    'total_price_cents' => $lineTotal,
                ];
            }

            $po = PurchaseOrder::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'vendor_id' => $payload['vendor_id'],
                'purchase_request_id' => $payload['purchase_request_id'] ?? null,
                'po_number' => $payload['po_number'],
                'total_amount_cents' => $totalAmountCents,
                'currency_code' => $payload['currency_code'] ?? 'BDT',
                'status' => 'draft',
            ]);

            // Create lines
            foreach ($linesData as $lineData) {
                $lineData['purchase_order_id'] = $po->id;
                PurchaseOrderLine::create($lineData);
            }

            return $po;
        });
    }

    /**
     * Approve a Purchase Order considering multi-tier limits.
     * Level 1: < $5,000 (500000 cents) -> needs 'approve-po-l1'
     * Level 2: >= $5,000 to < $25,000 (2500000 cents) -> needs 'approve-po-l2'
     * Level 3: >= $25,000 -> needs 'approve-po-l3'
     */
    public function approvePurchaseOrder(string $poId, User $user): PurchaseOrder
    {
        $tenantId = $this->tenantManager->getTenantId();

        $po = PurchaseOrder::where('tenant_id', $tenantId)->find($poId);
        if (!$po) {
            throw new InvalidArgumentException("Purchase order not found.");
        }

        if ($po->status !== 'draft') {
            throw new InvalidArgumentException("Only draft POs can be approved.");
        }

        $amount = $po->total_amount_cents;

        // Determine required permission level
        $requiredPermission = '';
        if ($amount < 500000) {
            $requiredPermission = 'approve-po-l1';
        } elseif ($amount < 2500000) {
            $requiredPermission = 'approve-po-l2';
        } else {
            $requiredPermission = 'approve-po-l3';
        }

        // Check if user has the specific required permission (or a higher one? We'll enforce exact or higher)
        // Let's assume having a higher tier implies lower tier access, or we just strictly check.
        // We'll check if user has at least the required permission.
        $hasPermission = false;

        $userPermissions = $user->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->pluck('slug')->unique()->toArray();

        if ($amount < 500000) {
            $hasPermission = count(array_intersect(['approve-po-l1', 'approve-po-l2', 'approve-po-l3'], $userPermissions)) > 0;
        } elseif ($amount < 2500000) {
            $hasPermission = count(array_intersect(['approve-po-l2', 'approve-po-l3'], $userPermissions)) > 0;
        } else {
            $hasPermission = in_array('approve-po-l3', $userPermissions);
        }

        if (!$hasPermission) {
            throw new \Illuminate\Auth\Access\AuthorizationException("User lacks authorization to approve a PO of this amount.");
        }

                // Budget Earmarking
        // Assuming a generic default account mapped for Procurement or taking from PO
        // For MVP, we will assume account "5001" (Expense/Procurement)
        try {
            // Find account ID for code 5001
            $account = \Modules\Finance\Models\LedgerAccount::where('tenant_id', $tenantId)->where('code', '5001')->first();
            if ($account) {
                $this->budgetManager->encumberFunds($account->id, 'purchase_order', $po->id, $po->total_amount_cents);
            }
        } catch (\Modules\Finance\Services\InsufficientBudgetException $e) {
            $po->update(['status' => 'cancelled']); // or 'rejected_due_to_budget' as per prompt, but schema enum is 'cancelled'
            throw new InvalidArgumentException("PO Approval rejected: Insufficient budget available.");
        }

        $po->update(['status' => 'sent_to_vendor']);

        return $po;
    }
}
