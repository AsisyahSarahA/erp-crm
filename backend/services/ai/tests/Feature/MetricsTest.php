<?php

namespace Tests\Feature;

use App\Models\AiRun;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    public function test_observability_metrics_calculation(): void
    {
        // Run 1: Accepted, 80 estimated vs 88 actual (10% error)
        AiRun::create([
            'module' => 'crm',
            'purpose' => 'generate_rab',
            'provider' => 'mock',
            'model' => 'mock-model',
            'input_hash' => 'hash1',
            'input_payload' => [],
            'status' => 'SUCCESS',
            'human_decision' => 'ACCEPTED',
            'estimated_mandays' => 80.0,
            'actual_mandays' => 88.0,
            'tokens_in' => 200,
            'tokens_out' => 400,
            'cost' => 0.001,
        ]);

        // Run 2: Rejected
        AiRun::create([
            'module' => 'crm',
            'purpose' => 'generate_rab',
            'provider' => 'mock',
            'model' => 'mock-model',
            'input_hash' => 'hash2',
            'input_payload' => [],
            'status' => 'SUCCESS',
            'human_decision' => 'REJECTED',
            'tokens_in' => 150,
            'tokens_out' => 300,
            'cost' => 0.0008,
        ]);

        $response = $this->getJson('/api/metrics');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_runs', 2)
            ->assertJsonPath('data.human_acceptance_rate_pct', 50) // 1 accepted, 1 rejected
            ->assertJsonPath('data.estimation_accuracy_mape_pct', 9.09); // abs(88-80)/88 = 9.09%
    }
}
