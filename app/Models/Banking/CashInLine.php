<?php

namespace App\Models\Banking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashInLine extends Model
{
    protected $fillable = [
        'cash_in_id',
        'account_id',
        'amount',
        'memo',
        'project_id',
        'fund_id',
        'dept_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function cashIn(): BelongsTo
    {
        return $this->belongsTo(CashIn::class, 'cash_in_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Accounting\Account::class, 'account_id');
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
}
