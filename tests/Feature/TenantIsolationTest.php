<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_context_is_set_in_database()
    {
        $tenantId = Str::uuid()->toString();
        $manager = app(TenantContextManager::class);

        if (config('database.default') !== 'sqlite') {
            $manager->setTenantId($tenantId);

            $result = DB::selectOne("SELECT current_setting('app.current_tenant_id') as tenant");
            $this->assertEquals($tenantId, $result->tenant);

            $manager->clearTenantId();
        } else {
            // Mock test for sqlite
            $manager->setTenantId($tenantId);
            $this->assertEquals($tenantId, $manager->getTenantId());
            $manager->clearTenantId();
            $this->assertNull($manager->getTenantId());
        }
    }

    public function test_middleware_blocks_access_without_tenant_id()
    {
        $user = User::factory()->create(['tenant_id' => null]);

        // Use a dummy route that uses the middleware
        Route::get('/api/test-tenant', function () {
            return 'success';
        })->middleware(['api', 'tenant']);

        $response = $this->actingAs($user)->getJson('/api/test-tenant');

        $response->assertStatus(403);
    }

    public function test_middleware_allows_access_with_tenant_id()
    {
        $tenantId = Str::uuid()->toString();
        $user = User::factory()->create(['tenant_id' => $tenantId]);

        // Use a dummy route that uses the middleware
        Route::get('/api/test-tenant', function () {
            return app(TenantContextManager::class)->getTenantId();
        })->middleware(['api', 'tenant']);

        $response = $this->actingAs($user)->getJson('/api/test-tenant');

        $response->assertStatus(200);
        $this->assertEquals($tenantId, $response->getContent());

        // Ensure it's cleared after request
        $this->assertNull(app(TenantContextManager::class)->getTenantId());
    }
}
