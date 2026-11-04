<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'code' => 'SHIFT_REGULAR',
                'name' => 'Shift Normal Kantor (09:00 - 18:00 WIB)',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'break_minutes' => 60,
                'grace_period_minutes' => 15,
                'is_night_shift' => false,
            ],
            [
                'code' => 'SHIFT_FLEXI',
                'name' => 'Shift Fleksibel (8 Jam Kerja Harian)',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 60,
                'grace_period_minutes' => 30,
                'is_night_shift' => false,
            ],
            [
                'code' => 'SHIFT_NIGHT',
                'name' => 'Shift Malam / Standby Server (> 22:00 WIB)',
                'start_time' => '20:00:00',
                'end_time' => '05:00:00',
                'break_minutes' => 60,
                'grace_period_minutes' => 15,
                'is_night_shift' => true,
            ],
        ];

        foreach ($shifts as $s) {
            DB::table('hrd_shifts')->updateOrInsert(
                ['code' => $s['code']],
                array_merge($s, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

