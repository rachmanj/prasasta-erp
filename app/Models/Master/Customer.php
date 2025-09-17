<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'npwp',
        'address',
        'student_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'student_status',
        'enrollment_count',
        'total_paid'
    ];

    protected $casts = [
        'enrollment_count' => 'integer',
        'total_paid' => 'decimal:2',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'student_id');
    }

    public function isStudent(): bool
    {
        return !is_null($this->student_id);
    }

    public function getActiveEnrollmentsAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->get();
    }
}
