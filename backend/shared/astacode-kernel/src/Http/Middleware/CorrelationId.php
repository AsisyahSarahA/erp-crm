<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware CorrelationId
 * Ensures an end-to-end tracing correlation ID exists on all requests and responses.
 */
class CorrelationId
{
    public const HEADER_NAME = 'X-Correlation-Id';

    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header(self::HEADER_NAME);

        if (!$correlationId || !is_string($correlationId)) {
            $correlationId = (string) Str::uuid();
            $request->headers->set(self::HEADER_NAME, $correlationId);
        }

        // Set into container for contextual logging
        app()->instance('correlation_id', $correlationId);

        $response = $next($request);

        $response->headers->set(self::HEADER_NAME, $correlationId);

        return $response;
    }
}

