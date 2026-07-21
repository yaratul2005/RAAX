<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\TraceIncomingRequest::class);
        $middleware->alias([
            'tenant' => \App\Http\Middleware\ResolveTenantContext::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'edi.auth' => \App\Http\Middleware\AuthenticateEdiPartner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
