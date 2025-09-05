<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['stock_adjustment_id','product_id','qty_change','unit_cost','note'];

    protected $casts = [
        'qty_change' => 'decimal:3',
        'unit_cost' => 'decimal:4',
    ];
}
