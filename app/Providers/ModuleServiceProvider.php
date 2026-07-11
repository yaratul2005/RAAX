<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $modules = ['Finance', 'HR', 'Inventory'];

        foreach ($modules as $module) {
            $providerPath = base_path("modules/{$module}/Providers/{$module}ServiceProvider.php");
            $providerClass = "Modules\\{$module}\\Providers\\{$module}ServiceProvider";

            if (file_exists($providerPath)) {
                $this->app->register($providerClass);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $modules = ['Finance', 'HR', 'Inventory'];

        foreach ($modules as $module) {
            $modulePath = base_path("modules/{$module}");

            // Load Migrations
            $migrationPath = $modulePath.'/Database/Migrations';
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }

            // Load Routes
            $routesPathApi = $modulePath.'/Routes/api.php';
            if (file_exists($routesPathApi)) {
                Route::prefix('api')
                    ->middleware('api')
                    ->group($routesPathApi);
            }

            $routesPathWeb = $modulePath.'/Routes/web.php';
            if (file_exists($routesPathWeb)) {
                Route::middleware('web')
                    ->group($routesPathWeb);
            }
        }
    }
}
