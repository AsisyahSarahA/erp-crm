<?php

declare(strict_types=1);

namespace Astacode\Kernel\Outbox;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * Model OutboxEvent
 * Persists transactional outbox domain events to guarantee at-least-once asynchronous event delivery.
 */
class OutboxEvent extends Model
{
    use HasUlids;

    protected $table = 'outbox_events';

    protected $fillable = [
        'event_name',
        'aggregate_type',
        'aggregate_id',
        'payload',
        'idempotency_key',
        'status',
        'attempts',
        'error_message',
        'available_at',
        'published_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'available_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_DEAD_LETTER = 'DEAD_LETTER';
}

