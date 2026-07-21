<?php

namespace Modules\Finance\Providers;

use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
        $this->app->bind(\Modules\Finance\Contracts\BudgetManagerInterface::class, \Modules\Finance\Services\BudgetManager::class);
    {
        // Register Finance module services
    }

    public function boot(): void
        \Illuminate\Support\Facades\Event::listen(\Modules\Inventory\Events\IntercompanyTransferCompleted::class, \Modules\Finance\Listeners\ReconcileIntercompanyTransfer::class);
    {
        // Boot Finance module services
    }
}
