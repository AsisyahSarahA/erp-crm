<?php

namespace Tests\Feature;

use App\Models\AiRun;
use Tests\TestCase;

class RabGeneratorTest extends TestCase
{
    public function test_dod_generate_rab_returns_valid_structured_json_and_records_run(): void
    {
        $payload = [
            'project_name' => 'Sistem Presensi GPS & Payroll HRIS',
            'features' => [
                'Login 2FA TOTP',
                'Absensi Mobile Geofence GPS 50m',
                'Perhitungan Payroll Gaji & PPh 21 TER',
                'Automated E2E Testing',
            ],
            'target_weeks' => 9,
        ];

        $response = $this->postJson('/internal/ai/run', [
            'module' => 'crm',
            'purpose' => 'generate_rab',
            'prompt_slug' => 'rab.estimator',
            'payload' => $payload,
            'options' => [
                'schema_name' => 'rab',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'SUCCESS')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'module',
                    'purpose',
                    'provider',
                    'model',
                    'output_json' => [
                        'summary' => [
                            'total_mandays',
                            'duration_weeks',
                            'assumptions',
                            'risks',
                            'clarifying_questions',
                            'tech_stack',
                        ],
                        'items' => [
                            '*' => [
                                'category',
                                'layer',
                                'name',
                                'description',
                                'role',
                                'mandays',
                                'complexity',
                                'confidence',
                                'notes',
                            ],
                        ],
                        'out_of_scope',
                    ],
                    'tokens_in',
                    'tokens_out',
                    'cost',
                    'latency_ms',
                ],
            ]);

        $runId = $response->json('data.id');

        // Verify ai_runs record in database
        $this->assertDatabaseHas('ai_runs', [
            'id' => $runId,
            'module' => 'crm',
            'purpose' => 'generate_rab',
            'status' => 'SUCCESS',
        ]);

        $aiRun = AiRun::find($runId);
        $this->assertNotNull($aiRun);
        $this->assertEquals(84.0, $aiRun->estimated_mandays);
        $this->assertGreaterThan(0, $aiRun->tokens_in);
        $this->assertGreaterThan(0, $aiRun->tokens_out);

        // Verify Outbox Event ai.run.completed was emitted
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'ai.run.completed',
            'aggregate_id' => $runId,
        ]);
    }
}

