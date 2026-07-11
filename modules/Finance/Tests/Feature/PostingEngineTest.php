<?php

namespace Modules\Finance\Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\LedgerAccount;
use Modules\Finance\Services\PostingEngine;
use Tests\TestCase;

class PostingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;

    protected PostingEngine $engine;

    protected string $tenantId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->engine = app(PostingEngine::class);
        $this->tenantId = Str::uuid()->toString();
        $this->tenantManager->setTenantId($this->tenantId);
    }

    public function test_balanced_journal_commits_successfully(): void
    {
        $account1 = LedgerAccount::create([
            'tenant_id' => $this->tenantId,
            'account_code' => '1000',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'currency_code' => 'USD',
        ]);

        $account2 = LedgerAccount::create([
            'tenant_id' => $this->tenantId,
            'account_code' => '4000',
            'account_name' => 'Revenue',
            'account_type' => 'Revenue',
            'currency_code' => 'USD',
        ]);

        $payload = [
            'entry_date' => '2024-01-01',
            'reference' => 'INV-001',
            'description' => 'Test Sales',
            'currency_code' => 'USD',
            'lines' => [
                [
                    'ledger_account_id' => $account1->id,
                    'debit_amount' => 10000,
                    'credit_amount' => 0,
                ],
                [
                    'ledger_account_id' => $account2->id,
                    'debit_amount' => 0,
                    'credit_amount' => 10000,
                ],
            ],
        ];

        $journal = $this->engine->post($payload);

        $this->assertEquals(10000, $journal->amount);
        $this->assertEquals($this->tenantId, $journal->tenant_id);
        $this->assertCount(2, $journal->lines);
    }

    public function test_unbalanced_journal_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Journal is unbalanced.');

        $account1 = LedgerAccount::create([
            'tenant_id' => $this->tenantId,
            'account_code' => '1000',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'currency_code' => 'USD',
        ]);

        $payload = [
            'entry_date' => '2024-01-01',
            'currency_code' => 'USD',
            'lines' => [
                [
                    'ledger_account_id' => $account1->id,
                    'debit_amount' => 10000,
                    'credit_amount' => 0,
                ],
                [
                    'ledger_account_id' => $account1->id,
                    'debit_amount' => 0,
                    'credit_amount' => 9000,
                ],
            ],
        ];

        $this->engine->post($payload);
    }
}
