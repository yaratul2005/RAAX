<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\Mushak91Return;
use Modules\Finance\Models\TreasuryDeposit;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Sales\Models\Mushak63Invoice;
use Tests\TestCase;

class VATComplianceTest extends TestCase
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

        // Sales (Output VAT)
        // Subtotal = 10,000, VAT = 1,500
        Mushak63Invoice::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'challan_number' => 'CH-1',
            'issue_date' => '2024-05-15', 'subtotal_cents' => 1000000, 'vat_cents' => 150000, 'total_payable_cents' => 1150000,
        ]);

        // Purchases (Input VAT)
        // Subtotal = 5,000, VAT = 750
        PurchaseOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'vendor_id' => Str::uuid()->toString(),
            'po_number' => 'PO-1', 'total_amount_cents' => 500000, 'status' => 'completed', 'updated_at' => '2024-05-20 10:00:00'
        ]);

        $this->tenantManager->clearTenantId();
    }

    public function test_compile_monthly_return_aggregates_taxes_correctly(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/finance/vat/returns/2024-05', [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        // Net Payable = 150,000 - 75,000 = 75,000 cents
        $data = $response->json('data');
        $this->assertEquals(150000, $data['total_output_tax_cents']);
        $this->assertEquals(75000, $data['total_input_tax_cents']);
        $this->assertEquals(75000, $data['net_tax_payable_cents']);
        $this->assertEquals('draft', $data['status']);
    }

    public function test_submit_return_verifies_deposit_and_clears_liability(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Create deposit matching exact payable
        $deposit = TreasuryDeposit::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'challan_number' => 'TR6-1',
            'deposit_date' => '2024-06-10', 'bank_branch' => 'BB', 'code_of_analysis' => 'COA-1',
            'amount_cents' => 75000, 'status' => 'cleared'
        ]);

        $this->tenantManager->clearTenantId();

        $response = $this->actingAs($this->user)->postJson("/api/v1/finance/vat/returns/2024-05/submit", [
            'treasury_deposit_id' => $deposit->id
        ], ['X-Tenant-ID' => $this->tenantA]);

        $response->assertStatus(200);

        // Assert return submitted
        $this->assertDatabaseHas('mushak_9_1_returns', [
            'tenant_id' => $this->tenantA,
            'tax_period' => '2024-05',
            'status' => 'submitted',
            'treasury_deposit_id' => $deposit->id
        ]);

        // Assert journal entry created
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'reference' => 'TR6-TR6-1'
        ]);
    }

    public function test_submit_return_fails_if_deposit_is_insufficient(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $deposit = TreasuryDeposit::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'challan_number' => 'TR6-2',
            'deposit_date' => '2024-06-10', 'bank_branch' => 'BB', 'code_of_analysis' => 'COA-1',
            'amount_cents' => 50000, // Insufficient (needs 75000)
            'status' => 'cleared'
        ]);

        $this->tenantManager->clearTenantId();

        $response = $this->actingAs($this->user)->postJson("/api/v1/finance/vat/returns/2024-05/submit", [
            'treasury_deposit_id' => $deposit->id
        ], ['X-Tenant-ID' => $this->tenantA]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Treasury deposit amount does not cover the net VAT payable.']);
    }

    public function test_tenant_isolation_on_compliance_records(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        Mushak91Return::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'tax_period' => '2024-05',
            'total_sales_value_cents' => 0, 'total_output_tax_cents' => 0,
            'total_purchases_value_cents' => 0, 'total_input_tax_cents' => 0,
            'net_tax_payable_cents' => 0, 'status' => 'draft'
        ]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        $returns = Mushak91Return::all();
        $this->assertCount(0, $returns);
    }
}
