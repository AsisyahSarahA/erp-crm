<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CannedResponseSeeder extends Seeder
{
    public function run(): void
    {
        $responses = [
            [
                'code' => 'WA_FOLLOWUP_QUOTATION',
                'channel' => 'WHATSAPP',
                'category' => 'FOLLOW_UP',
                'title' => 'Follow Up Penawaran & Dokumen RAB',
                'content' => 'Halo Bapak/Ibu {{contact.name}}, salam hormat dari {{company.brand_name}}. Kami ingin menindaklanjuti dokumen penawaran resmi Nomor: {{quotation.number}} terkait proyek {{lead.title}} yang telah kami kirimkan sebelumnya. Apakah ada bagian ruang lingkup atau skema termin yang ingin didiskusikan lebih lanjut bersama tim Solution Architect kami? Terima kasih.',
                'merge_fields' => ['contact.name', 'company.brand_name', 'quotation.number', 'lead.title'],
            ],
            [
                'code' => 'WA_UAT_REMINDER',
                'channel' => 'WHATSAPP',
                'category' => 'UAT',
                'title' => 'Pengingat Periode User Acceptance Testing (UAT)',
                'content' => 'Halo tim {{customer.legal_name}}, kami menginformasikan bahwa periode pengujian UAT pada server staging untuk proyek {{project.title}} menyisakan {{days_left}} hari kerja lagi. Mohon segera menyelesaikan checklist pengujian dan menyampaikan catatan masukan agar proses persiapan deployment produksi (Go-Live) dapat berjalan tepat waktu. Salam hangat, Project Management Astacode.',
                'merge_fields' => ['customer.legal_name', 'project.title', 'days_left'],
            ],
            [
                'code' => 'WA_INVOICE_TERM_NOTIF',
                'channel' => 'WHATSAPP',
                'category' => 'FINANCE',
                'title' => 'Notifikasi Terbit Invoice Termin Pembayaran',
                'content' => 'Yth. Bapak/Ibu {{contact.name}} ({{customer.legal_name}}), invoice resmi untuk termin {{term.name}} proyek {{project.title}} telah terbit dengan Nomor: {{invoice.number}} sebesar Rp {{invoice.amount}} (Jatuh tempo: {{invoice.due_date}}). Dokumen invoice dan rincian rekening pembayaran BCA PT Astacode Solusindo Teknologi dapat diunduh melalui portal klien: {{invoice.link}}. Bukti potong PPh 23 dapat diunggah pada portal setelah pelunasan. Terima kasih atas kerja samanya.',
                'merge_fields' => ['contact.name', 'customer.legal_name', 'term.name', 'project.title', 'invoice.number', 'invoice.amount', 'invoice.due_date', 'invoice.link'],
            ],
            [
                'code' => 'WA_WELCOME_GREETING',
                'channel' => 'WHATSAPP',
                'category' => 'ONBOARDING',
                'title' => 'Penyambutan Lead Baru Inbound',
                'content' => 'Halo Bapak/Ibu {{contact.name}}, terima kasih telah menghubungi PT Astacode Solusindo Teknologi. Saya {{sales.name}} selaku Account Executive. Kami telah menerima permohonan informasi solusi teknologi untuk {{lead.title}}. Boleh kami menjadwalkan sesi konsultasi singkat selama 20 menit melalui Google Meet untuk mendalami kebutuhan sistem perusahaan Anda?',
                'merge_fields' => ['contact.name', 'sales.name', 'lead.title'],
            ],
            [
                'code' => 'WA_DISCOVERY_MEETING',
                'channel' => 'WHATSAPP',
                'category' => 'FOLLOW_UP',
                'title' => 'Konfirmasi Jadwal Discovery Meeting',
                'content' => 'Selamat pagi Bapak/Ibu {{contact.name}}, ini konfirmasi tautan meeting discovery kita hari ini pukul {{meeting.time}} WIB: {{meeting.link}}. Tim Solution Architect kami telah menyiapkan beberapa arsitektur awal untuk didiskusikan bersama tim {{customer.legal_name}}. Sampai jumpa nanti.',
                'merge_fields' => ['contact.name', 'meeting.time', 'meeting.link', 'customer.legal_name'],
            ],
        ];

        foreach ($responses as $r) {
            DB::table('crm_canned_responses')->updateOrInsert(
                ['code' => $r['code']],
                [
                    'channel' => $r['channel'],
                    'category' => $r['category'],
                    'title' => $r['title'],
                    'content' => $r['content'],
                    'merge_fields' => json_encode($r['merge_fields']),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

