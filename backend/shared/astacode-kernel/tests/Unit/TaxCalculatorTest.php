<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Finance\TaxCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    #[DataProvider('provideTaxCases')]
    public function test_calculates_tax_and_net_cash_accurately(
        string|int|float $dpp,
        float $ppnRate,
        bool $isTaxWithholder,
        float $pph23Rate,
        string $expectedPpn,
        string $expectedPph23,
        string $expectedTotal,
        string $expectedNetCash
    ): void {
        $result = TaxCalculator::calculate($dpp, $ppnRate, $isTaxWithholder, $pph23Rate);

        $this->assertSame($expectedPpn, $result->ppnAmount);
        $this->assertSame($expectedPph23, $result->pph23Amount);
        $this->assertSame($expectedTotal, $result->invoiceTotal);
        $this->assertSame($expectedNetCash, $result->netCashReceivable);
    }

    public static function provideTaxCases(): array
    {
        return [
            // Standard: DPP 100M, PPN 11%, PPh 23 2% (Withholder)
            'Case 1: Standard withholder 100M' => [
                '100000000.00', 11.0, true, 2.0,
                '11000000.00', '2000000.00', '111000000.00', '109000000.00'
            ],
            // Non-withholder client (PPh 23 = 0, Net cash = Invoice Total)
            'Case 2: Non-withholder client 100M' => [
                '100000000.00', 11.0, false, 2.0,
                '11000000.00', '0.00', '111000000.00', '111000000.00'
            ],
            // Non-PKP company (PPN = 0%)
            'Case 3: Non-PKP company 50M' => [
                '50000000.00', 0.0, true, 2.0,
                '0.00', '1000000.00', '50000000.00', '49000000.00'
            ],
            // Zero DPP
            'Case 4: Zero DPP' => [
                '0.00', 11.0, true, 2.0,
                '0.00', '0.00', '0.00', '0.00'
            ],
            // DPP 10M
            'Case 5: 10M withholder' => [
                '10000000.00', 11.0, true, 2.0,
                '1100000.00', '200000.00', '11100000.00', '10900000.00'
            ],
            // DPP 250M
            'Case 6: 250M enterprise client' => [
                '250000000.00', 11.0, true, 2.0,
                '27500000.00', '5000000.00', '277500000.00', '272500000.00'
            ],
            // Future PPN 12% scenario
            'Case 7: Future PPN 12% on 100M' => [
                '100000000.00', 12.0, true, 2.0,
                '12000000.00', '2000000.00', '112000000.00', '110000000.00'
            ],
            // Custom PPh 23 rate 1.5%
            'Case 8: Custom PPh 23 1.5%' => [
                '100000000.00', 11.0, true, 1.5,
                '11000000.00', '1500000.00', '111000000.00', '109500000.00'
            ],
            // Odd amount: 92.966.940
            'Case 9: Exact PRD 92.966.940 withholder' => [
                '92966940.00', 11.0, true, 2.0,
                '10226363.40', '1859338.80', '103193303.40', '101333964.60'
            ],
            // Small amount: 1M
            'Case 10: 1M small service' => [
                '1000000.00', 11.0, true, 2.0,
                '110000.00', '20000.00', '1110000.00', '1090000.00'
            ],
            // 75M milestone termin
            'Case 11: 75M milestone termin' => [
                '75000000.00', 11.0, true, 2.0,
                '8250000.00', '1500000.00', '83250000.00', '81750000.00'
            ],
            // 500M large project
            'Case 12: 500M large project' => [
                '500000000.00', 11.0, true, 2.0,
                '55000000.00', '10000000.00', '555000000.00', '545000000.00'
            ],
            // 15M with integer input
            'Case 13: 15M integer input' => [
                15000000, 11.0, true, 2.0,
                '1650000.00', '300000.00', '16650000.00', '16350000.00'
            ],
            // 35M with string formatted IDR
            'Case 14: 35M formatted string' => [
                'Rp 35.000.000,00', 11.0, true, 2.0,
                '3850000.00', '700000.00', '38850000.00', '38150000.00'
            ],
            // 1 Billion enterprise
            'Case 15: 1 Billion enterprise project' => [
                '1000000000.00', 11.0, true, 2.0,
                '110000000.00', '20000000.00', '1110000000.00', '1090000000.00'
            ],
        ];
    }

    public function test_throws_exception_on_negative_rates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TaxCalculator::calculate(10000000, -5.0);
    }

    public function test_throws_exception_on_negative_dpp(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TaxCalculator::calculate(-1000000);
    }
}
