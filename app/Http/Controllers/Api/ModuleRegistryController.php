<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModuleRegistryService;
use Illuminate\Http\JsonResponse;

class ModuleRegistryController extends Controller
{
    protected ModuleRegistryService $registryService;

    public function __construct(ModuleRegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'modules' => $this->registryService->getRegisteredModules(),
                'system' => $this->registryService->getSystemConfig(),
            ]
        ]);
    }
}
