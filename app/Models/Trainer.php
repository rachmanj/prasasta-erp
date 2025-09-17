<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'type',
        'specialization',
        'qualifications',
        'hourly_rate',
        'batch_rate',
        'revenue_share_percentage',
        'bank_account',
        'tax_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'batch_rate' => 'decimal:2',
        'revenue_share_percentage' => 'decimal:2',
    ];

    public function courseBatches(): HasMany
    {
        return $this->hasMany(CourseBatch::class, 'trainer_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInternal(): bool
    {
        return $this->type === 'internal';
    }

    public function isExternal(): bool
    {
        return $this->type === 'external';
    }

    public function getFormattedHourlyRateAttribute(): string
    {
        return $this->hourly_rate ? 'Rp ' . number_format($this->hourly_rate, 0, ',', '.') : '-';
    }

    public function getFormattedBatchRateAttribute(): string
    {
        return $this->batch_rate ? 'Rp ' . number_format($this->batch_rate, 0, ',', '.') : '-';
    }

    public function getActiveBatchesCountAttribute(): int
    {
        return $this->courseBatches()->whereIn('status', ['planned', 'ongoing'])->count();
    }
}
