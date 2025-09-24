<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentLine extends Model
{
    protected $fillable = [
        'payment_id',
        'account_id',
        'description',
        'amount',
        'project_id',
        'fund_id',
        'dept_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(PurchasePayment::class, 'payment_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Accounting\Account::class, 'account_id');
    }
}
