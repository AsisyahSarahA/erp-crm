<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PmDemoSeeder;
use Database\Seeders\ProjectStatusSeeder;
use Database\Seeders\TaskStatusSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PmMasterAndDemoSeederTest extends TestCase
{
    public function test_pm_master_seeders_run_and_are_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialProjStatus = DB::table('pm_project_statuses')->count();
        $initialTaskStatus = DB::table('pm_task_statuses')->count();

        $this->assertEquals(6, $initialProjStatus);
        $this->assertEquals(6, $initialTaskStatus);

        // Run second time to verify idempotency
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals(6, DB::table('pm_project_statuses')->count());
        $this->assertEquals(6, DB::table('pm_task_statuses')->count());
    }

    public function test_pm_demo_seeder_creates_5_projects_with_milestones_and_tasks(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(PmDemoSeeder::class);

        $projects = DB::table('pm_projects')->get();
        $this->assertCount(5, $projects);

        $expectedCodes = ['PRJ-001', 'PRJ-002', 'PRJ-003', 'PRJ-004', 'PRJ-005'];
        foreach ($expectedCodes as $code) {
            $proj = $projects->firstWhere('project_code', $code);
            $this->assertNotNull($proj, "Project {$code} harus terdaftar");
            $this->assertGreaterThan(0, $proj->contract_value);
            $this->assertGreaterThan(0, $proj->budget_mandays);

            // Milestones check
            $milestones = DB::table('pm_milestones')->where('project_id', $proj->id)->get();
            $this->assertGreaterThanOrEqual(3, $milestones->count());

            // Tasks check
            $tasks = DB::table('pm_tasks')->where('project_id', $proj->id)->get();
            $this->assertGreaterThanOrEqual(2, $tasks->count());
        }
    }
}

