<?php

namespace App\Models;

use App\Models\Concerns\FiltersByLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'code','user_id','location_id','date','amount','status','approved_by','approved_at','note',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function requester()
    {
    return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function approver()
    {
    return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
