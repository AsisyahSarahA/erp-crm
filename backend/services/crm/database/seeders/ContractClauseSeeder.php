<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractClauseSeeder extends Seeder
{
    public function run(): void
    {
        $clauses = [
            [
                'clause_no' => 1,
                'title' => 'Ketentuan Umum & Definisi',
                'body' => 'Dalam Perjanjian Kerja Sama ini, yang dimaksud dengan: (1) "Pekerjaan" adalah jasa pengembangan perangkat lunak sesuai dengan rincian pada Lampiran Rencana Anggaran Biaya (RAB) dan Work Breakdown Structure (WBS); (2) "Pihak Pertama" adalah {{customer.legal_name}} sebagai pemberi kerja; (3) "Pihak Kedua" adalah PT Astacode Solusindo Teknologi sebagai penyedia jasa teknologi pelaksana.',
                'variables' => ['customer.legal_name', 'company.legal_name'],
            ],
            [
                'clause_no' => 2,
                'title' => 'Maksud, Tujuan & Penunjukan',
                'body' => 'Pihak Pertama dengan ini menunjuk secara sah Pihak Kedua, dan Pihak Kedua menyatakan menerima penunjukan tersebut, untuk merancang, membangun, menguji, dan menyerahterimakan sistem perangkat lunak sebagaimana disepakati dalam dokumen penawaran teknis.',
                'variables' => ['customer.legal_name'],
            ],
            [
                'clause_no' => 3,
                'title' => 'Ruang Lingkup & Deliverable',
                'body' => 'Ruang lingkup pekerjaan terkunci mengacu pada modul-modul yang tercantum secara spesifik pada Lampiran Ruang Lingkup dan RAB. Setiap modul tambahan yang tidak terdaftar akan diperlakukan sebagai Change Request (CR) terpisah.',
                'variables' => ['scope.modules'],
            ],
            [
                'clause_no' => 4,
                'title' => 'Metodologi Pengembangan',
                'body' => 'Pengembangan dilakukan dengan metodologi Agile Scrum yang mencakup sprint dua mingguan, sesi demo kemajuan berkala kepada Product Owner Pihak Pertama, dan peninjauan backlog secara transparan.',
                'variables' => [],
            ],
            [
                'clause_no' => 5,
                'title' => 'Jangka Waktu & Masa Efektif',
                'body' => 'Perjanjian ini berlaku efektif terhitung sejak ditandatanganinya kontrak ini dan diterimanya pelunasan uang muka (Down Payment/Termin 1) secara penuh di rekening resmi Pihak Kedua.',
                'variables' => ['timeline.start_date', 'timeline.duration_weeks'],
            ],
            [
                'clause_no' => 6,
                'title' => 'Nilai Kontrak & Termin (TOP)',
                'body' => 'Total nilai investasi pekerjaan adalah sebesar Rp {{rab.total}} ({{rab.total_terbilang}}). Pembayaran dilakukan secara bertahap mengacu pada milestone Termin Pembayaran (TOP). Setiap termin jatuh tempo selambat-lambatnya 14 (empat belas) hari kalender terhitung sejak tanggal terbitnya invoice resmi.',
                'variables' => ['rab.total', 'rab.total_terbilang', 'top.table', 'finance.invoice_due_days'],
            ],
            [
                'clause_no' => 7,
                'title' => 'Ketentuan Perpajakan',
                'body' => 'Nilai kontrak belum/telah memperhitungkan Pajak Pertambahan Nilai (PPN) sebesar 11% sesuai perundang-undangan perpajakan Republik Indonesia yang berlaku. Apabila Pihak Pertama merupakan badan pemotong PPh Pasal 23 (tarif 2%), maka bukti pemotongan pajak wajib diserahkan kepada Pihak Kedua maksimal 30 hari kalender.',
                'variables' => ['tax.vat_rate_pct', 'tax.pph23_rate_pct'],
            ],
            [
                'clause_no' => 8,
                'title' => 'Prosedur Pengujian & UAT',
                'body' => 'User Acceptance Testing (UAT) dilakukan pada server staging selama maksimal 14 (empat belas) hari kerja. Apabila dalam periode tersebut Pihak Pertama tidak menyampaikan catatan bug secara tertulis, maka deliverable dianggap telah diterima dengan baik.',
                'variables' => ['sla.uat_period_workdays'],
            ],
            [
                'clause_no' => 9,
                'title' => 'Serah Terima & BAST',
                'body' => 'Setelah UAT disetujui, Para Pihak akan menandatangani Berita Acara Serah Terima (BAST). Penandatanganan BAST menjadi prasyarat mutlak dilakukannya deployment produksi (Go-Live) dan penerbitan invoice termin pelunasan.',
                'variables' => ['customer.legal_name'],
            ],
            [
                'clause_no' => 10,
                'title' => 'Change Request (CR)',
                'body' => 'Permintaan modifikasi atau penambahan fitur di luar batasan RAB awal wajib diajukan secara tertulis. Pihak Kedua akan menerbitkan estimasi mandays dan biaya tambahan dalam bentuk Dokumen Addendum Kontrak resmi sebelum pengerjaan dimulai.',
                'variables' => [],
            ],
            [
                'clause_no' => 11,
                'title' => 'Hak Cipta & Kepemilikan Source Code',
                'body' => 'Hak cipta source code kustom yang dikembangkan khusus beralih menjadi milik Pihak Pertama HANYA SETELAH seluruh kewajiban pembayaran (pelunasan 100%) diselesaikan secara lunas. Kerangka kerja inti internal, library dasar, dan komponen reusable milik Pihak Kedua tetap menjadi hak kekayaan intelektual Pihak Kedua dengan lisensi pakai perpetual non-eksklusif kepada Pihak Pertama.',
                'variables' => ['customer.legal_name'],
            ],
            [
                'clause_no' => 12,
                'title' => 'Kerahasiaan Informasi (NDA)',
                'body' => 'Para Pihak sepakat menjaga kerahasiaan seluruh informasi teknis, rahasia dagang, strategi bisnis, dan arsitektur data milik pihak lainnya selama masa kerja sama hingga 3 (tiga) tahun setelah berakhirnya Perjanjian ini.',
                'variables' => [],
            ],
            [
                'clause_no' => 13,
                'title' => 'Kepatuhan Pelindungan Data Pribadi (UU PDP No. 27/2022)',
                'body' => 'Pihak Kedua bertindak sebagai pemroses data (data processor) dan wajib menerapkan langkah-langkah teknis keamanan enkripsi guna melindungi kerahasiaan dan integritas data pribadi yang diproses.',
                'variables' => [],
            ],
            [
                'clause_no' => 14,
                'title' => 'Garansi Pemeliharaan Bug (Warranty)',
                'body' => 'Pihak Kedua memberikan masa garansi perbaikan cacat program (bug fixes) bebas biaya selama 90 (sembilan puluh) hari kalender terhitung sejak tanggal BAST ditandatangani, mencakup fungsionalitas yang ada pada WBS awal.',
                'variables' => ['warranty.days'],
            ],
            [
                'clause_no' => 15,
                'title' => 'SLA Dukungan Helpdesk',
                'body' => 'Dukungan teknis selama masa pemeliharaan beroperasi pada hari dan jam kerja dengan target response time: Severity P1 (Kritis) kurang dari 1 jam; Severity P2 (Tinggi) kurang dari 4 jam; Severity P3 (Normal) kurang dari 24 jam.',
                'variables' => ['sla.helpdesk_p1_response_hours', 'sla.helpdesk_p2_response_hours', 'sla.helpdesk_p3_response_hours'],
            ],
            [
                'clause_no' => 16,
                'title' => 'Keamanan Siber & Audit VAPT',
                'body' => 'Aplikasi dibangun berpedoman pada standar keamanan OWASP Top 10. Pihak Pertama berhak melakukan uji penetrasi (Vulnerability Assessment & Penetration Testing) pada lingkungan staging sebelum tahap Go-Live.',
                'variables' => [],
            ],
            [
                'clause_no' => 17,
                'title' => 'Server & Cloud Deployment',
                'body' => 'Seluruh biaya sewa infrastruktur server, cloud, domain, dan lisensi pihak ketiga menjadi beban dan tanggung jawab Pihak Pertama. Pihak Kedua bertanggung jawab mengelola pipeline deployment CI/CD ke server yang disediakan.',
                'variables' => [],
            ],
            [
                'clause_no' => 18,
                'title' => 'Keadaan Kahar (Force Majeure)',
                'body' => 'Para Pihak dibebaskan dari tanggung jawab atas keterlambatan atau kegagalan pemenuhan kewajiban akibat peristiwa di luar kendali wajar manusia (bencana alam, huru-hara, perang, kebakaran hebat, atau regulasi pembatasan darurat pemerintah).',
                'variables' => [],
            ],
            [
                'clause_no' => 19,
                'title' => 'Sanksi Keterlambatan & Denda',
                'body' => 'Keterlambatan penyelesaian pekerjaan atau pembayaran yang bukan disebabkan oleh keadaan kahar dikenakan penalti sebesar 1‰ (satu per mil) per hari keterlambatan, dengan batas akumulasi penalti maksimal 5% dari nilai kontrak termin terkait.',
                'variables' => ['penalty.daily_permille', 'penalty.max_pct'],
            ],
            [
                'clause_no' => 20,
                'title' => 'Pengakhiran Perjanjian',
                'body' => 'Perjanjian ini dapat diakhiri secara sepihak apabila salah satu pihak melakukan cidera janji material (wanprestasi) dan tidak dapat memperbaikinya dalam jangka waktu 14 (empat belas) hari kalender setelah menerima peringatan tertulis.',
                'variables' => [],
            ],
            [
                'clause_no' => 21,
                'title' => 'Penyelesaian Sengketa',
                'body' => 'Setiap perselisihan yang timbul dari penafsiran atau pelaksanaan perjanjian ini akan diselesaikan secara musyawarah untuk mufakat dalam jangka waktu 30 hari kalender. Apabila musyawarah tidak mencapai mufakat, sengketa akan diselesaikan melalui Pengadilan Negeri domisili Pihak Kedua.',
                'variables' => [],
            ],
            [
                'clause_no' => 22,
                'title' => 'Keabsahan Tanda Tangan Elektronik & e-Meterai',
                'body' => 'Para Pihak sepakat bahwa penandatanganan perjanjian ini secara digital melalui tanda tangan elektronik (e-Sign bersertifikasi) dan pembubuhan meterai elektronik resmi (e-Meterai) memiliki kekuatan hukum yang sah dan mengikat sesuai UU ITE (UU No. 1/2024) dan UU Bea Meterai (UU No. 10/2020).',
                'variables' => ['signer.company.name', 'signer.client.name'],
            ],
        ];

        foreach ($clauses as $c) {
            DB::table('crm_contract_clauses')->updateOrInsert(
                ['clause_no' => $c['clause_no']],
                [
                    'title' => $c['title'],
                    'body' => $c['body'],
                    'variables' => json_encode($c['variables']),
                    'version' => 1,
                    'is_mandatory' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
