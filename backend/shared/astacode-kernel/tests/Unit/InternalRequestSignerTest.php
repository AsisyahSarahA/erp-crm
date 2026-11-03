<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Security\InternalRequestSigner;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class InternalRequestSignerTest extends TestCase
{
    public function test_can_sign_and_verify_request(): void
    {
        $origin = 'crm-service';
        $body = json_encode(['lead_id' => '01HQ7N01', 'amount' => 50000000]);
        $secret = 'test-secret-key-12345';

        $headers = InternalRequestSigner::sign($origin, $body, $secret);

        $this->assertArrayHasKey(InternalRequestSigner::HEADER_SIGNATURE, $headers);
        $this->assertArrayHasKey(InternalRequestSigner::HEADER_TIMESTAMP, $headers);
        $this->assertArrayHasKey(InternalRequestSigner::HEADER_ORIGIN, $headers);

        $request = Request::create('/internal/test', 'POST', [], [], [], [
            'HTTP_X_SERVICE_SIGNATURE' => $headers[InternalRequestSigner::HEADER_SIGNATURE],
            'HTTP_X_SERVICE_TIMESTAMP' => $headers[InternalRequestSigner::HEADER_TIMESTAMP],
            'HTTP_X_SERVICE_ORIGIN' => $headers[InternalRequestSigner::HEADER_ORIGIN],
        ], $body);

        $this->assertTrue(InternalRequestSigner::verify($request, $secret));
    }

    public function test_rejects_tampered_payload(): void
    {
        $origin = 'crm-service';
        $body = json_encode(['amount' => 100000]);
        $secret = 'test-secret-key-12345';

        $headers = InternalRequestSigner::sign($origin, $body, $secret);

        // Tamper body
        $tamperedBody = json_encode(['amount' => 999999999]);

        $request = Request::create('/internal/test', 'POST', [], [], [], [
            'HTTP_X_SERVICE_SIGNATURE' => $headers[InternalRequestSigner::HEADER_SIGNATURE],
            'HTTP_X_SERVICE_TIMESTAMP' => $headers[InternalRequestSigner::HEADER_TIMESTAMP],
            'HTTP_X_SERVICE_ORIGIN' => $headers[InternalRequestSigner::HEADER_ORIGIN],
        ], $tamperedBody);

        $this->assertFalse(InternalRequestSigner::verify($request, $secret));
    }
}

