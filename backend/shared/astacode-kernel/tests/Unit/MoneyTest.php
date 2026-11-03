<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_create_and_format_money(): void
    {
        $m = Money::of('92966940');
        $this->assertSame('92966940.00', $m->getAmount());
        $this->assertSame('IDR', $m->getCurrency());
        $this->assertSame('Rp92.966.940', $m->formatRupiah());
    }

    public function test_money_addition_and_subtraction(): void
    {
        $m1 = Money::of('100000.50');
        $m2 = Money::of('50000.25');

        $sum = $m1->add($m2);
        $this->assertSame('150000.75', $sum->getAmount());

        $diff = $m1->subtract($m2);
        $this->assertSame('50000.25', $diff->getAmount());
    }

    public function test_money_multiplication_and_percentage(): void
    {
        $base = Money::of('1000000');
        $vat = $base->percentage(11); // 11% PPN
        $this->assertSame('110000.00', $vat->getAmount());

        $timesThree = $base->multiply(3);
        $this->assertSame('3000000.00', $timesThree->getAmount());
    }

    public function test_money_comparison(): void
    {
        $m1 = Money::of('500000');
        $m2 = Money::of('300000');
        $m3 = Money::of('500000');

        $this->assertTrue($m1->greaterThan($m2));
        $this->assertTrue($m2->lessThan($m1));
        $this->assertTrue($m1->equals($m3));
        $this->assertFalse($m1->isZero());
    }
}

