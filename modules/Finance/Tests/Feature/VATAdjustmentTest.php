<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\CreditNote;
use Modules\Finance\Models\DebitNote;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Models\VdsCertificate;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\SalesOrder;
use Tests\TestCase;

class VATAdjustmentTest extends TestCase
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
    }

    public function test_vds_certificate_issuance_and_ledger_adjustment(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $invoice = FinanceInvoice::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AP',
            'invoice_number' => 'INV-001', 'party_id' => Str::uuid(),
            'issue_date' => '2024-01-01', 'due_date' => '2024-01-31',
            'amount_cents' => 115000, 'paid_cents' => 0, 'status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/vat/vds', [
            'finance_invoice_id' => $invoice->id,
            'withheld_amount_cents' => 15000,
            'deposit_date' => '2024-01-15'
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('vds_certificates', [
            'finance_invoice_id' => $invoice->id,
            'withheld_amount_cents' => 15000,
            'status' => 'issued'
        ]);

        // Assert invoice paid_cents updated
        $this->assertEquals(15000, $invoice->fresh()->paid_cents);

        // Assert journal entry created
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'description' => "VDS Certificate issued for Invoice INV-001"
        ]);
    }

    public function test_credit_note_reverses_output_tax_and_updates_customer_balance(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $customer = Customer::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Cust 1', 'credit_limit_cents' => 500000, 'outstanding_balance_cents' => 115000
        ]);

        $order = SalesOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'customer_id' => $customer->id,
            'order_number' => 'SO-1', 'subtotal_cents' => 100000, 'tax_cents' => 15000, 'grand_total_cents' => 115000
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/vat/credit-notes', [
            'sales_order_id' => $order->id,
            'original_tax_invoice_number' => 'INV-1',
            'returned_amount_cents' => 10000 // Subtotal return
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('credit_notes', [
            'sales_order_id' => $order->id,
            'returned_amount_cents' => 10000,
            'adjusted_vat_cents' => 1500, // 15%
        ]);

        $this->assertEquals(115000 - 11500, $customer->fresh()->outstanding_balance_cents);
    }

    public function test_debit_note_reverses_input_tax(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $order = PurchaseOrder::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'vendor_id' => Str::uuid(),
            'po_number' => 'PO-1', 'total_amount_cents' => 115000
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/vat/debit-notes', [
            'purchase_order_id' => $order->id,
            'original_purchase_invoice_number' => 'PINV-1',
            'returned_amount_cents' => 10000
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('debit_notes', [
            'purchase_order_id' => $order->id,
            'returned_amount_cents' => 10000,
            'adjusted_vat_cents' => 1500, // 15%
        ]);
    }

    public function test_tenant_isolation_on_adjustments(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        $invoiceB = FinanceInvoice::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'type' => 'AP',
            'invoice_number' => 'INV-B', 'party_id' => Str::uuid(),
            'issue_date' => '2024-01-01', 'due_date' => '2024-01-31',
            'amount_cents' => 115000
        ]);
        $this->tenantManager->clearTenantId();

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/vat/vds', [
            'finance_invoice_id' => $invoiceB->id,
            'withheld_amount_cents' => 15000,
            'deposit_date' => '2024-01-15'
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(422); // Invoice not found in Tenant A
    }
}
