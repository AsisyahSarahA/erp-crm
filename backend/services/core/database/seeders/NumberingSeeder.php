<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NumberingSequence;
use Illuminate\Database\Seeder;

class NumberingSeeder extends Seeder
{
    public function run(): void
    {
        $patterns = [
            ['code_type' => 'lead', 'format_pattern' => 'LD-{YYYY}-{NNNN}', 'reset_frequency' => 'yearly', 'padding' => 4],
            ['code_type' => 'quotation', 'format_pattern' => 'QT/{YYYY}/{MM}/{NNNN}-R{REV}', 'reset_frequency' => 'monthly', 'padding' => 4],
            ['code_type' => 'mou', 'format_pattern' => 'MOU/ASTA/{YYYY}/{MM}/{NNNN}', 'reset_frequency' => 'monthly', 'padding' => 4],
            ['code_type' => 'addendum', 'format_pattern' => 'ADD/ASTA/{YYYY}/{MM}/{NNNN}', 'reset_frequency' => 'monthly', 'padding' => 4],
            ['code_type' => 'customer', 'format_pattern' => 'CUS-{NNNN}', 'reset_frequency' => 'never', 'padding' => 4],
            ['code_type' => 'project', 'format_pattern' => 'PRJ-{YYYY}-{NNNN}', 'reset_frequency' => 'yearly', 'padding' => 4],
            ['code_type' => 'invoice', 'format_pattern' => 'INV/{YYYY}/{MM}/{NNNN}', 'reset_frequency' => 'monthly', 'padding' => 4],
            ['code_type' => 'ticket', 'format_pattern' => 'TICKET-{YYYY}-{NNNN}', 'reset_frequency' => 'yearly', 'padding' => 4],
            ['code_type' => 'journal', 'format_pattern' => 'JRN-{SUBTYPE}/{YYYY}/{MM}/{NNNN}', 'reset_frequency' => 'monthly', 'padding' => 4],
            ['code_type' => 'payroll', 'format_pattern' => 'PAY/{YYYY}/{MM}', 'reset_frequency' => 'monthly', 'padding' => 0],
            ['code_type' => 'spk', 'format_pattern' => 'SPK/ASTA/{YYYY}/{MM}/{NNNN}', 'reset_frequency' => 'monthly', 'padding' => 4],
        ];

        foreach ($patterns as $p) {
            NumberingSequence::updateOrCreate(
                ['code_type' => $p['code_type']],
                [
                    'format_pattern' => $p['format_pattern'],
                    'current_number' => 0,
                    'current_period' => date('Y'),
                    'reset_frequency' => $p['reset_frequency'],
                    'padding' => $p['padding'],
                ]
            );
        }
    }
}

