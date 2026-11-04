<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

/**
 * Class PhoneNormalizer
 * Normalizes, validates, and formats Indonesian telephone and WhatsApp numbers into E.164 (+628...).
 */
final class PhoneNormalizer
{
    /**
     * Convert any raw Indonesian phone number string into E.164 format (+628xxxxxxxx).
     */
    public static function toE164(string $phone): string
    {
        $cleaned = trim($phone);
        // Strip non-digits except leading plus
        $cleaned = preg_replace('/[^\d+]/', '', $cleaned);

        if (str_starts_with($cleaned, '+')) {
            $cleaned = substr($cleaned, 1);
        }

        // Replace leading 0 with 62
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62')) {
            // If user typed e.g. 8123456789
            $cleaned = '62' . $cleaned;
        }

        return '+' . $cleaned;
    }

    /**
     * Validate whether a phone number is a valid Indonesian mobile/cellular number.
     * Must be: +628[1-9] followed by 7 to 11 digits (total 10 to 14 digits after 62).
     */
    public static function isValid(string $phone): bool
    {
        if (empty(trim($phone))) {
            return false;
        }

        $e164 = self::toE164($phone);
        return (bool) preg_match('/^\+628[1-9][0-9]{7,11}$/', $e164);
    }

    /**
     * Format for display (international: "+62 812-3456-7890", national: "0812-3456-7890").
     */
    public static function formatDisplay(string $phone, string $format = 'international'): string
    {
        $e164 = self::toE164($phone);
        $digits = substr($e164, 3); // strip '+62'

        if (strlen($digits) < 8) {
            return $format === 'national' ? '0' . $digits : '+62 ' . $digits;
        }

        $prefix = substr($digits, 0, 3); // e.g. 812
        $middle = substr($digits, 3, 4); // e.g. 3456
        $suffix = substr($digits, 7);    // e.g. 7890

        if ($format === 'national') {
            return "0{$prefix}-{$middle}-{$suffix}";
        }

        return "+62 {$prefix}-{$middle}-{$suffix}";
    }
}

