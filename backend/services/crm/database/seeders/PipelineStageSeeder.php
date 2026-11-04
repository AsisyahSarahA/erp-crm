<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'code' => 'NEW_LEAD',
                'name' => 'New Lead',
                'sequence' => 1,
                'win_probability_pct' => 10,
                'sla_hours' => 1, // First contact SLA <= 30-60 mins
            ],
            [
                'code' => 'FOLLOW_UP',
                'name' => 'Follow Up & Discovery',
                'sequence' => 2,
                'win_probability_pct' => 20,
                'sla_hours' => 48,
            ],
            [
                'code' => 'REQUIREMENT',
                'name' => 'Requirement Gathering & WBS',
                'sequence' => 3,
                'win_probability_pct' => 35,
                'sla_hours' => 72,
            ],
            [
                'code' => 'QUOTATION',
                'name' => 'Quotation & RAB Review',
                'sequence' => 4,
                'win_probability_pct' => 55,
                'sla_hours' => 96,
            ],
            [
                'code' => 'NEGOTIATION',
                'name' => 'Negotiation & Legal MoU',
                'sequence' => 5,
                'win_probability_pct' => 75,
                'sla_hours' => 120,
            ],
            [
                'code' => 'DEAL_WON',
                'name' => 'Deal Won (Project Kickoff)',
                'sequence' => 6,
                'win_probability_pct' => 100,
                'sla_hours' => 0,
            ],
            [
                'code' => 'LOST',
                'name' => 'Lost / Rejected',
                'sequence' => 7,
                'win_probability_pct' => 0,
                'sla_hours' => 0,
            ],
        ];

        foreach ($stages as $stage) {
            DB::table('crm_pipeline_stages')->updateOrInsert(
                ['code' => $stage['code']],
                array_merge($stage, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
