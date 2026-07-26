<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class StartupRouterService
{
    /**
     * Determine if the application is running for the first time (zero DB users).
     */
    public static function isFirstRun(): bool
    {
        try {
            if (!Schema::hasTable('users')) {
                return true;
            }
            return User::count() === 0;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Resolve the landing view model based on the authenticated user's permissions.
     */
    public static function resolveLandingView(?User $user): string
    {
        if (!$user) {
            return 'my_workspace';
        }

        $isOwner = isset($user->is_owner) ? (bool) $user->is_owner : false;
        $hasRole = function (string $role) use ($user) {
            return method_exists($user, 'hasRole') ? $user->hasRole($role) : false;
        };
        $hasPerm = function (string $perm) use ($user) {
            return method_exists($user, 'hasPermissionTo') ? $user->hasPermissionTo($perm) : false;
        };

        // Owner or Super Admin -> Executive Dashboard
        if ($isOwner || $hasRole('Super Admin') || $hasRole('Owner') || $hasPerm('settings.edit')) {
            return 'executive_dashboard';
        }

        // Manager / Department Mod -> Operations Dashboard
        if ($hasRole('Manager') || $hasRole('CFO') || $hasRole('Procurement Manager') || $hasPerm('approvals.approve')) {
            return 'operations_dashboard';
        }

        // Standard Employee -> My Workspace
        return 'my_workspace';
    }

    /**
     * Security Boundary Enforcement: Check module capability flags.
     */
    public static function hasPermission(?User $user, string $module, string $action): bool
    {
        if (!$user) {
            return false;
        }

        $isOwner = isset($user->is_owner) ? (bool) $user->is_owner : false;
        if ($isOwner || (method_exists($user, 'hasRole') && $user->hasRole('Super Admin'))) {
            return true;
        }

        return method_exists($user, 'hasPermissionTo') ? $user->hasPermissionTo("{$module}.{$action}") : false;
    }
}
