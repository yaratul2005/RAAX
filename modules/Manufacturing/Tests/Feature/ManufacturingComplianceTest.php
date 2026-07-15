<?php

namespace Modules\Manufacturing\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Inventory\Models\InventoryBatch;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\WarehouseBin;
use Modules\Manufacturing\Models\BillOfMaterials;
use Modules\Manufacturing\Models\BOMItem;
use Modules\Manufacturing\Models\ProductionWorkOrder;
use Tests\TestCase;

class ManufacturingComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected WarehouseBin $bin;
    protected BillOfMaterials $bom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        $this->tenantManager->setTenantId($this->tenantA);

        $wh = Warehouse::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Main', 'code' => 'WH1']);
        $this->bin = WarehouseBin::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_id' => $wh->id, 'bin_label' => 'A1']);

        // Stock up raw materials
        InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'RAW-1', 'original_qty' => 100, 'remaining_qty' => 100, 'unit_cost_cents' => 500]); // 5.00 BDT
        InventoryBatch::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'warehouse_bin_id' => $this->bin->id, 'item_sku' => 'RAW-2', 'original_qty' => 100, 'remaining_qty' => 100, 'unit_cost_cents' => 1000]); // 10.00 BDT

        // Create BOM
        $this->bom = BillOfMaterials::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'finished_item_sku' => 'FIN-1', 'name' => 'Widget']);
        BOMItem::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'bill_of_materials_id' => $this->bom->id, 'raw_item_sku' => 'RAW-1', 'qty_required' => 2, 'wastage_allowance_percentage_cents' => 500]); // 2 units, 5% wastage
        BOMItem::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'bill_of_materials_id' => $this->bom->id, 'raw_item_sku' => 'RAW-2', 'qty_required' => 1, 'wastage_allowance_percentage_cents' => 0]); // 1 unit, 0% wastage

        $this->tenantManager->clearTenantId();
    }

    public function test_work_order_completion_depletes_raw_fifo_and_generates_finished_stock(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $wo = ProductionWorkOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'bill_of_materials_id' => $this->bom->id,
            'work_order_number' => 'WO-1', 'qty_to_produce' => 10, 'total_overhead_cost_cents' => 2000 // 20.00 BDT overhead
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/manufacturing/work-orders/{$wo->id}/complete", [
            'warehouse_bin_id' => $this->bin->id
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        // Verification of Raw Material Depletion
        // RAW-1: req 2 * 10 = 20. + 5% waste (1) = 21. COGS = 21 * 500 = 10500 cents
        // RAW-2: req 1 * 10 = 10. + 0% waste (0) = 10. COGS = 10 * 1000 = 10000 cents
        // Total RM COGS = 20500 cents.

        $this->assertEquals(100 - 21, InventoryBatch::where('item_sku', 'RAW-1')->first()->remaining_qty);
        $this->assertEquals(100 - 10, InventoryBatch::where('item_sku', 'RAW-2')->first()->remaining_qty);

        // Finished Goods Valuation
        // Total Production Cost = 20500 + 2000 overhead = 22500 cents.
        // Qty = 10. Unit Cost = 2250 cents.
        $finishedBatch = InventoryBatch::where('item_sku', 'FIN-1')->first();
        $this->assertNotNull($finishedBatch);
        $this->assertEquals(10, $finishedBatch->remaining_qty);
        $this->assertEquals(2250, $finishedBatch->unit_cost_cents);
    }

    public function test_mushak_4_3_generation_calculates_correct_coefficients(): void
    {
        $response = $this->actingAs($this->user)->postJson("/api/v1/manufacturing/mushak-4-3", [
            'bill_of_materials_id' => $this->bom->id,
            'overhead_cents' => 200, // per unit
            'profit_cents' => 500, // per unit
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        // Cost Base Calculation for 1 unit:
        // RAW-1: 2 * 1.05 = 2.1 -> ceil(2.1) = 3. 3 * 500 = 1500
        // RAW-2: 1 * 1.00 = 1.0 -> ceil(1.0) = 1. 1 * 1000 = 1000
        // Total Cost Base = 2500
        // Declared Sale Price = 2500 + 200 + 500 = 3200

        $this->assertDatabaseHas('mushak_4_3_declarations', [
            'tenant_id' => $this->tenantA,
            'bill_of_materials_id' => $this->bom->id,
            'declared_cost_base_cents' => 2500,
            'declared_overhead_cents' => 200,
            'declared_profit_cents' => 500,
            'declared_sale_price_cents' => 3200,
        ]);
    }

    public function test_tenant_isolation_for_bom(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        $bomB = BillOfMaterials::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'finished_item_sku' => 'FIN-B', 'name' => 'Widget B']);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        $boms = BillOfMaterials::all();
        $this->assertCount(1, $boms);
        $this->assertEquals('FIN-1', $boms->first()->finished_item_sku);
    }
}
