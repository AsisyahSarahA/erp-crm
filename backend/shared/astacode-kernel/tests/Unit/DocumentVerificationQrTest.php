<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Document\DocumentVerificationQr;
use PHPUnit\Framework\TestCase;

class DocumentVerificationQrTest extends TestCase
{
    public function test_build_url(): void
    {
        $url = DocumentVerificationQr::buildUrl(
            'https://erp.astacode.com',
            'QT-2026-11-0001',
            '7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1fa3d677284addd200126d9069',
            '2026-11-04'
        );

        $this->assertStringStartsWith('https://erp.astacode.com/verify/document?', $url);
        $this->assertStringContainsString('doc=QT-2026-11-0001', $url);
        $this->assertStringContainsString('hash=7f83b1657ff1fc53', $url);
    }

    public function test_generate_payload(): void
    {
        $payload = DocumentVerificationQr::generatePayload(
            'INV-2026-11-0089',
            '7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1fa3d677284addd200126d9069',
            'https://erp.astacode.com/verify/document?doc=INV-2026-11-0089',
            '2026-11-04T09:00:00Z'
        );

        $this->assertSame('INV-2026-11-0089', $payload['document_number']);
        $this->assertSame(DocumentVerificationQr::ISSUER_NAME, $payload['issuer']);
        $this->assertSame(DocumentVerificationQr::PLATFORM_TAG, $payload['platform']);
        $this->assertStringStartsWith('SHA256:', $payload['fingerprint']);
    }

    public function test_to_svg_generates_valid_scalable_vector(): void
    {
        $data = 'https://erp.astacode.com/verify/document?doc=QT-2026-11-0001';
        $svg = DocumentVerificationQr::toSvg($data, 250);

        $this->assertStringStartsWith('<svg', trim($svg));
        $this->assertStringEndsWith('</svg>', trim($svg));
        $this->assertStringContainsString('width="250"', $svg);
        $this->assertStringContainsString('height="250"', $svg);
        $this->assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }
}

