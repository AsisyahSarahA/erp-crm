<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\Terbilang;
use PHPUnit\Framework\TestCase;

class TerbilangTest extends TestCase
{
    public function test_converts_92966940_to_indonesian_rupiah_text(): void
    {
        // Mandatory requirement in Master Prompt: Uji "Rp92.966.940"
        $result = Terbilang::rupiah('92966940');
        $expected = 'Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah';

        $this->assertSame($expected, $result);
    }

    public function test_converts_formatted_rupiah_string(): void
    {
        $result = Terbilang::rupiah('Rp92.966.940');
        $expected = 'Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah';

        $this->assertSame($expected, $result);
    }

    public function test_converts_zero(): void
    {
        $this->assertSame('Nol Rupiah', Terbilang::rupiah('0'));
    }

    public function test_converts_various_scales(): void
    {
        $this->assertSame('Sepuluh Ribu Rupiah', Terbilang::rupiah(10000));
        $this->assertSame('Seratus Lima Puluh Juta Rupiah', Terbilang::rupiah(150000000));
        $this->assertSame('Satu Miliar Dua Ratus Juta Rupiah', Terbilang::rupiah(1200000000));
    }
}

