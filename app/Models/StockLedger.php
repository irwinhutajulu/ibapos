<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'stock_ledger';

    protected $fillable = [
        'product_id','location_id','ref_type','ref_id','qty_change','balance_after','cost_per_unit_at_time','total_cost_effect','user_id','note','created_at'
    ];

    protected $casts = [
        'qty_change' => 'decimal:3',
        'balance_after' => 'decimal:3',
        'cost_per_unit_at_time' => 'decimal:4',
        'total_cost_effect' => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
