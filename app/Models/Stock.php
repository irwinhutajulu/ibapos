<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','location_id','qty','avg_cost'];

    protected $casts = [
        'qty' => 'decimal:3',
        'avg_cost' => 'decimal:4',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(Location::class); }
}
