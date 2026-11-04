<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'BACKLOG', 'name' => 'Backlog WBS', 'sequence' => 1, 'is_completed' => false],
            ['code' => 'TODO', 'name' => 'To Do (Sprint Ready)', 'sequence' => 2, 'is_completed' => false],
            ['code' => 'IN_PROGRESS', 'name' => 'In Progress (Sedang Dikerjakan)', 'sequence' => 3, 'is_completed' => false],
            ['code' => 'IN_REVIEW', 'name' => 'In Code Review / PR Review', 'sequence' => 4, 'is_completed' => false],
            ['code' => 'QA_TESTING', 'name' => 'QA & Staging Testing', 'sequence' => 5, 'is_completed' => false],
            ['code' => 'DONE', 'name' => 'Done & Verified', 'sequence' => 6, 'is_completed' => true],
        ];

        foreach ($statuses as $s) {
            DB::table('pm_task_statuses')->updateOrInsert(
                ['code' => $s['code']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

