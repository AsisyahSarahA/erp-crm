<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PmDemoSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'project_code' => 'PRJ-001',
                'customer_id' => 'CUS-0001',
                'customer_name' => 'PT Megah Nusantara Teknologi',
                'deal_id' => 'LD-2026-0022',
                'title' => 'Aplikasi HRD & Presensi GPS 500 Karyawan',
                'project_type' => 'ERP_CUSTOM',
                'status' => 'ACTIVE',
                'health' => 'GREEN',
                'contract_value' => 250000000,
                'budget_mandays' => 110,
                'consumed_mandays' => 45.5,
                'start_date' => '2026-02-15',
                'target_end_date' => '2026-05-30',
                'pm_user_name' => 'Doni Project Manager',
                'milestones' => [
                    ['name' => 'Milestone 1 — Setup Arsitektur & Database', 'term_number' => 1, 'percentage' => 30.0, 'status' => 'APPROVED'],
                    ['name' => 'Milestone 2 — Presensi Geofencing & Approval Cuti', 'term_number' => 2, 'percentage' => 40.0, 'status' => 'IN_PROGRESS'],
                    ['name' => 'Milestone 3 — Multi-model Payroll & UAT Final', 'term_number' => 3, 'percentage' => 30.0, 'status' => 'PENDING'],
                ],
                'tasks' => [
                    ['key' => 'PRJ-001-01', 'title' => 'Setup Geofence Radius Calculator & GPS Mock Detection', 'status' => 'DONE', 'priority' => 'HIGH', 'role' => 'BE', 'est' => 3.0, 'act' => 24.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-001-02', 'title' => 'Implementasi UI Check-in Selfie & Camera Capture', 'status' => 'DONE', 'priority' => 'HIGH', 'role' => 'FE', 'est' => 2.5, 'act' => 20.0, 'assignee' => 'Rian Hidayat'],
                    ['key' => 'PRJ-001-03', 'title' => 'Engine Perhitungan Saldo Cuti Berjenjang Multi-Level', 'status' => 'IN_PROGRESS', 'priority' => 'MEDIUM', 'role' => 'BE', 'est' => 4.0, 'act' => 16.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-001-04', 'title' => 'Export Slip Gaji PDF Terenkripsi Password Email', 'status' => 'TODO', 'priority' => 'MEDIUM', 'role' => 'BE', 'est' => 3.0, 'act' => 0.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-001-05', 'title' => 'UAT Testing Modul Presensi & Verifikasi Koordinat', 'status' => 'TODO', 'priority' => 'HIGH', 'role' => 'QA', 'est' => 2.0, 'act' => 0.0, 'assignee' => 'Kiki Amalia'],
                ],
            ],
            [
                'project_code' => 'PRJ-002',
                'customer_id' => 'CUS-0002',
                'customer_name' => 'CV Borneo Digital Prima',
                'deal_id' => 'LD-2026-0023',
                'title' => 'Sistem Telematika IoT Armada Truk Ekspedisi',
                'project_type' => 'IOT_TELEMATICS',
                'status' => 'ACTIVE',
                'health' => 'GREEN',
                'contract_value' => 195000000,
                'budget_mandays' => 85,
                'consumed_mandays' => 32.0,
                'start_date' => '2026-02-20',
                'target_end_date' => '2026-05-15',
                'pm_user_name' => 'Doni Project Manager',
                'milestones' => [
                    ['name' => 'Milestone 1 — Protokol Telemetry GPS IoT Ingestion', 'term_number' => 1, 'percentage' => 30.0, 'status' => 'APPROVED'],
                    ['name' => 'Milestone 2 — Peta Monitoring Armada Realtime & Rute', 'term_number' => 2, 'percentage' => 40.0, 'status' => 'IN_PROGRESS'],
                    ['name' => 'Milestone 3 — Laporan Pemeliharaan Kendaraan & UAT', 'term_number' => 3, 'percentage' => 30.0, 'status' => 'PENDING'],
                ],
                'tasks' => [
                    ['key' => 'PRJ-002-01', 'title' => 'Ingestion Server MQTT Sensor Bahan Bakar & Kecepatan', 'status' => 'DONE', 'priority' => 'URGENT', 'role' => 'BE', 'est' => 4.0, 'act' => 32.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-002-02', 'title' => 'Komponen Leaflet/Mapbox Tracking Armada Realtime', 'status' => 'IN_PROGRESS', 'priority' => 'HIGH', 'role' => 'FE', 'est' => 3.5, 'act' => 18.0, 'assignee' => 'Rian Hidayat'],
                    ['key' => 'PRJ-002-03', 'title' => 'Alert Notifikasi WhatsApp Saat Truk Keluar Jalur', 'status' => 'TODO', 'priority' => 'MEDIUM', 'role' => 'BE', 'est' => 2.0, 'act' => 0.0, 'assignee' => 'Joko Susilo'],
                ],
            ],
            [
                'project_code' => 'PRJ-003',
                'customer_id' => 'CUS-0003',
                'customer_name' => 'PT Sinar Surya Logistik',
                'deal_id' => 'LD-2026-0024',
                'title' => 'ERP Distribusi Multi-Cabang & Manajemen Gudang',
                'project_type' => 'ERP_CUSTOM',
                'status' => 'ACTIVE',
                'health' => 'YELLOW',
                'contract_value' => 380000000,
                'budget_mandays' => 160,
                'consumed_mandays' => 78.0,
                'start_date' => '2026-01-20',
                'target_end_date' => '2026-06-30',
                'pm_user_name' => 'Doni Project Manager',
                'milestones' => [
                    ['name' => 'Milestone 1 — Master SKU & Struktur Multi-Gudang', 'term_number' => 1, 'percentage' => 30.0, 'status' => 'APPROVED'],
                    ['name' => 'Milestone 2 — Modul Surat Jalan & Mutasi Antar Gudang', 'term_number' => 2, 'percentage' => 40.0, 'status' => 'IN_PROGRESS'],
                    ['name' => 'Milestone 3 — Integrasi Akuntansi Jurnal HPP Otomatis', 'term_number' => 3, 'percentage' => 30.0, 'status' => 'PENDING'],
                ],
                'tasks' => [
                    ['key' => 'PRJ-003-01', 'title' => 'Migrasi Master 15.000 SKU Barang dan Satuan Bertingkat', 'status' => 'DONE', 'priority' => 'HIGH', 'role' => 'BE', 'est' => 4.0, 'act' => 35.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-003-02', 'title' => 'UI Spreadsheet Mutasi Stok Cepat Antar Cabang', 'status' => 'IN_PROGRESS', 'priority' => 'HIGH', 'role' => 'FE', 'est' => 5.0, 'act' => 30.0, 'assignee' => 'Rian Hidayat'],
                    ['key' => 'PRJ-003-03', 'title' => 'Cetak Barcode QR Code Label Palet Standar Logistik', 'status' => 'TODO', 'priority' => 'MEDIUM', 'role' => 'BE', 'est' => 2.5, 'act' => 0.0, 'assignee' => 'Joko Susilo'],
                ],
            ],
            [
                'project_code' => 'PRJ-004',
                'customer_id' => 'CUS-0004',
                'customer_name' => 'PT Solusi Finansial Sejahtera',
                'deal_id' => 'LD-2026-0025',
                'title' => 'Mobile Banking & Payment Hub Korporat',
                'project_type' => 'FINTECH',
                'status' => 'PROVISIONED',
                'health' => 'GREEN',
                'contract_value' => 450000000,
                'budget_mandays' => 190,
                'consumed_mandays' => 0.0,
                'start_date' => '2026-03-15',
                'target_end_date' => '2026-08-31',
                'pm_user_name' => 'Doni Project Manager',
                'milestones' => [
                    ['name' => 'Milestone 1 — Arsitektur Microservice Finansial & HSM', 'term_number' => 1, 'percentage' => 30.0, 'status' => 'PENDING'],
                    ['name' => 'Milestone 2 — Integrasi Core Banking API & Transfer Valas', 'term_number' => 2, 'percentage' => 40.0, 'status' => 'PENDING'],
                    ['name' => 'Milestone 3 — Audit Keamanan VAPT & UAT Sertifikasi', 'term_number' => 3, 'percentage' => 30.0, 'status' => 'PENDING'],
                ],
                'tasks' => [
                    ['key' => 'PRJ-004-01', 'title' => 'Setup Enkripsi End-to-End Payload Jaringan ISO 8583', 'status' => 'BACKLOG', 'priority' => 'URGENT', 'role' => 'BE', 'est' => 6.0, 'act' => 0.0, 'assignee' => null],
                    ['key' => 'PRJ-004-02', 'title' => 'Desain Prototype Mobile Banking High Fidelity', 'status' => 'BACKLOG', 'priority' => 'HIGH', 'role' => 'UIUX', 'est' => 5.0, 'act' => 0.0, 'assignee' => 'Lutfi Hakim'],
                ],
            ],
            [
                'project_code' => 'PRJ-005',
                'customer_id' => 'CUS-0006',
                'customer_name' => 'PT Bahari Retail Indonesia',
                'deal_id' => 'LD-2026-0026',
                'title' => 'E-Commerce Omnichannel B2B & Kasir POS Multi-Store',
                'project_type' => 'ECOMMERCE',
                'status' => 'ACTIVE',
                'health' => 'GREEN',
                'contract_value' => 280000000,
                'budget_mandays' => 125,
                'consumed_mandays' => 40.0,
                'start_date' => '2026-02-25',
                'target_end_date' => '2026-06-15',
                'pm_user_name' => 'Doni Project Manager',
                'milestones' => [
                    ['name' => 'Milestone 1 — Core POS Engine & Offline Synchronization', 'term_number' => 1, 'percentage' => 30.0, 'status' => 'APPROVED'],
                    ['name' => 'Milestone 2 — Web Portal B2B Wholesale & Payment Gateway', 'term_number' => 2, 'percentage' => 40.0, 'status' => 'IN_PROGRESS'],
                    ['name' => 'Milestone 3 — Integrasi Kurir Ekspedisi & BAST Go-Live', 'term_number' => 3, 'percentage' => 30.0, 'status' => 'PENDING'],
                ],
                'tasks' => [
                    ['key' => 'PRJ-005-01', 'title' => 'IndexedDB Local Cache Storage untuk POS Transaksi Offline', 'status' => 'DONE', 'priority' => 'HIGH', 'role' => 'FE', 'est' => 4.0, 'act' => 32.0, 'assignee' => 'Rian Hidayat'],
                    ['key' => 'PRJ-005-02', 'title' => 'Engine Sinkronisasi Konflik Antrian Struk Offline', 'status' => 'IN_PROGRESS', 'priority' => 'URGENT', 'role' => 'BE', 'est' => 5.0, 'act' => 28.0, 'assignee' => 'Joko Susilo'],
                    ['key' => 'PRJ-005-03', 'title' => 'Integrasi API Cek Resi Logistik JNE & SiCepat', 'status' => 'TODO', 'priority' => 'MEDIUM', 'role' => 'BE', 'est' => 3.0, 'act' => 0.0, 'assignee' => 'Joko Susilo'],
                ],
            ],
        ];

        foreach ($projects as $p) {
            $milestones = $p['milestones'];
            $tasks = $p['tasks'];
            unset($p['milestones'], $p['tasks']);

            DB::table('pm_projects')->updateOrInsert(
                ['project_code' => $p['project_code']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );

            $projId = DB::table('pm_projects')->where('project_code', $p['project_code'])->value('id');

            // Milestones
            foreach ($milestones as $m) {
                DB::table('pm_milestones')->updateOrInsert(
                    ['project_id' => $projId, 'term_number' => $m['term_number']],
                    array_merge($m, ['project_id' => $projId, 'created_at' => now(), 'updated_at' => now()])
                );
            }

            // Tasks
            foreach ($tasks as $t) {
                DB::table('pm_tasks')->updateOrInsert(
                    ['task_key' => $t['key']],
                    [
                        'project_id' => $projId,
                        'title' => $t['title'],
                        'status' => $t['status'],
                        'priority' => $t['priority'],
                        'role_needed' => $t['role'],
                        'estimated_mandays' => $t['est'],
                        'actual_hours' => $t['act'],
                        'assignee_name' => $t['assignee'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

