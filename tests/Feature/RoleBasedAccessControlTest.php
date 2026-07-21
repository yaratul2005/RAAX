<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;
    protected User $userA;
    protected User $userB;
    protected Permission $postJournalPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();

        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();

        // Register dummy routes for testing middleware
        Route::middleware(['auth', 'tenant', 'permission:post-journal'])->group(function () {
            Route::post('/api/test-journal', function () {
                return response()->json(['success' => true]);
            });
        });

        // Setup global permission
        $this->postJournalPermission = Permission::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Post Journal Entry',
            'slug' => 'post-journal',
        ]);
    }

    public function test_user_with_correct_permission_can_access_endpoint(): void
    {
        // Give User A the role with permission in Tenant A
        $roleA = Role::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'name' => 'Finance Manager',
            'slug' => 'finance-manager',
        ]);
        $roleA->permissions()->attach($this->postJournalPermission->id, ['tenant_id' => $this->tenantA]);
        $this->userA->roles()->attach($roleA->id, ['tenant_id' => $this->tenantA]);

        $response = $this->actingAs($this->userA)->postJson('/api/test-journal', [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_user_without_permission_is_blocked_with_403(): void
    {
        // Give User B a different role in Tenant A that lacks permission
        $roleB = Role::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'name' => 'Operator',
            'slug' => 'operator',
        ]);
        $this->userB->roles()->attach($roleB->id, ['tenant_id' => $this->tenantA]);

        $response = $this->actingAs($this->userB)->postJson('/api/test-journal', [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'User does not possess the required permission scope.',
        ]);
    }

    public function test_unauthenticated_user_gets_401(): void
    {
        $response = $this->postJson('/api/test-journal', [], [
            'X-Tenant-ID' => $this->tenantA,
        ]);

        $response->assertStatus(401);
    }

    public function test_tenant_isolation_on_rbac_tables(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        // Create roles in Tenant A and B
        Role::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Role A', 'slug' => 'role-a']);
        Role::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'name' => 'Role B', 'slug' => 'role-b']);

        // Test as Tenant A
        $this->tenantManager->setTenantId($this->tenantA);
        $rolesA = Role::all();
        $this->assertCount(1, $rolesA);
        $this->assertEquals('Role A', $rolesA->first()->name);

        $this->tenantManager->clearTenantId();

        // Test as Tenant B
        $this->tenantManager->setTenantId($this->tenantB);
        $rolesB = Role::all();
        $this->assertCount(1, $rolesB);
        $this->assertEquals('Role B', $rolesB->first()->name);
    }
}
