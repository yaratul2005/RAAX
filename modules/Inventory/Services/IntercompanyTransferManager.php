<?php

namespace Modules\Inventory\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Inventory\Contracts\FIFOValuationEngineInterface;
use Modules\Inventory\Events\IntercompanyTransferCompleted;
use Modules\Inventory\Models\IntercompanyTransfer;
use Modules\Inventory\Models\IntercompanyTransferLine;
use Modules\Inventory\Models\Mushak65Challan;

class IntercompanyTransferManager
{
    protected TenantContextManager $tenantManager;
    protected FIFOValuationEngineInterface $fifoEngine;

    public function __construct(TenantContextManager $tenantManager, FIFOValuationEngineInterface $fifoEngine)
    {
        $this->tenantManager = $tenantManager;
        $this->fifoEngine = $fifoEngine;
    }

    public function shipTransfer(string $transferId, string $vehicleNumber, string $driverName): Mushak65Challan
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        return DB::transaction(function () use ($tenantId, $transferId, $vehicleNumber, $driverName) {
            $transfer = IntercompanyTransfer::where('tenant_id', $tenantId)
                ->with('lines')
                ->lockForUpdate()
                ->find($transferId);

            if (!$transfer) {
                throw new InvalidArgumentException("Transfer not found.");
            }

            if ($transfer->status !== 'draft') {
                throw new InvalidArgumentException("Only draft transfers can be shipped.");
            }

            $totalCostCents = 0;

            foreach ($transfer->lines as $line) {
                // Deplete source inventory via FIFO and determine actual cost basis
                $cogs = $this->fifoEngine->calculateStockOutCost(
                    $line->item_sku,
                    $line->qty,
                    "Intercompany Transfer {$transfer->transfer_number} to {$transfer->destination_tenant_id}"
                );

                $unitCost = (int) round($cogs / $line->qty);
                $line->update(['unit_transfer_cost_cents' => $unitCost]);

                $totalCostCents += $cogs;
            }

            $transfer->update([
                'total_cost_cents' => $totalCostCents,
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);

            // Generate NBR Mushak 6.5
            $challan = Mushak65Challan::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'intercompany_transfer_id' => $transfer->id,
                'challan_number' => 'M65-' . $transfer->transfer_number,
                'vehicle_number' => $vehicleNumber,
                'driver_name' => $driverName,
                'declared_at' => now(),
            ]);

            return $challan;
        });
    }

    public function receiveTransfer(string $transferId, string $destinationBinId): void
    {
        $destinationTenantId = $this->tenantManager->getTenantId();

        DB::transaction(function () use ($destinationTenantId, $transferId, $destinationBinId) {
            // Need to retrieve transfer using destination ID
            // Since it's created under source tenant_id, we search by destination_tenant_id
            $transfer = IntercompanyTransfer::where('destination_tenant_id', $destinationTenantId)
                ->with('lines')
                ->lockForUpdate()
                ->find($transferId);

            if (!$transfer) {
                throw new InvalidArgumentException("Transfer not found or you are not the destination.");
            }

            if ($transfer->status !== 'in_transit') {
                throw new InvalidArgumentException("Transfer must be in_transit to be received.");
            }

            foreach ($transfer->lines as $line) {
                // Seed FIFO batches in destination branch using the transferred unit cost
                $this->fifoEngine->addInboundStock(
                    $line->item_sku,
                    $line->qty,
                    $line->unit_transfer_cost_cents,
                    $destinationBinId,
                    "Received Transfer {$transfer->transfer_number} from {$transfer->tenant_id}"
                );
            }

            $transfer->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Trigger async reconciliation
            event(new IntercompanyTransferCompleted($transfer));
        });
    }
}
