<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Security\FieldMasker;
use PHPUnit\Framework\TestCase;

class FieldMaskerTest extends TestCase
{
    public function test_masks_npwp(): void
    {
        $masked = FieldMasker::npwp('01.234.567.8-901.000');
        $this->assertSame('01.234.***.*-***.000', $masked);
    }

    public function test_masks_bank_account(): void
    {
        $masked = FieldMasker::bankAccount('1234567890');
        $this->assertSame('******7890', $masked);
    }

    public function test_masks_sensitive_amount_by_permission(): void
    {
        $amount = '15000000.00';

        $this->assertSame('15000000.00', FieldMasker::sensitiveAmount($amount, canView: true));
        $this->assertSame('******', FieldMasker::sensitiveAmount($amount, canView: false));
    }

    public function test_masks_email_and_phone(): void
    {
        $maskedEmail = FieldMasker::email('budi.santoso@majusejahtera.co.id');
        $this->assertStringContainsString('@majusejahtera.co.id', $maskedEmail);
        $this->assertStringContainsString('*', $maskedEmail);

        $maskedPhone = FieldMasker::phoneNumber('+6281234567890');
        $this->assertSame('+6281****7890', $maskedPhone);
    }
}

