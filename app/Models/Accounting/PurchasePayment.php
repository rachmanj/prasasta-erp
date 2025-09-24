<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'payment_no',
        'date',
        'vendor_id',
        'description',
        'status',
        'total_amount',
        'posted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(PurchasePaymentLine::class, 'payment_id');
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }
}
