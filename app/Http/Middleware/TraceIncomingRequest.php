<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TraceIncomingRequest
{
    public function handle(Request $request, Closure $next)
    {
        $correlationId = $request->header('X-Correlation-ID', Str::uuid()->toString());

        // Share it with the application container so log processors/jobs can use it
        app()->instance('correlation_id', $correlationId);

        Log::withContext([
            'correlation_id' => $correlationId,
            'client_ip' => $request->ip(),
        ]);

        $response = $next($request);

        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}
