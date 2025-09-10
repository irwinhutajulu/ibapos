<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\FiltersByLocation;

class Purchase extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'invoice_no','date','user_id','location_id','supplier_id','total','total_weight','freight_cost','status','received_at','received_by','posted_at','posted_by','voided_at','voided_by'
    ];

    protected $casts = [
        'date' => 'datetime',
        'total' => 'decimal:2',
        'total_weight' => 'decimal:3',
        'freight_cost' => 'decimal:2',
        'received_at' => 'datetime',
        'posted_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(PurchaseItem::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function user() { return $this->belongsTo(User::class)->withTrashed(); }
}
