<?php

namespace App\Services\Tenant;

use Illuminate\Support\Facades\DB;

class TenantContextManager
{
    protected ?string $currentTenantId = null;

    /**
     * Set the current tenant context.
     */
    public function setTenantId(string $uuid): void
    {
        $this->currentTenantId = $uuid;

        // Note: Using SET LOCAL ensures this only applies to the current transaction.
        // For persistent connections outside transactions, you might need a different approach,
        // but Laravel wraps each request nicely, and DB::transaction uses transactions.
        // Actually, let's use SET SESSION to ensure it persists for the whole connection lifespan during this request,
        // but we MUST clear it afterwards.
        if (config('database.default') !== 'sqlite') {
            DB::statement("SET app.current_tenant_id = '{$uuid}'");
        }
    }

    /**
     * Get the current tenant context.
     */
    public function getTenantId(): ?string
    {
        return $this->currentTenantId;
    }

    /**
     * Clear the current tenant context.
     */
    public function clearTenantId(): void
    {
        $this->currentTenantId = null;
        if (config('database.default') !== 'sqlite') {
            DB::statement('RESET app.current_tenant_id');
        }
    }
}
