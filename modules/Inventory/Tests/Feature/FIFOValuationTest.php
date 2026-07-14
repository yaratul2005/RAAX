<?php

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Inventory\Contracts\PurchaseOrderFetcherInterface;
use Modules\Inventory\Models\GoodsReceivedNote;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\WarehouseBin;
use Modules\Inventory\Services\FIFOValuationEngine;
use Modules\Inventory\Services\GoodsReceivedNoteManager;
use Tests\TestCase;

class MockPOFetcher implements PurchaseOrderFetcherInterface
{
    public function fetchPurchaseOrder(string $poId, string $tenantId): ?array
    {
        if ($poId === 'mock-po-id') {
            return [
                'id' => 'mock-po-id',
                'status' => 'sent_to_vendor',
                'currency_code' => 'BDT',
                'lines' => [
                    ['item_sku' => 'SKU-TEST', 'qty' => 100, 'unit_price_cents' => 1000] // 100 qty @ 10 BDT
                ]
            ];
        }
        return null;
    }
}

class FIFOValuationTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected WarehouseBin $bin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        // Swap real PO fetcher with mock
        $this->app->instance(PurchaseOrderFetcherInterface::class, new MockPOFetcher());

        $this->tenantManager->setTenantId($this->tenantA);
        $wh = Warehouse::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Main', 'code' => 'WH1']);
        $this->bin = WarehouseBin::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_id' => $wh->id, 'bin_label' => 'A1']);
        $this->tenantManager->clearTenantId();
    }

    public function test_grn_receipt_verifies_po_tolerance_and_creates_fifo_batch(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $payload = [
            'purchase_order_id' => 'mock-po-id',
            'grn_number' => 'GRN-001',
            'received_by' => $this->user->id,
            'lines' => [
                ['item_sku' => 'SKU-TEST', 'qty' => 105, 'warehouse_bin_id' => $this->bin->id] // 105 is within 10% of 100
            ]
        ];

        $manager = app(GoodsReceivedNoteManager::class);
        $grn = $manager->receiveGoods($payload);

        $this->assertEquals('verified', $grn->status);
        $this->assertDatabaseHas('inventory_batches', [
            'tenant_id' => $this->tenantA,
            'item_sku' => 'SKU-TEST',
            'original_qty' => 105,
            'unit_cost_cents' => 1000,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'in',
            'qty' => 105,
        ]);
    }

    public function test_grn_receipt_rejects_exceeding_tolerance(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $payload = [
            'purchase_order_id' => 'mock-po-id',
            'grn_number' => 'GRN-002',
            'received_by' => $this->user->id,
            'lines' => [
                ['item_sku' => 'SKU-TEST', 'qty' => 115, 'warehouse_bin_id' => $this->bin->id] // 115 > 110 max
            ]
        ];

        $manager = app(GoodsReceivedNoteManager::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('exceeds 10% PO tolerance threshold');

        $manager->receiveGoods($payload);
    }

    public function test_fifo_valuation_consumes_oldest_stock_first(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Batch 1 (Older, cheaper)
        $batch1 = InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'SKU-FIFO', 'original_qty' => 10, 'remaining_qty' => 10, 'unit_cost_cents' => 1000, 'created_at' => now()->subDays(2)]);
        // Batch 2 (Newer, more expensive)
        $batch2 = InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'SKU-FIFO', 'original_qty' => 10, 'remaining_qty' => 10, 'unit_cost_cents' => 1500, 'created_at' => now()->subDay()]);

        $engine = app(FIFOValuationEngine::class);

        // Consume 15 units. Should take 10 from Batch 1, 5 from Batch 2
        // COGS = (10 * 1000) + (5 * 1500) = 10000 + 7500 = 17500 cents
        $cogs = $engine->calculateStockOutCost('SKU-FIFO', 15);

        $this->assertEquals(17500, $cogs);

        $this->assertEquals(0, $batch1->fresh()->remaining_qty);
        $this->assertEquals(5, $batch2->fresh()->remaining_qty);

        // Verify stock movements
        $this->assertDatabaseHas('stock_movements', ['inventory_batch_id' => $batch1->id, 'type' => 'out', 'qty' => 10]);
        $this->assertDatabaseHas('stock_movements', ['inventory_batch_id' => $batch2->id, 'type' => 'out', 'qty' => 5]);
    }

    public function test_fifo_valuation_prevents_negative_stock(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'SKU-NEG', 'original_qty' => 5, 'remaining_qty' => 5, 'unit_cost_cents' => 1000]);

        $engine = app(FIFOValuationEngine::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient stock available');

        $engine->calculateStockOutCost('SKU-NEG', 10);
    }

    public function test_tenant_isolation_on_inventory(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantA);
        InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'SKU-ISO', 'original_qty' => 10, 'remaining_qty' => 10, 'unit_cost_cents' => 1000]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $batchesB = InventoryBatch::all();
        $this->assertCount(0, $batchesB);

        $engine = app(FIFOValuationEngine::class);
        $this->expectException(\InvalidArgumentException::class);
        $engine->calculateStockOutCost('SKU-ISO', 5); // Fails because Tenant B has no stock
    }
}
