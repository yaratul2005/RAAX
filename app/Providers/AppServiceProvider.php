<?php

namespace App\Providers;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
        $this->app->bind(\Modules\Inventory\Contracts\FIFOValuationEngineInterface::class, \Modules\Inventory\Services\FIFOValuationEngine::class);
        $this->app->bind(\Modules\Inventory\Contracts\PurchaseOrderFetcherInterface::class, \App\Services\Integrations\ProcurementToInventoryFetcher::class);
    {
        $this->app->singleton(TenantContextManager::class, function ($app) {
            return new TenantContextManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
