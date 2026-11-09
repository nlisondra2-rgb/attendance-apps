<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'fine_id',
        'action',
        'performed_by_type',
        'performed_by_id',
        'performed_by_name',
        'details',
        'created_at',
    ];

    protected $casts = [
        'details'    => 'array',
        'created_at' => 'datetime',
    ];

    public function fine()
    {
        return $this->belongsTo(Fine::class);
    }
}
