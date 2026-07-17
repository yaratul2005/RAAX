<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\FiscalYear;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\LedgerAccount;
use Tests\TestCase;

class FinancialConsolidationTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $adminUser;
    protected User $normalUser;
    protected LedgerAccount $revAccountA;
    protected LedgerAccount $expAccountA;
    protected LedgerAccount $reAccountA;
    protected LedgerAccount $revAccountB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();

        $this->adminUser = User::factory()->create();
        $this->normalUser = User::factory()->create();

        // Setup RBAC
        $permission = Permission::create(['id' => Str::uuid(), 'name' => 'Consolidated', 'slug' => 'consolidated-reporting']);
        $role = Role::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'CFO', 'slug' => 'cfo']);
        $role->permissions()->attach($permission->id, ['tenant_id' => $this->tenantA]);
        $this->adminUser->roles()->attach($role->id, ['tenant_id' => $this->tenantA]);

        // Setup Tenant A Accounts & Journals
        $this->tenantManager->setTenantId($this->tenantA);
        $this->revAccountA = LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Sales', 'code' => '4001', 'type' => 'revenue']);
        $this->expAccountA = LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Rent', 'code' => '5001', 'type' => 'expense']);
        $this->reAccountA = LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Retained Earnings', 'code' => '3001', 'type' => 'equity']);

        // $1000 revenue
        $je1 = JournalEntry::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'date' => '2024-06-15', 'reference' => 'REV-1', 'description' => 'Test']);
        JournalEntryLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'journal_entry_id' => $je1->id, 'account_id' => $this->revAccountA->id, 'debit_cents' => 0, 'credit_cents' => 100000]);
        // $400 expense
        $je2 = JournalEntry::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'date' => '2024-06-20', 'reference' => 'EXP-1', 'description' => 'Test']);
        JournalEntryLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'journal_entry_id' => $je2->id, 'account_id' => $this->expAccountA->id, 'debit_cents' => 40000, 'credit_cents' => 0]);
        $this->tenantManager->clearTenantId();

        // Setup Tenant B Accounts & Journals
        $this->tenantManager->setTenantId($this->tenantB);
        // Shared Code
        $this->revAccountB = LedgerAccount::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'name' => 'Sales', 'code' => '4001', 'type' => 'revenue']);

        // $500 revenue
        $je3 = JournalEntry::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'date' => '2024-06-25', 'reference' => 'REV-B', 'description' => 'Test']);
        JournalEntryLine::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'journal_entry_id' => $je3->id, 'account_id' => $this->revAccountB->id, 'debit_cents' => 0, 'credit_cents' => 50000]);
        $this->tenantManager->clearTenantId();
    }

    public function test_consolidated_trial_balance_aggregates_across_tenants_and_enforces_rbac(): void
    {
        // Normal user fails (403)
        $response = $this->actingAs($this->normalUser)->getJson('/api/v1/finance/reports/consolidated-trial-balance?start_date=2024-01-01&end_date=2024-12-31&branch_uuids[]=' . $this->tenantA . '&branch_uuids[]=' . $this->tenantB);
        $response->assertStatus(403);

        // Admin succeeds
        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/finance/reports/consolidated-trial-balance?start_date=2024-01-01&end_date=2024-12-31&branch_uuids[]=' . $this->tenantA . '&branch_uuids[]=' . $this->tenantB);
        $response->assertStatus(200);

        $data = $response->json('data.accounts');

        // Find 4001 (Sales) which exists in both. Tenant A: 100000 cr. Tenant B: 50000 cr. Total: 150000 cr.
        // Balance for revenue is Credit - Debit = 150000.
        $salesAccount = collect($data)->firstWhere('code', '4001');
        $this->assertNotNull($salesAccount);
        $this->assertEquals(150000, $salesAccount['credit_cents']);
        $this->assertEquals(150000, $salesAccount['balance_cents']);
    }

    public function test_year_end_closing_posts_to_retained_earnings_and_locks_period(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $fy = FiscalYear::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'FY-24',
            'start_date' => '2024-01-01', 'end_date' => '2024-12-31'
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/api/v1/finance/fiscal-years/{$fy->id}/close", [
            'retained_earnings_account_id' => $this->reAccountA->id
        ], ['X-Tenant-ID' => $this->tenantA]);

        $response->assertStatus(200);
        $this->assertEquals('closed', $fy->fresh()->status);

        // Check RE log
        $this->assertDatabaseHas('retained_earnings_logs', [
            'tenant_id' => $this->tenantA,
            'fiscal_year_id' => $fy->id,
            // Net Income = 100,000 (Rev) - 40,000 (Exp) = 60,000 cents
            'closing_net_income_cents' => 60000,
        ]);

        // Attempting to post new journal in closed year should fail via PostingEngine
        $payload = [
            'date' => '2024-07-01', // Inside closed year
            'reference' => 'POST-LATE',
            'description' => 'Test',
            'lines' => [
                ['account_code' => '4001', 'debit_cents' => 100, 'credit_cents' => 0],
                ['account_code' => '5001', 'debit_cents' => 0, 'credit_cents' => 100],
            ]
        ];

        $postResponse = $this->postJson('/api/v1/finance/journals', $payload, ['X-Tenant-ID' => $this->tenantA]);
        $postResponse->assertStatus(422);
        $postResponse->assertJsonFragment(['message' => 'Cannot post journal entry to a closed fiscal year.']);
    }
}
