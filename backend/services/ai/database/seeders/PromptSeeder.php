<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Seeder;

class PromptSeeder extends Seeder
{
    public function run(): void
    {
        $prompts = [
            [
                'slug' => 'rab.estimator',
                'name' => 'WBS & RAB Estimator Mandays',
                'description' => 'Menghasilkan breakdown WBS, estimasi mandays per role, durasi, asumsi, dan risiko teknis.',
                'module' => 'crm',
                'version' => 1,
                'system_prompt' => 'Anda adalah Lead Solution Architect & Presales Engineer ASTACODE. Hasilkan breakdown estimasi WBS dan RAB perangkat lunak secara realistis dalam format JSON terstruktur.',
                'user_template' => "Proyek: {{project_name}}\nKebutuhan Teknis:\n{{features}}\nTarget Selesai: {{target_weeks}} minggu",
                'schema_name' => 'rab',
                'status' => 'active',
                'model_preference' => 'accurate',
                'temperature' => 0.15,
                'is_active' => true,
            ],
            [
                'slug' => 'voicenote.summary',
                'name' => 'Voice Note Presales Summarizer',
                'description' => 'Mengekstraksi ringkasan eksekutif, kebutuhan inti klien, cakupan teknis, dan action items dari transkrip audio.',
                'module' => 'crm',
                'version' => 1,
                'system_prompt' => 'Anda adalah Business Analyst ASTACODE. Ekstraksi notula teknis dari rekaman suara percakapan presales ke dalam format JSON.',
                'user_template' => "Transkrip Voice Note:\n{{transcript}}",
                'schema_name' => 'voicenote',
                'status' => 'active',
                'model_preference' => 'fast',
                'temperature' => 0.20,
                'is_active' => true,
            ],
            [
                'slug' => 'wa.reply',
                'name' => 'Saran Balasan WhatsApp Prospek',
                'description' => 'Memberikan saran balasan ramah, profesional, dan berorientasi konversi untuk pesan chat WhatsApp calon klien.',
                'module' => 'crm',
                'version' => 1,
                'system_prompt' => 'Anda adalah Customer Relations & Inside Sales ASTACODE. Berikan saran jawaban WhatsApp yang sopan, solutif, dan mengarahkan ke jadwal meeting.',
                'user_template' => "Pesan Prospek:\n{{incoming_message}}",
                'schema_name' => 'wa_reply',
                'status' => 'active',
                'model_preference' => 'fast',
                'temperature' => 0.30,
                'is_active' => true,
            ],
            [
                'slug' => 'marketing.content_writer',
                'name' => 'AI Marketing Content Generator',
                'description' => 'Membuat draf konten carousel edukatif, naskah video pendek, dan postingan blog sosial media B2B.',
                'module' => 'marketing',
                'version' => 1,
                'system_prompt' => 'Anda adalah Content Strategist & Copywriter agensi software ASTACODE. Tulis konten B2B yang menarik dan bernilai edukasi tinggi.',
                'user_template' => "Topik: {{topic}}\nFormat: {{format}}\nTarget Pembaca: {{target_audience}}",
                'schema_name' => 'content',
                'status' => 'active',
                'model_preference' => 'accurate',
                'temperature' => 0.40,
                'is_active' => true,
            ],
        ];

        foreach ($prompts as $promptData) {
            Prompt::firstOrCreate(
                ['slug' => $promptData['slug'], 'version' => $promptData['version']],
                $promptData
            );
        }
    }
}

