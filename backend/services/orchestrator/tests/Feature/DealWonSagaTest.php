<?php

namespace Tests\Feature;

use App\Models\Saga;
use App\Models\SagaStep;
use App\Services\DealWonSagaService;
use Tests\TestCase;

class DealWonSagaTest extends TestCase
{
    private DealWonSagaService $sagaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sagaService = new DealWonSagaService();
    }

    private function getDealPayload(string $dealId = '01HQ7DEAL890'): array
    {
        return [
            'deal_id' => $dealId,
            'deal_code' => 'AST-2026-0042',
            'title' => 'Pengembangan Sistem Enterprise HRIS',
            'contract_value' => '221445000.00',
            'customer' => [
                'id' => '01HQ7CUST111',
                'legal_name' => 'PT Maju Sejahtera Industri',
                'npwp' => '01.234.567.8-901.000',
            ],
            'total_mandays' => 142.0,
            'top_scheme' => [
                ['term_no' => 1, 'percentage' => 30.0, 'amount' => '66433500.00'],
                ['term_no' => 2, 'percentage' => 40.0, 'amount' => '88578000.00'],
                ['term_no' => 3, 'percentage' => 30.0, 'amount' => '66433500.00'],
            ],
            'wbs_modules' => [
                ['epic_name' => 'Autentikasi & RBAC', 'role' => 'Backend', 'mandays' => 12.0],
                ['epic_name' => 'Absensi GPS', 'role' => 'Mobile', 'mandays' => 18.0],
            ],
        ];
    }

    public function test_saga_executes_all_steps_and_completes_successfully(): void
    {
        $payload = $this->getDealPayload('DEAL-SUCCESS-001');

        $saga = $this->sagaService->start($payload);

        $this->assertEquals('COMPLETED', $saga->status);
        $this->assertEquals(4, $saga->completed_steps);
        $this->assertNotNull($saga->completed_at);

        // Verify all 4 steps completed
        $this->assertEquals(4, $saga->steps()->where('status', 'COMPLETED')->count());

        // Verify outbox event deal.handover_completed was published
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'deal.handover_completed',
            'aggregate_id' => 'DEAL-SUCCESS-001',
        ]);
    }

    public function test_saga_partial_failure_and_successful_retry(): void
    {
        $payload = $this->getDealPayload('DEAL-PARTIAL-002');
        // Simulate network failure on Step 2 (PM project creation)
        $payload['simulate_failures'] = ['pm_project_init'];

        $saga = $this->sagaService->start($payload);

        // Saga should be PARTIAL because Step 1 succeeded but Step 2 failed
        $this->assertEquals('PARTIAL', $saga->status);
        $this->assertStringContainsString('pm_project_init', $saga->failure_reason);

        $step2 = $saga->steps()->where('step_order', 2)->first();
        $this->assertEquals('FAILED', $step2->status);

        // Execute Retry Handover endpoint on Step 2
        $retryResponse = $this->postJson("/api/sagas/{$saga->id}/retry-step/2");
        $retryResponse->assertStatus(200);

        $saga->refresh();
        $this->assertEquals('COMPLETED', $saga->status);
        $this->assertEquals(4, $saga->completed_steps);
    }

    public function test_idempotency_prevents_duplicate_project_creation_br_crm_13(): void
    {
        $payload = $this->getDealPayload('DEAL-IDEMPOTENT-003');

        // First start
        $saga1 = $this->sagaService->start($payload);

        // Second start attempt with identical deal_id
        $saga2 = $this->sagaService->start($payload);

        // Must return the exact same saga ID
        $this->assertEquals($saga1->id, $saga2->id);

        // Exactly one saga record should exist for this deal
        $this->assertEquals(1, Saga::where('reference_id', 'DEAL-IDEMPOTENT-003')->count());

        // Exactly one PM project init step should exist
        $this->assertEquals(1, SagaStep::where('idempotency_key', 'deal-DEAL-IDEMPOTENT-003+pm')->count());
    }

    public function test_kickoff_gate_readiness_and_waive_workflow_fr_crm_23(): void
    {
        $payload = $this->getDealPayload('DEAL-GATE-004');
        $saga = $this->sagaService->start($payload);

        // Initial state: DP unpaid -> Kickoff Gate is LOCKED
        $this->assertFalse($saga->checkKickoffReadiness());
        $this->assertEquals('LOCKED', $saga->kickoff_gate_status);

        // 1. Simulate client pays DP Invoice
        $this->postJson("/api/sagas/{$saga->id}/readiness", [
            'dp_paid' => true,
        ])->assertStatus(200);

        $saga->refresh();
        $this->assertTrue($saga->checkKickoffReadiness());
        $this->assertEquals('PASSED', $saga->kickoff_gate_status);

        // 2. Test Direktur waive kickoff gate on another saga
        $sagaUnpaid = $this->sagaService->start($this->getDealPayload('DEAL-WAIVE-005'));
        $this->assertEquals('LOCKED', $sagaUnpaid->kickoff_gate_status);

        $this->postJson("/api/sagas/{$sagaUnpaid->id}/waive-kickoff", [
            'reason' => 'Klien enterprise terpercaya BUMN, DP disepakati mundur 14 hari.',
        ])->assertStatus(200);

        $sagaUnpaid->refresh();
        $this->assertEquals('WAIVED', $sagaUnpaid->kickoff_gate_status);
        $this->assertTrue($sagaUnpaid->checkKickoffReadiness());
    }

    public function test_manual_compensation_cancels_saga_before_kickoff(): void
    {
        $payload = $this->getDealPayload('DEAL-COMPENSATE-006');
        $saga = $this->sagaService->start($payload);

        $this->assertEquals('COMPLETED', $saga->status);

        // Deal cancelled by client before kickoff
        $response = $this->postJson("/api/sagas/{$saga->id}/compensate", [
            'reason' => 'Klien membatalkan proyek sebelum penandatanganan final BAST.',
        ]);

        $response->assertStatus(200);

        $saga->refresh();
        $this->assertEquals('COMPENSATED', $saga->status);

        // All steps compensated
        $this->assertEquals(4, $saga->steps()->where('status', 'COMPENSATED')->count());

        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'saga.deal_won.compensated',
            'aggregate_id' => (string) $saga->id,
        ]);
    }
}

