<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

/**
 * Class Terbilang
 * Converts numeric values to Indonesian grammatical number words.
 * Handles single digits up to trillions, negative amounts, cents (sen), and monetary suffixes (Rupiah).
 */
final class Terbilang
{
    private static array $units = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas'
    ];

    /**
     * Converts a number to Indonesian grammatical words.
     */
    public static function make(string|int|float $number): string
    {
        $clean = self::cleanNumber($number);
        $parts = explode('.', $clean);
        $integerPart = intval($parts[0]);

        if ($integerPart === 0) {
            return 'Nol';
        }

        return trim(self::convertInteger($integerPart));
    }

    /**
     * Converts a number to Indonesian Rupiah representation.
     * Example: 92966940 -> "Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah"
     */
    public static function rupiah(string|int|float $amount, bool $titleCase = true): string
    {
        $clean = self::cleanNumber($amount);
        $parts = explode('.', $clean);
        $integerPart = intval($parts[0]);
        $decimalPart = isset($parts[1]) ? intval(substr($parts[1], 0, 2)) : 0;

        if ($integerPart === 0 && $decimalPart === 0) {
            return $titleCase ? 'Nol Rupiah' : 'nol rupiah';
        }

        $result = '';
        if ($integerPart < 0) {
            $result = 'Minus ' . self::convertInteger(abs($integerPart)) . ' Rupiah';
        } elseif ($integerPart === 0) {
            $result = 'Nol Rupiah';
        } else {
            $result = self::convertInteger($integerPart) . ' Rupiah';
        }

        if ($decimalPart > 0) {
            $result .= ' ' . self::convertInteger($decimalPart) . ' Sen';
        }

        $result = preg_replace('/\s+/', ' ', trim($result));

        return $titleCase ? ucwords(strtolower($result)) : strtolower($result);
    }

    /**
     * Converts to sentence case (Capital first letter only).
     */
    public static function kalimat(string|int|float $amount): string
    {
        $text = self::rupiah($amount, false);
        return ucfirst($text);
    }

    /**
     * Converts to uppercase words.
     */
    public static function uppercase(string|int|float $amount): string
    {
        return strtoupper(self::rupiah($amount, false));
    }

    private static function convertInteger(int $number): string
    {
        if ($number < 0) {
            return 'Minus ' . self::convertInteger(abs($number));
        }

        if ($number < 12) {
            return self::$units[$number];
        }

        if ($number < 20) {
            return self::$units[$number - 10] . ' Belas';
        }

        if ($number < 100) {
            return self::$units[intdiv($number, 10)] . ' Puluh ' . self::$units[$number % 10];
        }

        if ($number < 200) {
            return 'Seratus ' . self::convertInteger($number - 100);
        }

        if ($number < 1000) {
            return self::$units[intdiv($number, 100)] . ' Ratus ' . self::convertInteger($number % 100);
        }

        if ($number < 2000) {
            return 'Seribu ' . self::convertInteger($number - 1000);
        }

        if ($number < 1000000) {
            return self::convertInteger(intdiv($number, 1000)) . ' Ribu ' . self::convertInteger($number % 1000);
        }

        if ($number < 1000000000) {
            return self::convertInteger(intdiv($number, 1000000)) . ' Juta ' . self::convertInteger($number % 1000000);
        }

        if ($number < 1000000000000) {
            return self::convertInteger(intdiv($number, 1000000000)) . ' Miliar ' . self::convertInteger($number % 1000000000);
        }

        return self::convertInteger(intdiv($number, 1000000000000)) . ' Triliun ' . self::convertInteger($number % 1000000000000);
    }

    private static function cleanNumber(string|int|float $number): string
    {
        if (is_int($number)) {
            return (string) $number;
        }

        if (is_float($number)) {
            return number_format($number, 2, '.', '');
        }

        $clean = trim((string) $number);
        $clean = preg_replace('/[^\d.,-]/', '', $clean);

        // If multiple dots (e.g. 92.966.940), dots are thousand separators
        if (substr_count($clean, '.') > 1) {
            $clean = str_replace('.', '', $clean);
            if (str_contains($clean, ',')) {
                $clean = str_replace(',', '.', $clean);
            }
        } elseif (substr_count($clean, ',') > 1) {
            // Commas as thousand separators (e.g. 92,966,940)
            $clean = str_replace(',', '', $clean);
        } elseif (str_contains($clean, ',') && str_contains($clean, '.')) {
            // Determine decimal vs thousand
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif (str_contains($clean, ',')) {
            // Single comma: in Indonesian financial notation, e.g. "1500000,50" -> comma is decimal
            $clean = str_replace(',', '.', $clean);
        }

        return $clean === '' ? '0' : $clean;
    }
}
