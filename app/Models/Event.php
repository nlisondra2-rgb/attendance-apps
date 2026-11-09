<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Event extends Model
{
    protected $fillable = [
        'name',
        'status',
        'date',
        'starts_at',
        'ends_at',
        'grace_period_minutes',
        'fine_amount',
        'fine_policy',
        'description',
        'target_year_level',
        'target_course',
        'venue',
        'latitude',
        'longitude',
        'radius_meters',
    ];

    protected $casts = [
        'date'                 => 'date',
        'starts_at'            => 'datetime',
        'ends_at'              => 'datetime',
        'grace_period_minutes' => 'integer',
        'fine_amount'          => 'decimal:2',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function excuses()
    {
        return $this->hasMany(FineExcuse::class);
    }

    public function activeSession()
    {
        return $this->sessions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Calculate check-in deadline = starts_at + grace_period_minutes.
     */
    public function checkInDeadline(): ?Carbon
    {
        if (! $this->starts_at) {
            return null;
        }

        return $this->starts_at->copy()->addMinutes($this->grace_period_minutes ?? 30);
    }

    /**
     * Determine if attendance is strictly open right now (or at given carbon time).
     */
    public function isCheckInOpen(?Carbon $time = null): bool
    {
        $now = $time ?? now();

        if ($this->status !== 'published') {
            return false;
        }

        if (! $this->starts_at) {
            return false;
        }

        $deadline = $this->checkInDeadline();

        // Must be on or after starts_at AND on or before deadline
        return $now->greaterThanOrEqualTo($this->starts_at) && $now->lessThanOrEqualTo($deadline);
    }

    /**
     * Check student eligibility based on target_year_level and target_course.
     */
    public function isStudentEligible(Student $student): bool
    {
        // Year level check
        if ($this->target_year_level && strtolower($this->target_year_level) !== 'all') {
            if (trim($student->year_level ?? '') !== trim($this->target_year_level)) {
                return false;
            }
        }

        // Course check
        if ($this->target_course && strtolower($this->target_course) !== 'all') {
            if (trim($student->course ?? '') !== trim($this->target_course)) {
                return false;
            }
        }

        return true;
    }
}
