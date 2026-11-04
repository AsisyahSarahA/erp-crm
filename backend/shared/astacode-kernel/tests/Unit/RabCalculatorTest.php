<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Finance\MarginCalculator;
use Astacode\Kernel\Finance\RabCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RabCalculatorTest extends TestCase
{
    public function test_standard_rab_calculation_with_default_contingency_10_percent(): void
    {
        $items = [
            ['name' => 'Backend Development', 'qty' => 10, 'price' => 2000000, 'cost_rate' => 1200000],
            ['name' => 'Frontend Development', 'qty' => 10, 'price' => 1500000, 'cost_rate' => 1000000],
        ];

        // Subtotal = (10 * 2.000.000) + (10 * 1.500.000) = 20.000.000 + 15.000.000 = 35.000.000
        // Contingency 10% = 3.500.000
        // DPP = 38.500.000
        // PPN 11% = 4.235.000
        // Total Investasi = 42.735.000
        // HPP = (10 * 1.200.000) + (10 * 1.000.000) = 12.000.000 + 10.000.000 = 22.000.000
        // Gross Margin = (38.500.000 - 22.000.000) / 38.500.000 = 42.86% (GREEN)

        $result = RabCalculator::calculate($items);

        $this->assertSame('35000000.00', $result->subtotal);
        $this->assertSame('3500000.00', $result->contingencyAmount);
        $this->assertSame('38500000.00', $result->dpp);
        $this->assertSame('4235000.00', $result->ppnAmount);
        $this->assertSame('42735000.00', $result->totalInvestasi);
        $this->assertSame('22000000.00', $result->hppInternal);
        $this->assertSame('16500000.00', $result->grossMarginNominal);
        $this->assertSame(42.86, $result->grossMarginPercent);
        $this->assertSame(MarginCalculator::COLOR_GREEN, $result->marginIndicatorColor);
        $this->assertFalse($result->requiresApproval);
        $this->assertStringContainsString('Empat Puluh Dua Juta', $result->terbilang);
    }

    public function test_contingency_less_than_5_percent_requires_reason(): void
    {
        $items = [
            ['name' => 'Design Sprint', 'qty' => 5, 'price' => 1000000, 'cost_rate' => 600000],
        ];

        // Should throw exception when reason is empty and contingency is < 5%
        $this->expectException(InvalidArgumentException::class);
        RabCalculator::calculate($items, contingencyPercent: 3.0, contingencyReason: null);
    }

    public function test_contingency_less_than_5_percent_succeeds_with_valid_reason(): void
    {
        $items = [
            ['name' => 'Design Sprint', 'qty' => 5, 'price' => 1000000, 'cost_rate' => 600000],
        ];

        $result = RabCalculator::calculate(
            $items,
            contingencyPercent: 0.0,
            contingencyReason: 'Proyek repeat order dengan arsitektur yang sudah teruji dan minim risiko.'
        );

        $this->assertSame('5000000.00', $result->subtotal);
        $this->assertSame('0.00', $result->contingencyAmount);
        $this->assertSame('5000000.00', $result->dpp);
    }

    public function test_discount_nominal_and_percentage(): void
    {
        $items = [
            ['name' => 'Custom Module', 'qty' => 10, 'price' => 10000000, 'cost_rate' => 5000000],
        ];
        // Subtotal = 100M, Contingency 10% = 10M, Base = 110M
        // Discount 10% = 11M, DPP = 99M
        $result = RabCalculator::calculate($items, discountPercent: 10.0);
        $this->assertSame('11000000.00', $result->discountAmount);
        $this->assertSame('99000000.00', $result->dpp);
    }

    public function test_terms_of_payment_absorbs_rounding_into_last_term(): void
    {
        // Odd total that creates decimal fractions: 100.000.001
        $items = [
            ['name' => 'Legacy Migration', 'qty' => 1, 'price' => 100000001, 'cost_rate' => 60000000],
        ];

        // 3 terms: 33.33%, 33.33%, 33.34%
        $result = RabCalculator::calculate(
            $items,
            contingencyPercent: 0.0,
            contingencyReason: 'Zero contingency allowed with reason',
            ppnRate: 0.0, // test raw DPP split
            topPercentages: [33.33, 33.33, 33.34]
        );

        $terms = $result->terms;
        $this->assertCount(3, $terms);

        // Sum of all term DPPs must equal exactly total DPP
        $sumTermDpp = bcadd(bcadd($terms[0]['dpp'], $terms[1]['dpp'], 2), $terms[2]['dpp'], 2);
        $this->assertSame($result->dpp, $sumTermDpp);

        // Sum of all term invoices must equal exactly total investasi
        $sumTermTotal = bcadd(bcadd($terms[0]['invoice_total'], $terms[1]['invoice_total'], 2), $terms[2]['invoice_total'], 2);
        $this->assertSame($result->totalInvestasi, $sumTermTotal);
    }

    public function test_optional_and_free_items(): void
    {
        $items = [
            ['name' => 'Core Feature', 'qty' => 5, 'price' => 2000000, 'cost_rate' => 1000000, 'is_optional' => false],
            ['name' => 'Addon AI (Optional)', 'qty' => 2, 'price' => 5000000, 'cost_rate' => 2000000, 'is_optional' => true],
            ['name' => 'Free Bonus QA', 'qty' => 1, 'price' => 1000000, 'cost_rate' => 500000, 'is_free' => true],
        ];

        // Subtotal only includes Core Feature (10.000.000)
        $result = RabCalculator::calculate($items, contingencyPercent: 0.0, contingencyReason: 'Test');

        $this->assertSame('10000000.00', $result->subtotal);
        // HPP includes Core (5M) + Free QA (500k) = 5.500.000. Optional is excluded.
        $this->assertSame('5500000.00', $result->hppInternal);
    }
}

