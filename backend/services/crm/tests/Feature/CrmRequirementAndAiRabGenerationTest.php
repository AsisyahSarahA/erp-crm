<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateRabJob;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Models\RequirementVersion;
use Astacode\Kernel\Outbox\OutboxEvent;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RejectionReasonSeeder;
use Database\Seeders\StageChecklistSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CrmRequirementAndAiRabGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            PipelineStageSeeder::class,
            RejectionReasonSeeder::class,
            StageChecklistSeeder::class,
            RateCardSeeder::class,
            ModuleCatalogSeeder::class,
        ]);
    }

    private function createBaseLead(array $overrides = []): Lead
    {
        $customer = Customer::factory()->create([
            'legal_name' => 'PT Maju Bersama Sejahtera',
        ]);

        return Lead::factory()->create(array_merge([
            'customer_id' => $customer->id,
            'title' => 'Sistem Informasi HRIS & Payroll 200 Karyawan',
            'project_type' => 'erp_custom',
            'priority' => 'hot',
            'stage' => 'FOLLOW_UP',
            'stage_id' => 'follow_up',
            'est_value' => 250_000_000,
            'owner_id' => 10,
            'sales_pic_id' => 10,
            'sales_pic_name' => 'Budi Santoso',
            'has_next_step' => true,
        ], $overrides));
    }

    /**
     * BDD Test AC-CRM-03 & BR-CRM-10 (DoD):
     * GIVEN tab Requirement berisi prompt "aplikasi HRD..."
     * WHEN klik Generate
     * THEN dalam <= 60 detik tab RAB terisi item modul (Master Data, Transaksi, Laporan, Dashboard & KPI, Non-Fungsional, Integrasi)
     * dengan mandays per role, layer WBS, dan badge sumber AI (origin = AI),
     * notifikasi RabGenerated terkirim, dan hasil AI TIDAK pernah otomatis masuk ke Quotation (BR-CRM-10).
     */
    public function test_ac_crm_03_and_br_crm_10_ai_generate_rab_populates_decomposed_modules_under_60s_and_never_creates_quotation(): void
    {
        Http::fake([
            '*/internal/ai/run' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'ai_run_mock_001',
                    'provider' => 'gemini',
                    'model' => 'gemini-2.0-flash',
                    'tokens_in' => 510,
                    'tokens_out' => 940,
                    'cost' => 0.0024,
                    'output_json' => [
                        'summary' => [
                            'total_mandays' => 94,
                            'duration_weeks' => 10,
                            'assumptions' => ['Klien menyiapkan data master karyawan dalam format Excel.'],
                            'risks' => ['Integrasi API bank memerlukan persetujuan tim kepatuhan.'],
                            'clarifying_questions' => ['Perlu integrasi mesin fingerprint fisik?'],
                            'tech_stack' => [
                                'frontend' => 'React 18 + TypeScript',
                                'backend' => 'Laravel 11',
                                'database' => 'PostgreSQL',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $lead = $this->createBaseLead();

        $res = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'prompt_text' => 'Client ingin aplikasi HRD untuk 200 karyawan, ada presensi GPS, payroll, rekrutmen, laporan, dashboard KPI.',
            'params' => [
                'platform' => 'keduanya',
                'tech_preference' => 'Laravel + React + Flutter',
                'user_count' => 200,
                'mandatory_integrations' => ['WhatsApp API', 'BCA Corporate'],
                'detail_level' => 'Standar',
                'catalog_template' => 'ERP HRD standar',
            ],
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.initial_status', 'PROCESSING')
            ->assertJsonPath('data.requirement.status', 'COMPLETED')
            ->assertJsonPath('data.lead_stage_id', 'requirement');

        $latencyMs = (int) $res->json('data.requirement.latency_ms');
        $this->assertLessThanOrEqual(60000, $latencyMs, 'Eksekusi AI Generate RAB wajib selesai dalam <= 60 detik (AC-CRM-03).');

        $rabDraft = $res->json('data.rab_draft');
        $this->assertNotNull($rabDraft);
        $this->assertEquals('AI', $rabDraft['source']);
        $this->assertEquals('DRAFT', $rabDraft['status']);
        $this->assertGreaterThan(0, $rabDraft['total_mandays']);
        $this->assertGreaterThan(0, $rabDraft['total']);
        $this->assertNotEmpty($rabDraft['clarifying_questions']);
        $this->assertNotEmpty($rabDraft['tech_stack']);
        $this->assertNotEmpty($rabDraft['out_of_scope']);

        $items = $rabDraft['items'];
        $this->assertNotEmpty($items);

        $categories = array_unique(array_column($items, 'category'));
        $this->assertContains('Master Data', $categories);
        $this->assertContains('Transaksi', $categories);
        $this->assertContains('Laporan', $categories);
        $this->assertContains('Dashboard & KPI', $categories);
        $this->assertContains('Non-Fungsional', $categories);
        $this->assertContains('Integrasi', $categories);

        $layers = array_unique(array_column($items, 'layer'));
        $this->assertContains('Frontend UI/UX', $layers);
        $this->assertContains('Backend Core & DB', $layers);
        $this->assertContains('Mobile', $layers);
        $this->assertContains('Integrasi API/Payment Gateway', $layers);
        $this->assertContains('DevOps & Cloud Security', $layers);
        $this->assertContains('QA & UAT', $layers);

        foreach ($items as $item) {
            $this->assertEquals('AI', $item['origin'], 'Setiap item RAB dari AI wajib memiliki badge sumber AI (origin=AI).');
            $this->assertGreaterThan(0, $item['ai_confidence']);
            $this->assertGreaterThan(0, $item['mandays']);
        }

        // Verify Outbox SSE notification `RabGenerated` was emitted
        $outbox = OutboxEvent::where('event_name', 'RabGenerated')->first();
        $this->assertNotNull($outbox, 'Event notifikasi SSE RabGenerated harus tercatat di outbox.');
        $this->assertEquals($lead->id, $outbox->payload['lead_id']);

        // DoD BR-CRM-10: Hasil AI bersifat draf; tidak pernah otomatis masuk quotation tanpa review manusia
        $this->assertEquals(0, Quotation::where('lead_id', $lead->id)->count(), 'BR-CRM-10: Hasil AI tidak boleh otomatis membuat Quotation.');
        $this->assertNull($lead->fresh()->active_quotation_id);
    }

    /**
     * Test Queue::fake() verifies RequirementVersion is saved with status=PROCESSING and GenerateRabJob is dispatched.
     */
    public function test_generate_endpoint_saves_processing_status_and_dispatches_generate_rab_job(): void
    {
        Queue::fake();

        $lead = $this->createBaseLead();

        $res = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'prompt_text' => 'Aplikasi ERP Logistik & Tracking Armada',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.requirement.status', 'PROCESSING');

        $reqId = (int) $res->json('data.requirement.id');
        Queue::assertPushed(GenerateRabJob::class, fn (GenerateRabJob $job) => $job->requirementId === $reqId);
    }

    /**
     * Test Upload Validation:
     * - Missing both prompt and files -> 422
     * - File > 25 MB -> 422 FILE_TOO_LARGE
     * - Antivirus hook detects EICAR signature or .exe -> 422 VIRUS_DETECTED
     * - Multi-kind files (doc, audio, image) -> saved & transcribed during generation
     */
    public function test_upload_validation_antivirus_25mb_limit_and_multi_kind_files(): void
    {
        $lead = $this->createBaseLead();

        // 1. Neither prompt nor files -> 422
        $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements", [])
            ->assertStatus(422);

        // 2. File exceeding 25 MB -> 422 FILE_TOO_LARGE
        $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements", [
            'files' => [
                [
                    'file_name' => 'huge_video_recording.mp4',
                    'mime_type' => 'video/mp4',
                    'file_size_bytes' => 27 * 1024 * 1024, // 27 MB
                ],
            ],
        ])->assertStatus(422)
            ->assertJsonPath('error_code', 'FILE_TOO_LARGE');

        // 3. Antivirus hook blocks EICAR signature
        $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements", [
            'files' => [
                [
                    'file_name' => 'tor_infected.txt',
                    'mime_type' => 'text/plain',
                    'file_size_bytes' => 128,
                    'content' => 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*',
                ],
            ],
        ])->assertStatus(422)
            ->assertJsonPath('error_code', 'VIRUS_DETECTED');

        // 4. Valid files-only submission (doc + audio + image)
        $resValid = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'files' => [
                [
                    'file_name' => 'TOR_ERP_HRD_2026.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size_bytes' => 2 * 1024 * 1024,
                ],
                [
                    'file_name' => 'rekaman_discovery_klien.m4a',
                    'mime_type' => 'audio/mp4',
                    'file_size_bytes' => 5 * 1024 * 1024,
                ],
                [
                    'file_name' => 'wireframe_dashboard.png',
                    'mime_type' => 'image/png',
                    'file_size_bytes' => 800 * 1024,
                ],
            ],
        ]);

        $resValid->assertStatus(201)
            ->assertJsonPath('data.requirement.status', 'COMPLETED');

        $files = $resValid->json('data.requirement.files');
        $this->assertCount(3, $files);
        $kinds = array_column($files, 'kind');
        $this->assertEquals(['doc', 'audio', 'image'], $kinds);
        $this->assertNotEmpty($files[0]['transcript_text']);
        $this->assertNotEmpty($files[1]['transcript_text']);
        $this->assertNotEmpty($files[2]['transcript_text']);
    }

    /**
     * Test Token Chunking, PII Redaction, and Zero-Retention Policy.
     */
    public function test_token_chunking_pii_redaction_and_zero_retention_policy(): void
    {
        $lead = $this->createBaseLead();

        $longPrompt = "Kontak PIC adalah direktur@majubersama.co.id dengan WA +6281299887766 dan NPWP 0123456789012345. "
            . str_repeat("Kebutuhan modul manajemen karyawan, presensi, dan penggajian terintegrasi. ", 120);

        $res = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'prompt_text' => $longPrompt,
            'params' => [
                'redact_pii' => true,
                'zero_retention' => true,
            ],
        ]);

        $res->assertStatus(201);
        $reqData = $res->json('data.requirement');

        $this->assertTrue($reqData['pii_redacted']);
        $this->assertTrue($reqData['zero_retention']);
        $this->assertGreaterThanOrEqual(2, $reqData['token_chunks_count']);
        $this->assertStringNotContainsString('direktur@majubersama.co.id', $reqData['normalized_text']);
        $this->assertStringNotContainsString('+6281299887766', $reqData['normalized_text']);
        $this->assertStringNotContainsString('0123456789012345', $reqData['normalized_text']);
        $this->assertStringContainsString('[EMAIL_REDACTED]', $reqData['normalized_text']);
        $this->assertStringContainsString('[PHONE_REDACTED]', $reqData['normalized_text']);
        $this->assertStringContainsString('[ID_REDACTED]', $reqData['normalized_text']);
    }

    /**
     * Test Versioning, Does Not Overwrite Manually Edited RAB,
     * "Regenerate dengan Feedback", "Generate per Bagian",
     * "Gunakan sebagai RAB", and "Gabungkan (merge) ke RAB saat ini".
     */
    public function test_versioning_preserves_edited_rab_and_supports_use_merge_regenerate_and_partial_section(): void
    {
        $lead = $this->createBaseLead();

        // 1. Suppose user already has a manually edited RAB v1
        $manualRab = RabVersion::create([
            'lead_id' => $lead->id,
            'version' => 1,
            'source' => 'MANUAL',
            'status' => 'DRAFT',
            'total_mandays' => 15,
            'subtotal' => 30_000_000,
            'dpp' => 30_000_000,
            'total' => 33_300_000,
            'wbs_completed' => true,
        ]);
        RabItem::create([
            'rab_version_id' => $manualRab->id,
            'lead_id' => $lead->id,
            'category' => 'Custom Hardware',
            'layer' => 'Integrasi API/Payment Gateway',
            'name' => 'Konektor Mesin Timbangan Digital Pabrik',
            'role' => 'BE',
            'mandays' => 15,
            'subtotal_sell' => 30_000_000,
            'subtotal_cost' => 15_000_000,
            'origin' => 'MANUAL',
        ]);
        $lead->active_rab_version_id = $manualRab->id;
        $lead->saveQuietly();

        // 2. Run AI Generate RAB -> creates Requirement v1 and RAB v2 (does NOT overwrite manual RAB v1!)
        $resGen1 = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'prompt_text' => 'Aplikasi HRD & Presensi Karyawan Pabrik',
        ])->assertStatus(201);

        $reqV1Id = (int) $resGen1->json('data.requirement.id');
        $aiRabV2Id = (int) $resGen1->json('data.rab_draft.id');

        $this->assertEquals(1, $resGen1->json('data.requirement.version'));
        $this->assertEquals(2, $resGen1->json('data.rab_draft.version'));
        // Active RAB is still v1 (manual) until user chooses "Gunakan sebagai RAB" or "Merge"
        $this->assertEquals($manualRab->id, $lead->fresh()->active_rab_version_id);

        // 3. Regenerate with Feedback -> creates Requirement v2 and RAB v3
        $resRegen = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'requirement_id' => $reqV1Id,
            'generation_mode' => 'REGENERATE_FEEDBACK',
            'feedback_text' => 'Tambahkan modul inventori APD pabrik',
        ])->assertStatus(201);

        $reqV2Id = (int) $resRegen->json('data.requirement.id');
        $this->assertEquals(2, $resRegen->json('data.requirement.version'));
        $this->assertEquals(3, $resRegen->json('data.rab_draft.version'));

        // 4. Generate per bagian (Partial Section = Laporan) -> creates Requirement v3 and RAB v4
        $resPartial = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/generate", [
            'requirement_id' => $reqV1Id,
            'generation_mode' => 'PARTIAL_SECTION',
            'target_section' => 'Laporan',
        ])->assertStatus(201);

        $reqV3Id = (int) $resPartial->json('data.requirement.id');
        $partialItems = $resPartial->json('data.rab_draft.items');
        $this->assertCount(1, $partialItems);
        $this->assertEquals('Laporan', $partialItems[0]['category']);

        // 5. Action "Gabungkan (merge) ke RAB saat ini" using Requirement v3 (adds Laporan item to Manual RAB v1 -> creates RAB v5)
        $resMerge = $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/{$reqV3Id}/merge-to-rab")
            ->assertStatus(200)
            ->assertJsonPath('data.human_decision', 'MERGED_TO_RAB');

        $mergedRabId = (int) $resMerge->json('data.active_rab_version_id');
        $this->assertNotEquals($manualRab->id, $mergedRabId);
        $this->assertCount(2, $resMerge->json('data.rab_version.items'));

        // 6. Action "Gunakan sebagai RAB" using Requirement v2
        $this->postJson("/api/v1/crm/leads/{$lead->id}/requirements/{$reqV2Id}/use-as-rab")
            ->assertStatus(200)
            ->assertJsonPath('data.human_decision', 'USED_AS_RAB');

        $this->assertEquals($resRegen->json('data.rab_draft.id'), $lead->fresh()->active_rab_version_id);
        // Verify original Manual RAB v1 still exists intact in database
        $this->assertDatabaseHas('crm_rab_versions', ['id' => $manualRab->id, 'version' => 1, 'source' => 'MANUAL']);
    }
}

