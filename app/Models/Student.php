<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'course',
        'year_level',
        'password',
        'school_qr_raw',
    ];

    protected $hidden = [
        'password',
    ];

    public function getRouteKeyName(): string
    {
        return 'student_id';
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function excuses(): HasMany
    {
        return $this->hasMany(FineExcuse::class);
    }
}
