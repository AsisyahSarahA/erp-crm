<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_id',
        'event_name',
        'payload',
        'response_status',
        'response_body',
        'status',
        'attempts',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_status' => 'integer',
        'attempts' => 'integer',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}

