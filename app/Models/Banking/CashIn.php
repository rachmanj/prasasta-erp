<?php

namespace App\Models\Banking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashIn extends Model
{
    protected $fillable = [
        'voucher_number',
        'date',
        'description',
        'cash_account_id',
        'total_amount',
        'status',
        'created_by',
        'project_id',
        'fund_id',
        'dept_id',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function lines(): HasMany
    {
        return $this->hasMany(CashInLine::class, 'cash_in_id');
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Accounting\Account::class, 'cash_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Project::class, 'project_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Fund::class, 'fund_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Department::class, 'dept_id');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    // Helper methods
    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }
}
