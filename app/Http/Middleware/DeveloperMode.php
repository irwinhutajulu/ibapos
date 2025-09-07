<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DeveloperMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only activate developer mode in local environment
        if (app()->environment('local') && config('app.developer_mode', false)) {
            // If not authenticated, auto-login as first user
            if (!Auth::check()) {
                $user = User::first();
                if ($user) {
                    Auth::login($user);
                    
                    // Set default active location if user has locations
                    if ($user->locations->isNotEmpty() && !session('active_location_id')) {
                        session(['active_location_id' => $user->locations->first()->id]);
                    }
                }
            }
            
            // 🚨 SECURITY BYPASS - ONLY FOR DEVELOPMENT
            // Mark request as developer mode to bypass permission checks
            $request->attributes->set('developer_mode_bypass', true);
        }

        return $next($request);
    }
}
