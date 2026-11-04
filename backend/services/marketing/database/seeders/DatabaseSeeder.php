<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Marketing Master Data (Idempotent)
        $this->call([
            MarketingChannelSeeder::class,
            ContentPillarSeeder::class,
        ]);

        // 2. Demo Data (Triggered via --demo flag or SEED_DEMO=1)
        $isDemo = filter_var(env('SEED_DEMO', false), FILTER_VALIDATE_BOOLEAN)
            || in_array('--demo', $_SERVER['argv'] ?? [], true);

        if ($isDemo) {
            $this->command?->info('🚀 Seeding Marketing Demo Data (Campaigns & Content Items)...');
            $this->call(MarketingDemoSeeder::class);
        }
    }
}
