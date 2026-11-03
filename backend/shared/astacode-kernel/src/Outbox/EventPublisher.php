<?php

declare(strict_types=1);

namespace Astacode\Kernel\Outbox;

use Astacode\Kernel\Security\InternalRequestSigner;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class EventPublisher
 * Publishes events either to the local Outbox table or directly dispatches to Orchestrator via HTTP.
 */
final class EventPublisher
{
    /**
     * Records event into local transactional outbox.
     */
    public static function toOutbox(
        string $eventName,
        array $payload,
        string $aggregateType,
        string $aggregateId,
        ?string $idempotencyKey = null
    ): OutboxEvent {
        $idempKey = $idempotencyKey ?? hash('sha256', "{$eventName}:{$aggregateType}:{$aggregateId}:" . microtime(true));

        return OutboxEvent::create([
            'event_name' => $eventName,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'payload' => $payload,
            'idempotency_key' => $idempKey,
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
        ]);
    }

    /**
     * Relays an outbox event to Orchestrator service via signed HTTP.
     */
    public static function sendToOrchestrator(OutboxEvent $event, ?string $orchestratorUrl = null): bool
    {
        $url = $orchestratorUrl ?? env('ORCHESTRATOR_SERVICE_URL', 'http://127.0.0.1:8008');
        $endpoint = rtrim($url, '/') . '/internal/events';

        $origin = config('app.name', 'service');
        $body = json_encode([
            'event_id' => $event->id,
            'event_name' => $event->event_name,
            'aggregate_type' => $event->aggregate_type,
            'aggregate_id' => $event->aggregate_id,
            'payload' => $event->payload,
            'idempotency_key' => $event->idempotency_key,
            'occurred_at' => $event->created_at->toIso8601String(),
        ], JSON_THROW_ON_ERROR);

        $headers = InternalRequestSigner::sign($origin, $body);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->withBody($body, 'application/json')
                ->post($endpoint);

            if ($response->successful()) {
                $event->update([
                    'status' => OutboxEvent::STATUS_PUBLISHED,
                    'published_at' => now(),
                ]);
                return true;
            }

            $event->increment('attempts');
            $event->update([
                'error_message' => "HTTP {$response->status()}: " . substr($response->body(), 0, 500),
                'available_at' => now()->addSeconds(pow(2, min($event->attempts, 6)) * 5),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("[EventPublisher] Failed to send event {$event->event_name} to orchestrator: " . $e->getMessage());
            $event->increment('attempts');
            $event->update([
                'error_message' => $e->getMessage(),
                'available_at' => now()->addSeconds(pow(2, min($event->attempts, 6)) * 5),
            ]);

            return false;
        }
    }
}

