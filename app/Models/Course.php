<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'duration_hours',
        'capacity',
        'base_price',
        'status'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'duration_hours' => 'integer',
        'capacity' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class, 'course_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasManyThrough(Enrollment::class, CourseBatch::class, 'course_id', 'batch_id');
    }
}
