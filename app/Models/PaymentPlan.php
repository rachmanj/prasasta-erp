<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'installment_count',
        'installment_interval_days',
        'down_payment_percentage',
        'late_fee_percentage',
        'grace_period_days',
        'is_active'
    ];

    protected $casts = [
        'installment_count' => 'integer',
        'installment_interval_days' => 'integer',
        'down_payment_percentage' => 'decimal:2',
        'late_fee_percentage' => 'decimal:2',
        'grace_period_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'payment_plan_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function calculateDownPaymentAmount(float $totalAmount): float
    {
        if (!$this->down_payment_percentage) {
            return 0;
        }
        return ($totalAmount * $this->down_payment_percentage) / 100;
    }

    public function calculateInstallmentAmount(float $totalAmount): float
    {
        $downPayment = $this->calculateDownPaymentAmount($totalAmount);
        $remainingAmount = $totalAmount - $downPayment;
        return $remainingAmount / $this->installment_count;
    }

    public function getFormattedDownPaymentPercentageAttribute(): string
    {
        return $this->down_payment_percentage ? $this->down_payment_percentage . '%' : 'No DP';
    }

    public function getFormattedLateFeePercentageAttribute(): string
    {
        return $this->late_fee_percentage ? $this->late_fee_percentage . '%' : 'No Late Fee';
    }

    public function getActiveEnrollmentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }
}
