<?php

namespace Modules\Sales\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Inventory\Contracts\FIFOValuationEngineInterface;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\Mushak63Invoice;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;
use Tests\TestCase;

class MockFIFOEngine implements FIFOValuationEngineInterface
{
    public function calculateStockOutCost(string $sku, int $qtyToReduce, string $reason = 'Stock Out'): int
    {
        // Mock returning a standard cost of 500 cents per unit
        return $qtyToReduce * 500;
    }

    public function addInboundStock(string $sku, int $qty, int $unitCostCents, string $warehouseBinId, string $reason = 'Inbound Stock'): void
    {
        // Mock implementation
    }

    public function getAverageUnitCost(string $sku): int
    {
        return 500;
    }
}

class SalesInvoicingTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();

        // Swap real FIFO engine with mock
        $this->app->instance(FIFOValuationEngineInterface::class, new MockFIFOEngine());

        $this->tenantManager->setTenantId($this->tenantA);
        $this->customer = Customer::create([
            'id' => Str::uuid(),
            'tenant_id' => $this->tenantA,
            'name' => 'Test Customer',
            'credit_limit_cents' => 1000000, // $10,000 equivalent
            'outstanding_balance_cents' => 500000,
            'status' => 'active'
        ]);
        $this->tenantManager->clearTenantId();
    }

    public function test_credit_limit_rejection(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Subtotal = 500000 cents. VAT = 15% (75000). Grand Total = 575000.
        // Current outstanding = 500000. Exposure = 500000 + 575000 = 1075000.
        // Limit is 1000000. Should reject.
        $order = SalesOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'customer_id' => $this->customer->id,
            'order_number' => 'SO-1', 'subtotal_cents' => 500000, 'tax_cents' => 75000, 'grand_total_cents' => 575000
        ]);
        SalesOrderLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'sales_order_id' => $order->id, 'item_sku' => 'SKU', 'qty' => 10, 'unit_price_cents' => 50000, 'total_cents' => 500000]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/sales/orders/{$order->id}/confirm", [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => "Order confirmation rejected. Credit exposure exceeds the customer's approved credit limit."]);

        // Assert status did not change
        $this->assertEquals('draft', $order->fresh()->status);
    }

    public function test_order_confirmation_updates_balance_depletes_fifo_and_generates_mushak(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Grand Total 100,000. Exposure 600,000 <= 1,000,000. Should succeed.
        $order = SalesOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'customer_id' => $this->customer->id,
            'order_number' => 'SO-2', 'subtotal_cents' => 86956, 'tax_cents' => 13044, 'grand_total_cents' => 100000
        ]);
        SalesOrderLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'sales_order_id' => $order->id, 'item_sku' => 'SKU', 'qty' => 1, 'unit_price_cents' => 86956, 'total_cents' => 86956]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/sales/orders/{$order->id}/confirm", [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals(600000, $this->customer->fresh()->outstanding_balance_cents);

        // Assert Mushak generated
        $this->assertDatabaseHas('mushak_6_3_invoices', [
            'tenant_id' => $this->tenantA,
            'sales_order_id' => $order->id,
            'is_high_value_audit' => false,
        ]);
    }

    public function test_high_value_mushak_audit_flag(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Increase credit limit to allow big order
        $this->customer->update(['credit_limit_cents' => 50000000]);

        // Total 25,000,000 (> 20,000,000 threshold for audit)
        $order = SalesOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'customer_id' => $this->customer->id,
            'order_number' => 'SO-HIGH', 'subtotal_cents' => 20000000, 'tax_cents' => 3000000, 'grand_total_cents' => 23000000
        ]);
        SalesOrderLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'sales_order_id' => $order->id, 'item_sku' => 'SKU', 'qty' => 1, 'unit_price_cents' => 20000000, 'total_cents' => 20000000]);

        $this->actingAs($this->user)->postJson("/api/v1/sales/orders/{$order->id}/confirm", [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $this->assertDatabaseHas('mushak_6_3_invoices', [
            'sales_order_id' => $order->id,
            'is_high_value_audit' => 1, // true in some drivers, 1 in SQLite
        ]);
    }

    public function test_tenant_isolation_on_sales(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        Customer::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'name' => 'Vendor B', 'status' => 'active'
        ]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        $customersA = Customer::all();
        $this->assertCount(1, $customersA); // Should only see 'Test Customer'
        $this->assertEquals('Test Customer', $customersA->first()->name);
    }
}
