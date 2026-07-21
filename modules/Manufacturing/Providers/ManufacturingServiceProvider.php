<?php

namespace Modules\Manufacturing\Providers;

use Illuminate\Support\ServiceProvider;

class ManufacturingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\Manufacturing\Contracts\MrpDemandFetcherInterface::class,
            \Modules\Manufacturing\Services\MRP\SalesOrderDemandFetcher::class
        );
        $this->app->bind(
            \Modules\Manufacturing\Contracts\MrpInventoryFetcherInterface::class,
            \Modules\Manufacturing\Services\MRP\DefaultMrpInventoryFetcher::class
        );
        $this->app->bind(
            \Modules\Manufacturing\Contracts\MrpProcurementReleaserInterface::class,
            \Modules\Manufacturing\Services\MRP\DefaultMrpProcurementReleaser::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}
