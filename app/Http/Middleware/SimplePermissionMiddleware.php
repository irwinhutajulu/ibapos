<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimplePermissionMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * This is a simple permission middleware for development purposes.
     * In production, you should use a proper permission system like Spatie Permission.
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        // For development, allow all permissions if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // In development mode, always allow access for authenticated users
        if (app()->environment('local') || config('app.debug')) {
            return $next($request);
        }
        
        // For production, you would check actual permissions here
        // Example: if (!auth()->user()->can($permission)) {
        //     abort(403, 'Access denied. Required permission: ' . $permission);
        // }
        
        return $next($request);
    }
}
