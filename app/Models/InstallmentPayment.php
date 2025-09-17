<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InstallmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'paid_amount',
        'late_fee',
        'status',
        'notes',
        'payment_method',
        'reference_number'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function isOverdue(): bool
    {
        return $this->due_date < Carbon::today() && $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return Carbon::today()->diffInDays($this->due_date);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->late_fee;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return $this->paid_amount ? 'Rp ' . number_format($this->paid_amount, 0, ',', '.') : '-';
    }

    public function getFormattedLateFeeAttribute(): string
    {
        return $this->late_fee > 0 ? 'Rp ' . number_format($this->late_fee, 0, ',', '.') : '-';
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function markAsPaid(float $paidAmount, string $paymentMethod = null, string $referenceNumber = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => Carbon::today(),
            'paid_amount' => $paidAmount,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
        ]);
    }

    public function calculateLateFee(float $lateFeePercentage): float
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $daysOverdue = $this->days_overdue;
        return ($this->amount * $lateFeePercentage / 100) * $daysOverdue;
    }

    public function updateLateFee(float $lateFeePercentage): void
    {
        $lateFee = $this->calculateLateFee($lateFeePercentage);
        $this->update(['late_fee' => $lateFee]);

        if ($lateFee > 0 && $this->status === 'pending') {
            $this->update(['status' => 'overdue']);
        }
    }
}
