<?php

declare(strict_types=1);

namespace Astacode\Kernel\Outbox;

/**
 * Trait HasOutbox
 * Allows Eloquent models or service classes to easily record domain events to the outbox table.
 */
trait HasOutbox
{
    /**
     * Records an outbox event within current database transaction.
     */
    public function recordEvent(
        string $eventName,
        array $payload,
        ?string $aggregateType = null,
        ?string $aggregateId = null,
        ?string $idempotencyKey = null
    ): OutboxEvent {
        $aggType = $aggregateType ?? (property_exists($this, 'table') ? $this->table : class_basename(static::class));
        $aggId = $aggregateId ?? ($this->getKey() ? (string) $this->getKey() : (string) str()->uuid());
        $idempKey = $idempotencyKey ?? hash('sha256', "{$eventName}:{$aggType}:{$aggId}:" . microtime(true));

        return OutboxEvent::create([
            'event_name' => $eventName,
            'aggregate_type' => $aggType,
            'aggregate_id' => $aggId,
            'payload' => $payload,
            'idempotency_key' => $idempKey,
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
        ]);
    }
}

