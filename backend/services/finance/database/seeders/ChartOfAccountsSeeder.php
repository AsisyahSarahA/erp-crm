<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // LAMPIRAN A — Chart of Accounts (23 Akun Standar Software House)
        $accounts = [
            // ASET (1xxx)
            [
                'code' => '1010',
                'name' => 'Kas/Bank Operasional (BCA)',
                'category' => 'ASET',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Rekening utama giro BCA untuk penerimaan pembayaran klien dan disbursement.',
            ],
            [
                'code' => '1020',
                'name' => 'Kas Kecil (Petty Cash)',
                'category' => 'ASET',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Kas tunai kantor untuk biaya konsumsi rapat, ongkos darurat, dan kebutuhan operasional harian.',
            ],
            [
                'code' => '1030',
                'name' => 'Piutang Usaha',
                'category' => 'ASET',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Tagihan termin invoice yang telah diterbitkan dan menunggu pelunasan klien.',
            ],
            [
                'code' => '1040',
                'name' => 'Uang Muka Pajak PPh 23',
                'category' => 'ASET',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Pajak penghasilan jasa 2% yang telah dipotong klien dan dapat dikreditkan pada SPT Tahunan.',
            ],

            // KEWAJIBAN (2xxx)
            [
                'code' => '2010',
                'name' => 'Utang Vendor',
                'category' => 'KEWAJIBAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Kewajiban pembayaran kepada penyedia layanan server cloud, lisensi, dan subkontraktor.',
            ],
            [
                'code' => '2020',
                'name' => 'Utang PPN',
                'category' => 'KEWAJIBAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'PPN Keluaran 11% yang ditagihkan kepada klien dan wajib disetorkan ke kas negara.',
            ],
            [
                'code' => '2030',
                'name' => 'Utang Gaji',
                'category' => 'KEWAJIBAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Akrual kewajiban payroll bulanan karyawan yang belum ditransfer.',
            ],
            [
                'code' => '2040',
                'name' => 'Pendapatan Diterima Dimuka (DP)',
                'category' => 'KEWAJIBAN',
                'normal_balance' => 'K',
                'is_original_prd' => false,
                'description' => 'Uang muka proyek (DP) yang diterima sebelum deliverable/milestone diserahkan (PRD Lampiran A).',
            ],
            [
                'code' => '2050',
                'name' => 'Utang BPJS & PPh 21/23/26',
                'category' => 'KEWAJIBAN',
                'normal_balance' => 'K',
                'is_original_prd' => false,
                'description' => 'Titipan potongan iuran BPJS ketenagakerjaan/kesehatan dan pajak penghasilan karyawan.',
            ],

            // EKUITAS (3xxx)
            [
                'code' => '3010',
                'name' => 'Modal Disetor',
                'category' => 'EKUITAS',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Modal awal pendirian perusahaan yang disetor oleh para pendiri / pemegang saham.',
            ],
            [
                'code' => '3020',
                'name' => 'Laba Ditahan',
                'category' => 'EKUITAS',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Akumulasi laba bersih periode-periode sebelumnya yang diinvestasikan kembali.',
            ],

            // PENDAPATAN (4xxx)
            [
                'code' => '4010',
                'name' => 'Pendapatan Jasa Pengembangan',
                'category' => 'PENDAPATAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Penghasilan dari penyelesaian milestone proyek software custom (web, mobile, ERP).',
            ],
            [
                'code' => '4020',
                'name' => 'Pendapatan Cloud/SLA',
                'category' => 'PENDAPATAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Pendapatan berulang bulanan/tahunan dari hosting server terkelola dan komitmen SLA.',
            ],
            [
                'code' => '4030',
                'name' => 'Pendapatan Maintenance',
                'category' => 'PENDAPATAN',
                'normal_balance' => 'K',
                'is_original_prd' => true,
                'description' => 'Pendapatan dari kontrak perpanjangan garansi bug fixes dan dukungan teknis helpdesk.',
            ],

            // HPP (5xxx)
            [
                'code' => '5010',
                'name' => 'HPP — Direct Cost Proyek',
                'category' => 'HPP',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Biaya langsung proyek seperti domain, SSL khusus klien, lisensi template, dan perjalanan dinas onsite.',
            ],
            [
                'code' => '5020',
                'name' => 'HPP — Subkon & Freelancer',
                'category' => 'HPP',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Imbalan jasa mitra pengembang freelance dan subkontraktor spesialis per SPK.',
            ],
            [
                'code' => '5030',
                'name' => 'HPP — Cloud & Server Proyek',
                'category' => 'HPP',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Biaya komputasi cloud (AWS/GCP/Alibaba) yang didedikasikan langsung untuk proyek klien.',
            ],
            [
                'code' => '5040',
                'name' => 'HPP — Direct Labor (dari timelog)',
                'category' => 'HPP',
                'normal_balance' => 'D',
                'is_original_prd' => false,
                'description' => 'Beban alokasi biaya tenaga kerja internal berdasarkan jam kerja riil timesheet proyek.',
            ],

            // OPEX (6xxx)
            [
                'code' => '6010',
                'name' => 'Beban Gaji & Upah Tetap',
                'category' => 'OPEX',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Gaji pokok dan tunjangan tetap staf internal kantor pusat (management, sales, HR, finance).',
            ],
            [
                'code' => '6020',
                'name' => 'Beban Sewa Gedung',
                'category' => 'OPEX',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Sewa ruang kantor Menara Astacode dan biaya service charge bulanan.',
            ],
            [
                'code' => '6030',
                'name' => 'Beban Langganan Software/SaaS',
                'category' => 'OPEX',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Biaya langganan tools kerja internal (Google Workspace, Slack, GitHub Enterprise, Figma).',
            ],
            [
                'code' => '6040',
                'name' => 'Beban Utilitas & Operasional Kantor',
                'category' => 'OPEX',
                'normal_balance' => 'D',
                'is_original_prd' => true,
                'description' => 'Listrik PLN, koneksi internet serat optik kantor, ATK, konsumsi, dan pantry.',
            ],
            [
                'code' => '6050',
                'name' => 'Beban Pemasaran & Iklan',
                'category' => 'OPEX',
                'normal_balance' => 'D',
                'is_original_prd' => false,
                'description' => 'Biaya belanja iklan Meta Ads, Google Ads, konten kreatif sosmed, dan sponsorship seminar.',
            ],
        ];

        foreach ($accounts as $acc) {
            DB::table('fin_chart_of_accounts')->updateOrInsert(
                ['code' => $acc['code']],
                [
                    'name' => $acc['name'],
                    'category' => $acc['category'],
                    'normal_balance' => $acc['normal_balance'],
                    'is_original_prd' => $acc['is_original_prd'],
                    'description' => $acc['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

