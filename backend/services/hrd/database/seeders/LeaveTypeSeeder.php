<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'ANNUAL',
                'name' => 'Cuti Tahunan (Annual Leave)',
                'default_quota_days' => 12,
                'requires_document' => false,
                'is_paid' => true,
            ],
            [
                'code' => 'SICK',
                'name' => 'Cuti Sakit dengan Surat Dokter',
                'default_quota_days' => 14,
                'requires_document' => true,
                'is_paid' => true,
            ],
            [
                'code' => 'MARRIAGE',
                'name' => 'Cuti Pernikahan Karyawan',
                'default_quota_days' => 3,
                'requires_document' => false,
                'is_paid' => true,
            ],
            [
                'code' => 'MATERNITY',
                'name' => 'Cuti Melahirkan / Bersalin',
                'default_quota_days' => 90,
                'requires_document' => true,
                'is_paid' => true,
            ],
            [
                'code' => 'BEREAVEMENT',
                'name' => 'Cuti Duka Cita / Keluarga Inti',
                'default_quota_days' => 2,
                'requires_document' => false,
                'is_paid' => true,
            ],
            [
                'code' => 'SPECIAL',
                'name' => 'Izin Khusus / Keperluan Mendesak',
                'default_quota_days' => 3,
                'requires_document' => false,
                'is_paid' => false,
            ],
        ];

        foreach ($types as $t) {
            DB::table('hrd_leave_types')->updateOrInsert(
                ['code' => $t['code']],
                array_merge($t, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

