<?php

namespace Modules\Finance\Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\ChartOfAccounts;
use Modules\Finance\Models\LedgerAccount;
use Modules\Finance\Services\PostingEngine;
use Modules\Finance\Services\TrialBalanceService;
use Tests\TestCase;

class TrialBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;

    protected PostingEngine $postingEngine;

    protected TrialBalanceService $tbService;

    protected string $tenantId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->postingEngine = app(PostingEngine::class);
        $this->tbService = app(TrialBalanceService::class);

        $this->tenantId = Str::uuid()->toString();
        $this->tenantManager->setTenantId($this->tenantId);
    }

    public function test_trial_balance_aggregates_correctly(): void
    {
        $cashAccount = LedgerAccount::create([
            'tenant_id' => $this->tenantId,
            'account_code' => '1000',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'currency_code' => 'USD',
        ]);

        $revenueAccount = LedgerAccount::create([
            'tenant_id' => $this->tenantId,
            'account_code' => '4000',
            'account_name' => 'Revenue',
            'account_type' => 'Revenue',
            'currency_code' => 'USD',
        ]);

        $this->postingEngine->post([
            'entry_date' => '2024-01-15',
            'reference' => 'TEST-01',
            'description' => 'Test Sales',
            'currency_code' => 'USD',
            'lines' => [
                [
                    'ledger_account_id' => $cashAccount->id,
                    'debit_amount' => 50000,
                    'credit_amount' => 0,
                ],
                [
                    'ledger_account_id' => $revenueAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => 50000,
                ],
            ],
        ]);

        $report = $this->tbService->generate('2024-01-01', '2024-01-31');

        $this->assertEquals(50000, $report['total_debits']);
        $this->assertEquals(50000, $report['total_credits']);
        $this->assertCount(2, $report['accounts']);

        /** @var array<string, mixed>[] $accounts */
        $accounts = $report['accounts'];

        $cashReport = collect($accounts)->firstWhere('account_id', $cashAccount->id);
        $this->assertNotNull($cashReport);
        $this->assertEquals(50000, $cashReport['period_debits']);
        $this->assertEquals(0, $cashReport['period_credits']);
        $this->assertEquals(50000, $cashReport['closing_balance']);

        $revReport = collect($accounts)->firstWhere('account_id', $revenueAccount->id);
        $this->assertNotNull($revReport);
        $this->assertEquals(0, $revReport['period_debits']);
        $this->assertEquals(50000, $revReport['period_credits']);
        $this->assertEquals(-50000, $revReport['closing_balance']);
    }

    public function test_chart_of_accounts_hierarchy(): void
    {
        $parent = ChartOfAccounts::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantId,
            'code' => '1000',
            'name' => 'Current Assets',
            'type' => 'Asset',
            'currency_code' => 'USD',
        ]);

        $child = ChartOfAccounts::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantId,
            'parent_id' => $parent->id,
            'code' => '1010',
            'name' => 'Cash',
            'type' => 'Asset',
            'currency_code' => 'USD',
        ]);

        /** @var ChartOfAccounts $loadedParent */
        $loadedParent = ChartOfAccounts::with('children')->findOrFail($parent->id);

        $this->assertCount(1, $loadedParent->children);

        $first = $loadedParent->children->first();
        $this->assertNotNull($first);
        $this->assertEquals($child->id, $first->id);
    }
}
