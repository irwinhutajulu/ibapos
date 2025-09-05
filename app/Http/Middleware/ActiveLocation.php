<?php

namespace App\Http\Middleware;

use App\Models\Location;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ActiveLocation
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $activeId = session('active_location_id');

        if (!$user) {
            return $next($request);
        }

        // Super admin can bypass if none selected
        if (!$activeId && $user->hasRole('super-admin')) {
            return $next($request);
        }

        if (!$activeId) {
            throw new AccessDeniedHttpException('Active location is required.');
        }

        $location = Location::find($activeId);
        if (!$location) {
            throw new AccessDeniedHttpException('Invalid active location.');
        }

        // Ensure user is assigned to the location
        if (!$user->locations()->where('locations.id', $activeId)->exists() && !$user->hasRole('super-admin')) {
            throw new AccessDeniedHttpException('You do not have access to this location.');
        }

        // Share globally if needed
        app()->instance('activeLocation', $location);

        return $next($request);
    }
}
