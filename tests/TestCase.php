<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $migrator = $this->app->make('migrator');
        $moduleDirs = glob(base_path('modules/*'), GLOB_ONLYDIR) ?: [];
        foreach ($moduleDirs as $moduleDir) {
            $migrationPath = $moduleDir . '/Database/Migrations';
            if (is_dir($migrationPath)) {
                $migrator->path($migrationPath);
            }
        }
    }
}
