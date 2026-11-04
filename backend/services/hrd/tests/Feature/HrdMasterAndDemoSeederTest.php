<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\HrdDemoSeeder;
use Database\Seeders\LeaveTypeSeeder;
use Database\Seeders\PklQuotaSeeder;
use Database\Seeders\ShiftSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HrdMasterAndDemoSeederTest extends TestCase
{
    public function test_hrd_master_seeders_run_and_are_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialLeaves = DB::table('hrd_leave_types')->count();
        $initialShifts = DB::table('hrd_shifts')->count();
        $initialPkl = DB::table('hrd_pkl_quotas')->count();

        $this->assertGreaterThanOrEqual(5, $initialLeaves);
        $this->assertEquals(3, $initialShifts);
        $this->assertEquals(4, $initialPkl);

        // Run second time to verify idempotency
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals($initialLeaves, DB::table('hrd_leave_types')->count());
        $this->assertEquals(3, DB::table('hrd_shifts')->count());
        $this->assertEquals(4, DB::table('hrd_pkl_quotas')->count());
    }

    public function test_hrd_shifts_contain_normal_flexi_and_night(): void
    {
        $this->seed(ShiftSeeder::class);

        $this->assertDatabaseHas('hrd_shifts', ['code' => 'SHIFT_REGULAR', 'is_night_shift' => 0]);
        $this->assertDatabaseHas('hrd_shifts', ['code' => 'SHIFT_FLEXI', 'is_night_shift' => 0]);
        $this->assertDatabaseHas('hrd_shifts', ['code' => 'SHIFT_NIGHT', 'is_night_shift' => 1]);
    }

    public function test_hrd_demo_seeder_creates_16_employees_and_daily_logs(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(HrdDemoSeeder::class);

        $this->assertEquals(16, DB::table('hrd_employees')->count());

        // Check employment status distribution
        $statuses = DB::table('hrd_employees')->pluck('employment_status')->unique()->toArray();
        $this->assertContains('TETAP', $statuses);
        $this->assertContains('FREELANCE', $statuses);
        $this->assertContains('PKL', $statuses);

        // Check attendances and daily logs
        $this->assertGreaterThanOrEqual(16, DB::table('hrd_attendances')->count());
        $this->assertGreaterThanOrEqual(5, DB::table('hrd_daily_logs')->count());
    }
}

