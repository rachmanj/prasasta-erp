<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ControlAccount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'control_type',
        'is_active',
        'reconciliation_frequency',
        'tolerance_amount',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tolerance_amount' => 'decimal:2',
    ];

    // Relationships
    public function subsidiaryAccounts(): HasMany
    {
        return $this->hasMany(SubsidiaryLedgerAccount::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(ControlAccountBalance::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByControlType(Builder $query, string $controlType): Builder
    {
        return $query->where('control_type', $controlType);
    }

    // Helper Methods
    public function getCurrentBalance(): float
    {
        $latestBalance = $this->balances()
            ->orderBy('period', 'desc')
            ->first();

        return $latestBalance ? $latestBalance->calculated_balance : 0.00;
    }

    public function getSubsidiaryTotal(): float
    {
        return $this->subsidiaryAccounts()
            ->where('is_active', true)
            ->sum('current_balance');
    }

    public function calculateVariance(): float
    {
        return $this->getCurrentBalance() - $this->getSubsidiaryTotal();
    }

    public function hasVariance(): bool
    {
        return abs($this->calculateVariance()) > $this->tolerance_amount;
    }

    public function getReconciliationStatus(): string
    {
        $latestBalance = $this->balances()
            ->orderBy('period', 'desc')
            ->first();

        return $latestBalance ? $latestBalance->reconciliation_status : 'pending';
    }

    public function getActiveSubsidiaryCount(): int
    {
        return $this->subsidiaryAccounts()->where('is_active', true)->count();
    }

    public function getLastReconciliationDate(): ?string
    {
        $latestBalance = $this->balances()
            ->where('reconciliation_status', 'reconciled')
            ->orderBy('reconciled_at', 'desc')
            ->first();

        return $latestBalance && $latestBalance->reconciled_at ? $latestBalance->reconciled_at->format('Y-m-d') : null;
    }
}
