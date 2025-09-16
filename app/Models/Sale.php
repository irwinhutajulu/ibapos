<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\FiltersByLocation;

class Sale extends Model
{
    use HasFactory, FiltersByLocation;

    protected $fillable = [
        'invoice_no','date','user_id','location_id','customer_id','additional_fee','discount','total','payment','change','payment_type','status','posted_at','posted_by','voided_at','voided_by'
    ];

    protected $casts = [
        'date' => 'datetime',
        'additional_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment' => 'decimal:2',
        'change' => 'decimal:2',
        'posted_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function payments() { return $this->hasMany(SalesPayment::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function user() { return $this->belongsTo(User::class)->withTrashed(); }
    public function customer() { return $this->belongsTo(Customer::class); }

    // Payment type constants
    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_TRANSFER = 'transfer';
    public const PAYMENT_CARD = 'card';
    public const PAYMENT_QRIS = 'qris';
    public const PAYMENT_EWALLET = 'e-wallet';
    public const PAYMENT_MIXED = 'mixed';

    /**
     * Recalculate aggregated payment fields from related payments.
     * Sets: payment, change, payment_type
     */
    public function recalculatePayments(): void
    {
        $paid = (float) $this->payments()->sum('amount');
        $this->payment = $paid;
        $this->change = max(0, $paid - (float)$this->total);

        $types = $this->payments()->distinct()->pluck('type')->filter()->values()->all();
        if (count($types) === 1) {
            $this->payment_type = $types[0];
        } elseif (count($types) > 1) {
            $this->payment_type = self::PAYMENT_MIXED;
        }

        $this->save();
    }
}
