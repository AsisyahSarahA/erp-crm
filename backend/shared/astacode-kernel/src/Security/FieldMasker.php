<?php

declare(strict_types=1);

namespace Astacode\Kernel\Security;

/**
 * Class FieldMasker
 * Server-side data masking utility for sensitive fields (salaries, bank accounts, margins, NPWP).
 * PRD §3.3 & §17 Requirement: Data masking enforced at server layer before serialization.
 */
final class FieldMasker
{
    /**
     * Masks Indonesian NPWP: 01.234.567.8-901.000 -> 01.234.***.*-***.000
     */
    public static function npwp(?string $npwp): ?string
    {
        if (!$npwp) {
            return null;
        }

        $clean = preg_replace('/[^\d]/', '', $npwp);
        if (strlen($clean) < 15) {
            return '***.***.***';
        }

        $prefix = substr($clean, 0, 5); // 01234
        $suffix = substr($clean, -3);    // 000
        return substr($prefix, 0, 2) . '.' . substr($prefix, 2, 3) . '.***.*-***.' . $suffix;
    }

    /**
     * Masks bank account numbers: 1234567890 -> ******7890
     */
    public static function bankAccount(?string $accountNumber): ?string
    {
        if (!$accountNumber) {
            return null;
        }

        $clean = trim($accountNumber);
        $len = strlen($clean);

        if ($len <= 4) {
            return '****';
        }

        return str_repeat('*', $len - 4) . substr($clean, -4);
    }

    /**
     * Masks monetary figures (e.g., salary, gross profit margin) if user lacks specific permission.
     */
    public static function sensitiveAmount(string|int|float|null $amount, bool $canView): string|int|float|null
    {
        if ($amount === null) {
            return null;
        }

        if ($canView) {
            return $amount;
        }

        return '******';
    }

    /**
     * Masks email addresses: john.doe@example.com -> j***e@example.com
     */
    public static function email(?string $email): ?string
    {
        if (!$email || !str_contains($email, '@')) {
            return $email;
        }

        [$username, $domain] = explode('@', $email, 2);
        $len = strlen($username);

        if ($len <= 2) {
            $maskedUser = $username[0] . '*';
        } else {
            $maskedUser = $username[0] . str_repeat('*', $len - 2) . $username[$len - 1];
        }

        return "{$maskedUser}@{$domain}";
    }

    /**
     * Masks Indonesian phone numbers: +6281234567890 -> +62812****7890
     */
    public static function phoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = trim($phone);
        $len = strlen($clean);

        if ($len <= 7) {
            return '*******';
        }

        $prefix = substr($clean, 0, 5);
        $suffix = substr($clean, -4);

        return "{$prefix}****{$suffix}";
    }
}

