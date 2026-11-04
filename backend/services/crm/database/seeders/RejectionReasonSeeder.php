<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectionReasonSeeder extends Seeder
{
    public function run(): void
    {
        // FR-CRM-10 Reasons
        $reasons = [
            [
                'code' => 'PRICE_LOST',
                'name' => 'Kalah Harga / Over Budget',
                'category' => 'PRICE',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'CLIENT_CANCELLED',
                'name' => 'Klien Batal / Proyek Dibatalkan Internal',
                'category' => 'CLIENT_DECISION',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'COMPETITOR',
                'name' => 'Memilih Kompetitor Lain',
                'category' => 'COMPETITION',
                'requires_competitor' => true,
                'requires_evaluation_notes' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'SCOPE_MISMATCH',
                'name' => 'Scope / Kapabilitas Teknis Tidak Cocok',
                'category' => 'TECHNICAL',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'TIMELINE_MISMATCH',
                'name' => 'Timeline / Waktu Delivery Tidak Cocok',
                'category' => 'DELIVERY',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'UNRESPONSIVE',
                'name' => 'Tidak Ada Respons (Ghosting > 14 Hari)',
                'category' => 'COMMUNICATION',
                'requires_competitor' => false,
                'requires_evaluation_notes' => false,
                'sort_order' => 6,
            ],
            [
                'code' => 'BUDGET_UNAVAILABLE',
                'name' => 'Budget Belum Tersedia / Pending Tahun Depan',
                'category' => 'BUDGET',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 7,
            ],
            [
                'code' => 'OTHER',
                'name' => 'Alasan Lainnya',
                'category' => 'OTHER',
                'requires_competitor' => false,
                'requires_evaluation_notes' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($reasons as $reason) {
            DB::table('crm_rejection_reasons')->updateOrInsert(
                ['code' => $reason['code']],
                array_merge($reason, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}

