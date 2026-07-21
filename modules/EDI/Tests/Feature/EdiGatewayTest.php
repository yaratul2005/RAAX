<?php

namespace Modules\EDI\Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\EDI\Models\EdiPartner;
use Modules\Sales\Models\Customer;
use Tests\TestCase;

class EdiGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected string $rawApiKey;
    protected EdiPartner $partnerA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();

        $this->rawApiKey = 'test_secret_key_123';

        $this->tenantManager->setTenantId($this->tenantA);

        $this->partnerA = EdiPartner::create([
            'id' => Str::uuid(),
            'tenant_id' => $this->tenantA,
            'name' => 'B2B Partner A',
            'api_key_hash' => hash('sha256', $this->rawApiKey),
            'is_active' => true,
        ]);

        Customer::create([
            'id' => Str::uuid(),
            'tenant_id' => $this->tenantA,
            'name' => 'B2B Partner A Customer',
            'bin' => '123456789',
            'credit_limit_cents' => 1000000,
        ]);

        $this->tenantManager->clearTenantId();
    }

    public function test_edi_auth_middleware_accepts_valid_key(): void
    {
        $payload = [
            'payload' => [
                'customer_bin' => '123456789',
                'po_number' => 'PO-850-001',
                'items' => [
                    ['sku' => 'ITEM-A', 'qty' => 10, 'price_cents' => 500]
                ]
            ]
        ];

        // Ensure we make the request without any session actingAs
        $response = $this->postJson('/api/v1/edi/inbound', $payload, [
            'X-EDI-Partner-Key' => $this->rawApiKey,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sales_orders', [
            'tenant_id' => $this->tenantA,
            'order_number' => 'PO-850-001',
        ]);

        $this->assertDatabaseHas('edi_logs', [
            'edi_partner_id' => $this->partnerA->id,
            'status' => 'success'
        ]);
    }

    public function test_edi_auth_middleware_rejects_invalid_key(): void
    {
        $response = $this->postJson('/api/v1/edi/inbound', [], [
            'X-EDI-Partner-Key' => 'wrong_key',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Invalid or inactive EDI Partner Key.']);
    }

    public function test_tenant_isolation_on_edi_logs(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        // Simulate an inbound request for Tenant A
        $this->postJson('/api/v1/edi/inbound', [
            'payload' => [
                'customer_bin' => '123456789',
                'po_number' => 'PO-850-ISO',
                'items' => [['sku' => 'ITEM-ISO', 'qty' => 1, 'price_cents' => 100]]
            ]
        ], [
            'X-EDI-Partner-Key' => $this->rawApiKey,
        ]);

        // Tenant B tries to see EDI logs
        $this->tenantManager->setTenantId($this->tenantB);
        $logs = \Modules\EDI\Models\EdiLog::all();
        $this->assertCount(0, $logs);
    }
}
