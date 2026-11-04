<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RateCardSeeder extends Seeder
{
    public function run(): void
    {
        $validFrom = '2026-01-01';

        $rateCards = [
            // Project Manager
            ['role' => 'PROJECT_MANAGER', 'level' => 'JUNIOR', 'cost' => 350000, 'sell' => 900000],
            ['role' => 'PROJECT_MANAGER', 'level' => 'MID', 'cost' => 550000, 'sell' => 1400000],
            ['role' => 'PROJECT_MANAGER', 'level' => 'SENIOR', 'cost' => 800000, 'sell' => 2000000],
            ['role' => 'PROJECT_MANAGER', 'level' => 'LEAD', 'cost' => 1100000, 'sell' => 2800000],

            // Solution Architect
            ['role' => 'SOLUTION_ARCHITECT', 'level' => 'MID', 'cost' => 700000, 'sell' => 1800000],
            ['role' => 'SOLUTION_ARCHITECT', 'level' => 'SENIOR', 'cost' => 1000000, 'sell' => 2500000],
            ['role' => 'SOLUTION_ARCHITECT', 'level' => 'LEAD', 'cost' => 1400000, 'sell' => 3500000],

            // Backend Engineer
            ['role' => 'BACKEND_ENGINEER', 'level' => 'JUNIOR', 'cost' => 300000, 'sell' => 750000],
            ['role' => 'BACKEND_ENGINEER', 'level' => 'MID', 'cost' => 500000, 'sell' => 1250000],
            ['role' => 'BACKEND_ENGINEER', 'level' => 'SENIOR', 'cost' => 750000, 'sell' => 1900000],
            ['role' => 'BACKEND_ENGINEER', 'level' => 'LEAD', 'cost' => 1000000, 'sell' => 2500000],

            // Frontend Engineer
            ['role' => 'FRONTEND_ENGINEER', 'level' => 'JUNIOR', 'cost' => 280000, 'sell' => 700000],
            ['role' => 'FRONTEND_ENGINEER', 'level' => 'MID', 'cost' => 480000, 'sell' => 1200000],
            ['role' => 'FRONTEND_ENGINEER', 'level' => 'SENIOR', 'cost' => 700000, 'sell' => 1800000],
            ['role' => 'FRONTEND_ENGINEER', 'level' => 'LEAD', 'cost' => 950000, 'sell' => 2400000],

            // Mobile Engineer
            ['role' => 'MOBILE_ENGINEER', 'level' => 'JUNIOR', 'cost' => 300000, 'sell' => 750000],
            ['role' => 'MOBILE_ENGINEER', 'level' => 'MID', 'cost' => 500000, 'sell' => 1300000],
            ['role' => 'MOBILE_ENGINEER', 'level' => 'SENIOR', 'cost' => 750000, 'sell' => 1900000],
            ['role' => 'MOBILE_ENGINEER', 'level' => 'LEAD', 'cost' => 1000000, 'sell' => 2500000],

            // QA Engineer
            ['role' => 'QA_ENGINEER', 'level' => 'JUNIOR', 'cost' => 250000, 'sell' => 600000],
            ['role' => 'QA_ENGINEER', 'level' => 'MID', 'cost' => 400000, 'sell' => 1000000],
            ['role' => 'QA_ENGINEER', 'level' => 'SENIOR', 'cost' => 600000, 'sell' => 1500000],
            ['role' => 'QA_ENGINEER', 'level' => 'LEAD', 'cost' => 800000, 'sell' => 2000000],

            // UI/UX Designer
            ['role' => 'UIUX_DESIGNER', 'level' => 'JUNIOR', 'cost' => 250000, 'sell' => 650000],
            ['role' => 'UIUX_DESIGNER', 'level' => 'MID', 'cost' => 450000, 'sell' => 1150000],
            ['role' => 'UIUX_DESIGNER', 'level' => 'SENIOR', 'cost' => 650000, 'sell' => 1650000],
            ['role' => 'UIUX_DESIGNER', 'level' => 'LEAD', 'cost' => 900000, 'sell' => 2250000],

            // DevOps Engineer
            ['role' => 'DEVOPS_ENGINEER', 'level' => 'MID', 'cost' => 550000, 'sell' => 1400000],
            ['role' => 'DEVOPS_ENGINEER', 'level' => 'SENIOR', 'cost' => 800000, 'sell' => 2000000],
            ['role' => 'DEVOPS_ENGINEER', 'level' => 'LEAD', 'cost' => 1100000, 'sell' => 2800000],
        ];

        foreach ($rateCards as $rc) {
            DB::table('crm_rate_cards')->updateOrInsert(
                [
                    'role' => $rc['role'],
                    'level' => $rc['level'],
                    'valid_from' => $validFrom,
                ],
                [
                    'cost_rate_idr' => $rc['cost'],
                    'sell_rate_idr' => $rc['sell'],
                    'valid_to' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

