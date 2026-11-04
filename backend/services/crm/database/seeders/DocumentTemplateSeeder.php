<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'QUOTATION_STANDARD',
                'type' => 'QUOTATION',
                'title' => 'Surat Penawaran Resmi & Rencana Anggaran Biaya (RAB)',
                'body' => <<<HTML
<h1>SURAT PENAWARAN BIAYA & RUANG LINGKUP TEKNIS</h1>
<p>Nomor: {{quotation.number}}</p>
<p>Tanggal: {{quotation.date}}</p>
<p>Kepada Yth.<br><strong>{{customer.legal_name}}</strong><br>Up. {{contact.name}} ({{contact.position}})</p>

<h3>1. Pendahuluan</h3>
<p>PT Astacode Solusindo Teknologi dengan hormat menyampaikan proposal penawaran teknis dan estimasi biaya pengembangan sistem untuk proyek: <strong>{{lead.title}}</strong>.</p>

<h3>2. Rincian Rencana Anggaran Biaya (RAB)</h3>
{{rab.table}}
<table border="1" cellpadding="6" cellspacing="0" width="100%">
  <tr><td><strong>Subtotal Biaya Pengembangan</strong></td><td align="right">Rp {{rab.subtotal}}</td></tr>
  <tr><td><strong>Diskon Khusus</strong></td><td align="right">Rp {{rab.discount}}</td></tr>
  <tr><td><strong>Dasar Pengenaan Pajak (DPP)</strong></td><td align="right">Rp {{rab.dpp}}</td></tr>
  <tr><td><strong>PPN ({{tax.vat_rate_pct}}%)</strong></td><td align="right">Rp {{rab.tax}}</td></tr>
  <tr style="background:#f2f2f2;"><td><strong>Total Nilai Investasi</strong></td><td align="right"><strong>Rp {{rab.total}}</strong></td></tr>
</table>
<p><em>Terbilang: {{rab.total_terbilang}}</em></p>

<h3>3. Skema Termin Pembayaran (TOP)</h3>
{{top.table}}

<h3>4. Validitas Penawaran</h3>
<p>Penawaran ini berlaku hingga tanggal <strong>{{quotation.valid_until}}</strong>.</p>
HTML,
                'merge_variables' => ['quotation.number', 'quotation.date', 'customer.legal_name', 'contact.name', 'lead.title', 'rab.subtotal', 'rab.discount', 'rab.dpp', 'rab.tax', 'rab.total', 'rab.total_terbilang', 'quotation.valid_until'],
                'version' => 1,
                'is_default' => true,
            ],
            [
                'code' => 'MOU_STANDARD_22_PASAL',
                'type' => 'MOU',
                'title' => 'Perjanjian Kerja Sama Pengembangan Perangkat Lunak (22 Pasal Baku)',
                'body' => <<<HTML
<h1>PERJANJIAN KERJA SAMA PENGEMBANGAN SISTEM</h1>
<p>Nomor: {{mou.number}}</p>
<p>Pada hari ini, tanggal {{mou.date}}, dibuat dan ditandatangani perjanjian kerja sama antara:</p>
<ol>
  <li><strong>{{customer.legal_name}}</strong>, berkedudukan di {{customer.billing_address}}, dalam hal ini diwakili oleh {{contact.name}} selaku {{contact.position}} (selanjutnya disebut "PIHAK PERTAMA"); dan</li>
  <li><strong>PT Astacode Solusindo Teknologi</strong>, berkedudukan di Menara Astacode Lt. 18, Jakarta Selatan, diwakili oleh Ahmad Fauzi selaku Direktur Utama (selanjutnya disebut "PIHAK KEDUA").</li>
</ol>
<p>Para Pihak dengan ini sepakat mengikatkan diri dalam Perjanjian Kerja Sama Pengembangan Sistem dengan 22 Pasal Ketentuan Baku sebagaimana terlampir.</p>
{{mou.clauses_22}}
HTML,
                'merge_variables' => ['mou.number', 'mou.date', 'customer.legal_name', 'customer.billing_address', 'contact.name', 'contact.position', 'mou.clauses_22'],
                'version' => 1,
                'is_default' => true,
            ],
            [
                'code' => 'ADDENDUM_STANDARD',
                'type' => 'ADDENDUM',
                'title' => 'Addendum Perubahan Lingkup Kerja & Nilai Kontrak (Change Request)',
                'body' => <<<HTML
<h1>ADDENDUM PERJANJIAN KERJA SAMA</h1>
<p>Nomor: {{addendum.number}}</p>
<p>Addendum ini merupakan bagian tak terpisahkan dari Kontrak Utama Nomor: {{mou.number}}.</p>
<h3>Perubahan Ruang Lingkup:</h3>
<p>{{addendum.scope_changes}}</p>
<h3>Penyesuaian Nilai Investasi:</h3>
<p>Penambahan Nilai: Rp {{addendum.delta_value}}</p>
HTML,
                'merge_variables' => ['addendum.number', 'mou.number', 'addendum.scope_changes', 'addendum.delta_value'],
                'version' => 1,
                'is_default' => true,
            ],
        ];

        foreach ($templates as $t) {
            DB::table('crm_document_templates')->updateOrInsert(
                ['code' => $t['code']],
                [
                    'type' => $t['type'],
                    'title' => $t['title'],
                    'body' => $t['body'],
                    'merge_variables' => json_encode($t['merge_variables']),
                    'version' => $t['version'],
                    'is_default' => $t['is_default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

