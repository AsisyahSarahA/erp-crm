<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AiGatewayService;
use App\Services\Schemas\SchemaRegistry;
use Illuminate\Console\Command;

class AiEvalCommand extends Command
{
    protected $signature = 'ai:eval {--threshold=90 : Minimum passing percentage}';
    protected $description = 'Runs automated Golden Set evaluation on AI prompts and schemas';

    public function handle(AiGatewayService $gateway): int
    {
        $threshold = (float) $this->option('threshold');
        $this->info("🤖 Menjalankan Evaluasi Golden Set ASTACODE AI (Ambang batas kelulusan: {$threshold}%)...");

        $goldenSets = [
            [
                'name' => 'Estimasi WBS RAB Aplikasi HRIS',
                'module' => 'crm',
                'purpose' => 'generate_rab',
                'schema' => 'rab',
                'payload' => [
                    'project_name' => 'HRIS Enterprise',
                    'features' => ['Auth 2FA', 'Absensi GPS', 'Payroll PPh 21'],
                ],
            ],
            [
                'name' => 'Saran Balasan WhatsApp Prospek',
                'module' => 'crm',
                'purpose' => 'suggest_wa_reply',
                'schema' => 'wa_reply',
                'payload' => [
                    'incoming_message' => 'Berapa biaya pembuatan aplikasi enterprise?',
                ],
            ],
            [
                'name' => 'Ekstraksi Notula Voice Note Presales',
                'module' => 'crm',
                'purpose' => 'summarize_voicenote',
                'schema' => 'voicenote',
                'payload' => [
                    'transcript' => 'Klien ingin sistem presensi mobile geofencing dan integrasi payroll.',
                ],
            ],
        ];

        $passedCount = 0;
        $total = count($goldenSets);

        foreach ($goldenSets as $index => $testCase) {
            $this->line("Running [{$testCase['name']}]...");
            try {
                $run = $gateway->run(
                    module: $testCase['module'],
                    purpose: $testCase['purpose'],
                    payload: $testCase['payload'],
                    options: ['schema_name' => $testCase['schema']]
                );

                if ($run->status === 'SUCCESS' && is_array($run->output_json)) {
                    $schema = SchemaRegistry::getSchema($testCase['schema']);
                    if ($schema) {
                        $validation = SchemaRegistry::validate($run->output_json, $schema);
                        if ($validation['is_valid']) {
                            $passedCount++;
                            $this->info("  ✅ PASS: Output valid sesuai skema {$testCase['schema']}.");
                            continue;
                        }
                    }
                }
                $this->error("  ❌ FAIL: Output tidak memenuhi kriteria kelulusan.");
            } catch (\Exception $e) {
                $this->error("  ❌ ERROR: " . $e->getMessage());
            }
        }

        $score = round(($passedCount / $total) * 100, 2);
        $this->newLine();
        $this->info("Hasil Evaluasi: {$score}% ({$passedCount}/{$total} Golden Set lulus).");

        if ($score >= $threshold) {
            $this->info("🎉 Evaluasi BERHASIL: Prompt lolos ambang batas kelulusan.");
            return Command::SUCCESS;
        }

        $this->error("⛔ Evaluasi GAGAL: Skor berada di bawah ambang batas ({$threshold}%).");
        return Command::FAILURE;
    }
}

