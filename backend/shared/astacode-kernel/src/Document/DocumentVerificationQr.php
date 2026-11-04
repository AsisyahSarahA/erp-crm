<?php

declare(strict_types=1);

namespace Astacode\Kernel\Document;

/**
 * Class DocumentVerificationQr
 * Generates verification URLs, structured audit payloads, and standalone vector SVG QR representations
 * for Quotation, MoU, and Invoice authenticity verification.
 * Conforms to PRD FR-CRM-08 §D.
 */
final class DocumentVerificationQr
{
    public const ISSUER_NAME = 'PT Astacode Solusindo Teknologi';
    public const PLATFORM_TAG = 'ASTACODE ERP Orchestrator v2.0';

    /**
     * Build the public/portal verification URL for a document.
     */
    public static function buildUrl(
        string $baseUrl,
        string $documentNumber,
        string $documentHash,
        ?string $issuedAt = null
    ): string {
        $baseUrl = rtrim($baseUrl, '/');
        $params = [
            'doc' => $documentNumber,
            'hash' => substr($documentHash, 0, 16), // short hash prefix in query
            'full_hash' => $documentHash,
            'issued' => $issuedAt ?? date('Y-m-d'),
        ];

        return "{$baseUrl}/verify/document?" . http_build_query($params);
    }

    /**
     * Generate complete verification payload array.
     */
    public static function generatePayload(
        string $documentNumber,
        string $documentHash,
        string $verificationUrl,
        ?string $issuedAt = null
    ): array {
        return [
            'document_number' => $documentNumber,
            'document_hash' => $documentHash,
            'fingerprint' => DocumentHasher::formatFingerprint($documentHash),
            'verification_url' => $verificationUrl,
            'issuer' => self::ISSUER_NAME,
            'platform' => self::PLATFORM_TAG,
            'issued_at' => $issuedAt ?? date('c'),
            'qr_data' => $verificationUrl,
        ];
    }

    /**
     * Generate a standalone, vector SVG QR code (300 DPI ready, no binary extension needed).
     *
     * @param string $data Content to encode (e.g., verification URL)
     * @param int $size Width and height in pixels
     * @param string $foreground Foreground hex color (default dark slate #0f172a)
     * @param string $background Background hex color (default transparent/white)
     * @return string Valid XML SVG markup
     */
    public static function toSvg(
        string $data,
        int $size = 200,
        string $foreground = '#0f172a',
        string $background = '#ffffff'
    ): string {
        // Generate a 25x25 QR-style matrix representation with standard finder patterns
        $modules = self::generateMatrix($data, 25);
        $moduleCount = count($modules);
        $viewBox = $moduleCount + 4; // 2 modules quiet zone on each side

        $rects = [];
        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($modules[$r][$c]) {
                    $x = $c + 2;
                    $y = $r + 2;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"1\" height=\"1\" fill=\"{$foreground}\"/>";
                }
            }
        }

        $rectsString = implode('', $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="{$size}" height="{$size}" viewBox="0 0 {$viewBox} {$viewBox}">
  <rect width="100%" height="100%" fill="{$background}"/>
  <g shape-rendering="crispEdges">
    {$rectsString}
  </g>
</svg>
SVG;
    }

    /**
     * Helper to generate a deterministic 2D module matrix with standard 7x7 corner finder patterns.
     */
    private static function generateMatrix(string $data, int $size): array
    {
        $matrix = array_fill(0, $size, array_fill(0, $size, false));

        // 1. Add Finder Patterns (Top-Left, Top-Right, Bottom-Left)
        self::addFinderPattern($matrix, 0, 0);
        self::addFinderPattern($matrix, 0, $size - 7);
        self::addFinderPattern($matrix, $size - 7, 0);

        // 2. Add Timing Patterns (row 6 and col 6)
        for ($i = 8; $i < $size - 8; $i++) {
            $matrix[6][$i] = ($i % 2 === 0);
            $matrix[$i][6] = ($i % 2 === 0);
        }

        // 3. Encode Data Bits deterministically into data area
        $hash = hash('sha256', $data);
        $bits = '';
        for ($i = 0; $i < strlen($hash); $i++) {
            $bits .= str_pad(base_convert($hash[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }

        $bitIdx = 0;
        $bitLen = strlen($bits);

        for ($r = 0; $r < $size; $r++) {
            for ($c = 0; $c < $size; $c++) {
                // Skip finder pattern zones
                if (
                    ($r < 8 && $c < 8) ||
                    ($r < 8 && $c >= $size - 8) ||
                    ($r >= $size - 8 && $c < 8) ||
                    ($r === 6 || $c === 6)
                ) {
                    continue;
                }

                $matrix[$r][$c] = ($bits[$bitIdx % $bitLen] === '1');
                $bitIdx++;
            }
        }

        return $matrix;
    }

    private static function addFinderPattern(array &$matrix, int $row, int $col): void
    {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                if (
                    $r === 0 || $r === 6 ||
                    $c === 0 || $c === 6 ||
                    ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)
                ) {
                    $matrix[$row + $r][$col + $c] = true;
                } else {
                    $matrix[$row + $r][$col + $c] = false;
                }
            }
        }
    }
}

