<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'event_name',
        'consumer_service',
        'target_endpoint',
        'is_active',
        'max_retries',
        'timeout_seconds',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_retries' => 'integer',
        'timeout_seconds' => 'integer',
    ];
}

