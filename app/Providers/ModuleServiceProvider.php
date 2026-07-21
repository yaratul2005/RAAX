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
        $moduleDirs = glob(base_path('modules/*'), GLOB_ONLYDIR) ?: [];

        foreach ($moduleDirs as $moduleDir) {
            $module = basename($moduleDir);
            $providerPath = "{$moduleDir}/Providers/{$module}ServiceProvider.php";
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
        $moduleDirs = glob(base_path('modules/*'), GLOB_ONLYDIR) ?: [];

        foreach ($moduleDirs as $moduleDir) {
            // Load Migrations
            $migrationPath = $moduleDir.'/Database/Migrations';
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }

            // Load Routes
            $routesPathApi = $moduleDir.'/Routes/api.php';
            if (file_exists($routesPathApi)) {
                Route::middleware('api')
                    ->group($routesPathApi);
            }

            $routesPathWeb = $moduleDir.'/Routes/web.php';
            if (file_exists($routesPathWeb)) {
                Route::middleware('web')
                    ->group($routesPathWeb);
            }
        }
    }
}
