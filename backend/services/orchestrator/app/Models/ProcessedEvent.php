<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'consumer_name',
        'idempotency_key',
        'status',
        'response_summary',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}

