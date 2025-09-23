<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ControlAccountBalance extends Model
{
    protected $fillable = [
        'control_account_id',
        'period',
        'opening_balance',
        'total_debits',
        'total_credits',
        'calculated_balance',
        'subsidiary_total',
        'variance_amount',
        'reconciliation_status',
        'reconciled_at',
        'reconciled_by',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'calculated_balance' => 'decimal:2',
        'subsidiary_total' => 'decimal:2',
        'variance_amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    // Relationships
    public function controlAccount(): BelongsTo
    {
        return $this->belongsTo(ControlAccount::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    // Scopes
    public function scopeByPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    public function scopeReconciled(Builder $query): Builder
    {
        return $query->where('reconciliation_status', 'reconciled');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('reconciliation_status', 'pending');
    }

    public function scopeWithVariance(Builder $query): Builder
    {
        return $query->where('reconciliation_status', 'variance');
    }

    public function scopeByControlAccount(Builder $query, int $controlAccountId): Builder
    {
        return $query->where('control_account_id', $controlAccountId);
    }

    // Helper Methods
    public function calculateVariance(): float
    {
        return $this->calculated_balance - $this->subsidiary_total;
    }

    public function isReconciled(): bool
    {
        return $this->reconciliation_status === 'reconciled';
    }

    public function hasVariance(): bool
    {
        return $this->reconciliation_status === 'variance';
    }

    public function getVariancePercentage(): float
    {
        if ($this->calculated_balance == 0) {
            return 0;
        }

        return ($this->variance_amount / abs($this->calculated_balance)) * 100;
    }

    public function getVarianceFormatted(): string
    {
        return number_format($this->variance_amount, 2);
    }

    public function getCalculatedBalanceFormatted(): string
    {
        return number_format($this->calculated_balance, 2);
    }

    public function getSubsidiaryTotalFormatted(): string
    {
        return number_format($this->subsidiary_total, 2);
    }

    public function getReconciliationStatusBadge(): string
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'reconciled' => '<span class="badge badge-success">Reconciled</span>',
            'variance' => '<span class="badge badge-danger">Variance</span>',
        ];

        return $badges[$this->reconciliation_status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    public function getPeriodFormatted(): string
    {
        $date = \DateTime::createFromFormat('Y-m', $this->period);
        return $date ? $date->format('F Y') : $this->period;
    }

    public function getReconciledDateFormatted(): ?string
    {
        return $this->reconciled_at ? $this->reconciled_at->format('Y-m-d H:i') : null;
    }

    public function getReconciledByFormatted(): ?string
    {
        return $this->reconciledBy ? $this->reconciledBy->name : null;
    }

    public function markAsReconciled(int $userId, string $notes = null): void
    {
        $this->update([
            'reconciliation_status' => 'reconciled',
            'reconciled_at' => now(),
            'reconciled_by' => $userId,
            'notes' => $notes,
        ]);
    }

    public function markAsVariance(string $notes = null): void
    {
        $this->update([
            'reconciliation_status' => 'variance',
            'notes' => $notes,
        ]);
    }

    public function getNetMovement(): float
    {
        return $this->total_debits - $this->total_credits;
    }

    public function getNetMovementFormatted(): string
    {
        return number_format($this->getNetMovement(), 2);
    }
}
