<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';
}
