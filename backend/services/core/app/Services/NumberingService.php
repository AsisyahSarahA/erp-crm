<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NumberingSequence;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * Atomically generates the next sequence code with row-level locking.
     */
    public function next(string $codeType, array $context = []): string
    {
        return DB::transaction(function () use ($codeType, $context) {
            $year = date('Y');
            $month = date('m');

            // Find or create the sequence definition
            $sequence = NumberingSequence::where('code_type', $codeType)->lockForUpdate()->first();

            if (!$sequence) {
                $defaultPattern = self::getDefaultPattern($codeType);
                $sequence = NumberingSequence::create([
                    'code_type' => $codeType,
                    'format_pattern' => $defaultPattern,
                    'current_number' => 0,
                    'current_period' => $year,
                    'reset_frequency' => 'yearly',
                    'padding' => 4,
                ]);
                // Re-lock
                $sequence = NumberingSequence::where('id', $sequence->id)->lockForUpdate()->first();
            }

            // Determine if sequence must reset
            $targetPeriod = match ($sequence->reset_frequency) {
                'monthly' => "{$year}-{$month}",
                'yearly' => $year,
                default => 'all_time',
            };

            if ($sequence->current_period !== $targetPeriod) {
                $sequence->current_number = 1;
                $sequence->current_period = $targetPeriod;
            } else {
                $sequence->current_number++;
            }

            $sequence->save();

            // Format replacement
            $numberStr = str_pad((string) $sequence->current_number, $sequence->padding, '0', STR_PAD_LEFT);
            $pattern = $sequence->format_pattern;

            $replacements = [
                '{YYYY}' => $year,
                '{YY}' => substr($year, -2),
                '{MM}' => $month,
                '{NNNN}' => $numberStr,
                '{NNN}' => str_pad((string) $sequence->current_number, 3, '0', STR_PAD_LEFT),
                '{SUBTYPE}' => $context['subtype'] ?? 'GEN',
                '{REV}' => $context['revision'] ?? '0',
            ];

            return str_replace(array_keys($replacements), array_values($replacements), $pattern);
        });
    }

    private static function getDefaultPattern(string $codeType): string
    {
        return match (strtolower($codeType)) {
            'lead' => 'LD-{YYYY}-{NNNN}',
            'quotation' => 'QT/{YYYY}/{MM}/{NNNN}-R{REV}',
            'mou' => 'MOU/ASTA/{YYYY}/{MM}/{NNNN}',
            'addendum' => 'ADD/ASTA/{YYYY}/{MM}/{NNNN}',
            'customer' => 'CUS-{NNNN}',
            'project' => 'PRJ-{YYYY}-{NNNN}',
            'invoice' => 'INV/{YYYY}/{MM}/{NNNN}',
            'ticket' => 'TICKET-{YYYY}-{NNNN}',
            'journal' => 'JRN-{SUBTYPE}/{YYYY}/{MM}/{NNNN}',
            'payroll' => 'PAY/{YYYY}/{MM}',
            'spk' => 'SPK/ASTA/{YYYY}/{MM}/{NNNN}',
            default => strtoupper($codeType) . '-{YYYY}-{NNNN}',
        };
    }
}

