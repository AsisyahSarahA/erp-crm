<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    /**
     * List all configured outbound webhooks.
     */
    public function index(): JsonResponse
    {
        $webhooks = Webhook::withCount('deliveries')->get();
        return ApiResponse::success($webhooks);
    }

    /**
     * Register a new webhook endpoint.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'secret' => 'nullable|string|min:16',
        ]);

        $webhook = Webhook::create([
            'name' => $validated['name'],
            'target_url' => $validated['target_url'],
            'events' => $validated['events'],
            'secret' => $validated['secret'] ?? Str::random(32),
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'webhook.registered',
            'entity_type' => 'webhook',
            'entity_id' => (string) $webhook->id,
            'notes' => "Webhook {$webhook->name} didaftarkan ke {$webhook->target_url}.",
        ]);

        return ApiResponse::success($webhook, 'Webhook berhasil didaftarkan.', 201);
    }

    /**
     * List delivery attempts for a webhook.
     */
    public function deliveries(int $id): JsonResponse
    {
        $webhook = Webhook::find($id);
        if (!$webhook) {
            return ApiResponse::rfcProblem('Webhook Not Found', 404);
        }

        $deliveries = $webhook->deliveries()->latest()->paginate(25);
        return ApiResponse::paginated($deliveries);
    }

    /**
     * Replay a failed webhook delivery attempt.
     */
    public function replay(int $id, int $deliveryId): JsonResponse
    {
        $webhook = Webhook::find($id);
        $delivery = WebhookDelivery::where('webhook_id', $id)->find($deliveryId);

        if (!$webhook || !$delivery) {
            return ApiResponse::rfcProblem('Record Not Found', 404);
        }

        $payloadJson = json_encode($delivery->payload);
        $signature = hash_hmac('sha256', $payloadJson, $webhook->secret);

        try {
            $response = Http::withHeaders([
                'X-Astacode-Signature' => $signature,
                'X-Astacode-Event' => $delivery->event_name,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($webhook->target_url, $delivery->payload);

            $status = $response->successful() ? 'delivered' : 'failed';

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 1000),
                'status' => $status,
                'attempts' => $delivery->attempts + 1,
            ]);

            return ApiResponse::success($delivery, 'Replay berhasil dieksekusi.');
        } catch (\Exception $e) {
            $delivery->update([
                'response_status' => 500,
                'response_body' => $e->getMessage(),
                'status' => 'failed',
                'attempts' => $delivery->attempts + 1,
            ]);

            return ApiResponse::rfcProblem('Delivery Replay Failed', 502, $e->getMessage());
        }
    }

    /**
     * Inbound webhook receiver (WhatsApp, Payment Gateway, Git, etc.).
     */
    public function handleIncoming(Request $request, string $provider): JsonResponse
    {
        // Replay protection: check timestamp header if provided
        $timestamp = $request->header('X-Timestamp');
        if ($timestamp && abs(time() - (int) $timestamp) > 300) {
            return ApiResponse::rfcProblem('Replay Attack Detected', 400, 'Timestamp request webhook terlalu lampau (>5 menit).');
        }

        AuditLog::create([
            'user_id' => 'system',
            'service' => 'core-service',
            'action' => "webhook.inbound.{$provider}",
            'entity_type' => 'webhook_inbound',
            'new_values' => ['provider' => $provider, 'payload' => $request->all()],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Inbound webhook diterima dari provider {$provider}.",
        ]);

        return ApiResponse::success([
            'provider' => $provider,
            'status' => 'received',
            'received_at' => now()->toIso8601String(),
        ]);
    }
}

