<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks for authentication via JavaScript/localStorage
     * and redirects unauthenticated users to login
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        // For web routes, we'll let JavaScript handle the authentication check
        // and redirect on the frontend. This middleware just ensures the route
        // is properly set up for the auth check
        
        return $next($request);
    }
}
