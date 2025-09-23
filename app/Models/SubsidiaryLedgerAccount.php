<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SubsidiaryLedgerAccount extends Model
{
    protected $fillable = [
        'control_account_id',
        'subsidiary_code',
        'name',
        'subsidiary_type',
        'opening_balance',
        'current_balance',
        'last_transaction_date',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_transaction_date' => 'date',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function controlAccount(): BelongsTo
    {
        return $this->belongsTo(ControlAccount::class);
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(\App\Models\Accounting\JournalLine::class, 'account_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByControlAccount(Builder $query, int $controlAccountId): Builder
    {
        return $query->where('control_account_id', $controlAccountId);
    }

    public function scopeBySubsidiaryType(Builder $query, string $subsidiaryType): Builder
    {
        return $query->where('subsidiary_type', $subsidiaryType);
    }

    // Helper Methods
    public function getCurrentBalance(): float
    {
        return $this->current_balance;
    }

    public function getLastTransactionDate(): ?string
    {
        return $this->last_transaction_date ? $this->last_transaction_date->format('Y-m-d') : null;
    }

    public function updateBalance(float $newBalance): void
    {
        $this->update([
            'current_balance' => $newBalance,
            'last_transaction_date' => now(),
        ]);
    }

    public function addToBalance(float $amount): void
    {
        $this->updateBalance($this->current_balance + $amount);
    }

    public function subtractFromBalance(float $amount): void
    {
        $this->updateBalance($this->current_balance - $amount);
    }

    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }

    public function getDisplayName(): string
    {
        return "{$this->subsidiary_code} - {$this->name}";
    }

    public function getBalanceFormatted(): string
    {
        return number_format($this->current_balance, 2);
    }

    public function hasTransactions(): bool
    {
        return $this->last_transaction_date !== null;
    }

    public function getDaysSinceLastTransaction(): ?int
    {
        if (!$this->last_transaction_date) {
            return null;
        }

        return $this->last_transaction_date->diffInDays(now());
    }
}
