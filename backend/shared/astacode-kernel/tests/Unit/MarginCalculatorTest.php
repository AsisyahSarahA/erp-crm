<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Finance\MarginCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MarginCalculatorTest extends TestCase
{
    public function test_margin_green_above_or_equal_40_percent(): void
    {
        // Revenue 100M, Cost 50M -> Gross Margin 50%
        $result = MarginCalculator::calculate('100000000.00', '50000000.00');

        $this->assertSame(50.0, $result['gross_margin_percent']);
        $this->assertSame(MarginCalculator::COLOR_GREEN, $result['indicator_color']);
        $this->assertFalse($result['requires_approval']);
    }

    public function test_margin_yellow_between_25_and_40_percent(): void
    {
        // Revenue 100M, Cost 70M -> Gross Margin 30%
        $result = MarginCalculator::calculate('100000000.00', '70000000.00');

        $this->assertSame(30.0, $result['gross_margin_percent']);
        $this->assertSame(MarginCalculator::COLOR_YELLOW, $result['indicator_color']);
        $this->assertFalse($result['requires_approval']);
    }

    public function test_margin_red_below_25_percent_triggers_approval(): void
    {
        // Revenue 100M, Cost 82M -> Gross Margin 18%
        $result = MarginCalculator::calculate('100000000.00', '82000000.00');

        $this->assertSame(18.0, $result['gross_margin_percent']);
        $this->assertSame(MarginCalculator::COLOR_RED, $result['indicator_color']);
        $this->assertTrue($result['requires_approval']);
        $this->assertNotNull($result['approval_reason']);
    }

    public function test_zero_revenue_gives_zero_margin(): void
    {
        $result = MarginCalculator::calculate('0.00', '0.00');
        $this->assertSame(0.0, $result['gross_margin_percent']);
        $this->assertSame(MarginCalculator::COLOR_RED, $result['indicator_color']);
    }

    public function test_negative_values_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MarginCalculator::calculate('-100', '50');
    }
}

