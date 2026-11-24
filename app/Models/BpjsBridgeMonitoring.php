<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsBridgeMonitoring extends Model
{
    protected $fillable = [
        'service',
        'endpoint',
        'status',
        'bpjs_code',
        'message',
        'response_time_ms',
        'payload_count',
        'error_detail',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'response_time_ms' => 'integer',
        'payload_count' => 'integer',
    ];
}