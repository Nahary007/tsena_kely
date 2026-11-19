<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelemetryLog extends Model
{
    // Logs structures pour tracer les flux des requetes
    public $timestamps = false;

    protected $fillable = [
        'trace_id',
        'span_id',
        'service',
        'level',
        'message',
        'context',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];
}
