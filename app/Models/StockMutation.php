<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Location;
use App\Models\User;

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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
