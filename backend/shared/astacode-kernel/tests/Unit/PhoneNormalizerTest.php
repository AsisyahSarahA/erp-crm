<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\PhoneNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    public function test_normalizes_various_indonesian_phone_formats_to_e164(): void
    {
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('081234567890'));
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('+6281234567890'));
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('6281234567890'));
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('81234567890'));
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('0812-3456-7890'));
        $this->assertSame('+6281234567890', PhoneNormalizer::toE164('(0812) 3456 7890'));
        $this->assertSame('+6285712345678', PhoneNormalizer::toE164('0857 1234 5678'));
    }

    public function test_validates_legitimate_indonesian_mobile_numbers(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('081234567890'));
        $this->assertTrue(PhoneNormalizer::isValid('+6281234567890'));
        $this->assertTrue(PhoneNormalizer::isValid('085712345678'));
        $this->assertTrue(PhoneNormalizer::isValid('089612345678'));
        $this->assertTrue(PhoneNormalizer::isValid('087812345678'));

        // Invalid: fixed landline (021), short number, non-Indonesian
        $this->assertFalse(PhoneNormalizer::isValid('0211234567'));
        $this->assertFalse(PhoneNormalizer::isValid('12345'));
        $this->assertFalse(PhoneNormalizer::isValid(''));
    }

    public function test_format_display(): void
    {
        $this->assertSame('+62 812-3456-7890', PhoneNormalizer::formatDisplay('081234567890'));
        $this->assertSame('0812-3456-7890', PhoneNormalizer::formatDisplay('081234567890', 'national'));
    }
}

