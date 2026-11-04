<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentPillarSeeder extends Seeder
{
    public function run(): void
    {
        $pillars = [
            [
                'code' => 'TECH_EDUCATION',
                'name' => 'Edukasi Arsitektur Teknologi & Software Engineering',
                'description' => 'Konten infografis carousel dan tips microservice, clean code, dan scalability.',
            ],
            [
                'code' => 'PORTFOLIO_SHOWCASE',
                'name' => 'Showcase Studi Kasus Proyek & Transformasi Klien',
                'description' => 'Before-after transformasi sistem klien, metrik efisiensi dan testimonial.',
            ],
            [
                'code' => 'ENGINEERING_CULTURE',
                'name' => 'Budaya Kerja Developer & Behind The Scene Astacode',
                'description' => 'Aktivitas tim scrum, hackathon internal, tips produktivitas remote working.',
            ],
            [
                'code' => 'INDUSTRY_INSIGHTS',
                'name' => 'Wawasan Tren Industri Digital & Regulasi UU PDP',
                'description' => 'Analisis tren ERP, Fintech, keamanan siber, dan kebijakan regulasi terbaru.',
            ],
            [
                'code' => 'PROMO_LAUNCH',
                'name' => 'Peluncuran Fitur Baru & Promo Layanan',
                'description' => 'Pengumuman bundling penawaran awal tahun dan webinar arsitektur gratis.',
            ],
        ];

        foreach ($pillars as $p) {
            DB::table('mkt_content_pillars')->updateOrInsert(
                ['code' => $p['code']],
                array_merge($p, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

