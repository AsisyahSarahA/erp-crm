<?php

declare(strict_types=1);

namespace Astacode\Kernel\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model IdempotencyKey
 * Persists idempotency tokens to guarantee at-most-once execution for critical POST/PUT mutations.
 */
class IdempotencyKey extends Model
{
    protected $table = 'idempotency_keys';

    protected $fillable = [
        'key',
        'route',
        'request_hash',
        'status',
        'response_code',
        'response_body',
        'user_id',
        'locked_at',
    ];

    protected $casts = [
        'response_code' => 'integer',
        'response_body' => 'array',
        'locked_at' => 'datetime',
    ];

    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';
}

