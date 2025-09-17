<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueRecognition extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'batch_id',
        'recognition_date',
        'amount',
        'type',
        'description',
        'journal_entry_id',
        'is_posted'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recognition_date' => 'date',
        'is_posted' => 'boolean',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }

    public function isDeferred(): bool
    {
        return $this->type === 'deferred';
    }

    public function isRecognized(): bool
    {
        return $this->type === 'recognized';
    }

    public function isReversed(): bool
    {
        return $this->type === 'reversed';
    }

    public function isPosted(): bool
    {
        return $this->is_posted;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function markAsPosted(string $journalEntryId = null): void
    {
        $this->update([
            'is_posted' => true,
            'journal_entry_id' => $journalEntryId,
        ]);
    }

    public function reverse(string $description = null): void
    {
        $this->update([
            'type' => 'reversed',
            'description' => $description ?: 'Revenue recognition reversed',
            'is_posted' => false,
            'journal_entry_id' => null,
        ]);
    }

    public function recognize(string $description = null): void
    {
        $this->update([
            'type' => 'recognized',
            'description' => $description ?: 'Revenue recognized',
        ]);
    }

    public static function createDeferredRevenue(Enrollment $enrollment, float $amount, string $description = null): self
    {
        return self::create([
            'enrollment_id' => $enrollment->id,
            'batch_id' => $enrollment->batch_id,
            'recognition_date' => $enrollment->enrollment_date,
            'amount' => $amount,
            'type' => 'deferred',
            'description' => $description ?: 'Deferred revenue from enrollment',
        ]);
    }

    public static function recognizeRevenue(CourseBatch $batch, float $amount, string $description = null): self
    {
        return self::create([
            'enrollment_id' => null, // Batch-level recognition
            'batch_id' => $batch->id,
            'recognition_date' => $batch->start_date,
            'amount' => $amount,
            'type' => 'recognized',
            'description' => $description ?: 'Revenue recognized for batch start',
        ]);
    }
}
