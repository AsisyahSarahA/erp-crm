<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\CompanyMatcher;
use PHPUnit\Framework\TestCase;

class CompanyMatcherTest extends TestCase
{
    public function test_normalizes_legal_tokens_and_punctuation(): void
    {
        $normalized1 = CompanyMatcher::normalize('PT. Astra International, Tbk.');
        $normalized2 = CompanyMatcher::normalize('Astra International');

        $this->assertSame('astra international', $normalized1);
        $this->assertSame('astra international', $normalized2);
    }

    public function test_matches_companies_with_different_legal_prefixes(): void
    {
        // PT Astra International Tbk vs Astra International
        $sim = CompanyMatcher::similarity('PT Astra International Tbk', 'Astra International');
        $this->assertGreaterThanOrEqual(85.0, $sim);
        $this->assertTrue(CompanyMatcher::isMatch('PT Astra International Tbk', 'Astra International'));

        // PT Bank Central Asia vs Bank Central Asia
        $this->assertTrue(CompanyMatcher::isMatch('PT Bank Central Asia', 'Bank Central Asia'));

        // CV Sinar Mandiri vs PT Sinar Mandiri
        $this->assertTrue(CompanyMatcher::isMatch('CV Sinar Mandiri', 'PT Sinar Mandiri'));
    }

    public function test_handles_word_order_variations(): void
    {
        // "Nusantara Logistik" vs "Logistik Nusantara PT"
        $this->assertTrue(CompanyMatcher::isMatch('Nusantara Logistik', 'Logistik Nusantara PT'));
    }

    public function test_distinguishes_different_companies(): void
    {
        $sim = CompanyMatcher::similarity('PT Astra International', 'PT Telkom Indonesia');
        $this->assertLessThan(40.0, $sim);
        $this->assertFalse(CompanyMatcher::isMatch('PT Astra International', 'PT Telkom Indonesia'));
    }

    public function test_custom_threshold(): void
    {
        // Slight typo: "Astra Internasional" (ID) vs "Astra International" (EN)
        $sim = CompanyMatcher::similarity('Astra Internasional', 'Astra International');
        $this->assertGreaterThanOrEqual(75.0, $sim);
        $this->assertTrue(CompanyMatcher::isMatch('Astra Internasional', 'Astra International', 75.0));
    }
}
