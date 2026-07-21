<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\CurrencyExchangeRate;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Services\ForexGainLossEngine;
use Tests\TestCase;

class MultiCurrencyEngineTest extends TestCase
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

        // USD Rates
        // Jan 1: 1 USD = 120.00 BDT
        CurrencyExchangeRate::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'from_currency' => 'USD', 'to_currency' => 'BDT', 'rate_basis_points' => 12000, 'effective_date' => '2024-01-01']);
        // Jan 15: 1 USD = 125.00 BDT
        CurrencyExchangeRate::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'from_currency' => 'USD', 'to_currency' => 'BDT', 'rate_basis_points' => 12500, 'effective_date' => '2024-01-15']);
        // Jan 31 (Month End): 1 USD = 130.00 BDT
        CurrencyExchangeRate::create(['id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'from_currency' => 'USD', 'to_currency' => 'BDT', 'rate_basis_points' => 13000, 'effective_date' => '2024-01-31']);

        $this->tenantManager->clearTenantId();
    }

    public function test_realized_forex_gain_loss_calculation(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // AP Invoice on Jan 1 for 100 USD (10,000 cents). Base value = 12,000 BDT (1,200,000 cents)
        $invoice = FinanceInvoice::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'type' => 'AP', 'invoice_number' => 'INV-USD-1',
            'party_id' => Str::uuid()->toString(), 'issue_date' => '2024-01-01', 'due_date' => '2024-01-31',
            'amount_cents' => 10000, 'paid_cents' => 0, 'currency_code' => 'USD', 'status' => 'unpaid'
        ]);

        $engine = app(ForexGainLossEngine::class);

        // Paid on Jan 15. Base value = 12,500 BDT (1,250,000 cents)
        // Paying 100 USD (10000 cents)
        // Variance = 1,250,000 - 1,200,000 = 50,000 cents (Loss because AP and base payment > base invoice)
        $variance = $engine->calculateRealizedGainLoss($invoice, 10000, '2024-01-15');

        $this->assertEquals(50000, $variance);

        // Check journal entries
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'reference' => 'FXR-INV-USD-1'
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'tenant_id' => $this->tenantA,
            'debit_cents' => 50000, // Loss is a debit
        ]);
    }

    public function test_unrealized_month_end_revaluation_creates_and_reverses_entries(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // AR Invoice on Jan 1 for 200 USD (20,000 cents). Base value = 24,000 BDT (2,400,000 cents)
        $invoice = FinanceInvoice::create([
            'id' => Str::uuid()->toString(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-USD-2',
            'party_id' => Str::uuid()->toString(), 'issue_date' => '2024-01-01', 'due_date' => '2024-02-15',
            'amount_cents' => 20000, 'paid_cents' => 0, 'currency_code' => 'USD', 'status' => 'unpaid'
        ]);

        $this->tenantManager->clearTenantId();

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/forex/revalue', [
            'target_month' => '2024-01',
            'target_currency' => 'USD'
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        // Month end rate is 130.00. Base value = 26,000 BDT (2,600,000 cents)
        // Variance = 2,600,000 - 2,400,000 = 200,000 cents Gain (AR)
        $this->assertDatabaseHas('forex_revaluation_logs', [
            'tenant_id' => $this->tenantA,
            'revaluation_month' => '2024-01',
            'unrealized_gain_loss_cents' => 200000
        ]);

        // Check primary journal entry on Jan 31
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'reference' => 'FX-REVAL-USD-2024-01',
            'date' => '2024-01-31 00:00:00'
        ]);

        // Check reversing journal entry on Feb 1
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'reference' => 'FX-REV-REV-USD-2024-01',
            'date' => '2024-02-01 00:00:00'
        ]);
    }

    public function test_tenant_isolation_on_forex_rates(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        CurrencyExchangeRate::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'from_currency' => 'EUR', 'to_currency' => 'BDT', 'rate_basis_points' => 13000, 'effective_date' => '2024-01-01']);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        $rates = CurrencyExchangeRate::where('from_currency', 'EUR')->get();
        $this->assertCount(0, $rates);
    }
}
