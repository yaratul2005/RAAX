<?php

namespace Modules\Assets\Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Assets\Events\DepreciationRunCompleted;
use Modules\Assets\Models\DepreciationLog;
use Modules\Assets\Models\FixedAsset;
use Tests\TestCase;

class AssetDepreciationTest extends TestCase
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

    public function test_straight_line_depreciation_math_and_event(): void
    {
        Event::fake();

        $this->tenantManager->setTenantId($this->tenantA);

        // Acquisition Cost = 120,000 cents, Salvage = 20,000 cents, Lifespan = 10 months
        // Expected Monthly Depreciation = (120000 - 20000) / 10 = 10000 cents
        $asset = FixedAsset::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'asset_tag' => 'AST-SL', 'name' => 'Laptop',
            'acquisition_date' => '2024-01-01', 'acquisition_cost_cents' => 120000, 'salvage_value_cents' => 20000,
            'lifespan_months' => 10, 'depreciation_method' => 'straight_line'
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/assets/depreciate', ['target_month' => '2024-01'], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);

        $log = DepreciationLog::where('fixed_asset_id', $asset->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals(10000, $log->depreciation_amount_cents);
        $this->assertEquals(10000, $log->accumulated_depreciation_cents);
        $this->assertEquals(110000, $log->book_value_cents); // 120000 - 10000

        Event::assertDispatched(DepreciationRunCompleted::class, function ($e) {
            return $e->periodMonth === '2024-01' && $e->totalDepreciationCents === 10000 && $e->tenantId === $this->tenantA;
        });
    }

    public function test_reducing_balance_and_salvage_value_limit(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        // Cost = 100,000. Salvage = 80,000. Rate = 50.00% (5000 basis).
        // Month 1: 100000 * 0.50 = 50000. But max allowed is 100000 - 80000 = 20000.
        // So Month 1 depreciation should be capped at 20000, leaving book value at 80000, and marking fully depreciated.
        $asset = FixedAsset::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'asset_tag' => 'AST-RB', 'name' => 'Server',
            'acquisition_date' => '2024-01-01', 'acquisition_cost_cents' => 100000, 'salvage_value_cents' => 80000,
            'lifespan_months' => 5, 'depreciation_method' => 'reducing_balance', 'depreciation_rate_basis_cents' => 5000
        ]);

        $this->actingAs($this->user)->postJson('/api/v1/assets/depreciate', ['target_month' => '2024-01'], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $log = DepreciationLog::where('fixed_asset_id', $asset->id)->first();
        $this->assertEquals(20000, $log->depreciation_amount_cents);
        $this->assertEquals(80000, $log->book_value_cents);
        $this->assertEquals('fully_depreciated', $asset->fresh()->status);
    }

    public function test_tenant_isolation_on_assets(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $this->tenantManager->setTenantId($this->tenantA);
        FixedAsset::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'asset_tag' => 'AST-A', 'name' => 'Asset A',
            'acquisition_date' => '2024-01-01', 'acquisition_cost_cents' => 100000, 'salvage_value_cents' => 0,
            'lifespan_months' => 5, 'depreciation_method' => 'straight_line'
        ]);
        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $this->assertCount(0, FixedAsset::all());
    }
}
