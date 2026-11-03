<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Security\InternalRequestSigner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class ProxyController
 * Reverse proxy forwarding /api/v1/{service}/* requests to isolated microservices.
 * Injects HMAC signatures, correlation IDs, and resolved user credentials.
 */
class ProxyController extends Controller
{
    public function proxy(Request $request, string $service, ?string $path = ''): Response|JsonResponse
    {
        $services = config('services_map', []);

        if (!array_key_exists($service, $services)) {
            return ApiResponse::rfcProblem(
                title: 'Service Not Found',
                status: 404,
                detail: "The microservice '{$service}' is not registered in Gateway service map.",
                type: 'https://astacode.com/errors/unknown-service'
            );
        }

        $serviceConfig = $services[$service];
        $targetBaseUrl = rtrim($serviceConfig['url'], '/');
        $cleanPath = ltrim((string) $path, '/');
        $targetUrl = "{$targetBaseUrl}/api/{$cleanPath}";

        if ($request->getQueryString()) {
            $targetUrl .= '?' . $request->getQueryString();
        }

        $method = strtoupper($request->method());
        $body = $request->getContent();
        $correlationId = $request->header('X-Correlation-Id') ?? (string) str()->uuid();

        // Sign request with internal HMAC
        $internalHeaders = InternalRequestSigner::sign('gateway', $body, null, $correlationId);

        // Forward authenticated user context if logged in
        $user = $request->user();
        if ($user) {
            $userData = [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'permissions' => method_exists($user, 'getAllPermissions') 
                    ? $user->getAllPermissions()->pluck('name')->toArray() 
                    : ($user->permissions ?? []),
            ];
            $internalHeaders['X-Internal-User'] = base64_encode(json_encode($userData));
        }

        // Pass client IP and User Agent
        $internalHeaders['X-Forwarded-For'] = $request->ip();
        $internalHeaders['User-Agent'] = $request->userAgent();

        // Forward Idempotency-Key if present
        if ($request->hasHeader('Idempotency-Key')) {
            $internalHeaders['Idempotency-Key'] = $request->header('Idempotency-Key');
        }

        $timeout = $serviceConfig['timeout'] ?? 15;

        try {
            $client = Http::withHeaders($internalHeaders)
                ->timeout($timeout);

            // Forward content type
            if ($request->header('Content-Type')) {
                $client = $client->contentType($request->header('Content-Type'));
            }

            $response = match ($method) {
                'GET' => $client->get($targetUrl),
                'POST' => $client->withBody($body, $request->header('Content-Type') ?? 'application/json')->post($targetUrl),
                'PUT' => $client->withBody($body, $request->header('Content-Type') ?? 'application/json')->put($targetUrl),
                'PATCH' => $client->withBody($body, $request->header('Content-Type') ?? 'application/json')->patch($targetUrl),
                'DELETE' => $client->withBody($body, $request->header('Content-Type') ?? 'application/json')->delete($targetUrl),
                default => $client->send($method, $targetUrl),
            };

            return response(
                $response->body(),
                $response->status(),
                [
                    'Content-Type' => $response->header('Content-Type') ?? 'application/json',
                    'X-Correlation-Id' => $correlationId,
                    'X-Proxied-By' => 'astacode-gateway',
                ]
            );
        } catch (Exception $e) {
            Log::error("[Gateway] Error proxying request to {$targetUrl}: " . $e->getMessage());

            return ApiResponse::rfcProblem(
                title: 'Bad Gateway / Service Unavailable',
                status: 502,
                detail: "Unable to reach service '{$service}' at {$targetBaseUrl}: " . $e->getMessage(),
                type: 'https://astacode.com/errors/bad-gateway',
                extra: [
                    'target_service' => $service,
                    'target_url' => $targetUrl,
                ]
            );
        }
    }
}

