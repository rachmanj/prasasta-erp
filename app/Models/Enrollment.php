<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'batch_id',
        'enrollment_date',
        'status',
        'payment_plan_id',
        'total_amount'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Customer::class, 'student_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }

    public function course(): BelongsTo
    {
        return $this->batch->course();
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    }

    public function installmentPayments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class, 'enrollment_id');
    }

    public function revenueRecognitions(): HasMany
    {
        return $this->hasMany(RevenueRecognition::class, 'enrollment_id');
    }
}
