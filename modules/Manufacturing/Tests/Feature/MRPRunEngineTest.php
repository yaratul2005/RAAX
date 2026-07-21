<?php

namespace Modules\Manufacturing\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Manufacturing\Models\BillOfMaterials;
use Modules\Manufacturing\Models\BOMItem;
use Modules\Manufacturing\Models\MrpPlannedOrder;
use Modules\Manufacturing\Models\MrpRun;
use Modules\Procurement\Models\PurchaseRequest;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;
use Tests\TestCase;

class MRPRunEngineTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        $this->tenantManager->setTenantId($this->tenantA);

        // Create BOM: FIN-A requires 2x RAW-1 (0% waste) and 1x RAW-2 (10% waste)
        $bom = BillOfMaterials::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'finished_item_sku' => 'FIN-A', 'name' => 'Product A']);
        BOMItem::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'bill_of_materials_id' => $bom->id, 'raw_item_sku' => 'RAW-1', 'qty_required' => 2, 'wastage_allowance_percentage_cents' => 0]);
        BOMItem::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'bill_of_materials_id' => $bom->id, 'raw_item_sku' => 'RAW-2', 'qty_required' => 1, 'wastage_allowance_percentage_cents' => 1000]); // 10%

        // Create Sales Order for 10 units of FIN-A (confirmed)
        $customer = Customer::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Cust A', 'credit_limit_cents' => 10000000]);
        $order = SalesOrder::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'customer_id' => $customer->id, 'order_number' => 'SO-1', 'status' => 'confirmed']);
        SalesOrderLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'sales_order_id' => $order->id, 'item_sku' => 'FIN-A', 'qty' => 10, 'unit_price_cents' => 100, 'total_cents' => 1000]);

        $this->tenantManager->clearTenantId();
    }

    public function test_mrp_run_engine_calculates_net_requirements(): void
    {
        // Demand for FIN-A = 10 units
        // RAW-1 Gross Demand = 10 * 2 = 20. Waste 0. Total Gross = 20.
        // Safety Stock = 100. On Hand = 0. In Transit = 0.
        // Net = max(0, 20 + 100 - 0 - 0) = 120

        // RAW-2 Gross Demand = 10 * 1 = 10. Waste 10%. Total Gross = ceil(10 * 1.1) = 11.
        // Safety Stock = 100. On Hand = 0. In Transit = 0.
        // Net = max(0, 11 + 100 - 0 - 0) = 111

        $response = $this->actingAs($this->user)->postJson('/api/v1/manufacturing/mrp/run', [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);
        $runId = $response->json('data.id');

        $this->assertDatabaseHas('mrp_planned_orders', [
            'mrp_run_id' => $runId,
            'item_sku' => 'RAW-1',
            'gross_requirement_qty' => 20,
            'net_requirement_qty' => 120, // 20 + 100 safety
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('mrp_planned_orders', [
            'mrp_run_id' => $runId,
            'item_sku' => 'RAW-2',
            'gross_requirement_qty' => 11,
            'net_requirement_qty' => 111, // 11 + 100 safety
            'status' => 'pending'
        ]);
    }

    public function test_planned_order_release_creates_purchase_requests(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $run = MrpRun::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'run_number' => 'MRP-1', 'status' => 'completed']);
        $order = MrpPlannedOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'mrp_run_id' => $run->id, 'item_sku' => 'RAW-3',
            'gross_requirement_qty' => 10, 'net_requirement_qty' => 50, 'safety_stock_threshold' => 10,
            'lead_time_days' => 10, 'order_recommendation_type' => 'purchase_requisition',
            'planned_order_date' => now(), 'planned_delivery_date' => now(), 'status' => 'pending'
        ]);

        $this->tenantManager->clearTenantId();

        $response = $this->actingAs($this->user)->postJson("/api/v1/manufacturing/mrp/runs/{$run->id}/release", [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        // Assert order is released
        $this->assertEquals('released', $order->fresh()->status);

        // Assert PR is created
        $this->assertDatabaseHas('purchase_requests', [
            'tenant_id' => $this->tenantA,
            'status' => 'submitted' // Handled by the release manager mock logic
        ]);
    }

    public function test_tenant_isolation_on_mrp_runs(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        $runB = MrpRun::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'run_number' => 'MRP-B', 'status' => 'processing']);
        $this->tenantManager->clearTenantId();

        // Tenant A tries to see Tenant B's planned orders
        $response = $this->actingAs($this->user)->getJson("/api/v1/manufacturing/mrp/runs/{$runB->id}/orders", [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(404);
    }
}
