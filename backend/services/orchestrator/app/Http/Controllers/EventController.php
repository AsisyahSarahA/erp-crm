<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventLog;
use App\Services\EventBusService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    private EventBusService $eventBus;

    public function __construct(EventBusService $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * Ingestion endpoint for internal microservice domain events.
     * POST /internal/events
     */
    public function receive(Request $request): JsonResponse
    {
        $request->validate([
            'event_name' => 'required|string',
            'payload' => 'required|array',
            'producer_service' => 'nullable|string',
            'event_id' => 'nullable|string',
        ]);

        $producer = $request->producer_service ?? $request->header('X-Service-Name') ?? 'unknown';
        $eventId = $request->event_id ?? $request->header('X-Event-Id');

        $headers = [
            'X-Correlation-Id' => $request->header('X-Correlation-Id'),
            'X-Service-Name' => $producer,
            'Idempotency-Key' => $request->header('Idempotency-Key'),
        ];

        try {
            $eventLog = $this->eventBus->receiveEvent(
                eventName: $request->event_name,
                payload: $request->payload,
                producerService: $producer,
                eventId: $eventId,
                headers: $headers
            );

            return ApiResponse::success([
                'event_id' => $eventLog->event_id,
                'event_name' => $eventLog->event_name,
                'status' => $eventLog->status,
                'received_at' => $eventLog->received_at->toIso8601String(),
            ], 'Event diterima dan diproses oleh Orchestrator.', 200);
        } catch (Exception $e) {
            return ApiResponse::rfcProblem(
                title: 'Event Processing Failed',
                status: 422,
                detail: $e->getMessage()
            );
        }
    }

    /**
     * List central event logs with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EventLog::query();

        if ($request->filled('event_name')) {
            $query->where('event_name', $request->event_name);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('producer')) {
            $query->where('producer_service', $request->producer);
        }

        $perPage = min((int) $request->get('per_page', 25), 100);
        $logs = $query->latest('received_at')->paginate($perPage);

        return ApiResponse::paginated($logs);
    }

    /**
     * View detailed event log.
     */
    public function show(int $id): JsonResponse
    {
        $eventLog = EventLog::find($id);
        if (!$eventLog) {
            return ApiResponse::rfcProblem('Event Not Found', 404);
        }

        return ApiResponse::success($eventLog);
    }

    /**
     * Replay a failed or dead-letter event.
     */
    public function replay(int $id): JsonResponse
    {
        try {
            $replayed = $this->eventBus->replayEvent($id);
            return ApiResponse::success($replayed, 'Event berhasil di-replay ke konsumen terdaftar.');
        } catch (Exception $e) {
            return ApiResponse::rfcProblem('Replay Failed', 422, $e->getMessage());
        }
    }

    /**
     * List dead-letter events.
     */
    public function deadLetters(Request $request): JsonResponse
    {
        $deadLetters = EventLog::where('status', 'dead_letter')
            ->latest('received_at')
            ->paginate(25);

        return ApiResponse::paginated($deadLetters);
    }
}

