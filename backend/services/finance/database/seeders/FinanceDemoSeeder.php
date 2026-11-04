<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Invoices for the 5 projects
        $invoices = [
            [
                'invoice_number' => 'INV/2026/02/0001',
                'customer_id' => 'CUS-0001',
                'customer_name' => 'PT Megah Nusantara Teknologi',
                'project_id' => 'PRJ-001',
                'project_title' => 'Aplikasi HRD & Presensi GPS 500 Karyawan',
                'term_number' => 1,
                'term_name' => 'Termin 1 — Uang Muka (DP 30%)',
                'dpp_amount' => 75000000,
                'vat_rate' => 11.0,
                'vat_amount' => 8250000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 1500000,
                'total_bill' => 83250000, // 75jt + 8.25jt
                'net_cash_receivable' => 81750000, // 83.25jt - 1.5jt
                'status' => 'PAID',
                'issue_date' => '2026-02-05',
                'due_date' => '2026-02-19',
                'paid_at' => '2026-02-12 14:30:00',
            ],
            [
                'invoice_number' => 'INV/2026/03/0002',
                'customer_id' => 'CUS-0001',
                'customer_name' => 'PT Megah Nusantara Teknologi',
                'project_id' => 'PRJ-001',
                'project_title' => 'Aplikasi HRD & Presensi GPS 500 Karyawan',
                'term_number' => 2,
                'term_name' => 'Termin 2 — Progress Sprint Demo (40%)',
                'dpp_amount' => 100000000,
                'vat_rate' => 11.0,
                'vat_amount' => 11000000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 2000000,
                'total_bill' => 111000000,
                'net_cash_receivable' => 109000000,
                'status' => 'ISSUED',
                'issue_date' => '2026-03-01',
                'due_date' => '2026-03-15',
                'paid_at' => null,
            ],
            [
                'invoice_number' => 'INV/2026/02/0003',
                'customer_id' => 'CUS-0002',
                'customer_name' => 'CV Borneo Digital Prima',
                'project_id' => 'PRJ-002',
                'project_title' => 'Sistem Telematika IoT Armada Truk Ekspedisi',
                'term_number' => 1,
                'term_name' => 'Termin 1 — Uang Muka (DP 30%)',
                'dpp_amount' => 58500000,
                'vat_rate' => 11.0,
                'vat_amount' => 6435000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 1170000,
                'total_bill' => 64935000,
                'net_cash_receivable' => 63765000,
                'status' => 'PAID',
                'issue_date' => '2026-02-10',
                'due_date' => '2026-02-24',
                'paid_at' => '2026-02-18 10:15:00',
            ],
            [
                'invoice_number' => 'INV/2026/02/0004',
                'customer_id' => 'CUS-0003',
                'customer_name' => 'PT Sinar Surya Logistik',
                'project_id' => 'PRJ-003',
                'project_title' => 'ERP Distribusi Multi-Cabang & Manajemen Gudang',
                'term_number' => 1,
                'term_name' => 'Termin 1 — Uang Muka (DP 30%)',
                'dpp_amount' => 114000000,
                'vat_rate' => 11.0,
                'vat_amount' => 12540000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 2280000,
                'total_bill' => 126540000,
                'net_cash_receivable' => 124260000,
                'status' => 'PAID',
                'issue_date' => '2026-02-15',
                'due_date' => '2026-03-01',
                'paid_at' => '2026-02-25 11:00:00',
            ],
            [
                'invoice_number' => 'INV/2026/03/0005',
                'customer_id' => 'CUS-0004',
                'customer_name' => 'PT Solusi Finansial Sejahtera',
                'project_id' => 'PRJ-004',
                'project_title' => 'Mobile Banking & Payment Hub Korporat',
                'term_number' => 1,
                'term_name' => 'Termin 1 — Uang Muka (DP 30%)',
                'dpp_amount' => 135000000,
                'vat_rate' => 11.0,
                'vat_amount' => 14850000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 2700000,
                'total_bill' => 149850000,
                'net_cash_receivable' => 147150000,
                'status' => 'ISSUED',
                'issue_date' => '2026-03-02',
                'due_date' => '2026-03-16',
                'paid_at' => null,
            ],
            [
                'invoice_number' => 'INV/2026/02/0006',
                'customer_id' => 'CUS-0006',
                'customer_name' => 'PT Bahari Retail Indonesia',
                'project_id' => 'PRJ-005',
                'project_title' => 'E-Commerce Omnichannel B2B & Kasir POS Multi-Store',
                'term_number' => 1,
                'term_name' => 'Termin 1 — Uang Muka (DP 30%)',
                'dpp_amount' => 84000000,
                'vat_rate' => 11.0,
                'vat_amount' => 9240000,
                'pph23_rate' => 2.0,
                'pph23_amount' => 1680000,
                'total_bill' => 93240000,
                'net_cash_receivable' => 91560000,
                'status' => 'PAID',
                'issue_date' => '2026-02-20',
                'due_date' => '2026-03-06',
                'paid_at' => '2026-02-27 16:45:00',
            ],
        ];

        foreach ($invoices as $inv) {
            DB::table('fin_invoices')->updateOrInsert(
                ['invoice_number' => $inv['invoice_number']],
                array_merge($inv, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Balanced Double-Entry General Journals (AC-FIN-01, AC-FIN-04)
        $journals = [
            // JRN 1: Terbit Invoice DP PT Megah (D: 1030 Piutang / K: 2040 DP, K: 2020 PPN)
            [
                'entry_number' => 'JRN-AR/2026/02/0001',
                'source_type' => 'AR',
                'reference_number' => 'INV/2026/02/0001',
                'date' => '2026-02-05',
                'description' => 'Penerbitan Invoice DP 30% Proyek HRD GPS PT Megah Nusantara',
                'total_debit' => 83250000,
                'total_credit' => 83250000,
                'lines' => [
                    ['account_code' => '1030', 'account_name' => 'Piutang Usaha', 'entry_type' => 'DEBIT', 'amount' => 83250000, 'notes' => 'Tagihan termin 1'],
                    ['account_code' => '2040', 'account_name' => 'Pendapatan Diterima Dimuka (DP)', 'entry_type' => 'CREDIT', 'amount' => 75000000, 'notes' => 'DPP uang muka'],
                    ['account_code' => '2020', 'account_name' => 'Utang PPN', 'entry_type' => 'CREDIT', 'amount' => 8250000, 'notes' => 'PPN 11%'],
                ],
            ],
            // JRN 2: Pelunasan Invoice DP PT Megah (D: 1010 Bank BCA, D: 1040 PPh 23 / K: 1030 Piutang)
            [
                'entry_number' => 'JRN-AR/2026/02/0002',
                'source_type' => 'AR',
                'reference_number' => 'INV/2026/02/0001',
                'date' => '2026-02-12',
                'description' => 'Penerimaan Pelunasan DP Proyek HRD GPS PT Megah Nusantara Teknologi',
                'total_debit' => 83250000,
                'total_credit' => 83250000,
                'lines' => [
                    ['account_code' => '1010', 'account_name' => 'Kas/Bank Operasional (BCA)', 'entry_type' => 'DEBIT', 'amount' => 81750000, 'notes' => 'Kas bersih masuk rekening BCA'],
                    ['account_code' => '1040', 'account_name' => 'Uang Muka Pajak PPh 23', 'entry_type' => 'DEBIT', 'amount' => 1500000, 'notes' => 'Pemotongan PPh 23 2% oleh klien'],
                    ['account_code' => '1030', 'account_name' => 'Piutang Usaha', 'entry_type' => 'CREDIT', 'amount' => 83250000, 'notes' => 'Pelunasan piutang termin 1'],
                ],
            ],
            // JRN 3: Mutasi Internal BCA -> Kas Kecil (D: 1020 Petty Cash / K: 1010 Bank BCA)
            [
                'entry_number' => 'JRN-TRF/2026/02/0003',
                'source_type' => 'TRF',
                'reference_number' => 'PETTY-REFILL-01',
                'date' => '2026-02-15',
                'description' => 'Penarikan Kas Operasional Pengisian Kas Kecil Kantor (Non-P&L)',
                'total_debit' => 10000000,
                'total_credit' => 10000000,
                'lines' => [
                    ['account_code' => '1020', 'account_name' => 'Kas Kecil (Petty Cash)', 'entry_type' => 'DEBIT', 'amount' => 10000000, 'notes' => 'Penerimaan fisik kas kecil'],
                    ['account_code' => '1010', 'account_name' => 'Kas/Bank Operasional (BCA)', 'entry_type' => 'CREDIT', 'amount' => 10000000, 'notes' => 'Debet rekening giro BCA'],
                ],
            ],
            // JRN 4: Beban Gaji Bulanan (D: 6010 Beban Gaji HQ / K: 2030 Utang Gaji)
            [
                'entry_number' => 'JRN-HR/2026/02/0004',
                'source_type' => 'HR',
                'reference_number' => 'PAY/2026/02',
                'date' => '2026-02-28',
                'description' => 'Akrual Payroll Staf Tetap Periode Februari 2026',
                'total_debit' => 85000000,
                'total_credit' => 85000000,
                'lines' => [
                    ['account_code' => '6010', 'account_name' => 'Beban Gaji & Upah Tetap', 'entry_type' => 'DEBIT', 'amount' => 85000000, 'notes' => 'Total gaji pokok dan tunjangan'],
                    ['account_code' => '2030', 'account_name' => 'Utang Gaji', 'entry_type' => 'CREDIT', 'amount' => 85000000, 'notes' => 'Kewajiban payroll berjalan'],
                ],
            ],
            // JRN 5: Pengeluaran Kantor via Kas Kecil (D: 6040 Utilitas / K: 1020 Kas Kecil)
            [
                'entry_number' => 'JRN-OPEX/2026/02/0005',
                'source_type' => 'OPEX',
                'reference_number' => 'EXP-2026-0042',
                'date' => '2026-02-20',
                'description' => 'Reimbursement Pembelian ATK, Snack Rapat, dan Konsumsi Lembur Kantor',
                'total_debit' => 4500000,
                'total_credit' => 4500000,
                'lines' => [
                    ['account_code' => '6040', 'account_name' => 'Beban Utilitas & Operasional Kantor', 'entry_type' => 'DEBIT', 'amount' => 4500000, 'notes' => 'Klaim operasional bulanan'],
                    ['account_code' => '1020', 'account_name' => 'Kas Kecil (Petty Cash)', 'entry_type' => 'CREDIT', 'amount' => 4500000, 'notes' => 'Pengeluaran fisik kas kecil'],
                ],
            ],
        ];

        foreach ($journals as $j) {
            $lines = $j['lines'];
            unset($j['lines']);

            $j['is_balanced'] = ($j['total_debit'] === $j['total_credit']);
            $j['is_posted'] = true;
            $j['posted_at'] = now();
            $j['created_at'] = now();
            $j['updated_at'] = now();

            DB::table('fin_journal_entries')->updateOrInsert(
                ['entry_number' => $j['entry_number']],
                $j
            );

            $entryId = DB::table('fin_journal_entries')->where('entry_number', $j['entry_number'])->value('id');

            // Delete old lines to keep idempotent
            DB::table('fin_journal_lines')->where('journal_entry_id', $entryId)->delete();

            foreach ($lines as $line) {
                DB::table('fin_journal_lines')->insert(
                    array_merge($line, [
                        'journal_entry_id' => $entryId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }
    }
}

