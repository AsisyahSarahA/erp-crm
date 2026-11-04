<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'PROVISIONED', 'name' => 'Provisioned (Menunggu DP)', 'badge_color' => 'amber', 'sort_order' => 1],
            ['code' => 'ACTIVE', 'name' => 'Aktif Berjalan (In Progress)', 'badge_color' => 'blue', 'sort_order' => 2],
            ['code' => 'IN_UAT', 'name' => 'Tahap Pengujian UAT Klien', 'badge_color' => 'purple', 'sort_order' => 3],
            ['code' => 'MAINTENANCE', 'name' => 'Masa Garansi / Maintenance SLA', 'badge_color' => 'emerald', 'sort_order' => 4],
            ['code' => 'COMPLETED', 'name' => 'Selesai & Serah Terima (BAST)', 'badge_color' => 'green', 'sort_order' => 5],
            ['code' => 'ON_HOLD', 'name' => 'Ditunda Sementara (On Hold)', 'badge_color' => 'zinc', 'sort_order' => 6],
        ];

        foreach ($statuses as $s) {
            DB::table('pm_project_statuses')->updateOrInsert(
                ['code' => $s['code']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

