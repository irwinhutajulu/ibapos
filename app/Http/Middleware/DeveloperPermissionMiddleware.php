<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class DeveloperPermissionMiddleware extends PermissionMiddleware
{
    /**
     * Handle an incoming request with developer mode bypass.
     */
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        // 🚨 DEVELOPER MODE BYPASS - ONLY FOR LOCAL DEVELOPMENT
        if (app()->environment('local') && 
            config('app.developer_mode', false) && 
            $request->attributes->get('developer_mode_bypass', false)) {
            
            // Log bypass for security awareness
            \Log::warning('🚨 DEVELOPER MODE: Permission check bypassed', [
                'permission' => $permission,
                'user' => auth()->id(),
                'route' => $request->route()?->getName(),
                'ip' => $request->ip()
            ]);
            
            return $next($request);
        }
        
        // Normal permission check using Spatie
        return parent::handle($request, $next, $permission, $guard);
    }
}
