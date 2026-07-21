<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\LedgerAccount;
use Modules\Finance\Models\LedgerChain;
use Modules\Finance\Services\PostingEngine;
use Tests\TestCase;

class LedgerChainVerificationTest extends TestCase
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
        LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'account_name' => 'Cash', 'account_code' => '1001', 'account_type' => 'asset', 'currency_code' => 'BDT']);
        LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'account_name' => 'Sales', 'account_code' => '4001', 'account_type' => 'revenue', 'currency_code' => 'BDT']);
        $this->tenantManager->clearTenantId();
    }

    public function test_journal_posting_generates_sequential_cryptographic_chain(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);
        $engine = app(PostingEngine::class);

        // Entry 1
        $engine->postJournal([
            'date' => '2024-01-01', 'reference' => 'JE-1', 'description' => 'Test 1',
            'lines' => [
                ['account_code' => '1001', 'debit_cents' => 1000, 'credit_cents' => 0],
                ['account_code' => '4001', 'debit_cents' => 0, 'credit_cents' => 1000]
            ]
        ]);

        // Entry 2
        $engine->postJournal([
            'date' => '2024-01-02', 'reference' => 'JE-2', 'description' => 'Test 2',
            'lines' => [
                ['account_code' => '1001', 'debit_cents' => 2000, 'credit_cents' => 0],
                ['account_code' => '4001', 'debit_cents' => 0, 'credit_cents' => 2000]
            ]
        ]);

        $chains = LedgerChain::where('tenant_id', $this->tenantA)->orderBy('sequence_number')->get();

        $this->assertCount(2, $chains);
        $this->assertEquals(1, $chains[0]->sequence_number);
        $this->assertEquals(2, $chains[1]->sequence_number);

        // Run Verification Command
        $exitCode = Artisan::call('raax:ledger:verify', ['tenant_id' => $this->tenantA]);
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('The ledger chain is cryptographically intact', Artisan::output());
    }

    public function test_manual_tamper_breaks_chain_and_returns_error(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);
        $engine = app(PostingEngine::class);

        $entry = $engine->postJournal([
            'date' => '2024-01-01', 'reference' => 'JE-3', 'description' => 'Tamper Test',
            'lines' => [
                ['account_code' => '1001', 'debit_cents' => 5000, 'credit_cents' => 0],
                ['account_code' => '4001', 'debit_cents' => 0, 'credit_cents' => 5000]
            ]
        ]);

        // Tamper with the database manually (bypassing the app layer protections)
        $line = JournalEntryLine::where('journal_entry_id', $entry->id)->where('debit_cents', 5000)->first();
        $line->update(['debit_cents' => 10000]); // Alter the amount

        // Run Verification Command
        $exitCode = Artisan::call('raax:ledger:verify', ['tenant_id' => $this->tenantA]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('CRITICAL TAMPER DETECTED', Artisan::output());
    }

    public function test_tenant_isolation_on_ledger_chains(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantA);
        $engine = app(PostingEngine::class);
        $engine->postJournal([
            'date' => '2024-01-01', 'reference' => 'JE-ISO', 'description' => 'Iso Test',
            'lines' => [
                ['account_code' => '1001', 'debit_cents' => 100, 'credit_cents' => 0],
                ['account_code' => '4001', 'debit_cents' => 0, 'credit_cents' => 100]
            ]
        ]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $chainsB = LedgerChain::all();
        $this->assertCount(0, $chainsB);
    }
}
