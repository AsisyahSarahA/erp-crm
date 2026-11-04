<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CRM Master Data (Idempotent)
        $this->call([
            LeadSourceSeeder::class,
            RejectionReasonSeeder::class,
            ProjectTypeSeeder::class,
            IndustrySeeder::class,
            PipelineStageSeeder::class,
            StageChecklistSeeder::class,
            RateCardSeeder::class,
            ModuleCatalogSeeder::class,
            DocumentTemplateSeeder::class,
            ContractClauseSeeder::class,
            CannedResponseSeeder::class,
        ]);

        // 2. Demo Data (Triggered via --demo flag or SEED_DEMO=1)
        $isDemo = filter_var(env('SEED_DEMO', false), FILTER_VALIDATE_BOOLEAN)
            || in_array('--demo', $_SERVER['argv'] ?? [], true);

        if ($isDemo) {
            $this->command?->info('🚀 Seeding CRM Demo Data (30 Leads & 10 Customers)...');
            $this->call(CrmDemoSeeder::class);
        }
    }
}
