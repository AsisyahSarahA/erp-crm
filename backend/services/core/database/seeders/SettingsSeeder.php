<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'tax.vat_rate_pct', 'value' => 11.0, 'type' => 'number', 'category' => 'tax'],
            ['key' => 'tax.pph23_rate_pct', 'value' => 2.0, 'type' => 'number', 'category' => 'tax'],
            ['key' => 'finance.default_invoice_due_days', 'value' => 14, 'type' => 'number', 'category' => 'finance'],
            ['key' => 'crm.discount_threshold_sales_pct', 'value' => 10.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.discount_threshold_manager_pct', 'value' => 15.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.margin_warning_threshold_pct', 'value' => 30.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'hrd.working_hours_per_week', 'value' => 40, 'type' => 'number', 'category' => 'hrd'],
            ['key' => 'hrd.overtime_night_start', 'value' => '22:00', 'type' => 'string', 'category' => 'hrd'],
            ['key' => 'company.legal_name', 'value' => 'PT Astacode Solusindo Teknologi', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.letterhead_address', 'value' => 'Menara Astacode Lt. 18, Jakarta Selatan 12950', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.indonesian_holidays_2026', 'value' => [
                ['date' => '2026-01-01', 'name' => 'Tahun Baru 2026'],
                ['date' => '2026-03-21', 'name' => 'Hari Raya Idul Fitri 1447 H'],
                ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional'],
                ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan RI'],
                ['date' => '2026-12-25', 'name' => 'Hari Raya Natal'],
            ], 'type' => 'json', 'category' => 'general'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                [
                    'value' => $s['value'],
                    'type' => $s['type'],
                    'category' => $s['category'],
                    'version' => 1,
                ]
            );
        }
    }
}

