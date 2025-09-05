<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\FiltersByLocation;

class StockAdjustment extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'code','date','location_id','user_id','reason','note','status','posted_at','posted_by','voided_at','voided_by'
    ];

    protected $casts = [
        'date' => 'datetime',
        'posted_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(StockAdjustmentItem::class); }
}
