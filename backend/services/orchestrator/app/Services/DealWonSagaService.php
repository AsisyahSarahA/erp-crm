<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Saga;
use App\Models\SagaStep;
use Astacode\Kernel\Outbox\EventPublisher;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DealWonSagaService
{
    /**
     * Initializes and starts a new Deal Won Saga handover workflow.
     */
    public function start(array $dealPayload, ?string $correlationId = null): Saga
    {
        $dealId = $dealPayload['deal_id'] ?? (string) str()->uuid();
        $correlationId = $correlationId ?? (string) str()->uuid();

        // 1. Idempotency Check: prevent duplicate saga for same deal (BR-CRM-13)
        $existing = Saga::where('saga_name', 'deal_won_handover')
            ->where('reference_id', $dealId)
            ->first();

        if ($existing) {
            Log::info("Saga deal_won_handover already exists for deal {$dealId}. Resuming/Returning existing saga.");
            return $existing;
        }

        return DB::transaction(function () use ($dealPayload, $dealId, $correlationId) {
            $saga = Saga::create([
                'saga_name' => 'deal_won_handover',
                'correlation_id' => $correlationId,
                'reference_type' => 'deal',
                'reference_id' => $dealId,
                'status' => 'IN_PROGRESS',
                'total_steps' => 4,
                'completed_steps' => 0,
                'context' => $dealPayload,
                'readiness_check' => [
                    'dp_paid' => false,
                    'dp_waived' => false,
                    'contract_signed' => true,
                    'scope_locked' => true,
                ],
                'kickoff_gate_status' => 'LOCKED',
                'started_at' => now(),
            ]);

            // Define the 4 standard saga steps per PRD §5.3
            $stepsDefinitions = [
                [
                    'order' => 1,
                    'name' => 'crm_customer_upsert',
                    'service' => 'crm',
                    'key' => "deal-{$dealId}+crm",
                ],
                [
                    'order' => 2,
                    'name' => 'pm_project_init',
                    'service' => 'pm',
                    'key' => "deal-{$dealId}+pm", // BR-CRM-13 idempotent key
                ],
                [
                    'order' => 3,
                    'name' => 'finance_receivable_init',
                    'service' => 'finance',
                    'key' => "deal-{$dealId}+fin",
                ],
                [
                    'order' => 4,
                    'name' => 'hrd_capacity_demand',
                    'service' => 'hrd',
                    'key' => "deal-{$dealId}+hrd",
                ],
            ];

            foreach ($stepsDefinitions as $def) {
                SagaStep::create([
                    'saga_id' => $saga->id,
                    'step_order' => $def['order'],
                    'step_name' => $def['name'],
                    'target_service' => $def['service'],
                    'status' => 'PENDING',
                    'idempotency_key' => $def['key'],
                    'request_payload' => $dealPayload,
                ]);
            }

            return $this->executeSaga($saga);
        });
    }

    /**
     * Executes pending steps in order.
     */
    public function executeSaga(Saga $saga): Saga
    {
        $steps = $saga->steps()->orderBy('step_order', 'asc')->get();

        foreach ($steps as $step) {
            if ($step->status === 'COMPLETED') {
                continue;
            }

            $success = $this->executeStep($step, $saga->context);

            if (!$success) {
                // If any step fails, Saga enters PARTIAL or FAILED state
                $hasCompletedAny = $saga->steps()->where('status', 'COMPLETED')->exists();
                $saga->update([
                    'status' => $hasCompletedAny ? 'PARTIAL' : 'FAILED',
                    'failure_reason' => "Langkah {$step->step_order} ({$step->step_name}) gagal: {$step->error_message}",
                ]);

                EventPublisher::toOutbox(
                    eventName: 'saga.deal_won.step_failed',
                    payload: [
                        'saga_id' => $saga->id,
                        'deal_id' => $saga->reference_id,
                        'failed_step' => $step->step_order,
                        'step_name' => $step->step_name,
                        'error' => $step->error_message,
                    ],
                    aggregateType: 'saga',
                    aggregateId: (string) $saga->id
                );

                return $saga->fresh(['steps']);
            }
        }

        // All steps succeeded
        $saga->update([
            'status' => 'COMPLETED',
            'completed_steps' => $saga->total_steps,
            'completed_at' => now(),
            'failure_reason' => null,
        ]);

        EventPublisher::toOutbox(
            eventName: 'deal.handover_completed',
            payload: [
                'saga_id' => $saga->id,
                'deal_id' => $saga->reference_id,
                'status' => 'COMPLETED',
                'customer_id' => $saga->context['customer']['id'] ?? null,
                'project_id' => $saga->context['project_id'] ?? 'PRJ-2026-0001',
                'completed_at' => now()->toIso8601String(),
            ],
            aggregateType: 'deal',
            aggregateId: (string) $saga->reference_id
        );

        return $saga->fresh(['steps']);
    }

    /**
     * Executes a single saga step with simulated dispatch / response.
     */
    public function executeStep(SagaStep $step, array $context): bool
    {
        $step->increment('attempts');
        $step->update(['status' => 'RUNNING']);

        // Check if failure is explicitly simulated in context for testing
        $simulatedFailures = $context['simulate_failures'] ?? [];
        if (in_array($step->step_name, $simulatedFailures, true) && $step->attempts < 2) {
            $step->update([
                'status' => 'FAILED',
                'error_message' => "Simulated network timeout connecting to {$step->target_service}-service",
            ]);
            return false;
        }

        try {
            $response = match ($step->step_name) {
                'crm_customer_upsert' => [
                    'customer_id' => $context['customer']['id'] ?? 'CUST-001',
                    'customer_tier' => 'Enterprise',
                    'deal_status' => 'LOCKED_HANDOVER',
                ],
                'pm_project_init' => [
                    'project_id' => 'PRJ-' . date('Y') . '-0042',
                    'project_name' => $context['title'] ?? 'Proyek Baru',
                    'epics_count' => count($context['wbs_modules'] ?? [1, 2, 3]),
                    'tasks_created' => 12,
                    'idempotency_key' => $step->idempotency_key,
                ],
                'finance_receivable_init' => [
                    'receivable_schedule_id' => 'RS-2026-0088',
                    'terms_count' => count($context['top_scheme'] ?? [1, 2, 3]),
                    'dp_invoice_number' => 'INV/' . date('Y/m') . '/0001',
                    'dp_amount' => '66433500.00',
                ],
                'hrd_capacity_demand' => [
                    'demand_id' => 'DEMAND-' . str()->uuid(),
                    'total_mandays' => $context['total_mandays'] ?? 142.0,
                    'roles_allocated' => ['Backend', 'Mobile', 'QA'],
                ],
                default => ['status' => 'ok'],
            };

            $step->update([
                'status' => 'COMPLETED',
                'response_payload' => $response,
                'executed_at' => now(),
                'error_message' => null,
            ]);

            return true;
        } catch (Exception $e) {
            $step->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Retries a specific failed handover step (Endpoint: Retry Handover per langkah).
     */
    public function retryStep(Saga $saga, int $stepOrder): Saga
    {
        $step = $saga->steps()->where('step_order', $stepOrder)->first();
        if (!$step) {
            throw new Exception("Langkah handover urutan {$stepOrder} tidak ditemukan pada Saga ini.");
        }

        if ($step->status === 'COMPLETED') {
            return $saga->fresh(['steps']);
        }

        // Clean any simulated failure for this step so retry can succeed
        $context = $saga->context;
        if (isset($context['simulate_failures'])) {
            $context['simulate_failures'] = array_values(array_diff($context['simulate_failures'], [$step->step_name]));
            $saga->update(['context' => $context]);
        }

        $step->update(['status' => 'PENDING']);
        $this->executeSaga($saga);

        return $saga->fresh(['steps']);
    }

    /**
     * Evaluates and updates Kickoff Gate Readiness Check (FR-CRM-23).
     */
    public function updateReadiness(Saga $saga, array $checks): Saga
    {
        $current = $saga->readiness_check ?? [];
        $merged = array_merge($current, $checks);

        $passed = ($merged['dp_paid'] ?? false || $merged['dp_waived'] ?? false)
            && ($merged['contract_signed'] ?? false)
            && ($merged['scope_locked'] ?? false);

        $gateStatus = $passed ? 'PASSED' : 'LOCKED';

        $saga->update([
            'readiness_check' => $merged,
            'kickoff_gate_status' => $gateStatus,
        ]);

        return $saga->fresh();
    }

    /**
     * Waives Kickoff Gate condition (e.g. Direktur membebaskan DP sebelum kickoff).
     */
    public function waiveKickoffGate(Saga $saga, string $waivedBy, string $reason): Saga
    {
        $current = $saga->readiness_check ?? [];
        $current['dp_waived'] = true;
        $current['waived_by'] = $waivedBy;
        $current['waive_reason'] = $reason;

        $saga->update([
            'readiness_check' => $current,
            'kickoff_gate_status' => 'WAIVED',
        ]);

        return $saga->fresh();
    }

    /**
     * Compensates (rolls back) the Saga if deal is cancelled before Kickoff Gate.
     */
    public function compensate(Saga $saga, string $reason): Saga
    {
        $saga->update([
            'status' => 'COMPENSATING',
            'failure_reason' => $reason,
        ]);

        // Compensate steps in reverse order
        $steps = $saga->steps()->where('status', 'COMPLETED')->orderBy('step_order', 'desc')->get();
        foreach ($steps as $step) {
            $step->update(['status' => 'COMPENSATED']);
        }

        $saga->update([
            'status' => 'COMPENSATED',
            'completed_at' => now(),
        ]);

        EventPublisher::toOutbox(
            eventName: 'saga.deal_won.compensated',
            payload: [
                'saga_id' => $saga->id,
                'deal_id' => $saga->reference_id,
                'reason' => $reason,
                'compensated_at' => now()->toIso8601String(),
            ],
            aggregateType: 'saga',
            aggregateId: (string) $saga->id
        );

        return $saga->fresh(['steps']);
    }
}

