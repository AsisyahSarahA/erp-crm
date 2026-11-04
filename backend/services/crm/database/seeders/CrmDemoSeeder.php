<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 10 Master Customers
        $customers = [
            [
                'customer_code' => 'CUS-0001',
                'legal_name' => 'PT Megah Nusantara Teknologi',
                'brand_name' => 'Megah Tech',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'TECH_SAAS',
                'npwp' => '01.442.123.4-012.000',
                'billing_address' => 'Gedung Cyber 2 Lt. 12, Jl. HR Rasuna Said, Jakarta Selatan',
                'phone' => '+6281234567890',
                'email' => 'procurement@megahnusantara.co.id',
                'status' => 'ACTIVE',
                'health_score' => 95,
                'contacts' => [
                    ['name' => 'Budi Santoso', 'position' => 'Head of Technology', 'phone' => '+6281234567890', 'email' => 'budi.santoso@megahnusantara.co.id', 'is_primary' => true],
                    ['name' => 'Dewi Anggraini', 'position' => 'Finance Controller', 'phone' => '+6281234567891', 'email' => 'dewi.finance@megahnusantara.co.id', 'is_primary' => false],
                ],
            ],
            [
                'customer_code' => 'CUS-0002',
                'legal_name' => 'CV Borneo Digital Prima',
                'brand_name' => 'Borneo Digital',
                'customer_type' => 'STARTUP',
                'industry' => 'MFG_LOGISTICS',
                'npwp' => '02.553.882.1-721.000',
                'billing_address' => 'Jl. Jenderal Sudirman No. 88, Balikpapan, Kalimantan Timur',
                'phone' => '+6281344556677',
                'email' => 'halo@borneodigital.co.id',
                'status' => 'ACTIVE',
                'health_score' => 88,
                'contacts' => [
                    ['name' => 'Hendra Wijaya', 'position' => 'Managing Director', 'phone' => '+6281344556677', 'email' => 'hendra@borneodigital.co.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0003',
                'legal_name' => 'PT Sinar Surya Logistik',
                'brand_name' => 'Sinar Express',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'MFG_LOGISTICS',
                'npwp' => '01.992.441.9-051.000',
                'billing_address' => 'Kawasan Industri Rungkut Blok C-4, Surabaya, Jawa Timur',
                'phone' => '+628118899001',
                'email' => 'finance@sinarsuryalogistik.com',
                'status' => 'ACTIVE',
                'health_score' => 92,
                'contacts' => [
                    ['name' => 'Agus Pratama', 'position' => 'VP Supply Chain', 'phone' => '+628118899001', 'email' => 'agus@sinarsuryalogistik.com', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0004',
                'legal_name' => 'PT Solusi Finansial Sejahtera',
                'brand_name' => 'FinSejahtera',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'FINANCE_BANKING',
                'npwp' => '01.332.119.8-015.000',
                'billing_address' => 'Sudirman Central Business District (SCBD) Lot 9, Jakarta Selatan',
                'phone' => '+628119922334',
                'email' => 'it-procure@finsejahtera.co.id',
                'status' => 'ACTIVE',
                'health_score' => 97,
                'contacts' => [
                    ['name' => 'Rina Sugiarto', 'position' => 'Chief Technology Officer', 'phone' => '+628119922334', 'email' => 'rina.cto@finsejahtera.co.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0005',
                'legal_name' => 'PT Jaya Farmasi Medika',
                'brand_name' => 'Jaya Medika',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'HEALTHCARE',
                'npwp' => '03.112.556.7-422.000',
                'billing_address' => 'Jl. Pasteur No. 45, Bandung, Jawa Barat',
                'phone' => '+6281223344556',
                'email' => 'operasional@jayamedika.id',
                'status' => 'PROSPECT',
                'health_score' => 75,
                'contacts' => [
                    ['name' => 'dr. Farhan Syahputra', 'position' => 'Direktur Operasional', 'phone' => '+6281223344556', 'email' => 'farhan@jayamedika.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0006',
                'legal_name' => 'PT Bahari Retail Indonesia',
                'brand_name' => 'Bahari Mart',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'RETAIL_ECOMMERCE',
                'npwp' => '01.776.332.1-504.000',
                'billing_address' => 'Jl. Pemuda No. 120, Semarang, Jawa Tengah',
                'phone' => '+628157788990',
                'email' => 'digital@bahariretail.co.id',
                'status' => 'ACTIVE',
                'health_score' => 90,
                'contacts' => [
                    ['name' => 'Gunawan Wibowo', 'position' => 'Head of Omnichannel', 'phone' => '+628157788990', 'email' => 'gunawan@bahariretail.co.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0007',
                'legal_name' => 'CV Kreatif Mandiri Studio',
                'brand_name' => 'Kreatif Studio',
                'customer_type' => 'UMKM',
                'industry' => 'PROFESSIONAL_SERVICES',
                'npwp' => '04.887.665.4-541.000',
                'billing_address' => 'Jl. Kaliurang Km 5, Sleman, DI Yogyakarta',
                'phone' => '+628180998877',
                'email' => 'info@kreatifstudio.net',
                'status' => 'PROSPECT',
                'health_score' => 70,
                'contacts' => [
                    ['name' => 'Luki Aditia', 'position' => 'Founder & Lead Designer', 'phone' => '+628180998877', 'email' => 'luki@kreatifstudio.net', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0008',
                'legal_name' => 'PT Pendidikan Cerdas Bangsa',
                'brand_name' => 'EduCerdas',
                'customer_type' => 'STARTUP',
                'industry' => 'EDUCATION',
                'npwp' => '02.441.998.2-652.000',
                'billing_address' => 'Jl. Ijen No. 25, Malang, Jawa Timur',
                'phone' => '+6281333444555',
                'email' => 'academic@educerdas.ac.id',
                'status' => 'PROSPECT',
                'health_score' => 80,
                'contacts' => [
                    ['name' => 'Siti Nurhaliza', 'position' => 'Product Lead', 'phone' => '+6281333444555', 'email' => 'siti@educerdas.ac.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0009',
                'legal_name' => 'PT Makmur Properti Sentosa',
                'brand_name' => 'Makmur Land',
                'customer_type' => 'PERUSAHAAN',
                'industry' => 'PROPERTY_CONSTR',
                'npwp' => '01.665.443.2-411.000',
                'billing_address' => 'CBD BSD City Kav. 2 No. 10, Tangerang Selatan, Banten',
                'phone' => '+628114455660',
                'email' => 'sales-estate@makmurproperti.co.id',
                'status' => 'PROSPECT',
                'health_score' => 72,
                'contacts' => [
                    ['name' => 'Bambang Soedjarwo', 'position' => 'Commercial Director', 'phone' => '+628114455660', 'email' => 'bambang@makmurproperti.co.id', 'is_primary' => true],
                ],
            ],
            [
                'customer_code' => 'CUS-0010',
                'legal_name' => 'BUMDes Tirta Bening Sejahtera',
                'brand_name' => 'Tirta Bening',
                'customer_type' => 'INSTANSI',
                'industry' => 'GOV_BUMN',
                'npwp' => '05.991.223.4-525.000',
                'billing_address' => 'Desa Ponggok, Polanharjo, Klaten, Jawa Tengah',
                'phone' => '+6285712345678',
                'email' => 'sekretariat@tirtabening.desa.id',
                'status' => 'PROSPECT',
                'health_score' => 65,
                'contacts' => [
                    ['name' => 'Joko Widodo MP', 'position' => 'Ketua BUMDes', 'phone' => '+6285712345678', 'email' => 'joko@tirtabening.desa.id', 'is_primary' => true],
                ],
            ],
        ];

        foreach ($customers as $custData) {
            $contacts = $custData['contacts'];
            unset($custData['contacts']);

            $custData['code'] = $custData['customer_code'];
            $custData['type'] = strtolower($custData['customer_type'] ?? 'perusahaan');
            $custData['updated_at'] = now();
            $custData['created_at'] = now();

            DB::table('crm_customers')->updateOrInsert(
                ['customer_code' => $custData['customer_code']],
                $custData
            );

            $customerId = DB::table('crm_customers')->where('customer_code', $custData['customer_code'])->value('id');

            foreach ($contacts as $contact) {
                $contact['title'] = $contact['position'] ?? null;
                $contact['phone_wa'] = $contact['phone'] ?? null;
                $contact['role'] = ($contact['is_primary'] ?? false) ? 'decision_maker' : 'finance_lead';
                DB::table('crm_contacts')->updateOrInsert(
                    ['customer_id' => $customerId, 'phone' => $contact['phone']],
                    array_merge($contact, ['customer_id' => $customerId, 'created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // 2. 30 Realistic Leads across all 7 stages
        $leads = [
            // Stage: NEW_LEAD (5)
            [
                'code' => 'LD-2026-0001', 'cust' => 'CUS-0005', 'title' => 'Sistem Informasi Rumah Sakit & Antrean BPJS Online',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'HIGH', 'stage' => 'NEW_LEAD', 'source' => 'GOOGLE_ADS', 'sub' => 'Search Intent ERP',
                'est' => 125000000, 'prob' => 10, 'sales' => 'Citra Sales Executive', 'days' => 1,
            ],
            [
                'code' => 'LD-2026-0002', 'cust' => 'CUS-0007', 'title' => 'Revamp UI/UX Portal Agensi Digital & Portfolio Studio',
                'type' => 'UI_UX', 'priority' => 'MEDIUM', 'stage' => 'NEW_LEAD', 'source' => 'INSTAGRAM', 'sub' => 'Instagram Bio Link',
                'est' => 45000000, 'prob' => 10, 'sales' => 'Citra Sales Executive', 'days' => 2,
            ],
            [
                'code' => 'LD-2026-0003', 'cust' => 'CUS-0008', 'title' => 'Platform Pembelajaran Daring & Ujian CBT Siswa',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'HIGH', 'stage' => 'NEW_LEAD', 'source' => 'LINKEDIN', 'sub' => 'InMail Outreach',
                'est' => 85000000, 'prob' => 10, 'sales' => 'Bambang Sales Manager', 'days' => 1,
            ],
            [
                'code' => 'LD-2026-0004', 'cust' => 'CUS-0009', 'title' => 'Portal Booking Unit Perumahan & Simulasi KPR',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'MEDIUM', 'stage' => 'NEW_LEAD', 'source' => 'WEBSITE', 'sub' => 'Contact Form',
                'est' => 95000000, 'prob' => 10, 'sales' => 'Citra Sales Executive', 'days' => 3,
            ],
            [
                'code' => 'LD-2026-0005', 'cust' => 'CUS-0010', 'title' => 'Sistem Tiket Wisata Air Desa & Kasir Terpadu',
                'type' => 'ECOMMERCE', 'priority' => 'LOW', 'stage' => 'NEW_LEAD', 'source' => 'REFERRAL', 'sub' => 'Partner Software House',
                'est' => 35000000, 'prob' => 10, 'sales' => 'Citra Sales Executive', 'days' => 2,
            ],

            // Stage: FOLLOW_UP (4)
            [
                'code' => 'LD-2026-0006', 'cust' => 'CUS-0005', 'title' => 'Aplikasi Mobile Konsultasi Dokter & Rekam Medis Elektronik',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'HIGH', 'stage' => 'FOLLOW_UP', 'source' => 'META_ADS', 'sub' => 'Lead Gen Form',
                'est' => 180000000, 'prob' => 30, 'sales' => 'Citra Sales Executive', 'days' => 4,
            ],
            [
                'code' => 'LD-2026-0007', 'cust' => 'CUS-0007', 'title' => 'Sistem Manajemen Task Desain & Proofing Asset Klien',
                'type' => 'ERP_CUSTOM', 'priority' => 'MEDIUM', 'stage' => 'FOLLOW_UP', 'source' => 'WHATSAPP_MARKETING', 'sub' => 'Direct Click to Chat',
                'est' => 60000000, 'prob' => 30, 'sales' => 'Citra Sales Executive', 'days' => 5,
            ],
            [
                'code' => 'LD-2026-0008', 'cust' => 'CUS-0008', 'title' => 'Aplikasi Pembayaran SPP Sekolah dengan Virtual Account',
                'type' => 'FINTECH', 'priority' => 'HIGH', 'stage' => 'FOLLOW_UP', 'source' => 'WEBSITE', 'sub' => 'Pricing Calculator',
                'est' => 75000000, 'prob' => 30, 'sales' => 'Bambang Sales Manager', 'days' => 3,
            ],
            [
                'code' => 'LD-2026-0009', 'cust' => 'CUS-0009', 'title' => 'Sistem Pemeliharaan Gedung & Smart Tenant Billing',
                'type' => 'ERP_CUSTOM', 'priority' => 'MEDIUM', 'stage' => 'FOLLOW_UP', 'source' => 'EVENT_WEBINAR', 'sub' => 'Workshop IT Architecture',
                'est' => 110000000, 'prob' => 30, 'sales' => 'Citra Sales Executive', 'days' => 6,
            ],

            // Stage: REQUIREMENT (4)
            [
                'code' => 'LD-2026-0010', 'cust' => 'CUS-0001', 'title' => 'Modul Penilaian Kinerja OKR & 360 Review Karyawan',
                'type' => 'ERP_CUSTOM', 'priority' => 'HIGH', 'stage' => 'REQUIREMENT', 'source' => 'REFERRAL', 'sub' => 'Client Referral',
                'est' => 90000000, 'prob' => 50, 'sales' => 'Bambang Sales Manager', 'days' => 8,
            ],
            [
                'code' => 'LD-2026-0011', 'cust' => 'CUS-0002', 'title' => 'Integrasi Sensor Bahan Bakar Truk IoT & Peringatan Kebocoran',
                'type' => 'IOT_TELEMATICS', 'priority' => 'HOT', 'stage' => 'REQUIREMENT', 'source' => 'WEBSITE', 'sub' => 'Live Chat Widget',
                'est' => 135000000, 'prob' => 50, 'sales' => 'Citra Sales Executive', 'days' => 7,
            ],
            [
                'code' => 'LD-2026-0012', 'cust' => 'CUS-0003', 'title' => 'Sistem WMS Barcode Scanner Handheld Gudang Transit',
                'type' => 'ERP_CUSTOM', 'priority' => 'HIGH', 'stage' => 'REQUIREMENT', 'source' => 'COLD_OUTREACH', 'sub' => 'Cold Email Sequence',
                'est' => 160000000, 'prob' => 50, 'sales' => 'Bambang Sales Manager', 'days' => 9,
            ],
            [
                'code' => 'LD-2026-0013', 'cust' => 'CUS-0006', 'title' => 'Sistem Poin Loyalitas Pelanggan & Voucher Diskon Belanja',
                'type' => 'ECOMMERCE', 'priority' => 'MEDIUM', 'stage' => 'REQUIREMENT', 'source' => 'INSTAGRAM', 'sub' => 'Instagram DM',
                'est' => 80000000, 'prob' => 50, 'sales' => 'Citra Sales Executive', 'days' => 10,
            ],

            // Stage: QUOTATION (4)
            [
                'code' => 'LD-2026-0014', 'cust' => 'CUS-0004', 'title' => 'Audit Keamanan Penetration Testing & Enkripsi Data Keuangan',
                'type' => 'CONSULTING', 'priority' => 'HOT', 'stage' => 'QUOTATION', 'source' => 'LINKEDIN', 'sub' => 'Company Page Lead Form',
                'est' => 120000000, 'prob' => 70, 'sales' => 'Bambang Sales Manager', 'days' => 12,
            ],
            [
                'code' => 'LD-2026-0015', 'cust' => 'CUS-0005', 'title' => 'Aplikasi Pengadaan Obat Apotek & Resep Elektronik',
                'type' => 'ERP_CUSTOM', 'priority' => 'HIGH', 'stage' => 'QUOTATION', 'source' => 'GOOGLE_ADS', 'sub' => 'Search Intent ERP',
                'est' => 140000000, 'prob' => 70, 'sales' => 'Citra Sales Executive', 'days' => 11,
            ],
            [
                'code' => 'LD-2026-0016', 'cust' => 'CUS-0008', 'title' => 'LMS Interaktif Sekolah Menengah Berstandar Kurikulum Merdeka',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'MEDIUM', 'stage' => 'QUOTATION', 'source' => 'WEBSITE', 'sub' => 'Contact Form',
                'est' => 115000000, 'prob' => 70, 'sales' => 'Citra Sales Executive', 'days' => 14,
            ],
            [
                'code' => 'LD-2026-0017', 'cust' => 'CUS-0010', 'title' => 'Aplikasi Administrasi Surat Menyurat & Layanan Warga Mandiri',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'LOW', 'stage' => 'QUOTATION', 'source' => 'OTHER', 'sub' => 'Walk-in Office',
                'est' => 50000000, 'prob' => 70, 'sales' => 'Citra Sales Executive', 'days' => 13,
            ],

            // Stage: NEGOTIATION (4)
            [
                'code' => 'LD-2026-0018', 'cust' => 'CUS-0001', 'title' => 'Enterprise Service Bus (ESB) & Microservice Integrator',
                'type' => 'ERP_CUSTOM', 'priority' => 'HOT', 'stage' => 'NEGOTIATION', 'source' => 'REFERRAL', 'sub' => 'Client Referral',
                'est' => 220000000, 'prob' => 85, 'sales' => 'Bambang Sales Manager', 'days' => 18,
            ],
            [
                'code' => 'LD-2026-0019', 'cust' => 'CUS-0003', 'title' => 'Sistem Pemantauan Suhu Kontainer Refrigerator Dingin Logistik',
                'type' => 'IOT_TELEMATICS', 'priority' => 'HIGH', 'stage' => 'NEGOTIATION', 'source' => 'COLD_OUTREACH', 'sub' => 'Cold Call Phone',
                'est' => 175000000, 'prob' => 85, 'sales' => 'Bambang Sales Manager', 'days' => 16,
            ],
            [
                'code' => 'LD-2026-0020', 'cust' => 'CUS-0006', 'title' => 'Integrasi POS Toko Fisik dengan Gudang Konsolidasi Marketplace',
                'type' => 'ECOMMERCE', 'priority' => 'HIGH', 'stage' => 'NEGOTIATION', 'source' => 'META_ADS', 'sub' => 'Traffic Campaign',
                'est' => 150000000, 'prob' => 85, 'sales' => 'Citra Sales Executive', 'days' => 19,
            ],
            [
                'code' => 'LD-2026-0021', 'cust' => 'CUS-0009', 'title' => 'Aplikasi Customer Relationship Real Estate & SPK Pembelian Rumah',
                'type' => 'ERP_CUSTOM', 'priority' => 'MEDIUM', 'stage' => 'NEGOTIATION', 'source' => 'LINKEDIN', 'sub' => 'Post Engagement',
                'est' => 130000000, 'prob' => 85, 'sales' => 'Citra Sales Executive', 'days' => 21,
            ],

            // Stage: DEAL_WON (5 projects matching PM & Finance)
            [
                'code' => 'LD-2026-0022', 'cust' => 'CUS-0001', 'title' => 'Aplikasi HRD & Presensi GPS 500 Karyawan',
                'type' => 'ERP_CUSTOM', 'priority' => 'HOT', 'stage' => 'DEAL_WON', 'source' => 'WEBSITE', 'sub' => 'Pricing Calculator',
                'est' => 250000000, 'prob' => 100, 'sales' => 'Bambang Sales Manager', 'days' => 30,
            ],
            [
                'code' => 'LD-2026-0023', 'cust' => 'CUS-0002', 'title' => 'Sistem Telematika IoT Armada Truk Ekspedisi',
                'type' => 'IOT_TELEMATICS', 'priority' => 'HOT', 'stage' => 'DEAL_WON', 'source' => 'GOOGLE_ADS', 'sub' => 'Search Intent ERP',
                'est' => 195000000, 'prob' => 100, 'sales' => 'Citra Sales Executive', 'days' => 35,
            ],
            [
                'code' => 'LD-2026-0024', 'cust' => 'CUS-0003', 'title' => 'ERP Distribusi Multi-Cabang & Manajemen Gudang',
                'type' => 'ERP_CUSTOM', 'priority' => 'HOT', 'stage' => 'DEAL_WON', 'source' => 'REFERRAL', 'sub' => 'Client Referral',
                'est' => 380000000, 'prob' => 100, 'sales' => 'Bambang Sales Manager', 'days' => 42,
            ],
            [
                'code' => 'LD-2026-0025', 'cust' => 'CUS-0004', 'title' => 'Mobile Banking & Payment Hub Korporat',
                'type' => 'FINTECH', 'priority' => 'HOT', 'stage' => 'DEAL_WON', 'source' => 'LINKEDIN', 'sub' => 'Company Page Lead Form',
                'est' => 450000000, 'prob' => 100, 'sales' => 'Bambang Sales Manager', 'days' => 48,
            ],
            [
                'code' => 'LD-2026-0026', 'cust' => 'CUS-0006', 'title' => 'E-Commerce Omnichannel B2B & Kasir POS Multi-Store',
                'type' => 'ECOMMERCE', 'priority' => 'HOT', 'stage' => 'DEAL_WON', 'source' => 'META_ADS', 'sub' => 'Lead Gen Form',
                'est' => 280000000, 'prob' => 100, 'sales' => 'Citra Sales Executive', 'days' => 38,
            ],

            // Stage: LOST (4)
            [
                'code' => 'LD-2026-0027', 'cust' => 'CUS-0005', 'title' => 'Platform Telemedicine & Pengiriman Obat Kurir Cepat',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'HIGH', 'stage' => 'LOST', 'source' => 'GOOGLE_ADS', 'sub' => 'Search Intent ERP',
                'est' => 165000000, 'prob' => 0, 'sales' => 'Citra Sales Executive', 'days' => 25,
                'reject_code' => 'PRICE_LOST', 'reject_notes' => 'Klien memilih vendor lokal dengan tawaran harga 30% di bawah estimasi RAB kami.',
            ],
            [
                'code' => 'LD-2026-0028', 'cust' => 'CUS-0007', 'title' => 'Aplikasi Booking Studio Foto Online & Pembayaran DP Otomatis',
                'type' => 'ECOMMERCE', 'priority' => 'LOW', 'stage' => 'LOST', 'source' => 'TIKTOK', 'sub' => 'TikTok Direct Message',
                'est' => 35000000, 'prob' => 0, 'sales' => 'Citra Sales Executive', 'days' => 28,
                'reject_code' => 'CLIENT_CANCELLED', 'reject_notes' => 'Rencana ekspansi studio foto ditunda karena relokasi gedung operasional.',
            ],
            [
                'code' => 'LD-2026-0029', 'cust' => 'CUS-0008', 'title' => 'Sistem Rekrutmen Pengajar Les Privat & Evaluasi Wali Murid',
                'type' => 'ERP_CUSTOM', 'priority' => 'MEDIUM', 'stage' => 'LOST', 'source' => 'FACEBOOK', 'sub' => 'Messenger Inbound',
                'est' => 70000000, 'prob' => 0, 'sales' => 'Citra Sales Executive', 'days' => 31,
                'reject_code' => 'UNRESPONSIVE', 'reject_notes' => 'Tidak ada tanggapan setelah 4x follow-up berkala via WhatsApp dan email.',
            ],
            [
                'code' => 'LD-2026-0030', 'cust' => 'CUS-0010', 'title' => 'Portal Informasi Pariwisata Desa & Penginapan Homestay',
                'type' => 'WEB_MOBILE_APP', 'priority' => 'LOW', 'stage' => 'LOST', 'source' => 'OTHER', 'sub' => 'Walk-in Office',
                'est' => 40000000, 'prob' => 0, 'sales' => 'Citra Sales Executive', 'days' => 33,
                'reject_code' => 'BUDGET_UNAVAILABLE', 'reject_notes' => 'Pengajuan anggaran dana desa untuk sistem belum disetujui pada musrenbang tahun ini.',
            ],
        ];

        foreach ($leads as $l) {
            $custId = DB::table('crm_customers')->where('customer_code', $l['cust'])->value('id');
            $sourceId = DB::table('crm_lead_sources')->where('code', $l['source'])->value('id');

            DB::table('crm_leads')->updateOrInsert(
                ['lead_code' => $l['code']],
                [
                    'code' => $l['code'],
                    'customer_id' => $custId,
                    'source_id' => $sourceId,
                    'title' => $l['title'],
                    'project_type' => $l['type'],
                    'priority' => $l['priority'],
                    'stage' => $l['stage'],
                    'stage_id' => strtolower($l['stage']),
                    'source' => $l['source'],
                    'sub_source' => $l['sub'],
                    'est_value' => $l['est'],
                    'win_probability' => $l['prob'],
                    'sales_pic_id' => 10,
                    'owner_id' => 10,
                    'team_id' => 1,
                    'sales_pic_name' => $l['sales'],
                    'score' => match ($l['priority']) {
                        'HOT' => 85,
                        'HIGH' => 70,
                        'MEDIUM' => 50,
                        default => 30,
                    },
                    'score_label' => match ($l['priority']) {
                        'HOT' => 'HOT',
                        'HIGH' => 'HOT',
                        'MEDIUM' => 'WARM',
                        default => 'COLD',
                    },
                    'reject_reason_code' => $l['reject_code'] ?? null,
                    'reject_notes' => $l['reject_notes'] ?? null,
                    'days_in_stage' => $l['days'],
                    'is_stale' => $l['days'] >= 5,
                    'created_at' => now()->subDays($l['days']),
                    'updated_at' => now(),
                ]
            );

            $leadId = DB::table('crm_leads')->where('lead_code', $l['code'])->value('id');

            // Seed Quotation for Quotation, Negotiation, and Deal Won leads
            if (in_array($l['stage'], ['QUOTATION', 'NEGOTIATION', 'DEAL_WON'], true)) {
                $subtotal = $l['est'];
                $discount = in_array($l['stage'], ['NEGOTIATION', 'DEAL_WON'], true) ? round($subtotal * 0.05) : 0;
                $dpp = $subtotal - $discount;
                $vat = round($dpp * 0.11);
                $total = $dpp + $vat;
                $hpp = round($dpp * 0.55);
                $margin = round((($dpp - $hpp) / $dpp) * 100, 2);

                $quotationStatus = match ($l['stage']) {
                    'QUOTATION' => 'SENT',
                    'NEGOTIATION' => 'SENT',
                    'DEAL_WON' => 'ACCEPTED',
                    default => 'DRAFT',
                };

                $qtNumber = 'QT/2026/03/' . str_pad((string) $leadId, 4, '0', STR_PAD_LEFT) . '-R0';

                DB::table('crm_quotations')->updateOrInsert(
                    ['lead_id' => $leadId],
                    [
                        'quotation_number' => $qtNumber,
                        'version' => 1,
                        'status' => $quotationStatus,
                        'subtotal' => $subtotal,
                        'contingency_pct' => 10.0,
                        'discount_pct' => $discount > 0 ? 5.0 : 0.0,
                        'dpp' => $dpp,
                        'vat_rate' => 11.0,
                        'vat_amount' => $vat,
                        'total' => $total,
                        'total_terbilang' => 'Dihitung Otomatis Terbilang Rupiah',
                        'hpp_internal' => $hpp,
                        'margin_pct' => $margin,
                        'valid_until' => now()->addDays(30)->toDateString(),
                        'created_at' => now()->subDays(10),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

