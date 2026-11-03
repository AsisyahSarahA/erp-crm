<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AutomationRule;
use Illuminate\Database\Seeder;

class AutomationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Auto Assign Round-Robin Lead Baru',
                'description' => 'Mendistribusikan lead masuk baru ke sales secara bergiliran (round-robin) (PRD §5.4).',
                'trigger_type' => 'event',
                'trigger_event' => 'lead.captured',
                'conditions' => [
                    '==' => [['var' => 'source'], 'web_form'],
                ],
                'actions' => [
                    [
                        'type' => 'round_robin_assign',
                        'pool' => ['Citra Dewi', 'Dimas Prasetyo', 'Dewi Anggraini'],
                    ],
                ],
                'is_active' => true,
                'priority' => 1,
            ],
            [
                'name' => 'Reminder Follow-Up Lead H+1',
                'description' => 'Membuat task pengingat follow-up satu hari setelah lead masuk bila belum dihubungi.',
                'trigger_type' => 'event',
                'trigger_event' => 'lead.captured',
                'conditions' => [
                    '==' => [['var' => 'contact_channel'], 'whatsapp'],
                ],
                'actions' => [
                    [
                        'type' => 'create_task',
                        'title' => 'Follow up WhatsApp prospek baru dalam 24 jam',
                        'due_in_days' => 1,
                    ],
                ],
                'is_active' => true,
                'priority' => 2,
            ],
            [
                'name' => 'Eskalasi Stale Lead >= 5 Hari ke Sales Manager',
                'description' => 'Mengirim notifikasi eskalasi jika lead tidak disentuh selama 5 hari (>= 10 hari ke Direktur).',
                'trigger_type' => 'idle',
                'trigger_event' => null,
                'trigger_config' => ['idle_days' => 5],
                'conditions' => [
                    '>=' => [['var' => 'idle_days'], 5],
                ],
                'actions' => [
                    [
                        'type' => 'escalate_stale_lead',
                    ],
                    [
                        'type' => 'send_telegram',
                        'chat_id' => 'sales_management_alerts',
                        'message' => '⚠️ Lead terindikasi stale (diam tanpa progres >= 5 hari). Mohon tindak lanjut.',
                    ],
                ],
                'is_active' => true,
                'priority' => 3,
            ],
            [
                'name' => 'Pengingat Jatuh Tempo Tagihan Invoice H-3 / H+1 / H+7',
                'description' => 'Kirim pesan pengingat tagihan invoice klien sebelum dan sesudah tanggal jatuh tempo.',
                'trigger_type' => 'overdue',
                'trigger_event' => 'invoice.issued',
                'conditions' => [
                    '==' => [['var' => 'status'], 'UNPAID'],
                ],
                'actions' => [
                    [
                        'type' => 'send_whatsapp',
                        'template' => 'invoice_due_reminder',
                    ],
                ],
                'is_active' => true,
                'priority' => 4,
            ],
            [
                'name' => 'Peringatan Margin Profit Kotor < 30%',
                'description' => 'Memberikan peringatan ke Direktur jika margin gross profit deal di bawah 30% (BR-CRM-08).',
                'trigger_type' => 'event',
                'trigger_event' => 'deal.won',
                'conditions' => [
                    '<' => [['var' => 'margin_percentage'], 30],
                ],
                'actions' => [
                    [
                        'type' => 'send_telegram',
                        'chat_id' => 'executive_directors',
                        'message' => '🚨 Deal Won dengan margin di bawah batas aman 30%! Evaluasi efisiensi WBS diperlukan.',
                    ],
                ],
                'is_active' => true,
                'priority' => 5,
            ],
            [
                'name' => 'Peringatan Utilisasi Kapasitas Tim > 100%',
                'description' => 'Mendeteksi beban kerja tim pengembang melebihi kapasitas jam normal (PRD §5.4).',
                'trigger_type' => 'event',
                'trigger_event' => 'timelog.recorded',
                'conditions' => [
                    '>' => [['var' => 'utilization_pct'], 100],
                ],
                'actions' => [
                    [
                        'type' => 'send_telegram',
                        'chat_id' => 'pm_hrd_alerts',
                        'message' => '⚠️ Overload terdeteksi! Utilisasi developer melampaui 100%. Harap atur ulang alokasi sprint.',
                    ],
                ],
                'is_active' => true,
                'priority' => 6,
            ],
            [
                'name' => 'Pengingat Kontrak SLA Maintenance H-60',
                'description' => 'Pemberitahuan kepada Sales & Account Manager 60 hari sebelum kontrak maintenance tahunan berakhir.',
                'trigger_type' => 'cron',
                'trigger_event' => null,
                'trigger_config' => ['cron' => '0 9 * * 1'], // Setiap Senin jam 9 pagi
                'conditions' => [
                    '<=' => [['var' => 'days_until_sla_expiration'], 60],
                ],
                'actions' => [
                    [
                        'type' => 'create_task',
                        'title' => 'Diskusi perpanjangan kontrak SLA & retainer tahun depan',
                        'due_in_days' => 14,
                    ],
                ],
                'is_active' => true,
                'priority' => 7,
            ],
        ];

        foreach ($rules as $ruleData) {
            AutomationRule::firstOrCreate(
                ['name' => $ruleData['name']],
                $ruleData
            );
        }
    }
}

