<?php

declare(strict_types=1);

namespace Astacode\Kernel\Document;

use InvalidArgumentException;

/**
 * Class DocumentHasher
 * Computes and verifies cryptographic SHA-256 integrity hashes for Quotations, MoUs, and Invoices.
 * Conforms to PRD FR-CRM-08 §C/D.
 */
final class DocumentHasher
{
    public const ALGORITHM = 'sha256';

    /**
     * Compute SHA-256 hash from raw content or JSON string.
     */
    public static function hash(string $content): string
    {
        return hash(self::ALGORITHM, $content);
    }

    /**
     * Compute SHA-256 hash of a local file.
     */
    public static function hashFile(string $filePath): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("File dokumen tidak ditemukan atau tidak dapat dibaca: {$filePath}");
        }

        $hash = hash_file(self::ALGORITHM, $filePath);
        if ($hash === false) {
            throw new InvalidArgumentException("Gagal menghitung hash berkas dokumen: {$filePath}");
        }

        return $hash;
    }

    /**
     * Verify content matches expected SHA-256 hash (constant time comparison).
     */
    public static function verify(string $content, string $expectedHash): bool
    {
        $computed = self::hash($content);
        return hash_equals(strtolower($expectedHash), strtolower($computed));
    }

    /**
     * Formats hash for display on document cover or e-Meterai sign-off block.
     * Example: "SHA256: 7f83b165... (64 hex characters)"
     */
    public static function formatFingerprint(string $hash): string
    {
        $clean = strtolower(trim($hash));
        return 'SHA256:' . strtoupper(substr($clean, 0, 8)) . '-' . strtoupper(substr($clean, 8, 8)) . '...' . strtoupper(substr($clean, -8));
    }
}

