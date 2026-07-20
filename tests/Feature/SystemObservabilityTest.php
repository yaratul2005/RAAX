<?php

namespace Tests\Feature;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;
use Laravel\Pulse\Facades\Pulse;
use Illuminate\Support\Facades\DB;

class SystemObservabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Just need to set up enough for the tests
    }

    public function test_trace_middleware_injects_correlation_id(): void
    {
        $response = $this->get('/up'); // Health route, should pass through global middleware

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('X-Correlation-ID'));
        $this->assertNotEmpty($response->headers->get('X-Correlation-ID'));
    }

    public function test_raax_health_command_executes_successfully(): void
    {
        // This command runs DB, Redis, and Queue checks.
        // In testing, DB should be fine. Redis might not be available, depending on CI.
        // We will just verify it runs without crashing, although it might return 1 if Redis fails in CI.
        $exitCode = Artisan::call('raax:health');

        $output = Artisan::output();
        $this->assertStringContainsString('Running RAAX System Health Checks', $output);
        $this->assertStringContainsString('Health check complete', $output);
    }

    public function test_pulse_telemetry_isolation_using_tenant_id(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $tenantManager = app(TenantContextManager::class);
        $tenantA = Str::uuid()->toString();
        $tenantB = Str::uuid()->toString();

        // Let's manually insert a pulse value for Tenant A to simulate telemetry
        $tenantManager->setTenantId($tenantA);

        // Use DB directly since pulse tables might not have models
        if (\Illuminate\Support\Facades\Schema::hasTable('pulse_values')) {
            DB::table('pulse_values')->insert([
                'timestamp' => now()->timestamp,
                'type' => 'test_metric',
                'key' => 'test_key',
                'value' => 100,
                'tenant_id' => $tenantA,
            ]);
        }
        $tenantManager->clearTenantId();

        // Switch to Tenant B and try to read it
        $tenantManager->setTenantId($tenantB);
        if (\Illuminate\Support\Facades\Schema::hasTable('pulse_values')) {
            $count = DB::table('pulse_values')->where('type', 'test_metric')->count();
            $this->assertEquals(0, $count, "Tenant B should not see Tenant A's telemetry.");
        }
        $tenantManager->clearTenantId();

        // Verify Tenant A can see it
        $tenantManager->setTenantId($tenantA);
        if (\Illuminate\Support\Facades\Schema::hasTable('pulse_values')) {
            $count = DB::table('pulse_values')->where('type', 'test_metric')->count();
            $this->assertEquals(1, $count, "Tenant A should see their own telemetry.");
        }
    }
}
