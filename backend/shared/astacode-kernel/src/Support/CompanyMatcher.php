<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

/**
 * Class CompanyMatcher
 * Performs intelligent fuzzy matching and duplicate detection for company and client names.
 * Normalized for Indonesian corporate suffixes (PT, CV, UD, Tbk, Persero, etc.)
 * Conforms to FR-CRM-01/02 deduplication rules (threshold >= 85%).
 */
final class CompanyMatcher
{
    private const NOISE_TOKENS = [
        'pt',
        'p.t',
        'p.t.',
        'cv',
        'c.v',
        'c.v.',
        'ud',
        'u.d',
        'tbk',
        't.b.k',
        'persero',
        'perum',
        'corp',
        'corporation',
        'inc',
        'incorporated',
        'ltd',
        'limited',
        'koperasi',
        'yayasan',
        'indonesia',
        'group',
        'holding',
        'solusindo',
        'teknologi',
    ];

    /**
     * Clean and normalize company name into tokenized canonical string.
     */
    public static function normalize(string $name): string
    {
        $clean = strtolower(trim($name));

        // Remove punctuation & symbols
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clean);

        // Split into words
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out noise tokens
        $filtered = array_filter($words, function ($word) {
            return !in_array($word, self::NOISE_TOKENS, true);
        });

        if (empty($filtered)) {
            $filtered = $words; // Keep original if all words were filtered
        }

        // Sort tokens alphabetically so "Astra International" == "International Astra"
        sort($filtered);

        return implode(' ', $filtered);
    }

    /**
     * Calculate similarity percentage between two company names (0.0% - 100.0%).
     */
    public static function similarity(string $name1, string $name2): float
    {
        $norm1 = self::normalize($name1);
        $norm2 = self::normalize($name2);

        if ($norm1 === '' && $norm2 === '') {
            return 100.0;
        }

        if ($norm1 === '' || $norm2 === '') {
            return 0.0;
        }

        if ($norm1 === $norm2) {
            return 100.0;
        }

        // 1. Calculate similar_text percentage
        similar_text($norm1, $norm2, $percentSimilar);

        // 2. Calculate Levenshtein similarity ratio
        $maxLen = max(strlen($norm1), strlen($norm2));
        $lev = levenshtein($norm1, $norm2);
        $levPercent = $maxLen > 0 ? ((1 - ($lev / $maxLen)) * 100) : 0.0;

        // 3. Token set intersection (Jaccard similarity on tokens)
        $tokens1 = explode(' ', $norm1);
        $tokens2 = explode(' ', $norm2);
        $intersection = count(array_intersect($tokens1, $tokens2));
        $union = count(array_unique(array_merge($tokens1, $tokens2)));
        $tokenPercent = $union > 0 ? (($intersection / $union) * 100) : 0.0;

        // Weighted blend prioritizing high token overlap and textual similarity
        $finalScore = ($percentSimilar * 0.4) + ($levPercent * 0.3) + ($tokenPercent * 0.3);

        return round(min(100.0, max(0.0, $finalScore)), 2);
    }

    /**
     * Check if two company names are duplicate / match (default threshold 85.0%).
     */
    public static function isMatch(string $name1, string $name2, float $threshold = 85.0): bool
    {
        return self::similarity($name1, $name2) >= $threshold;
    }
}

