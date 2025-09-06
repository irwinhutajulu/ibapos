<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LocationUser extends Pivot
{
    protected $table = 'location_user';
    protected $guarded = [];
}
