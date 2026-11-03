<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Middleware;

use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Models\IdempotencyKey;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IdempotencyMiddleware
 * Protects critical endpoints from duplicated submissions using Idempotency-Key header.
 */
class IdempotencyMiddleware
{
    public const HEADER_NAME = 'Idempotency-Key';

    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header(self::HEADER_NAME);

        // If no idempotency key provided or non-mutating method, continue normally
        if (!$idempotencyKey || !in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        // Check if database table exists before querying (graceful in tests/unmigrated DBs)
        try {
            if (!Schema::hasTable('idempotency_keys')) {
                return $next($request);
            }

            $requestHash = hash('sha256', $request->getContent());
            $existing = IdempotencyKey::where('key', $idempotencyKey)->first();

            if ($existing) {
                if ($existing->status === IdempotencyKey::STATUS_PROCESSING) {
                    return ApiResponse::rfcProblem(
                        title: 'Concurrent Request in Progress',
                        status: 409,
                        detail: 'A request with this Idempotency-Key is currently being processed. Please retry in a moment.',
                        type: 'https://astacode.com/errors/idempotency-conflict'
                    );
                }

                if ($existing->status === IdempotencyKey::STATUS_COMPLETED) {
                    $response = response()->json($existing->response_body, $existing->response_code);
                    $response->headers->set('X-Idempotent-Replay', 'true');
                    return $response;
                }
            }

            // Create record in PROCESSING status
            $record = IdempotencyKey::create([
                'key' => $idempotencyKey,
                'route' => $request->path(),
                'request_hash' => $requestHash,
                'status' => IdempotencyKey::STATUS_PROCESSING,
                'locked_at' => now(),
            ]);

            $response = $next($request);

            // On successful or handled response, cache result
            $bodyContent = json_decode($response->getContent(), true) ?? ['content' => $response->getContent()];
            $record->update([
                'status' => IdempotencyKey::STATUS_COMPLETED,
                'response_code' => $response->getStatusCode(),
                'response_body' => $bodyContent,
            ]);

            return $response;
        } catch (Exception $e) {
            // In case of any unhandled DB error, don't crash execution in dev
            return $next($request);
        }
    }
}

