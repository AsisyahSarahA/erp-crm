<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'account_code' => '1010-BCA-01',
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_number' => '8012-345-678',
                'account_holder' => 'PT Astacode Solusindo Teknologi',
                'coa_code' => '1010',
                'balance' => 850000000,
            ],
            [
                'account_code' => '1020-PETTY-01',
                'bank_name' => 'Kas Kecil Operasional Kantor (Petty Cash)',
                'account_number' => 'PETTY-OFFICE-01',
                'account_holder' => 'Finance Custodian Astacode',
                'coa_code' => '1020',
                'balance' => 15000000,
            ],
        ];

        foreach ($accounts as $acc) {
            DB::table('fin_bank_accounts')->updateOrInsert(
                ['account_code' => $acc['account_code']],
                [
                    'bank_name' => $acc['bank_name'],
                    'account_number' => $acc['account_number'],
                    'account_holder' => $acc['account_holder'],
                    'coa_code' => $acc['coa_code'],
                    'balance' => $acc['balance'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

