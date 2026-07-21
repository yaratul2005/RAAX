<?php

namespace Modules\Procurement\Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Vendor;
use Tests\TestCase;

class ProcurementEngineTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $userL1;
    protected User $userL2;
    protected User $userL3;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();

        $this->userL1 = User::factory()->create();
        $this->userL2 = User::factory()->create();
        $this->userL3 = User::factory()->create();

        // Setup permissions
        $p1 = Permission::create(['id' => Str::uuid()->toString(), 'name' => 'L1', 'slug' => 'approve-po-l1']);
        $p2 = Permission::create(['id' => Str::uuid()->toString(), 'name' => 'L2', 'slug' => 'approve-po-l2']);
        $p3 = Permission::create(['id' => Str::uuid()->toString(), 'name' => 'L3', 'slug' => 'approve-po-l3']);

        // Setup roles
        $role1 = Role::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'Manager', 'slug' => 'mgr']);
        $role1->permissions()->attach($p1->id, ['tenant_id' => $this->tenantA]);
        $this->userL1->roles()->attach($role1->id, ['tenant_id' => $this->tenantA]);

        $role2 = Role::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'Director', 'slug' => 'dir']);
        $role2->permissions()->attach($p2->id, ['tenant_id' => $this->tenantA]);
        $this->userL2->roles()->attach($role2->id, ['tenant_id' => $this->tenantA]);

        $role3 = Role::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'name' => 'CFO', 'slug' => 'cfo']);
        $role3->permissions()->attach($p3->id, ['tenant_id' => $this->tenantA]);
        $this->userL3->roles()->attach($role3->id, ['tenant_id' => $this->tenantA]);

        $this->tenantManager->setTenantId($this->tenantA);
        $this->vendor = Vendor::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Test Vendor', 'status' => 'active'
        ]);
        $this->tenantManager->clearTenantId();
    }

    public function test_po_creation_calculates_integer_math_correctly(): void
    {
        $payload = [
            'vendor_id' => $this->vendor->id,
            'po_number' => 'PO-001',
            'lines' => [
                ['item_sku' => 'SKU-A', 'qty' => 5, 'unit_price_cents' => 10000], // 5 * 100.00 = 50000 cents
                ['item_sku' => 'SKU-B', 'qty' => 2, 'unit_price_cents' => 25050], // 2 * 250.50 = 50100 cents
            ]
        ];

        $response = $this->actingAs($this->userL1)->postJson('/api/v1/procurement/purchase-orders', $payload, [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('purchase_orders', [
            'tenant_id' => $this->tenantA,
            'po_number' => 'PO-001',
            'total_amount_cents' => 100100, // 50000 + 50100 = 100100 cents
        ]);
    }

    public function test_po_approval_enforces_multi_tier_limits(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // PO < $5000 (499900 cents)
        $poL1 = PurchaseOrder::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'vendor_id' => $this->vendor->id, 'po_number' => 'PO-1', 'total_amount_cents' => 499900]);
        // PO > $5000, < $25000 (1000000 cents)
        $poL2 = PurchaseOrder::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'vendor_id' => $this->vendor->id, 'po_number' => 'PO-2', 'total_amount_cents' => 1000000]);
        // PO > $25000 (3000000 cents)
        $poL3 = PurchaseOrder::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'vendor_id' => $this->vendor->id, 'po_number' => 'PO-3', 'total_amount_cents' => 3000000]);

        $this->tenantManager->clearTenantId();

        // L1 tries to approve L2 -> 403
        $response = $this->actingAs($this->userL1)->postJson("/api/v1/procurement/purchase-orders/{$poL2->id}/approve", [], ['X-Tenant-ID' => $this->tenantA]);
        $response->assertStatus(403);

        // L2 tries to approve L2 -> 200
        $response = $this->actingAs($this->userL2)->postJson("/api/v1/procurement/purchase-orders/{$poL2->id}/approve", [], ['X-Tenant-ID' => $this->tenantA]);
        $response->assertStatus(200);

        // L2 tries to approve L3 -> 403
        $response = $this->actingAs($this->userL2)->postJson("/api/v1/procurement/purchase-orders/{$poL3->id}/approve", [], ['X-Tenant-ID' => $this->tenantA]);
        $response->assertStatus(403);

        // L3 tries to approve L3 -> 200
        $response = $this->actingAs($this->userL3)->postJson("/api/v1/procurement/purchase-orders/{$poL3->id}/approve", [], ['X-Tenant-ID' => $this->tenantA]);
        $response->assertStatus(200);
    }

    public function test_tenant_isolation_for_vendors_and_pos(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        $vendorB = Vendor::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'name' => 'Vendor B', 'status' => 'active']);
        $poB = PurchaseOrder::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'vendor_id' => $vendorB->id, 'po_number' => 'PO-B', 'total_amount_cents' => 1000]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        // Tenant A shouldn't see Vendor B
        $this->assertNull(Vendor::find($vendorB->id));
        // And shouldn't be able to approve Tenant B's PO
        $response = $this->actingAs($this->userL3)->postJson("/api/v1/procurement/purchase-orders/{$poB->id}/approve", [], ['X-Tenant-ID' => $this->tenantA]);
        $response->assertStatus(422); // Not found
    }
}
