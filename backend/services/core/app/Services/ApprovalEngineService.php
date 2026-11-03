<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalStep;
use Astacode\Kernel\Outbox\EventPublisher;
use Astacode\Kernel\Support\AssertDifferentUser;
use Exception;
use Illuminate\Support\Facades\DB;

class ApprovalEngineService
{
    /**
     * Submits a new approval request.
     */
    public function submit(
        string $referenceType,
        string $referenceId,
        string $approvalType,
        int $requesterId,
        array $stepsDefinition, // e.g. [['role' => 'sales_manager'], ['role' => 'director']]
        array $payload = [],
        ?string $notes = null,
        int $slaHours = 24
    ): Approval {
        return DB::transaction(function () use (
            $referenceType,
            $referenceId,
            $approvalType,
            $requesterId,
            $stepsDefinition,
            $payload,
            $notes,
            $slaHours
        ) {
            $totalStages = count($stepsDefinition);

            $approval = Approval::create([
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'approval_type' => $approvalType,
                'requester_id' => $requesterId,
                'current_stage' => 1,
                'total_stages' => max(1, $totalStages),
                'status' => 'pending',
                'sla_hours' => $slaHours,
                'deadline_at' => now()->addHours($slaHours),
                'notes' => $notes,
                'payload' => $payload,
            ]);

            foreach ($stepsDefinition as $index => $stepDef) {
                ApprovalStep::create([
                    'approval_id' => $approval->id,
                    'stage_order' => $index + 1,
                    'approver_role' => $stepDef['role'] ?? null,
                    'approver_id' => $stepDef['user_id'] ?? null,
                    'status' => 'pending',
                ]);
            }

            return $approval;
        });
    }

    /**
     * Records an approval or rejection decision.
     */
    public function decide(
        Approval $approval,
        int $approverId,
        string $action, // 'approved' or 'rejected'
        ?string $comments = null
    ): Approval {
        // Enforce Segregation of Duties (BR-FIN-08, §3.3)
        AssertDifferentUser::check(
            $approval->requester_id,
            $approverId,
            "Approval {$approval->approval_type}"
        );

        return DB::transaction(function () use ($approval, $approverId, $action, $comments) {
            $currentStep = $approval->currentStep();
            if (!$currentStep) {
                throw new Exception("No pending approval step for stage {$approval->current_stage}.");
            }

            $currentStep->update([
                'approver_id' => $approverId,
                'status' => $action,
                'comments' => $comments,
                'decided_at' => now(),
            ]);

            if ($action === 'rejected') {
                $approval->update(['status' => 'rejected']);
            } elseif ($action === 'approved') {
                if ($approval->current_stage >= $approval->total_stages) {
                    $approval->update(['status' => 'approved']);
                } else {
                    $approval->increment('current_stage');
                    $approval->update(['deadline_at' => now()->addHours($approval->sla_hours)]);
                }
            }

            // Emit approval.decided event if terminal status reached
            if (in_array($approval->status, ['approved', 'rejected'], true)) {
                EventPublisher::toOutbox(
                    eventName: 'approval.decided',
                    payload: [
                        'approval_id' => $approval->id,
                        'reference_type' => $approval->reference_type,
                        'reference_id' => $approval->reference_id,
                        'approval_type' => $approval->approval_type,
                        'status' => $approval->status,
                        'decided_by' => $approverId,
                        'comments' => $comments,
                        'decided_at' => now()->toIso8601String(),
                    ],
                    aggregateType: 'approval',
                    aggregateId: (string) $approval->id
                );
            }

            return $approval->fresh(['steps']);
        });
    }
}

