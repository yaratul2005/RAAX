<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\BankStatement;
use Modules\Finance\Models\FinanceInvoice;
use Tests\TestCase;

class BankReconciliationTest extends TestCase
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

    public function test_mt940_parser_and_validation(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $mt940 = <<<MT940
:25:ACC123456
:60F:C240101BDT1000,00
:61:2401050105CR150,00NMSCREF1
:61:2401060106DR50,00NMSCREF2
:62F:C240106BDT1100,00
MT940;

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/bank/statements', [
            'bank_name' => 'Test Bank',
            'mt940_content' => $mt940
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);

        $statementId = $response->json('data.id');

        $this->assertDatabaseHas('bank_statements', [
            'id' => $statementId,
            'opening_balance_cents' => 100000,
            'closing_balance_cents' => 110000,
        ]);

        $this->assertDatabaseHas('bank_statement_lines', [
            'bank_statement_id' => $statementId,
            'amount_cents' => 15000, // CR
            'reference' => 'NMSCREF1',
        ]);

        $this->assertDatabaseHas('bank_statement_lines', [
            'bank_statement_id' => $statementId,
            'amount_cents' => -5000, // DR
            'reference' => 'NMSCREF2',
        ]);
    }

    public function test_mt940_parser_rejects_invalid_math(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $mt940 = <<<MT940
:25:ACC123456
:60F:C240101BDT1000,00
:61:2401050105CR150,00NMSCREF1
:62F:C240106BDT1500,00
MT940;

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/bank/statements', [
            'bank_name' => 'Test Bank',
            'mt940_content' => $mt940
        ], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Mathematical validation failed: computed closing balance (115000) does not match statement closing balance (150000).']);
    }

    public function test_reconciliation_manager_auto_matches_invoices(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // 1. Create an AP invoice for 50.00 (5000 cents)
        $apInvoice = FinanceInvoice::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AP', 'invoice_number' => 'AP-1',
            'party_id' => Str::uuid(), 'issue_date' => '2024-01-01', 'due_date' => '2024-01-05',
            'amount_cents' => 5000, 'paid_cents' => 0, 'status' => 'unpaid'
        ]);

        // 2. Upload statement
        $mt940 = <<<MT940
:25:ACC123456
:60F:C240101BDT1000,00
:61:2401050105DR50,00NMSCPAYMENT
:62F:C240106BDT950,00
MT940;

        $uploadResponse = $this->actingAs($this->user)->postJson('/api/v1/finance/bank/statements', [
            'bank_name' => 'Test Bank', 'mt940_content' => $mt940
        ], ['X-Tenant-ID' => $this->tenantA]);

        $statementId = $uploadResponse->json('data.id');

        // 3. Trigger reconciliation
        $reconResponse = $this->actingAs($this->user)->postJson("/api/v1/finance/bank/statements/{$statementId}/reconcile", [], [
            'X-Tenant-ID' => $this->tenantA
        ]);

        $reconResponse->assertStatus(200);

        // Assert Invoice is paid
        $this->assertEquals('paid', $apInvoice->fresh()->status);
        $this->assertEquals(5000, $apInvoice->fresh()->paid_cents);

        // Assert Statement is reconciled
        $this->assertDatabaseHas('bank_statements', ['id' => $statementId, 'status' => 'reconciled']);
        $this->assertDatabaseHas('bank_statement_lines', ['bank_statement_id' => $statementId, 'is_reconciled' => 1]); // or true

        // Assert Clearing Journal created (check journal_entries table)
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenantA,
            'reference' => 'RECON-NMSCPAYMENT'
        ]);
    }

    public function test_tenant_isolation_on_bank_statements(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantB);
        BankStatement::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'bank_name' => 'Bank B',
            'account_number' => '123', 'statement_date' => '2024-01-01',
            'opening_balance_cents' => 0, 'closing_balance_cents' => 0
        ]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantA);
        $statements = BankStatement::all();
        $this->assertCount(0, $statements);
    }
}
