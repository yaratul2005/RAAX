<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'statusCode' => 401,
                    'message' => 'Unauthenticated.',
                    'data' => null
                ], 401)
            );
        }

        // Check if user has role with required permission
        // We load roles and their permissions
        $hasPermission = $user->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })->exists();

        if (!$hasPermission) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'statusCode' => 403,
                    'message' => 'User does not possess the required permission scope.',
                    'data' => null
                ], 403)
            );
        }

        return $next($request);
    }
}
