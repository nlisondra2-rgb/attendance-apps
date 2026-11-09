<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = [
        'reference_no',
        'student_id',
        'event_id',
        'attendance_id',
        'amount',
        'balance',
        'reason',
        'issue_date',
        'status',
        'review_status',
        'payment_status',
        'paid_at',
        'waived_at',
        'waiver_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'balance'     => 'decimal:2',
        'issue_date'  => 'date',
        'paid_at'     => 'datetime',
        'waived_at'   => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function excuses()
    {
        return $this->hasMany(FineExcuse::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(FineAuditLog::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Record an audit log entry for this fine.
     */
    public function recordAudit(string $action, string $byType = 'system', ?int $byId = null, ?string $byName = null, ?array $details = null): FineAuditLog
    {
        return $this->auditLogs()->create([
            'action'             => $action,
            'performed_by_type'  => $byType,
            'performed_by_id'    => $byId,
            'performed_by_name'  => $byName,
            'details'            => $details,
            'created_at'         => now(),
        ]);
    }
}
