<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseBatch extends Model
{
    protected $fillable = [
        'course_id',
        'batch_code',
        'start_date',
        'end_date',
        'schedule',
        'location',
        'trainer_id',
        'capacity',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'schedule' => 'array',
        'capacity' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'batch_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function getEnrollmentCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->capacity - $this->enrollment_count;
    }
}
