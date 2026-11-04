<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'code' => 'CAMP-2026-Q1-ERP',
                'name' => 'Kampanye Solusi ERP Manufaktur & Distribusi Q1 2026',
                'channel_code' => 'META_ADS',
                'budget_idr' => 25000000,
                'spent_idr' => 14500000,
                'leads_generated' => 18,
                'status' => 'ACTIVE',
                'start_date' => '2026-01-15',
                'end_date' => '2026-03-31',
            ],
            [
                'code' => 'CAMP-2026-SEARCH-TECH',
                'name' => 'Google Ads Search Jasa Pembuatan Software House Jakarta',
                'channel_code' => 'GOOGLE_ADS',
                'budget_idr' => 30000000,
                'spent_idr' => 18200000,
                'leads_generated' => 24,
                'status' => 'ACTIVE',
                'start_date' => '2026-01-01',
                'end_date' => '2026-04-30',
            ],
        ];

        foreach ($campaigns as $camp) {
            DB::table('mkt_campaigns')->updateOrInsert(
                ['code' => $camp['code']],
                array_merge($camp, ['created_at' => now(), 'updated_at' => now()])
            );

            $campId = DB::table('mkt_campaigns')->where('code', $camp['code'])->value('id');

            // Seed sample content items
            DB::table('mkt_contents')->updateOrInsert(
                ['campaign_id' => $campId, 'title' => '7 Tanda Bisnis Distribusi Anda Butuh ERP Terintegrasi'],
                [
                    'pillar_code' => 'TECH_EDUCATION',
                    'content_type' => 'CAROUSEL',
                    'status' => 'PUBLISHED',
                    'seo_score' => 92,
                    'caption' => 'Stok sering selisih antar cabang? Waktunya otomatisasi alur gudang dengan ERP Astacode. Geser untuk baca selengkapnya!',
                    'published_at' => now()->subDays(5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('mkt_contents')->updateOrInsert(
                ['campaign_id' => $campId, 'title' => 'Studi Kasus: Bagaimana PT Sinar Surya Memangkas Waktu Kirim 40%'],
                [
                    'pillar_code' => 'PORTFOLIO_SHOWCASE',
                    'content_type' => 'BLOG',
                    'status' => 'PUBLISHED',
                    'seo_score' => 88,
                    'caption' => 'Pelajari arsitektur WMS dan tracking GPS yang diterapkan pada armada logistik modern.',
                    'published_at' => now()->subDays(2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

