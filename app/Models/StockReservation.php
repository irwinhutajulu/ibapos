<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id','location_id','sale_id','sale_item_id','qty_reserved','status','expires_at','created_by','released_at','released_by','consumed_at','consumed_by','created_at'
    ];

    protected $casts = [
        'qty_reserved' => 'decimal:3',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
        'consumed_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
