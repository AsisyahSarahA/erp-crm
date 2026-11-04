<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            ['code' => 'TECH_SAAS', 'name' => 'Teknologi & SaaS', 'sort_order' => 1],
            ['code' => 'FINANCE_BANKING', 'name' => 'Finansial & Perbankan', 'sort_order' => 2],
            ['code' => 'RETAIL_ECOMMERCE', 'name' => 'Ritel & E-Commerce', 'sort_order' => 3],
            ['code' => 'MFG_LOGISTICS', 'name' => 'Manufaktur & Logistik', 'sort_order' => 4],
            ['code' => 'HEALTHCARE', 'name' => 'Kesehatan & Farmasi', 'sort_order' => 5],
            ['code' => 'EDUCATION', 'name' => 'Pendidikan & EdTech', 'sort_order' => 6],
            ['code' => 'PROPERTY_CONSTR', 'name' => 'Properti & Konstruksi', 'sort_order' => 7],
            ['code' => 'GOV_BUMN', 'name' => 'Pemerintahan & BUMN', 'sort_order' => 8],
            ['code' => 'HOSPITALITY_FNB', 'name' => 'Pariwisata & F&B', 'sort_order' => 9],
            ['code' => 'PROFESSIONAL_SERVICES', 'name' => 'Jasa Profesional & Agensi', 'sort_order' => 10],
        ];

        foreach ($industries as $industry) {
            DB::table('crm_industries')->updateOrInsert(
                ['code' => $industry['code']],
                array_merge($industry, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}

