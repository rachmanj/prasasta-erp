<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    protected $fillable = [
        'journal_id',
        'account_id',
        'debit',
        'credit',
        'project_id',
        'fund_id',
        'dept_id',
        'memo',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // Relationships
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
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
