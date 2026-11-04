<?php

namespace Tests\Feature;

use Database\Seeders\ContentPillarSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MarketingChannelSeeder;
use Database\Seeders\MarketingDemoSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MarketingMasterAndDemoSeederTest extends TestCase
{
    public function test_marketing_master_seeders_run_and_are_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialChannels = DB::table('mkt_channels')->count();
        $initialPillars = DB::table('mkt_content_pillars')->count();

        $this->assertEquals(6, $initialChannels);
        $this->assertEquals(5, $initialPillars);

        // Run second time to verify idempotency
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals(6, DB::table('mkt_channels')->count());
        $this->assertEquals(5, DB::table('mkt_content_pillars')->count());
    }

    public function test_marketing_demo_seeder_creates_campaigns_and_content(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(MarketingDemoSeeder::class);

        $this->assertGreaterThanOrEqual(2, DB::table('mkt_campaigns')->count());
        $this->assertGreaterThanOrEqual(2, DB::table('mkt_contents')->count());

        $content = DB::table('mkt_contents')->first();
        $this->assertNotNull($content);
        $this->assertGreaterThanOrEqual(80, $content->seo_score);
    }
}

