<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineExcuse extends Model
{
    protected $fillable = [
        'fine_id',
        'student_id',
        'event_id',
        'reason',
        'document_path',
        'status',
        'reviewer_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function fine()
    {
        return $this->belongsTo(Fine::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
