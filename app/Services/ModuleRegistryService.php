<?php

namespace App\Services;

class ModuleRegistryService
{
    /**
     * Get all active registered domain modules and their metadata.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRegisteredModules(): array
    {
        $moduleDirs = glob(base_path('modules/*'), GLOB_ONLYDIR) ?: [];
        $modules = [];

        foreach ($moduleDirs as $dir) {
            $name = basename($dir);
            $modules[] = [
                'name' => $name,
                'namespace' => "Modules\\{$name}",
                'path' => "modules/{$name}",
                'has_migrations' => is_dir("{$dir}/Database/Migrations"),
                'has_api_routes' => file_exists("{$dir}/Routes/api.php"),
                'status' => 'active',
                'version' => '2.0.0',
                'description' => "Encapsulated {$name} business domain service",
            ];
        }

        return $modules;
    }

    /**
     * Get system configuration & feature flags.
     *
     * @return array<string, mixed>
     */
    public function getSystemConfig(): array
    {
        return [
            'app_name' => config('app.name', 'RAAX ERP'),
            'environment' => config('app.env', 'production'),
            'architecture' => 'Domain-Driven Modular Monolith',
            'database_driver' => config('database.default'),
            'rls_enabled' => config('database.default') !== 'sqlite',
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'supported_locales' => ['en', 'bn'],
            'multi_tenancy' => [
                'mode' => 'row_level_security',
                'tenant_header' => 'X-Tenant-ID',
            ],
        ];
    }
}
