<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostingRuleSeeder extends Seeder
{
    public function run(): void
    {
        // PRD §10.5 Pola Jurnal Otomatis [Basis Akrual]
        $rules = [
            [
                'event_code' => 'INVOICE_ISSUED_SERVICE',
                'name' => 'Invoice Termin Diterbitkan (Jasa Dikerjakan)',
                'description' => 'Pencatatan akrual saat invoice milestone diterbitkan: (D) 1030 Piutang Usaha [DPP+PPN] / (K) 4010 Pendapatan Jasa [DPP], (K) 2020 Utang PPN [PPN].',
                'debit_account_code' => '1030',
                'credit_account_code' => '4010',
                'secondary_rules' => json_encode([
                    ['entry_type' => 'CREDIT', 'account_code' => '2020', 'amount_source' => 'vat_amount', 'name' => 'Utang PPN Keluaran'],
                ]),
            ],
            [
                'event_code' => 'INVOICE_ISSUED_DP',
                'name' => 'Invoice DP / Uang Muka Diterbitkan (Belum Ada Jasa)',
                'description' => 'Pencatatan akrual saat invoice uang muka diterbitkan: (D) 1030 Piutang Usaha / (K) 2040 Pendapatan Diterima Dimuka [DPP], (K) 2020 Utang PPN [PPN].',
                'debit_account_code' => '1030',
                'credit_account_code' => '2040',
                'secondary_rules' => json_encode([
                    ['entry_type' => 'CREDIT', 'account_code' => '2020', 'amount_source' => 'vat_amount', 'name' => 'Utang PPN Keluaran'],
                ]),
            ],
            [
                'event_code' => 'DP_REVENUE_RECOGNITION',
                'name' => 'Pengakuan Pendapatan dari Uang Muka (Milestone/UAT)',
                'description' => 'Reklasifikasi uang muka menjadi pendapatan saat deliverable disetujui: (D) 2040 Pendapatan Diterima Dimuka / (K) 4010 Pendapatan Jasa.',
                'debit_account_code' => '2040',
                'credit_account_code' => '4010',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'PAYMENT_RECEIVED',
                'name' => 'Pembayaran Klien Diterima di Bank',
                'description' => 'Pencatatan kas masuk dan bukti potong pajak: (D) 1010 Bank BCA [Kas Bersih], (D) 1040 Uang Muka PPh 23 [PPh 2%] / (K) 1030 Piutang Usaha [DPP+PPN].',
                'debit_account_code' => '1010',
                'credit_account_code' => '1030',
                'secondary_rules' => json_encode([
                    ['entry_type' => 'DEBIT', 'account_code' => '1040', 'amount_source' => 'pph23_amount', 'name' => 'Uang Muka PPh 23'],
                ]),
            ],
            [
                'event_code' => 'EXPENSE_PROJECT',
                'name' => 'Klaim Pengeluaran Langsung Proyek (Direct Cost)',
                'description' => 'Pengeluaran pembelian lisensi/server proyek via kas kecil/bank: (D) 5010 HPP Direct Cost / (K) 1020 Kas Kecil.',
                'debit_account_code' => '5010',
                'credit_account_code' => '1020',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'EXPENSE_OFFICE',
                'name' => 'Klaim Pengeluaran Operasional & Utilitas Kantor',
                'description' => 'Beban konsumsi rapat, ATK, pantry: (D) 6040 Beban Utilitas/Operasional / (K) 1020 Kas Kecil.',
                'debit_account_code' => '6040',
                'credit_account_code' => '1020',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'FREELANCER_SPK_PAID',
                'name' => 'Pembayaran SPK Freelance / Subkontraktor',
                'description' => 'Pelunasan deliverable mitra freelance proyek: (D) 5020 HPP Subkon & Freelancer / (K) 1010 Bank BCA.',
                'debit_account_code' => '5020',
                'credit_account_code' => '1010',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'CLOUD_BILL_PROJECT',
                'name' => 'Tagihan Cloud Dedicated Proyek Klien (AWS/GCP)',
                'description' => 'Beban komputasi server khusus proyek: (D) 5030 HPP Cloud Proyek / (K) 2010 Utang Vendor.',
                'debit_account_code' => '5030',
                'credit_account_code' => '2010',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'SAAS_BILL_INTERNAL',
                'name' => 'Tagihan Layanan SaaS Operasional Internal',
                'description' => 'Langganan software internal (Slack, Google Workspace): (D) 6030 Beban Langganan SaaS / (K) 1010 Bank BCA.',
                'debit_account_code' => '6030',
                'credit_account_code' => '1010',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'PAYROLL_STAFF',
                'name' => 'Beban Gaji Staf Tetap Bulanan',
                'description' => 'Akrual payroll bulanan karyawan kantor: (D) 6010 Beban Gaji & Upah Tetap / (K) 2030 Utang Gaji.',
                'debit_account_code' => '6010',
                'credit_account_code' => '2030',
                'secondary_rules' => null,
            ],
            [
                'event_code' => 'INTERNAL_CASH_TRANSFER',
                'name' => 'Mutasi Internal Antar Rekening (Non-P&L)',
                'description' => 'Tarik tunai dari bank BCA untuk isi kas kecil: (D) 1020 Kas Kecil / (K) 1010 Bank BCA (tidak mempengaruhi laba rugi).',
                'debit_account_code' => '1020',
                'credit_account_code' => '1010',
                'secondary_rules' => null,
            ],
        ];

        foreach ($rules as $r) {
            DB::table('fin_posting_rules')->updateOrInsert(
                ['event_code' => $r['event_code']],
                [
                    'name' => $r['name'],
                    'description' => $r['description'],
                    'debit_account_code' => $r['debit_account_code'],
                    'credit_account_code' => $r['credit_account_code'],
                    'secondary_rules' => $r['secondary_rules'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

