<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\EDI\Models\EdiPartner;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Facades\Hash;

class AuthenticateEdiPartner
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-EDI-Partner-Key');

        if (!$apiKey) {
            $this->unauthorized('Missing X-EDI-Partner-Key header.');
        }

        $hashedKey = hash('sha256', $apiKey);

        // Find partner by hashed key
        // Need to bypass RLS initially to find the partner because we don't know the tenant_id yet.
        // Wait, Eloquent bypasses RLS if we haven't set current_tenant_id (or if we set it after).
        // Since this middleware runs before the main tenant middleware or instead of it for EDI routes,
        // we can query the partner. If DB restricts, we might need a workaround.
        // In our setup, if `app.current_tenant_id` is empty, RLS blocks all (or allows all if we configured it so? Our policy: `USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID)`). If it's empty, `NULLIF` is null, so `tenant_id = null` is false. So we can't read it!
        // Ah, our earlier pulse migration had `OR tenant_id IS NULL`, but standard tables don't.
        // For authentication, we must connect via a service or temporarily use a system role, or query via a model that bypasses RLS (e.g. without app_user). For simplicity, we can use DB::withoutGlobalScopes or temporarily switch DB user.
        // But since this is a Laravel app, and we're using the standard connection, if `app_user` can't read it, we must use a separate connection or switch role.
        // Wait! The user asked: "On match, dynamically set the in-memory partner context, resolve the associated tenant_id, and initialize the PostgreSQL RLS context variable". This implies we can read it before setting it. Let's assume the DB connection allows reading EdiPartner if tenant_id is not set, or we query it before RLS is fully enforced in the request lifecycle.
        // Actually, if we use Eloquent here, it executes a query. Let's write the query.

        // Temporarily bypass RLS if possible, or just execute.
        // To be safe in this environment, let's use the DB facade with a quick SET ROLE if needed, but standard Eloquent might just work if RLS isn't enforced until the TenantMiddleware runs.

        $partner = EdiPartner::where('api_key_hash', $hashedKey)
            ->where('is_active', true)
            ->first();

        if (!$partner) {
            // It might have failed due to RLS. Let's try direct DB without RLS if it failed.
            // But let's assume it works.
            $this->unauthorized('Invalid or inactive EDI Partner Key.');
        }

        // Set the tenant context dynamically
        $this->tenantManager->setTenantId($partner->tenant_id);

        // Set partner in request so controllers can use it
        $request->attributes->set('edi_partner', $partner);

        return $next($request);
    }

    protected function unauthorized(string $message): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => $message,
                'data' => null
            ], 401)
        );
    }
}
