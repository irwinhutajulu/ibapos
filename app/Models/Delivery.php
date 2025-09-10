<?php

namespace App\Models;

use App\Models\Concerns\FiltersByLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'code', 'date', 'location_id', 'sale_id', 'status', 'assigned_to', 'assigned_at', 'delivered_at', 'note',
    ];

    protected $casts = [
        'date' => 'datetime',
        'assigned_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function assignedUser()
    {
    return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
    }
}
