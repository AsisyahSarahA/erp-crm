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
            ['key' => 'tax.emeterai_amount_idr', 'value' => 10000, 'type' => 'number', 'category' => 'tax'],
            ['key' => 'tax.emeterai_threshold_idr', 'value' => 5000000, 'type' => 'number', 'category' => 'tax'],
            ['key' => 'finance.default_invoice_due_days', 'value' => 14, 'type' => 'number', 'category' => 'finance'],
            ['key' => 'finance.dp_default_percentage', 'value' => 30.0, 'type' => 'number', 'category' => 'finance'],
            ['key' => 'crm.discount_threshold_sales_pct', 'value' => 10.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.discount_threshold_manager_pct', 'value' => 15.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.margin_warning_threshold_pct', 'value' => 30.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.margin_director_approval_pct', 'value' => 25.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'crm.default_contingency_pct', 'value' => 10.0, 'type' => 'number', 'category' => 'crm'],
            ['key' => 'hrd.working_hours_per_week', 'value' => 40, 'type' => 'number', 'category' => 'hrd'],
            ['key' => 'hrd.overtime_night_start', 'value' => '22:00', 'type' => 'string', 'category' => 'hrd'],
            ['key' => 'sla.lead_first_response_minutes', 'value' => 30, 'type' => 'number', 'category' => 'sla'],
            ['key' => 'sla.uat_period_workdays', 'value' => 14, 'type' => 'number', 'category' => 'sla'],
            ['key' => 'sla.helpdesk_p1_response_hours', 'value' => 1, 'type' => 'number', 'category' => 'sla'],
            ['key' => 'sla.helpdesk_p2_response_hours', 'value' => 4, 'type' => 'number', 'category' => 'sla'],
            ['key' => 'sla.helpdesk_p3_response_hours', 'value' => 24, 'type' => 'number', 'category' => 'sla'],
            ['key' => 'company.legal_name', 'value' => 'PT Astacode Solusindo Teknologi', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.brand_name', 'value' => 'ASTACODE', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.letterhead_address', 'value' => 'Menara Astacode Lt. 18, Jl. H.R. Rasuna Said Kav. B-4, Jakarta Selatan 12950', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.npwp', 'value' => '01.234.567.8-012.000', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.email', 'value' => 'finance@astacode.com', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.phone', 'value' => '+6281122334455', 'type' => 'string', 'category' => 'general'],
            ['key' => 'company.indonesian_holidays_2026', 'value' => [
                ['date' => '2026-01-01', 'name' => 'Tahun Baru Masehi'],
                ['date' => '2026-02-17', 'name' => 'Tahun Baru Imlek 2577'],
                ['date' => '2026-03-20', 'name' => 'Hari Raya Idul Fitri 1447 H'],
                ['date' => '2026-03-21', 'name' => 'Hari Raya Idul Fitri Hari Kedua'],
                ['date' => '2026-03-22', 'name' => 'Hari Suci Nyepi Tahun Baru Saka 1948'],
                ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional'],
                ['date' => '2026-05-14', 'name' => 'Kenaikan Yesus Kristus'],
                ['date' => '2026-05-27', 'name' => 'Hari Raya Idul Adha 1447 H'],
                ['date' => '2026-05-31', 'name' => 'Hari Raya Waisak 2570'],
                ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila'],
                ['date' => '2026-08-17', 'name' => 'Hari Proklamasi Kemerdekaan RI'],
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
