<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\LedgerAccount;
use Tests\TestCase;

class TenantIsolationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_in_database()
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        $tenantA = Str::uuid()->toString();
        $tenantB = Str::uuid()->toString();

        $userA = User::factory()->create(['tenant_id' => $tenantA]);
        $userB = User::factory()->create(['tenant_id' => $tenantB]);

        // Manually seed the database with Ledger Accounts bypassing RLS (e.g. as superuser, but we'll use eloquent before RLS applies if connection allows)
        // Wait, if RLS is on, how do we insert?
        // We set the context first.

        $manager = app(TenantContextManager::class);

        // Setup A
        $manager->setTenantId($tenantA);
        LedgerAccount::create([
            'tenant_id' => $tenantA,
            'account_code' => '1000',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'currency_code' => 'USD',
        ]);
        $manager->clearTenantId();

        // Setup B
        $manager->setTenantId($tenantB);
        LedgerAccount::create([
            'tenant_id' => $tenantB,
            'account_code' => '2000',
            'account_name' => 'AP',
            'account_type' => 'Liability',
            'currency_code' => 'USD',
        ]);
        $manager->clearTenantId();

        // Test User A context
        $manager->setTenantId($tenantA);
        $accountsA = LedgerAccount::all();
        $this->assertCount(1, $accountsA);
        $this->assertEquals('1000', $accountsA->first()->account_code);
        $manager->clearTenantId();

        // Test User B context
        $manager->setTenantId($tenantB);
        $accountsB = LedgerAccount::all();
        $this->assertCount(1, $accountsB);
        $this->assertEquals('2000', $accountsB->first()->account_code);
        $manager->clearTenantId();
    }
}
