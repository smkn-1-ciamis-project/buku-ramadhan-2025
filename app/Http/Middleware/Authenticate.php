<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Filament panels use their own login routes — determine which panel's
        // login to redirect to based on the current request path prefix.
        $path = $request->path();

        if (str_starts_with($path, 'portal-admin-smkn1')) {
            return '/portal-admin-smkn1/login';
        }
        if (str_starts_with($path, 'portal-kesiswaan-smkn1')) {
            return '/portal-kesiswaan-smkn1/login';
        }
        if (str_starts_with($path, 'portal-guru-smkn1')) {
            return '/portal-guru-smkn1/login';
        }
        if (str_starts_with($path, 'kesiswaan-exports')) {
            return '/portal-kesiswaan-smkn1/login';
        }
        if (str_starts_with($path, 'guru-exports')) {
            return '/portal-guru-smkn1/login';
        }

        // Default: siswa panel login
        return '/siswa/login';
    }
}
