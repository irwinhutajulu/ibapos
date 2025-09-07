<?php

if (!function_exists('developer_mode_active')) {
    /**
     * Check if developer mode is active
     */
    function developer_mode_active(): bool
    {
        return app()->environment('local') && config('app.developer_mode', false);
    }
}

if (!function_exists('can_with_developer_bypass')) {
    /**
     * Check permission with developer mode bypass
     */
    function can_with_developer_bypass(string $permission): bool
    {
        // Developer mode bypass
        if (developer_mode_active()) {
            return true;
        }
        
        // Normal permission check
        return auth()->user()?->can($permission) ?? false;
    }
}
