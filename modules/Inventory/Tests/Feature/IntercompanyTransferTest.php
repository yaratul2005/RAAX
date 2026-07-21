<?php

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Inventory\Events\IntercompanyTransferCompleted;
use Modules\Inventory\Models\IntercompanyTransfer;
use Modules\Inventory\Models\IntercompanyTransferLine;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\WarehouseBin;
use Tests\TestCase;

class IntercompanyTransferTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected WarehouseBin $binA;
    protected WarehouseBin $binB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        // Setup Tenant A stock
        $this->tenantManager->setTenantId($this->tenantA);
        $whA = Warehouse::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'Main', 'code' => 'WH1']);
        $this->binA = WarehouseBin::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'warehouse_id' => $whA->id, 'bin_label' => 'A1']);
        InventoryBatch::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->binA->id, 'item_sku' => 'SKU-TRANS', 'original_qty' => 50, 'remaining_qty' => 50, 'unit_cost_cents' => 1000]); // $10 per unit

        $this->tenantManager->clearTenantId();

        // Setup Tenant B bin
        $this->tenantManager->setTenantId($this->tenantB);
        $whB = Warehouse::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantB, 'name' => 'Branch B', 'code' => 'WH2']);
        $this->binB = WarehouseBin::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantB, 'warehouse_id' => $whB->id, 'bin_label' => 'B1']);

        $this->tenantManager->clearTenantId();
    }

    public function test_shipping_transfer_depletes_fifo_and_generates_mushak(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $transfer = IntercompanyTransfer::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'destination_tenant_id' => $this->tenantB,
            'transfer_number' => 'TRF-001', 'status' => 'draft'
        ]);

        IntercompanyTransferLine::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'intercompany_transfer_id' => $transfer->id,
            'item_sku' => 'SKU-TRANS', 'qty' => 10
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/inventory/transfers/{$transfer->id}/ship", [
            'vehicle_number' => 'DHA-11-2233',
            'driver_name' => 'Rahim'
        ], ['X-Tenant-ID' => $this->tenantA]);

        $response->assertStatus(200);

        $this->assertEquals('in_transit', $transfer->fresh()->status);
        $this->assertEquals(10000, $transfer->fresh()->total_cost_cents); // 10 * 1000

        // FIFO Depleted
        $this->assertEquals(40, InventoryBatch::where('tenant_id', $this->tenantA)->first()->remaining_qty);

        // Mushak 6.5 generated
        $this->assertDatabaseHas('mushak_6_5_challans', [
            'tenant_id' => $this->tenantA,
            'intercompany_transfer_id' => $transfer->id,
            'vehicle_number' => 'DHA-11-2233'
        ]);
    }

    public function test_receiving_transfer_seeds_fifo_and_dispatches_reconciliation_event(): void
    {
        Event::fake([IntercompanyTransferCompleted::class]);

        // 1. Ship from A
        $this->tenantManager->setTenantId($this->tenantA);
        $transfer = IntercompanyTransfer::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'destination_tenant_id' => $this->tenantB,
            'transfer_number' => 'TRF-002', 'status' => 'draft'
        ]);
        IntercompanyTransferLine::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'intercompany_transfer_id' => $transfer->id,
            'item_sku' => 'SKU-TRANS', 'qty' => 5
        ]);
        $this->actingAs($this->user)->postJson("/api/v1/inventory/transfers/{$transfer->id}/ship", [
            'vehicle_number' => 'A', 'driver_name' => 'B'
        ], ['X-Tenant-ID' => $this->tenantA]);
        $this->tenantManager->clearTenantId();

        // 2. Receive at B
        $this->tenantManager->setTenantId($this->tenantB);
        $response = $this->actingAs($this->user)->postJson("/api/v1/inventory/transfers/{$transfer->id}/receive", [
            'warehouse_bin_id' => $this->binB->id
        ], ['X-Tenant-ID' => $this->tenantB]);

        $response->assertStatus(200);

        // Assert B has the stock seeded
        $this->assertDatabaseHas('inventory_batches', [
            'tenant_id' => $this->tenantB,
            'warehouse_bin_id' => $this->binB->id,
            'item_sku' => 'SKU-TRANS',
            'original_qty' => 5,
            'unit_cost_cents' => 1000
        ]);

        // Assert event was fired
        Event::assertDispatched(IntercompanyTransferCompleted::class, function ($e) use ($transfer) {
            return $e->transfer->id === $transfer->id;
        });
    }

    public function test_tenant_isolation_on_transfers(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        // Tenant C
        $tenantC = Str::uuid()->toString();

        $this->tenantManager->setTenantId($this->tenantA);
        $transfer = IntercompanyTransfer::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'destination_tenant_id' => $this->tenantB,
            'transfer_number' => 'TRF-003', 'status' => 'in_transit'
        ]);
        $this->tenantManager->clearTenantId();

        // Tenant C tries to receive it
        $this->tenantManager->setTenantId($tenantC);

        $response = $this->actingAs($this->user)->postJson("/api/v1/inventory/transfers/{$transfer->id}/receive", [
            'warehouse_bin_id' => Str::uuid()->toString()
        ], ['X-Tenant-ID' => $tenantC]);

        // Fails because Tenant C cannot see the transfer where destination_tenant_id is Tenant B
        $response->assertStatus(422);
    }
}
