<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin Astacode',
                'email' => 'superadmin@astacode.com',
                'role' => 'super_admin',
                'phone' => '+628110000001',
            ],
            [
                'name' => 'Ahmad Direktur',
                'email' => 'director@astacode.com',
                'role' => 'director',
                'phone' => '+628110000002',
            ],
            [
                'name' => 'Bambang Sales Manager',
                'email' => 'sales.manager@astacode.com',
                'role' => 'sales_manager',
                'phone' => '+628110000003',
            ],
            [
                'name' => 'Citra Sales Executive',
                'email' => 'sales@astacode.com',
                'role' => 'sales',
                'phone' => '+628110000004',
            ],
            [
                'name' => 'Doni Project Manager',
                'email' => 'pm@astacode.com',
                'role' => 'project_manager',
                'phone' => '+628110000005',
            ],
            [
                'name' => 'Eka Finance Manager',
                'email' => 'finance@astacode.com',
                'role' => 'finance_manager',
                'phone' => '+628110000006',
            ],
            [
                'name' => 'Fajar HRD Manager',
                'email' => 'hrd@astacode.com',
                'role' => 'hrd_manager',
                'phone' => '+628110000007',
            ],
            [
                'name' => 'Gilang Tech Lead',
                'email' => 'techlead@astacode.com',
                'role' => 'tech_lead',
                'phone' => '+628110000008',
            ],
            [
                'name' => 'Hani Legal Counsel',
                'email' => 'legal@astacode.com',
                'role' => 'legal_counsel',
                'phone' => '+628110000009',
            ],
            [
                'name' => 'Budi Santoso (Klien PT Maju)',
                'email' => 'client@majusejahtera.co.id',
                'role' => 'client',
                'phone' => '+6281234567890',
                'customer_id' => 'CUS-0001',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'customer_id' => $data['customer_id'] ?? null,
                ]
            );

            $roleModel = Role::where('name', $data['role'])->first();
            if ($roleModel) {
                $user->roles()->syncWithoutDetaching([$roleModel->id]);
            }
        }
    }
}

