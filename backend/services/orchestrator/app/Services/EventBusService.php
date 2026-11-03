<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EventLog;
use App\Models\ProcessedEvent;
use App\Models\Subscription;
use Astacode\Kernel\Security\InternalRequestSigner;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventBusService
{
    private DealWonSagaService $sagaService;
    private AutomationEngineService $automationService;
    private ReadModelBuilderService $readModelService;

    public function __construct(
        ?DealWonSagaService $sagaService = null,
        ?AutomationEngineService $automationService = null,
        ?ReadModelBuilderService $readModelService = null
    ) {
        $this->sagaService = $sagaService ?? new DealWonSagaService();
        $this->automationService = $automationService ?? new AutomationEngineService();
        $this->readModelService = $readModelService ?? new ReadModelBuilderService();
    }

    /**
     * Ingests, logs, and processes an incoming domain event.
     */
    public function receiveEvent(
        string $eventName,
        array $payload,
        string $producerService,
        ?string $eventId = null,
        ?array $headers = []
    ): EventLog {
        $eventId = $eventId ?? (string) str()->uuid();

        // 1. Idempotency Check: prevent duplicate event processing
        $existing = EventLog::where('event_id', $eventId)->first();
        if ($existing) {
            Log::info("Event {$eventId} ({$eventName}) already processed. Returning existing record.");
            return $existing;
        }

        // 2. Validate against schema if definition file exists
        $this->validatePayloadSchema($eventName, $payload);

        // 3. Persist Event to Central Event Log
        $eventLog = EventLog::create([
            'event_id' => $eventId,
            'event_name' => $eventName,
            'producer_service' => $producerService,
            'aggregate_type' => $payload['aggregate_type'] ?? null,
            'aggregate_id' => (string) ($payload['aggregate_id'] ?? $payload['deal_id'] ?? $payload['lead_id'] ?? $payload['invoice_id'] ?? null),
            'payload' => $payload,
            'headers' => $headers,
            'status' => 'processing',
            'attempts' => 1,
            'received_at' => now(),
        ]);

        // 4. Update Read Model Projection
        try {
            $this->readModelService->project($eventName, $payload, $eventId);
        } catch (Exception $e) {
            Log::warning("Read model projection error for event {$eventName}: " . $e->getMessage());
        }

        // 5. Trigger Automation Rule Engine
        try {
            $this->automationService->trigger('event', $eventName, $payload);
        } catch (Exception $e) {
            Log::warning("Automation trigger error for event {$eventName}: " . $e->getMessage());
        }

        // 6. If deal.won, trigger Deal-Won Distributed Saga
        if ($eventName === 'deal.won') {
            try {
                $correlationId = $headers['X-Correlation-Id'] ?? null;
                $this->sagaService->start($payload, $correlationId);
            } catch (Exception $e) {
                Log::error("Failed to start Deal-Won Saga: " . $e->getMessage());
            }
        }

        // 7. Dispatch to Registered Subscriptions
        $dispatchedAll = $this->dispatchToConsumers($eventLog);

        $eventLog->update([
            'status' => $dispatchedAll ? 'completed' : 'failed',
            'processed_at' => now(),
        ]);

        return $eventLog->fresh();
    }

    /**
     * Dispatches event payload to all matching registered consumers.
     */
    public function dispatchToConsumers(EventLog $eventLog): bool
    {
        $subscriptions = Subscription::where('is_active', true)
            ->where(function ($q) use ($eventLog) {
                $q->where('event_name', $eventLog->event_name)
                  ->orWhere('event_name', '*');
            })
            ->get();

        if ($subscriptions->isEmpty()) {
            return true;
        }

        $allSuccess = true;

        foreach ($subscriptions as $sub) {
            $idempotencyKey = "{$eventLog->event_id}:{$sub->consumer_service}";

            // Check if already processed by this consumer
            $alreadyProcessed = ProcessedEvent::where('idempotency_key', $idempotencyKey)->exists();
            if ($alreadyProcessed) {
                continue;
            }

            $success = $this->deliverToConsumer($sub, $eventLog, $idempotencyKey);
            if (!$success) {
                $allSuccess = false;
            }
        }

        return $allSuccess;
    }

    /**
     * Deliver to consumer endpoint with retry and HMAC signing.
     */
    private function deliverToConsumer(Subscription $sub, EventLog $eventLog, string $idempotencyKey): bool
    {
        $payloadJson = json_encode([
            'event_id' => $eventLog->event_id,
            'event_name' => $eventLog->event_name,
            'producer' => $eventLog->producer_service,
            'payload' => $eventLog->payload,
            'timestamp' => now()->toIso8601String(),
        ]);

        $headers = InternalRequestSigner::sign(
            originService: 'orchestrator-service',
            body: $payloadJson,
            secret: null,
            correlationId: $eventLog->headers['X-Correlation-Id'] ?? null
        );
        $headers['Idempotency-Key'] = $idempotencyKey;
        $headers['Content-Type'] = 'application/json';

        $attempts = 0;
        $maxAttempts = $sub->max_retries;

        while ($attempts < $maxAttempts) {
            $attempts++;
            try {
                // In local sandbox or when target is stubbed
                if (app()->environment('testing') || str_starts_with($sub->target_endpoint, 'stub://')) {
                    // Record successful idempotent processed event
                    ProcessedEvent::create([
                        'event_id' => $eventLog->event_id,
                        'consumer_name' => $sub->consumer_service,
                        'idempotency_key' => $idempotencyKey,
                        'status' => 'SUCCESS',
                        'response_summary' => 'Stub delivery successful',
                        'processed_at' => now(),
                    ]);
                    return true;
                }

                $response = Http::withHeaders($headers)
                    ->timeout($sub->timeout_seconds)
                    ->post($sub->target_endpoint, $eventLog->payload);

                if ($response->successful()) {
                    ProcessedEvent::create([
                        'event_id' => $eventLog->event_id,
                        'consumer_name' => $sub->consumer_service,
                        'idempotency_key' => $idempotencyKey,
                        'status' => 'SUCCESS',
                        'response_summary' => substr($response->body(), 0, 500),
                        'processed_at' => now(),
                    ]);
                    return true;
                }
            } catch (Exception $e) {
                Log::warning("Delivery attempt {$attempts} to {$sub->consumer_service} failed: " . $e->getMessage());
            }

            // Exponential backoff wait (in seconds: 1, 2, 4...)
            if ($attempts < $maxAttempts && !app()->environment('testing')) {
                usleep((int) (pow(2, $attempts) * 100000));
            }
        }

        // Dead-letter reached
        ProcessedEvent::create([
            'event_id' => $eventLog->event_id,
            'consumer_name' => $sub->consumer_service,
            'idempotency_key' => $idempotencyKey,
            'status' => 'FAILED',
            'response_summary' => "Delivery failed after {$maxAttempts} attempts.",
            'processed_at' => now(),
        ]);

        $eventLog->update([
            'status' => 'dead_letter',
            'error_message' => "Dead-letter: failed delivering to {$sub->consumer_service}",
        ]);

        return false;
    }

    /**
     * Replays a failed or dead-letter event.
     */
    public function replayEvent(int $eventLogId): EventLog
    {
        $eventLog = EventLog::findOrFail($eventLogId);

        $eventLog->increment('attempts');
        $eventLog->update(['status' => 'processing', 'error_message' => null]);

        $dispatched = $this->dispatchToConsumers($eventLog);

        $eventLog->update([
            'status' => $dispatched ? 'completed' : 'dead_letter',
            'processed_at' => now(),
        ]);

        return $eventLog->fresh();
    }

    /**
     * Validates event payload against JSON schema if defined.
     */
    private function validatePayloadSchema(string $eventName, array $payload): void
    {
        $schemaPath = config_path("events/{$eventName}.json");
        if (!file_exists($schemaPath)) {
            return;
        }

        $schema = json_decode(file_get_contents($schemaPath), true);
        if (!$schema || !isset($schema['required'])) {
            return;
        }

        foreach ($schema['required'] as $requiredField) {
            if (!array_key_exists($requiredField, $payload)) {
                throw new Exception("Event payload {$eventName} missing required schema field '{$requiredField}'.");
            }
        }
    }
}
