<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'name','category_id','barcode','price','weight','unit','image_path'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:3',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
