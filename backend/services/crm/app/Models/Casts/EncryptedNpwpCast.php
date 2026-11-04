<?php

declare(strict_types=1);

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Custom Eloquent Cast for Indonesian NPWP (16-digit / 15-digit).
 * Encrypts on write, decrypts on read, and gracefully falls back if row was seeded unencrypted.
 */
final class EncryptedNpwpCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString((string) $value);
        } catch (DecryptException) {
            return (string) $value;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || trim((string) $value) === '') {
            return [
                $key => null,
                'npwp_hash' => null,
            ];
        }

        $raw = trim((string) $value);
        $digitsOnly = preg_replace('/[^\d]/', '', $raw) ?? '';

        return [
            $key => Crypt::encryptString($raw),
            'npwp_hash' => $digitsOnly !== '' ? hash('sha256', $digitsOnly) : null,
        ];
    }
}

