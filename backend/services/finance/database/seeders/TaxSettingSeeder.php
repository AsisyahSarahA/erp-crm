<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSettingSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            [
                'code' => 'VAT_PPN_11',
                'name' => 'Pajak Pertambahan Nilai (PPN 11%)',
                'rate_pct' => 11.0,
                'fixed_amount_idr' => 0,
            ],
            [
                'code' => 'PPH_23_SERVICE_2',
                'name' => 'Pajak Penghasilan Pasal 23 Jasa Software (2%)',
                'rate_pct' => 2.0,
                'fixed_amount_idr' => 0,
            ],
            [
                'code' => 'EMETERAI_10000',
                'name' => 'Bea Meterai Elektronik (e-Meterai Rp 10.000)',
                'rate_pct' => 0,
                'fixed_amount_idr' => 10000,
            ],
        ];

        foreach ($taxes as $t) {
            DB::table('fin_tax_settings')->updateOrInsert(
                ['code' => $t['code']],
                [
                    'name' => $t['name'],
                    'rate_pct' => $t['rate_pct'],
                    'fixed_amount_idr' => $t['fixed_amount_idr'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

