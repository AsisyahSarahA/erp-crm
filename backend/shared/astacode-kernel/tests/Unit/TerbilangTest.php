<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\Terbilang;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TerbilangTest extends TestCase
{
    #[DataProvider('provideTerbilangCases')]
    public function test_terbilang_converts_accurately(string|int|float $input, string $expected): void
    {
        $result = Terbilang::rupiah($input);
        $this->assertSame($expected, $result);
    }

    public static function provideTerbilangCases(): array
    {
        return [
            // 1. Mandatory PRD FR-CRM-06/07 test case
            'Case 1: 92966940 exact PRD string' => [
                '92966940',
                'Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah'
            ],
            'Case 2: 92.966.940 with dots' => [
                '92.966.940',
                'Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah'
            ],
            'Case 3: Rp92.966.940 with currency prefix' => [
                'Rp92.966.940',
                'Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah'
            ],
            'Case 4: Zero amount' => [
                0,
                'Nol Rupiah'
            ],
            'Case 5: Zero string' => [
                '0',
                'Nol Rupiah'
            ],
            'Case 6: Single digit 1' => [
                1,
                'Satu Rupiah'
            ],
            'Case 7: Single digit 7' => [
                7,
                'Tujuh Rupiah'
            ],
            'Case 8: Ten' => [
                10,
                'Sepuluh Rupiah'
            ],
            'Case 9: Eleven' => [
                11,
                'Sebelas Rupiah'
            ],
            'Case 10: Twelve (Belas)' => [
                12,
                'Dua Belas Rupiah'
            ],
            'Case 11: Nineteen (Belas)' => [
                19,
                'Sembilan Belas Rupiah'
            ],
            'Case 12: Twenty' => [
                20,
                'Dua Puluh Rupiah'
            ],
            'Case 13: Twenty One' => [
                21,
                'Dua Puluh Satu Rupiah'
            ],
            'Case 14: Fifty Five' => [
                55,
                'Lima Puluh Lima Rupiah'
            ],
            'Case 15: Ninety Nine' => [
                99,
                'Sembilan Puluh Sembilan Rupiah'
            ],
            'Case 16: One hundred' => [
                100,
                'Seratus Rupiah'
            ],
            'Case 17: One hundred and one' => [
                101,
                'Seratus Satu Rupiah'
            ],
            'Case 18: One hundred and eleven' => [
                111,
                'Seratus Sebelas Rupiah'
            ],
            'Case 19: One hundred and twenty five' => [
                125,
                'Seratus Dua Puluh Lima Rupiah'
            ],
            'Case 20: Two hundred' => [
                200,
                'Dua Ratus Rupiah'
            ],
            'Case 21: One thousand (Seribu)' => [
                1000,
                'Seribu Rupiah'
            ],
            'Case 22: One thousand and one' => [
                1001,
                'Seribu Satu Rupiah'
            ],
            'Case 23: One thousand one hundred' => [
                1100,
                'Seribu Seratus Rupiah'
            ],
            'Case 24: One thousand five hundred' => [
                1500,
                'Seribu Lima Ratus Rupiah'
            ],
            'Case 25: Ten thousand' => [
                10000,
                'Sepuluh Ribu Rupiah'
            ],
            'Case 26: One hundred thousand' => [
                100000,
                'Seratus Ribu Rupiah'
            ],
            'Case 27: One million (Satu Juta)' => [
                1000000,
                'Satu Juta Rupiah'
            ],
            'Case 28: Two million five hundred thousand' => [
                2500000,
                'Dua Juta Lima Ratus Ribu Rupiah'
            ],
            'Case 29: One hundred fifty million' => [
                150000000,
                'Seratus Lima Puluh Juta Rupiah'
            ],
            'Case 30: One billion (Satu Miliar)' => [
                1000000000,
                'Satu Miliar Rupiah'
            ],
            'Case 31: One billion two hundred million' => [
                1200000000,
                'Satu Miliar Dua Ratus Juta Rupiah'
            ],
            'Case 32: Ten billion' => [
                10000000000,
                'Sepuluh Miliar Rupiah'
            ],
            'Case 33: One trillion (Satu Triliun)' => [
                1000000000000,
                'Satu Triliun Rupiah'
            ],
            'Case 34: Rupiah with cents (sen)' => [
                '150000.75',
                'Seratus Lima Puluh Ribu Rupiah Tujuh Puluh Lima Sen'
            ],
            'Case 35: Negative amount' => [
                -50000,
                'Minus Lima Puluh Ribu Rupiah'
            ],
        ];
    }

    public function test_terbilang_uppercase_and_sentence_case(): void
    {
        $this->assertSame(
            'LIMA RATUS RIBU RUPIAH',
            Terbilang::uppercase(500000)
        );

        $this->assertSame(
            'Lima ratus ribu rupiah',
            Terbilang::kalimat(500000)
        );
    }
}
