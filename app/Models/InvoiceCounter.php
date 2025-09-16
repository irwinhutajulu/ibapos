<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCounter extends Model
{
    use HasFactory;

    protected $fillable = ['type','location_id','last_number','date'];

    protected $casts = [
        'last_number' => 'integer',
        'date' => 'date',
    ];
}
