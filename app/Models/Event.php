<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'date',
        'starts_at',
        'ends_at',
        'description',
        'target_year_level',
        'venue',
        'latitude',
        'longitude',
        'radius_meters',
    ];

    protected $casts = [
        'date' => 'date',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function activeSession()
    {
        return $this->sessions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
