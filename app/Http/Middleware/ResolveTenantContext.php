<?php

namespace App\Http\Middleware;

use App\Services\Tenant\TenantContextManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantContext
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (isset($user->tenant_id) && ! empty($user->tenant_id)) {
                $this->tenantManager->setTenantId((string) $user->tenant_id);
            } else {
                return response()->json(['error' => 'Tenant context missing.'], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $response = $next($request);

        // Clean up the session variable to prevent pool leakage
        $this->tenantManager->clearTenantId();

        return $response;
    }
}
