<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Str;
use Modules\Finance\Models\ChartOfAccounts;

class ChartOfAccountsService
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAccount(array $data): ChartOfAccounts
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (! $tenantId) {
            throw new \InvalidArgumentException('Tenant context required');
        }

        $data['id'] = Str::uuid()->toString();
        $data['tenant_id'] = $tenantId;

        return ChartOfAccounts::create($data);
    }
}
