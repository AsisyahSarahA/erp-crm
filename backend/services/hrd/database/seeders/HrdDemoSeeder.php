<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrdDemoSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'nik' => 'EMP-0001', 'name' => 'Ahmad Fauzi', 'email' => 'director@astacode.com', 'phone' => '+628110000002',
                'status' => 'TETAP', 'title' => 'Direktur Utama', 'dept' => 'Executive', 'role' => 'SALES',
                'base' => 35000000, 'allowance' => 5000000, 'hourly' => 0, 'join' => '2023-01-01',
            ],
            [
                'nik' => 'EMP-0002', 'name' => 'Bambang Irawan', 'email' => 'sales.manager@astacode.com', 'phone' => '+628110000003',
                'status' => 'TETAP', 'title' => 'Head of Sales', 'dept' => 'Sales', 'role' => 'SALES',
                'base' => 18000000, 'allowance' => 3000000, 'hourly' => 0, 'join' => '2023-03-01',
            ],
            [
                'nik' => 'EMP-0003', 'name' => 'Citra Dewi', 'email' => 'sales@astacode.com', 'phone' => '+628110000004',
                'status' => 'TETAP', 'title' => 'Account Executive', 'dept' => 'Sales', 'role' => 'SALES',
                'base' => 10000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-06-01',
            ],
            [
                'nik' => 'EMP-0004', 'name' => 'Dedi Prasetyo', 'email' => 'presales@astacode.com', 'phone' => '+628110000010',
                'status' => 'TETAP', 'title' => 'Solution Architect', 'dept' => 'Pre-Sales', 'role' => 'BE',
                'base' => 14000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-08-01',
            ],
            [
                'nik' => 'EMP-0005', 'name' => 'Maya Safitri', 'email' => 'marketing@astacode.com', 'phone' => '+628110000011',
                'status' => 'TETAP', 'title' => 'Marketing Lead', 'dept' => 'Marketing', 'role' => 'SALES',
                'base' => 12000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-09-01',
            ],
            [
                'nik' => 'EMP-0006', 'name' => 'Doni Setiawan', 'email' => 'pm@astacode.com', 'phone' => '+628110000005',
                'status' => 'TETAP', 'title' => 'Senior Project Manager', 'dept' => 'Project Management', 'role' => 'PM',
                'base' => 16000000, 'allowance' => 2500000, 'hourly' => 0, 'join' => '2023-04-01',
            ],
            [
                'nik' => 'EMP-0007', 'name' => 'Eka Wulandari', 'email' => 'finance@astacode.com', 'phone' => '+628110000006',
                'status' => 'TETAP', 'title' => 'Finance & Accounting Manager', 'dept' => 'Finance', 'role' => 'FINANCE',
                'base' => 15000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-02-01',
            ],
            [
                'nik' => 'EMP-0008', 'name' => 'Indra Kusuma', 'email' => 'finance.ar@astacode.com', 'phone' => '+628110000012',
                'status' => 'TETAP', 'title' => 'Finance AR Specialist', 'dept' => 'Finance', 'role' => 'FINANCE',
                'base' => 8500000, 'allowance' => 1500000, 'hourly' => 0, 'join' => '2024-01-15',
            ],
            [
                'nik' => 'EMP-0009', 'name' => 'Fajar Nugraha', 'email' => 'hrd@astacode.com', 'phone' => '+628110000007',
                'status' => 'TETAP', 'title' => 'Human Resources Manager', 'dept' => 'HRD', 'role' => 'HR',
                'base' => 14000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-05-01',
            ],
            [
                'nik' => 'EMP-0010', 'name' => 'Gilang Ramadhan', 'email' => 'techlead@astacode.com', 'phone' => '+628110000008',
                'status' => 'TETAP', 'title' => 'VP of Engineering / Tech Lead', 'dept' => 'Engineering', 'role' => 'FULLSTACK',
                'base' => 22000000, 'allowance' => 3000000, 'hourly' => 0, 'join' => '2023-01-15',
            ],
            [
                'nik' => 'EMP-0011', 'name' => 'Joko Susilo', 'email' => 'developer@astacode.com', 'phone' => '+628110000013',
                'status' => 'TETAP', 'title' => 'Senior Backend Engineer', 'dept' => 'Engineering', 'role' => 'BE',
                'base' => 15000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-07-01',
            ],
            [
                'nik' => 'EMP-0012', 'name' => 'Rian Hidayat', 'email' => 'frontend@astacode.com', 'phone' => '+628110000015',
                'status' => 'TETAP', 'title' => 'Senior Frontend Engineer', 'dept' => 'Engineering', 'role' => 'FE',
                'base' => 14000000, 'allowance' => 2000000, 'hourly' => 0, 'join' => '2023-09-15',
            ],
            [
                'nik' => 'EMP-0013', 'name' => 'Kiki Amalia', 'email' => 'qa@astacode.com', 'phone' => '+628110000014',
                'status' => 'TETAP', 'title' => 'Lead QA Engineer', 'dept' => 'Engineering', 'role' => 'QA',
                'base' => 11000000, 'allowance' => 1500000, 'hourly' => 0, 'join' => '2024-02-01',
            ],
            [
                'nik' => 'EMP-0014', 'name' => 'Lutfi Hakim', 'email' => 'uiux@astacode.com', 'phone' => '+628110000016',
                'status' => 'TETAP', 'title' => 'Lead UI/UX Designer', 'dept' => 'Design', 'role' => 'UIUX',
                'base' => 12000000, 'allowance' => 1500000, 'hourly' => 0, 'join' => '2023-11-01',
            ],
            [
                'nik' => 'EMP-0015', 'name' => 'Bayu Pratama', 'email' => 'bayu.freelance@astacode.com', 'phone' => '+628110000017',
                'status' => 'FREELANCE', 'title' => 'Mitra Flutter Mobile Specialist', 'dept' => 'Engineering', 'role' => 'FE',
                'base' => 0, 'allowance' => 0, 'hourly' => 250000, 'join' => '2024-03-01',
            ],
            [
                'nik' => 'EMP-0016', 'name' => 'Dimas Arya', 'email' => 'dimas.intern@astacode.com', 'phone' => '+628110000018',
                'status' => 'PKL', 'title' => 'Peserta PKL Backend Engineering', 'dept' => 'Engineering', 'role' => 'BE',
                'base' => 1500000, 'allowance' => 0, 'hourly' => 0, 'join' => '2026-01-05',
            ],
        ];

        foreach ($employees as $emp) {
            DB::table('hrd_employees')->updateOrInsert(
                ['nik' => $emp['nik']],
                [
                    'full_name' => $emp['name'],
                    'email' => $emp['email'],
                    'phone' => $emp['phone'],
                    'employment_status' => $emp['status'],
                    'job_title' => $emp['title'],
                    'department' => $emp['dept'],
                    'technical_role' => $emp['role'],
                    'base_salary' => $emp['base'],
                    'allowance' => $emp['allowance'],
                    'hourly_rate' => $emp['hourly'],
                    'join_date' => $emp['join'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $empId = DB::table('hrd_employees')->where('nik', $emp['nik'])->value('id');

            // Seed sample attendance for today
            DB::table('hrd_attendances')->updateOrInsert(
                ['employee_id' => $empId, 'date' => now()->toDateString()],
                [
                    'check_in' => '08:55:00',
                    'check_out' => '18:05:00',
                    'mode' => 'ONSITE',
                    'status' => 'ON_TIME',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Seed sample daily log for technical staff
            if (in_array($emp['role'], ['BE', 'FE', 'QA', 'UIUX', 'PM'], true)) {
                DB::table('hrd_daily_logs')->updateOrInsert(
                    ['employee_id' => $empId, 'date' => now()->toDateString()],
                    [
                        'task_ref' => 'PRJ-001-0' . rand(1, 9),
                        'progress_summary' => 'Menyelesaikan implementasi modul integrasi REST API dan optimasi unit tests.',
                        'hours_spent' => 8.0,
                        'is_synced_to_pm' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

