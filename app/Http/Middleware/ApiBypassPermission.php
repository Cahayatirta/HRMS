<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBypassPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika request adalah API dan user sudah login via sanctum
        if ($request->is('api/*') && auth('sanctum')->check()) {
            return $next($request);
        }
        
        // Jika bukan API atau tidak login, lanjutkan middleware normal
        return $next($request);
    }
}