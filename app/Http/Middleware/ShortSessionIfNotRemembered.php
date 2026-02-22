<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When a user logged in WITHOUT "Ingat Saya" checked, enforce:
 *  - session cookie expires when browser closes (expire_on_close = true)
 *  - session lifetime = 5 minutes of inactivity
 */
class ShortSessionIfNotRemembered
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('session_short') && session('session_short') === true) {
            config(['session.expire_on_close' => true]);
            config(['session.lifetime' => 5]);
        }

        return $next($request);
    }
}
