<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PklQuotaSeeder extends Seeder
{
    public function run(): void
    {
        $quotas = [
            [
                'department' => 'SOFTWARE_ENGINEERING',
                'period_year' => 2026,
                'max_quota' => 6,
                'current_accepted' => 2,
                'mentor_name' => 'Gilang Tech Lead',
                'is_accepting' => true,
            ],
            [
                'department' => 'UI_UX_DESIGN',
                'period_year' => 2026,
                'max_quota' => 3,
                'current_accepted' => 1,
                'mentor_name' => 'Lutfi Senior UI/UX',
                'is_accepting' => true,
            ],
            [
                'department' => 'QUALITY_ASSURANCE',
                'period_year' => 2026,
                'max_quota' => 3,
                'current_accepted' => 1,
                'mentor_name' => 'Kiki QA Engineer',
                'is_accepting' => true,
            ],
            [
                'department' => 'IT_SUPPORT',
                'period_year' => 2026,
                'max_quota' => 2,
                'current_accepted' => 0,
                'mentor_name' => 'Rian DevOps Engineer',
                'is_accepting' => true,
            ],
        ];

        foreach ($quotas as $q) {
            DB::table('hrd_pkl_quotas')->updateOrInsert(
                [
                    'department' => $q['department'],
                    'period_year' => $q['period_year'],
                ],
                array_merge($q, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

