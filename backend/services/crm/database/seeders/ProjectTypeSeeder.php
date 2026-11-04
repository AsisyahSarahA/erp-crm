<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'WEB_MOBILE_APP',
                'name' => 'Web & Mobile App Development',
                'description' => 'Aplikasi web portal dan mobile native/cross-platform iOS & Android',
                'sort_order' => 1,
            ],
            [
                'code' => 'ERP_CUSTOM',
                'name' => 'ERP Custom & Enterprise Workflow',
                'description' => 'Sistem automasi operasional perusahaan, inventori, distribusi & keuangan',
                'sort_order' => 2,
            ],
            [
                'code' => 'FINTECH',
                'name' => 'Fintech & Payment Hub',
                'description' => 'Integrasi payment gateway, e-wallet, micro-lending & perbankan',
                'sort_order' => 3,
            ],
            [
                'code' => 'IOT_TELEMATICS',
                'name' => 'IoT & Fleet Telematics',
                'description' => 'Pelacakan armada sensorik IoT, telemetry GPS & tracking real-time',
                'sort_order' => 4,
            ],
            [
                'code' => 'ECOMMERCE',
                'name' => 'E-Commerce Omnichannel',
                'description' => 'Toko online B2B/B2C, sinkronisasi marketplace & logistik',
                'sort_order' => 5,
            ],
            [
                'code' => 'MAINTENANCE',
                'name' => 'Maintenance & SLA Support',
                'description' => 'Pemeliharaan berkelanjutan, upgrade keamanan & garansi aplikasi',
                'sort_order' => 6,
            ],
            [
                'code' => 'UI_UX',
                'name' => 'UI/UX Design & Prototyping',
                'description' => 'Riset pengguna, design system Figma & clickable prototype',
                'sort_order' => 7,
            ],
            [
                'code' => 'CONSULTING',
                'name' => 'Konsultasi IT & Solution Architecture',
                'description' => 'Auditing arsitektur, pemilihan cloud stack & roadmap transformasi digital',
                'sort_order' => 8,
            ],
            [
                'code' => 'OTHER',
                'name' => 'Lainnya',
                'description' => 'Kategori kustom non-standar',
                'sort_order' => 99,
            ],
        ];

        foreach ($types as $type) {
            DB::table('crm_project_types')->updateOrInsert(
                ['code' => $type['code']],
                array_merge($type, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}

