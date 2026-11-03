<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

use InvalidArgumentException;

/**
 * Class Money
 * Handles monetary values strictly as string decimals using BCMath to prevent floating-point inaccuracies.
 * All monetary amounts in ASTACODE ERP are represented as DECIMAL(18,2).
 */
final class Money
{
    private string $amount;
    private string $currency;

    public const DEFAULT_SCALE = 2;
    public const DEFAULT_CURRENCY = 'IDR';

    public function __construct(string|int|float $amount, string $currency = self::DEFAULT_CURRENCY)
    {
        $this->amount = self::normalizeAmount($amount);
        $this->currency = strtoupper(trim($currency));
    }

    public static function of(string|int|float $amount, string $currency = self::DEFAULT_CURRENCY): self
    {
        return new self($amount, $currency);
    }

    public static function zero(string $currency = self::DEFAULT_CURRENCY): self
    {
        return new self('0.00', $currency);
    }

    private static function normalizeAmount(string|int|float $amount): string
    {
        if (is_int($amount)) {
            return number_format((float) $amount, self::DEFAULT_SCALE, '.', '');
        }

        if (is_float($amount)) {
            return number_format($amount, self::DEFAULT_SCALE, '.', '');
        }

        // Clean string from currency prefixes, dots/commas
        $clean = trim($amount);
        $clean = preg_replace('/[^\d.,-]/', '', $clean);

        // If formatted like Indonesian 92.966.940,00 or 92966940.00
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            // standard ID format: dot is thousand, comma is decimal
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif (str_contains($clean, ',')) {
            $clean = str_replace(',', '.', $clean);
        }

        if (!is_numeric($clean)) {
            throw new InvalidArgumentException("Invalid monetary amount: {$amount}");
        }

        return bcadd($clean, '0', self::DEFAULT_SCALE);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);
        return new self(bcadd($this->amount, $other->amount, self::DEFAULT_SCALE), $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);
        return new self(bcsub($this->amount, $other->amount, self::DEFAULT_SCALE), $this->currency);
    }

    public function multiply(string|int|float $factor): self
    {
        $factorStr = is_string($factor) ? $factor : (string) $factor;
        return new self(bcmul($this->amount, $factorStr, self::DEFAULT_SCALE), $this->currency);
    }

    public function divide(string|int|float $divisor): self
    {
        $divisorStr = is_string($divisor) ? $divisor : (string) $divisor;
        if (bccomp($divisorStr, '0', 6) === 0) {
            throw new InvalidArgumentException("Division by zero in Money calculation.");
        }
        return new self(bcdiv($this->amount, $divisorStr, self::DEFAULT_SCALE), $this->currency);
    }

    public function percentage(string|int|float $percent): self
    {
        $percentStr = is_string($percent) ? $percent : (string) $percent;
        $multiplied = bcmul($this->amount, $percentStr, 6);
        return new self(bcdiv($multiplied, '100', self::DEFAULT_SCALE), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && bccomp($this->amount, $other->amount, self::DEFAULT_SCALE) === 0;
    }

    public function greaterThan(self $other): bool
    {
        $this->ensureSameCurrency($other);
        return bccomp($this->amount, $other->amount, self::DEFAULT_SCALE) > 0;
    }

    public function lessThan(self $other): bool
    {
        $this->ensureSameCurrency($other);
        return bccomp($this->amount, $other->amount, self::DEFAULT_SCALE) < 0;
    }

    public function isZero(): bool
    {
        return bccomp($this->amount, '0.00', self::DEFAULT_SCALE) === 0;
    }

    public function isPositive(): bool
    {
        return bccomp($this->amount, '0.00', self::DEFAULT_SCALE) > 0;
    }

    public function formatRupiah(bool $withPrefix = true): string
    {
        $parts = explode('.', $this->amount);
        $integerPart = number_format((int) $parts[0], 0, ',', '.');
        $decimalPart = $parts[1] ?? '00';

        $formatted = $decimalPart === '00' ? $integerPart : "{$integerPart},{$decimalPart}";
        return $withPrefix ? "Rp{$formatted}" : $formatted;
    }

    public function toTerbilang(): string
    {
        return Terbilang::rupiah($this->amount);
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot operate between different currencies: {$this->currency} and {$other->currency}");
        }
    }
}

