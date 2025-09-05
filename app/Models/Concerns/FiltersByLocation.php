<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersByLocation
{
    public static function bootFiltersByLocation(): void
    {
        static::addGlobalScope('active_location', function (Builder $builder) {
            $active = session('active_location_id');
            if ($active && !auth()->user()?->hasRole('super-admin')) {
                $builder->where($builder->getQuery()->from.'.location_id', $active);
            }
        });
    }
}
