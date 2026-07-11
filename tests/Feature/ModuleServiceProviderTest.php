<?php

namespace Tests\Feature;

use App\Providers\ModuleServiceProvider;
use Tests\TestCase;

class ModuleServiceProviderTest extends TestCase
{
    public function test_module_service_provider_is_registered(): void
    {
        $this->assertTrue(app()->providerIsLoaded(ModuleServiceProvider::class));
    }
}
