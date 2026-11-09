<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'event_id',
        'session_id',
        'checked_in_at',
        'status',
        'student_lat',
        'student_lng',
        'distance_meters',
        'excuse_reason',
        'corrected_by_user_id',
        'corrected_at',
        'correction_reason',
    ];

    protected $casts = [
        'checked_in_at'   => 'datetime',
        'corrected_at'    => 'datetime',
        'student_lat'     => 'float',
        'student_lng'     => 'float',
        'distance_meters' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by_user_id');
    }
}
