<?php

declare(strict_types=1);

namespace App\Services\Guardrails;

use Astacode\Kernel\Security\FieldMasker;

class AiGuardrailService
{
    private const INJECTION_PATTERNS = [
        '/\bignore\s+(all\s+)?(previous|prior)\s+(instructions|prompts|rules)\b/i',
        '/\bdisregard\s+(all\s+)?(previous|prior)\s+instructions\b/i',
        '/\breveal\s+(the\s+)?(system\s+prompt|developer\s+instructions)\b/i',
        '/\byou\s+are\s+now\s+(unrestricted|DAN|jailbroken|unfiltered)\b/i',
        '/\bbypass\s+(all\s+)?(security|guardrails|safety\s+filters)\b/i',
        '/\bdo\s+anything\s+now\b/i',
        '/\boutput\s+all\s+passwords\b/i',
    ];

    /**
     * Inspects input text for prompt injection attempts.
     *
     * @return array{is_safe: bool, reason: ?string}
     */
    public function detectInjection(string $text): array
    {
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return [
                    'is_safe' => false,
                    'reason' => 'Prompt injection pattern detected: malicious directive attempting to override system instructions.',
                ];
            }
        }

        return [
            'is_safe' => true,
            'reason' => null,
        ];
    }

    /**
     * Redacts PII (Personally Identifiable Information) before sending prompt to public AI cloud.
     */
    public function redactPii(string $text): string
    {
        // 1. Redact Email addresses
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[REDACTED_EMAIL]', $text);

        // 2. Redact Indonesian mobile phone numbers (+628... or 08...)
        $text = preg_replace('/(?:\+62|62|0)8[1-9][0-9]{7,10}\b/', '[REDACTED_PHONE]', $text);

        // 3. Redact Indonesian NPWP
        $text = preg_replace('/\b\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3}\b/', '[REDACTED_NPWP]', $text);

        // 4. Redact credit card numbers (15-16 digits)
        $text = preg_replace('/\b(?:\d[ -]*?){15,16}\b/', '[REDACTED_CC]', $text);

        return $text;
    }

    /**
     * Validates whether an AI action is permitted.
     * Rule: AI cannot write directly to Finance/General Ledger or send live external messages without human approval.
     */
    public function validateActionPermission(string $actionType, string $targetModule): array
    {
        if ($targetModule === 'finance' && in_array($actionType, ['post_journal', 'release_payment', 'write_gl'], true)) {
            return [
                'allowed' => false,
                'reason' => 'AI is strictly prohibited from writing directly to Finance General Ledger without human verification (PRD §13 Guardrails).',
            ];
        }

        if ($actionType === 'send_direct_client_message') {
            return [
                'allowed' => false,
                'reason' => 'AI cannot dispatch live messages to external clients without human-in-the-loop approval.',
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }
}
