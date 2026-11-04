<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['code' => 'INSTAGRAM', 'name' => 'Instagram Business', 'handle' => '@astacode.tech'],
            ['code' => 'TIKTOK', 'name' => 'TikTok Creator', 'handle' => '@astacode_id'],
            ['code' => 'LINKEDIN', 'name' => 'LinkedIn Organization Page', 'handle' => 'company/astacode'],
            ['code' => 'YOUTUBE', 'name' => 'YouTube Tech Channel', 'handle' => '@astacodetech'],
            ['code' => 'META_ADS', 'name' => 'Meta Ads Manager (FB/IG)', 'handle' => 'act_88291024'],
            ['code' => 'GOOGLE_ADS', 'name' => 'Google Ads Search & Display', 'handle' => 'ads-552-192-9901'],
        ];

        foreach ($channels as $c) {
            DB::table('mkt_channels')->updateOrInsert(
                ['code' => $c['code']],
                array_merge($c, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

