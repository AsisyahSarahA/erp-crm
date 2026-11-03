<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Middleware;

use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Security\InternalRequestSigner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware VerifyServiceToken
 * Rejects requests with missing or invalid internal HMAC service signatures.
 */
class VerifyServiceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow bypass in local dev/testing if explicitly configured
        if (app()->environment('local', 'testing') && $request->header('X-Bypass-Signature') === 'true') {
            return $next($request);
        }

        if (!InternalRequestSigner::verify($request)) {
            return ApiResponse::rfcProblem(
                title: 'Invalid Internal Service Token or Signature',
                status: 401,
                detail: 'The provided internal service signature is missing, expired, or failed HMAC verification.',
                type: 'https://astacode.com/errors/unauthorized-internal-request'
            );
        }

        return $next($request);
    }
}

