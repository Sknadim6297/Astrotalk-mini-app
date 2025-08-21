<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AstrologerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }
            return redirect('/auth/login')->with('error', 'Please login to continue');
        }

        $user = Auth::user();

        // Check if user has astrologer role
        if ($user->role !== 'astrologer') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Astrologer access required'], 403);
            }
            return redirect('/')->with('error', 'Access denied. Astrologer privileges required.');
        }

        // Check if astrologer is approved
        $astrologer = $user->astrologer;
        if (!$astrologer || $astrologer->status !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Astrologer account not approved yet'], 403);
            }
            return redirect('/')->with('error', 'Your astrologer account is pending approval. Please wait for admin approval.');
        }

        return $next($request);
    }
}
