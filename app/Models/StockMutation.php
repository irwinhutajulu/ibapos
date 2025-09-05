<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','from_location_id','to_location_id','qty','date','note','status','requested_by','confirmed_by','confirmed_at'
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'date' => 'date',
        'confirmed_at' => 'datetime',
    ];
}
