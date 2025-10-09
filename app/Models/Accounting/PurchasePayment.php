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
        'payment_method',
        'check_number',
        'reference_number',
        'bank_account_id',
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
        return $this->belongsTo(\App\Models\Master\Vendor::class, 'vendor_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\Accounting\Account::class, 'bank_account_id');
    }
}
