<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiBypassMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Bypass semua permission check untuk API
        if ($request->is('api/*') && auth('sanctum')->check()) {
            // Set user sebagai super admin sementara untuk API
            $user = auth('sanctum')->user();
            if ($user) {
                // Hack: Override permission check
                $user->can = function() { return true; };
                return $next($request);
            }
        }
        
        return $next($request);
    }
}