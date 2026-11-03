<?php

declare(strict_types=1);

namespace Astacode\Kernel\Services;

use Astacode\Kernel\Security\InternalRequestSigner;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class NumberingClient
 * HTTP client for generating sequential, atomic document numbers from core-service.
 */
class NumberingClient
{
    protected string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? env('CORE_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
    }

    /**
     * Generates next document code atomically.
     * @param string $type Type slug (e.g., 'lead', 'quotation', 'invoice', 'mou')
     */
    public function next(string $type, ?string $companyId = null, array $context = []): string
    {
        $endpoint = "{$this->baseUrl}/internal/numbering/next";
        $payload = [
            'type' => $type,
            'company_id' => $companyId,
            'context' => $context,
        ];
        $body = json_encode($payload);
        $headers = InternalRequestSigner::sign(config('app.name', 'service'), $body);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->withBody($body, 'application/json')
                ->post($endpoint);

            if ($response->successful()) {
                return (string) $response->json('data.code');
            }

            Log::error("[NumberingClient] Failed to fetch number for {$type}: HTTP " . $response->status());
        } catch (Exception $e) {
            Log::error("[NumberingClient] Exception fetching number for {$type}: " . $e->getMessage());
        }

        // Fallback generator for dev/offline resilience
        $year = date('Y');
        $random = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return strtoupper("{$type}-{$year}-{$random}");
    }
}

