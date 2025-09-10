<?php

namespace App\Models;

use App\Models\Concerns\FiltersByLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'category_id','location_id','user_id','date','amount','description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
    return $this->belongsTo(User::class)->withTrashed();
    }
}
