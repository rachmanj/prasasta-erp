<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_no',
        'date',
        'reason',
        'total_adjustment_value',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'project_id',
        'fund_id',
        'dept_id',
    ];

    protected $casts = [
        'date' => 'date',
        'total_adjustment_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(StockAdjustmentLine::class, 'adjustment_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Project::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Fund::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dimensions\Department::class, 'dept_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
