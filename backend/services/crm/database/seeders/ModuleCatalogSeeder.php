<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // 1. AUTH & SECURITY
            [
                'code' => 'MOD-AUTH-01',
                'name' => 'Autentikasi Multi-Factor TOTP (RFC 6238)',
                'category' => 'AUTH_SECURITY',
                'description' => 'Two-Factor Authentication via Google Authenticator/Authy dengan QR code setup dan kode recovery darurat.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-AUTH-02',
                'name' => 'Single Sign-On (SSO) OAuth2 & Google/Microsoft',
                'category' => 'AUTH_SECURITY',
                'description' => 'Integrasi login korporat pihak ketiga via OpenID Connect / OAuth2.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-AUTH-03',
                'name' => 'Manajemen Role & Hak Akses Granular (RBAC)',
                'category' => 'AUTH_SECURITY',
                'description' => 'Hierarki permission modul.entitas.aksi dengan Spatie Laravel Permission dan UI switch izin.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-AUTH-04',
                'name' => 'Audit Trail & Activity Log Immutable',
                'category' => 'AUTH_SECURITY',
                'description' => 'Pencatatan riwayat perubahan data (siapa, kapan, sebelum, sesudah, IP) dengan tabel append-only.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-AUTH-05',
                'name' => 'Deteksi Brute Force & Session Lockout',
                'category' => 'AUTH_SECURITY',
                'description' => 'Proteksi login rate limiting, lockout 5x percobaan, dan pencabutan sesi perangkat aktif.',
                'mandays' => ['BACKEND_ENGINEER' => 2, 'FRONTEND_ENGINEER' => 1, 'QA_ENGINEER' => 1],
            ],

            // 2. HRD & WORKLOAD
            [
                'code' => 'MOD-HR-01',
                'name' => 'Presensi Mobile GPS Geofencing & Selfie',
                'category' => 'HR_PAYROLL',
                'description' => 'Check-in/out mobile dengan validasi radius geofence kantor ≤ 50m atau BSSID WiFi, dan deteksi mock location.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'MOBILE_ENGINEER' => 5, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-HR-02',
                'name' => 'Pengajuan & Approval Cuti Berjenjang Multi-Level',
                'category' => 'HR_PAYROLL',
                'description' => 'Alur permohonan cuti tahunan, sakit, dan izin dengan saldo kuota real-time dan notifikasi approval.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-HR-03',
                'name' => 'Engine Payroll Multi-Model (Tetap, Kontrak, Freelance)',
                'category' => 'HR_PAYROLL',
                'description' => 'Perhitungan gaji pokok, tunjangan, insentif lembur, potongan BPJS Ketenagakerjaan & PPh 21 TER.',
                'mandays' => ['BACKEND_ENGINEER' => 6, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 3],
            ],
            [
                'code' => 'MOD-HR-04',
                'name' => 'Slip Gaji Terenkripsi PDF & Pengiriman Email',
                'category' => 'HR_PAYROLL',
                'description' => 'Generator payslip berpassword otomatis dan distribusi massal via email.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 1, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-HR-05',
                'name' => 'Manajemen Shift Kerja & Klaim Night Workload',
                'category' => 'HR_PAYROLL',
                'description' => 'Jadwal shift normal, fleksibel, serta deteksi lembur di atas 22:00 dengan opsi auto-carryover cuti.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-HR-06',
                'name' => 'Manajemen Kuota & Evaluasi Magang / PKL Ber-QR',
                'category' => 'HR_PAYROLL',
                'description' => 'Portal pengajuan magang, kuota departemen, logbook pembimbing, dan e-Sertifikat PDF ber-QR.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-HR-07',
                'name' => 'Penilaian Kinerja 360 Derajat & KPI Karyawan',
                'category' => 'HR_PAYROLL',
                'description' => 'Kuesioner evaluasi atasan-bawahan-rekan sejawat dengan kalkulasi bobot skor otomatis.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],

            // 3. FINANCE & INVOICING
            [
                'code' => 'MOD-FIN-01',
                'name' => 'Payment Gateway Integration (Midtrans / Xendit)',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Virtual account otomatis, QRIS dinamis, kartu kredit, dan webhook callback verifikasi lunas.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-FIN-02',
                'name' => 'Invoicing Termin Bertahap & Penagihan Otomatis',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Penerbitan faktur tagihan berbasis milestone TOP dengan reminder jatuh tempo otomatis.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-FIN-03',
                'name' => 'Kalkulator Pajak Otomatis (PPN 11%, PPh 21, PPh 23)',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Perhitungan pajak jasa software, bukti potong withholding tax, dan integrasi e-Faktur/e-Bupot.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-FIN-04',
                'name' => 'Buku Besar & Jurnal Umum Double-Entry Akrual',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Sistem akuntansi 23 akun COA dengan validasi ketat Debit = Kredit dan closing periode.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 3],
            ],
            [
                'code' => 'MOD-FIN-05',
                'name' => 'Pencatatan Rekonsiliasi Bank & Kas Kecil (Petty Cash)',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Mutasi saldo non-P&L antar kas bank, pengisian kas kecil, dan pelaporan disbursement.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-FIN-06',
                'name' => 'Laporan Keuangan P&L, Neraca & Arus Kas Konsolidasi',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Penyajian laba rugi per proyek vs perusahaan, perhitungan EBITDA, dan proyeksi runway kas.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-FIN-07',
                'name' => 'OCR Pemindaian Struk Pengeluaran & Verifikasi Klaim',
                'category' => 'FINANCE_PAYMENT',
                'description' => 'Ekstraksi otomatis tanggal, nama vendor, nominal, dan pajak dari foto struk belanja.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],

            // 4. CRM & SALES
            [
                'code' => 'MOD-CRM-01',
                'name' => 'Pipeline Penjualan Visual Kanban & Lead Scoring',
                'category' => 'CRM_SALES',
                'description' => 'Manajemen lead interaktif drag-and-drop dengan probabilitas closing dan peringatan stale lead.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-CRM-02',
                'name' => 'Generator Penawaran (Quotation) & RAB Otomatis',
                'category' => 'CRM_SALES',
                'description' => 'Kalkulator mandays tim, margin gross target, skema termin TOP, dan ekspor PDF resmi ber-QR.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-CRM-03',
                'name' => 'Legal Studio & Integrasi e-Sign / e-Meterai Peruri',
                'category' => 'CRM_SALES',
                'description' => 'Manajemen 22 pasal MoU baku, pembuatan Addendum, dan log sertifikat digital penandatanganan.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-CRM-04',
                'name' => 'WhatsApp Client Care Inbox Terintegrasi AI',
                'category' => 'CRM_SALES',
                'description' => 'Inbox bersama tim CS/Sales via WhatsApp Cloud API dengan smart draft balasan berbasis AI.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-CRM-05',
                'name' => 'Customer 360 View & Riwayat Interaksi Omnichannel',
                'category' => 'CRM_SALES',
                'description' => 'Profil terpadu klien mencakup histori deal, proyek aktif, tagihan, tiket helpdesk, dan health score.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-CRM-06',
                'name' => 'Voice Note Recorder & Ekstraksi Kebutuhan AI',
                'category' => 'CRM_SALES',
                'description' => 'Perekaman audio percakapan meeting, transkripsi Whisper, dan peringkasan scope proyek otomatis.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],

            // 5. PROJECT MANAGEMENT
            [
                'code' => 'MOD-PM-01',
                'name' => 'Kanban Board Interaktif & Manajemen Sprint Agile',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Papan tugas backlog, to do, in progress, code review, QA, dengan estimasi story points.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 5, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-PM-02',
                'name' => 'Pencatatan Jam Kerja (Time Tracking) & Timesheet',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Pelacakan durasi pengerjaan per task yang tersinkronisasi langsung ke beban HPP proyek.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-PM-03',
                'name' => 'Manajemen Milestone Proyek & Portal UAT Klien',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Portal persetujuan serah terima deliverable oleh klien dengan Berita Acara UAT digital.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-PM-04',
                'name' => 'Helpdesk Ticketing & SLA Escalation Matrix',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Sistem penanganan keluhan klien tingkat keparahan P1/P2/P3 dengan matriks waktu respon.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-PM-05',
                'name' => 'Wiki Dokumentasi Proyek & Release Notes',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Knowledge base proyek Markdown, panduan instalasi, dan log pembaruan versi rilis.',
                'mandays' => ['BACKEND_ENGINEER' => 2, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-PM-06',
                'name' => 'Manajemen Subkontraktor & Penerbitan SPK Otomatis',
                'category' => 'PROJECT_MANAGEMENT',
                'description' => 'Perjanjian kerja freelance, kontrol deliverable, dan verifikasi batas nominal honor.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],

            // 6. COMMERCE & LOGISTICS
            [
                'code' => 'MOD-COM-01',
                'name' => 'Katalog Produk & Manajemen Stok Multi-Gudang',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'Manajemen SKU barang, varian produk, mutasi antar lokasi gudang, dan notifikasi low stock.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-COM-02',
                'name' => 'Sistem Kasir POS Offline-First & Sinkronisasi Cloud',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'Point of Sale berbasis IndexedDB yang tetap beroperasi lancar tanpa koneksi internet.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 6, 'QA_ENGINEER' => 3],
            ],
            [
                'code' => 'MOD-COM-03',
                'name' => 'Keranjang Belanja E-Commerce & Checkout Pembayaran',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'Flow belanja konsumen, kupon diskon promo, dan perhitungan ongkos kirim otomatis.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-COM-04',
                'name' => 'Integrasi Kurir Logistik (JNE, SiCepat, J&T) & Cek Ongkir',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'API pelacakan resi pengiriman ekspedisi dan booking pickup kurir real-time.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-COM-05',
                'name' => 'Pelacakan Armada Logistik IoT & GPS Telematika',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'Peta monitoring pergerakan kendaraan, rute pengiriman, dan estimasi waktu sampai (ETA).',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 4, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-COM-06',
                'name' => 'Sinkronisasi Marketplace (Tokopedia & Shopee)',
                'category' => 'COMMERCE_LOGISTICS',
                'description' => 'Penyesuaian stok produk dan penarikan order terpusat dari platform marketplace ternama.',
                'mandays' => ['BACKEND_ENGINEER' => 6, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 3],
            ],

            // 7. AI & AUTOMATION
            [
                'code' => 'MOD-AI-01',
                'name' => 'AI Smart Assistant & Chatbot Layanan Pelanggan',
                'category' => 'AI_AUTOMATION',
                'description' => 'LLM berbasis RAG dokumen perusahaan untuk menjawab FAQ klien 24/7 secara akurat.',
                'mandays' => ['BACKEND_ENGINEER' => 5, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-AI-02',
                'name' => 'AI Prompt Registry & Cost Budget Guardrail',
                'category' => 'AI_AUTOMATION',
                'description' => 'Pengelolaan versi prompt, pembatasan token bulanan per modul, dan pencegahan prompt injection.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-AI-03',
                'name' => 'AI Document Parser & Resume Screening ATS',
                'category' => 'AI_AUTOMATION',
                'description' => 'Ekstraksi riwayat CV pelamar kerja dan pencocokan skor kesesuaian dengan lowongan jabatan.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 2],
            ],
            [
                'code' => 'MOD-AI-04',
                'name' => 'Otomasi Rule Engine No-Code & Trigger Notifikasi',
                'category' => 'AI_AUTOMATION',
                'description' => 'Evaluasi logika kondisi JSON jika-maka untuk mengirim pesan broadcast WhatsApp & Email.',
                'mandays' => ['BACKEND_ENGINEER' => 4, 'FRONTEND_ENGINEER' => 3, 'QA_ENGINEER' => 2],
            ],

            // 8. INFRASTRUCTURE & UTILITIES
            [
                'code' => 'MOD-INF-01',
                'name' => 'File Storage Aman dengan Signed URLs & Virus Scanner',
                'category' => 'OPERATIONS_INFRA',
                'description' => 'Penyimpanan berkas privat terenkripsi dengan url berkedaluwarsa dan inspeksi malware.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 1, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-INF-02',
                'name' => 'Export & Import Data Excel / CSV Berkapasitas Besar',
                'category' => 'OPERATIONS_INFRA',
                'description' => 'Chunked streaming import/export ratusan ribu baris data tanpa membebani memory server.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
            [
                'code' => 'MOD-INF-03',
                'name' => 'Push Notification Realtime (Web Push & Firebase)',
                'category' => 'OPERATIONS_INFRA',
                'description' => 'Pengiriman notifikasi ke browser dan ponsel pengguna secara instan via Server-Sent Events / FCM.',
                'mandays' => ['BACKEND_ENGINEER' => 3, 'FRONTEND_ENGINEER' => 2, 'QA_ENGINEER' => 1],
            ],
        ];

        foreach ($modules as $mod) {
            $totalMandays = (int) array_sum($mod['mandays']);

            DB::table('crm_module_catalog')->updateOrInsert(
                ['code' => $mod['code']],
                [
                    'name' => $mod['name'],
                    'category' => $mod['category'],
                    'default_mandays_by_role' => json_encode($mod['mandays']),
                    'total_mandays' => $totalMandays,
                    'description' => $mod['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

