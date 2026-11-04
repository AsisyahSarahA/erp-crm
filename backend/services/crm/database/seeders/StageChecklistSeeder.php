<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StageChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // NEW_LEAD
            [
                'stage_id' => 'new_lead',
                'code' => 'VERIFY_PIC_WA',
                'label' => 'Verifikasi nomor WhatsApp & profil PIC utama',
                'description' => 'Pastikan nomor WA aktif dan format E.164 tervalidasi.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'stage_id' => 'new_lead',
                'code' => 'FIRST_CONTACT_30M',
                'label' => 'Kontak awal dalam <= 30 menit jam kerja',
                'description' => 'Kirim salam perkenalan dan konfirmasi jadwal diskusi awal.',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            // FOLLOW_UP
            [
                'stage_id' => 'follow_up',
                'label' => 'Kualifikasi BANT (Budget, Authority, Need, Timeline)',
                'code' => 'QUALIFY_BANT',
                'description' => 'Identifikasi Decision Maker dan kisaran anggaran proyek.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'stage_id' => 'follow_up',
                'code' => 'SCHEDULE_DISCOVERY',
                'label' => 'Jadwalkan Discovery Call bersama Presales/BA',
                'description' => 'Kirim undangan meeting dan agenda penggalian kebutuhan.',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            // REQUIREMENT
            [
                'stage_id' => 'requirement',
                'code' => 'CAPTURE_CORE_NEEDS',
                'label' => 'Dokumentasikan kebutuhan inti & ruang lingkup teknis',
                'description' => 'Catat hasil Discovery Call / Voice Note ke dalam dokumen requirement.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'stage_id' => 'requirement',
                'code' => 'PREPARE_RAB_WBS',
                'label' => 'Susun RAB & WBS bersama Presales/Tech Lead',
                'description' => 'Minimal 1 modul pada RAB dan struktur WBS selesai.',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            // QUOTATION
            [
                'stage_id' => 'quotation',
                'code' => 'REVIEW_MARGIN',
                'label' => 'Verifikasi Gross Margin RAB (target >= 40%)',
                'description' => 'Pastikan margin sehat sebelum menerbitkan Quotation resmi.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'stage_id' => 'quotation',
                'code' => 'SEND_QUOTE_MOU',
                'label' => 'Kirim dokumen Quotation & draf MoU ke klien',
                'description' => 'Ubah status Quotation menjadi SENT dan bagikan tautan portal.',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            // NEGOTIATION
            [
                'stage_id' => 'negotiation',
                'code' => 'FINALIZE_TOP_100',
                'label' => 'Finalisasi skema Termin Pembayaran (TOP 100%)',
                'description' => 'Pastikan total persentase termin tepat 100% dan nominal sesuai.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
            [
                'stage_id' => 'negotiation',
                'code' => 'PM_READINESS_CHECK',
                'label' => 'Konfirmasi Delivery Readiness Check dengan PM',
                'description' => 'Pastikan ketersediaan tim delivery sebelum closing Deal.',
                'is_mandatory' => true,
                'sort_order' => 2,
            ],
            [
                'stage_id' => 'negotiation',
                'code' => 'UPLOAD_SIGNED_DOCS',
                'label' => 'Unggah MoU / SPK / PO yang telah ditandatangani',
                'description' => 'Dokumen legal wajib terunggah pada tab Files.',
                'is_mandatory' => true,
                'sort_order' => 3,
            ],
            // DEAL
            [
                'stage_id' => 'deal',
                'code' => 'TRIGGER_HANDOVER_SAGA',
                'label' => 'Verifikasi Handover Deal Won ke PM, Finance, dan HRD',
                'description' => 'Pastikan SAGA Deal Won berjalan sukses dan jadwal Kickoff terkonfirmasi.',
                'is_mandatory' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($items as $item) {
            DB::table('crm_stage_checklists')->updateOrInsert(
                ['stage_id' => $item['stage_id'], 'code' => $item['code']],
                array_merge($item, [
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}

