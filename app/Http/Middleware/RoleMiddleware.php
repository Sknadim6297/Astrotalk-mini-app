<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login first.'
                ], 401);
            }
            // If the required role is admin, send to admin login page, otherwise to normal login
            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Please login as administrator to access this area.');
            }
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        if ($request->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. You do not have permission to access this resource.'
                ], 403);
            }
            
            // For admin role, redirect to admin login
            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Administrator access required.');
            }
            
            return redirect('/')->with('error', 'Access denied. You do not have permission to access this area.');
        }

        return $next($request);
    }
}
