<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\App\Http\Middleware\CheckDatabaseConnection::class);

        // Redirect unauthenticated guests to the appropriate panel login
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            $path = $request->path();
            return match (true) {
                str_starts_with($path, 'portal-admin-smkn1')     => '/portal-admin-smkn1/login',
                str_starts_with($path, 'portal-kesiswaan-smkn1') => '/portal-kesiswaan-smkn1/login',
                str_starts_with($path, 'portal-guru-smkn1')      => '/portal-guru-smkn1/login',
                str_starts_with($path, 'kesiswaan-exports')      => '/portal-kesiswaan-smkn1/login',
                str_starts_with($path, 'guru-exports')           => '/portal-guru-smkn1/login',
                default                                          => '/siswa/login',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
