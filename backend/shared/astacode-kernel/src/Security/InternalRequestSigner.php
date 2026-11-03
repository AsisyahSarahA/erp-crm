<?php

declare(strict_types=1);

namespace Astacode\Kernel\Security;

use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Class InternalRequestSigner
 * Signs and verifies REST requests communicated internally between ASTACODE microservices using HMAC-SHA256.
 */
final class InternalRequestSigner
{
    public const HEADER_SIGNATURE = 'X-Service-Signature';
    public const HEADER_TIMESTAMP = 'X-Service-Timestamp';
    public const HEADER_ORIGIN = 'X-Service-Origin';
    public const HEADER_TOKEN = 'X-Service-Token';
    public const HEADER_CORRELATION = 'X-Correlation-Id';

    public const MAX_TIMESTAMP_DRIFT_SECONDS = 300; // 5 minutes

    /**
     * Signs outgoing request payload and returns header array.
     */
    public static function sign(string $originService, string $body = '', ?string $secret = null, ?string $correlationId = null): array
    {
        $secretKey = $secret ?? config('kernel.internal_secret', env('INTERNAL_SERVICE_SECRET', 'astacode-default-internal-secret-2026'));
        $timestamp = (string) time();
        $corrId = $correlationId ?? (string) str()->uuid();
        $bodyHash = hash('sha256', $body);

        $dataToSign = "{$timestamp}.{$originService}.{$bodyHash}";
        $signature = hash_hmac('sha256', $dataToSign, $secretKey);

        return [
            self::HEADER_ORIGIN => $originService,
            self::HEADER_TIMESTAMP => $timestamp,
            self::HEADER_SIGNATURE => $signature,
            self::HEADER_TOKEN => hash_hmac('sha256', $originService, $secretKey),
            self::HEADER_CORRELATION => $corrId,
        ];
    }

    /**
     * Validates incoming request HMAC signature.
     */
    public static function verify(Request $request, ?string $secret = null): bool
    {
        $signature = $request->header(self::HEADER_SIGNATURE);
        $timestamp = $request->header(self::HEADER_TIMESTAMP);
        $origin = $request->header(self::HEADER_ORIGIN);

        if (!$signature || !$timestamp || !$origin) {
            return false;
        }

        // Verify timestamp freshness to prevent replay attacks
        $diff = abs(time() - intval($timestamp));
        if ($diff > self::MAX_TIMESTAMP_DRIFT_SECONDS) {
            return false;
        }

        $secretKey = $secret ?? config('kernel.internal_secret', env('INTERNAL_SERVICE_SECRET', 'astacode-default-internal-secret-2026'));
        $body = $request->getContent();
        $bodyHash = hash('sha256', $body);

        $expectedData = "{$timestamp}.{$origin}.{$bodyHash}";
        $expectedSignature = hash_hmac('sha256', $expectedData, $secretKey);

        return hash_equals($expectedSignature, $signature);
    }
}

