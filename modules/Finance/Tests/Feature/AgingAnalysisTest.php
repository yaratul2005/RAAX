<?php

namespace Modules\Finance\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\FinanceInvoice;
use Modules\Finance\Services\AgingAnalysisService;
use Tests\TestCase;
use Carbon\Carbon;

class AgingAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $user;
    protected AgingAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
        $this->user = User::factory()->create();
        $this->service = app(AgingAnalysisService::class);
    }

    public function test_invoice_creation_validates_and_stores(): void
    {
        $payload = [
            'type' => 'AP',
            'invoice_number' => 'INV-001',
            'party_id' => Str::uuid()->toString(),
            'issue_date' => '2024-01-01',
            'due_date' => '2024-01-31',
            'amount_cents' => 15000,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/finance/invoices', $payload, [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('finance_invoices', [
            'tenant_id' => $this->tenantA,
            'invoice_number' => 'INV-001',
            'amount_cents' => 15000,
            'outstanding_balance' => null, // Dynamic attribute
        ]);
    }

    public function test_invoices_are_bucketed_correctly(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $partyId = Str::uuid()->toString();

        // Current (due today)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-C', 'party_id' => $partyId, 'issue_date' => '2024-01-01', 'due_date' => '2024-01-31', 'amount_cents' => 1000]);
        // 1-30 Days (due 15 days ago)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-1-30', 'party_id' => $partyId, 'issue_date' => '2023-12-01', 'due_date' => '2024-01-16', 'amount_cents' => 2000]);
        // 31-60 Days (due 45 days ago)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-31-60', 'party_id' => $partyId, 'issue_date' => '2023-11-01', 'due_date' => '2023-12-17', 'amount_cents' => 3000]);
        // 61-90 Days (due 75 days ago)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-61-90', 'party_id' => $partyId, 'issue_date' => '2023-10-01', 'due_date' => '2023-11-17', 'amount_cents' => 4000]);
        // 91+ Days (due 100 days ago)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-91+', 'party_id' => $partyId, 'issue_date' => '2023-09-01', 'due_date' => '2023-10-23', 'amount_cents' => 5000]);

        // Paid invoice (should be ignored)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-PAID', 'party_id' => $partyId, 'issue_date' => '2023-09-01', 'due_date' => '2023-10-23', 'amount_cents' => 5000, 'paid_cents' => 5000, 'status' => 'paid']);

        // Partially paid (1-30 days overdue)
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AR', 'invoice_number' => 'INV-PARTIAL', 'party_id' => $partyId, 'issue_date' => '2023-12-01', 'due_date' => '2024-01-16', 'amount_cents' => 2000, 'paid_cents' => 500, 'status' => 'partially_paid']);

        $evaluationDate = '2024-01-31';
        $schedule = $this->service->getAgingSchedule('AR', $evaluationDate);

        $this->assertEquals(1000, $schedule['current']['total_balance']);
        $this->assertEquals(2000 + 1500, $schedule['1_30_days']['total_balance']); // 2000 + 1500 outstanding
        $this->assertEquals(3000, $schedule['31_60_days']['total_balance']);
        $this->assertEquals(4000, $schedule['61_90_days']['total_balance']);
        $this->assertEquals(5000, $schedule['91_plus_days']['total_balance']);
    }

    public function test_tenant_isolation_on_aging_analysis(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $partyId = Str::uuid()->toString();

        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'AP', 'invoice_number' => 'INV-A', 'party_id' => $partyId, 'issue_date' => '2024-01-01', 'due_date' => '2024-01-15', 'amount_cents' => 1000]);
        FinanceInvoice::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'type' => 'AP', 'invoice_number' => 'INV-B', 'party_id' => $partyId, 'issue_date' => '2024-01-01', 'due_date' => '2024-01-15', 'amount_cents' => 2000]);

        $this->tenantManager->setTenantId($this->tenantA);
        $scheduleA = $this->service->getAgingSchedule('AP', '2024-01-31');
        $this->assertEquals(1000, $scheduleA['1_30_days']['total_balance']);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $scheduleB = $this->service->getAgingSchedule('AP', '2024-01-31');
        $this->assertEquals(2000, $scheduleB['1_30_days']['total_balance']);
    }
}
