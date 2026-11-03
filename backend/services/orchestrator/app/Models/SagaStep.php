<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SagaStep extends Model
{
    protected $fillable = [
        'saga_id',
        'step_order',
        'step_name',
        'target_service',
        'status',
        'idempotency_key',
        'attempts',
        'max_attempts',
        'request_payload',
        'response_payload',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'executed_at' => 'datetime',
    ];

    public function saga(): BelongsTo
    {
        return $this->belongsTo(Saga::class);
    }
}

