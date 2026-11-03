<?php

declare(strict_types=1);

namespace Astacode\Kernel\Services;

use Astacode\Kernel\Security\InternalRequestSigner;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AuditClient
 * HTTP client for dispatching immutable audit logs to core-service.
 */
class AuditClient
{
    protected string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? env('CORE_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
    }

    public function log(
        string $action,
        string $entityType,
        string $entityId,
        array $oldValues = [],
        array $newValues = [],
        ?string $notes = null,
        ?string $userId = null
    ): bool {
        $endpoint = "{$this->baseUrl}/internal/audit-logs";
        $payload = [
            'service' => config('app.name', 'service'),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $userId ?? request()->attributes->get('current_user_id'),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'correlation_id' => request()->header('X-Correlation-Id'),
            'occurred_at' => now()->toIso8601String(),
        ];

        $body = json_encode($payload);
        $headers = InternalRequestSigner::sign(config('app.name', 'service'), $body);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->withBody($body, 'application/json')
                ->post($endpoint);

            return $response->successful();
        } catch (Exception $e) {
            Log::warning("[AuditClient] Failed to dispatch audit log: " . $e->getMessage());
            return false;
        }
    }
}

