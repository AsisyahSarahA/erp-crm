<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CrmImmutableAuditLog;
use App\Models\Lead;
use App\Models\LeadStageHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Centralized Pipeline Transition Guard (PRD §6.3, §6.6, FR-CRM-24, BR-CRM-01/02/03/12/14/15).
 *
 * Single authorized entry point to mutate Lead::$stage / Lead::$stage_id.
 * Used across Kanban drag-and-drop, action buttons, REST API, and Automation Rules.
 */
class PipelineTransitionGuard
{
    /**
     * Allowed forward transitions per PRD §6.3 & §6.6.
     * Note: backward transitions from active stages are governed by BR-CRM-12 (requires reason).
     */
    public const ALLOWED_FORWARD_TRANSITIONS = [
        'new_lead' => ['follow_up', 'reject'],
        'follow_up' => ['requirement', 'reject'],
        'requirement' => ['quotation', 'reject'],
        'quotation' => ['negotiation', 'reject'],
        'negotiation' => ['deal', 'quotation', 'reject'],
        'deal' => [],
        'reject' => ['follow_up'], // Only via Reopen by Sales Manager / Super Admin
    ];

    public const STAGE_SEQUENCE = [
        'new_lead' => 1,
        'follow_up' => 2,
        'requirement' => 3,
        'quotation' => 4,
        'negotiation' => 5,
        'deal' => 6,
        'reject' => 7,
    ];

    public const DEFAULT_PROBABILITIES = [
        'new_lead' => 10,
        'follow_up' => 20,
        'requirement' => 35,
        'quotation' => 55,
        'negotiation' => 75,
        'deal' => 100,
        'reject' => 0,
    ];

    /**
     * Execute a validated stage transition on a Lead.
     *
     * @param Lead $lead
     * @param string $targetStage
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $options {
     *   reason?: string|null,
     *   override?: bool,
     *   reject_reason_code?: string|null,
     *   reject_reason_id?: int|null,
     *   reject_notes?: string|null,
     *   trigger_source?: string
     * }
     * @return Lead
     * @throws PipelineGuardException
     */
    public function transition(
        Lead $lead,
        string $targetStage,
        array $userContext = [],
        array $options = [],
    ): Lead {
        $fromStageId = Lead::normalizeStageId($lead->stage_id ?: $lead->stage);
        $toStageId = Lead::normalizeStageId($targetStage);

        if (!array_key_exists($toStageId, self::STAGE_SEQUENCE)) {
            throw new PipelineGuardException(
                message: "Stage tujuan '{$targetStage}' tidak valid.",
                guardFailureCode: 'INVALID_TARGET_STAGE',
                deepLinkTab: 'overview',
                httpStatus: 422,
            );
        }

        if ($fromStageId === $toStageId) {
            return $lead;
        }

        $isOverride = (bool) ($options['override'] ?? false);
        $reason = isset($options['reason']) ? trim((string) $options['reason']) : null;

        if ($isOverride) {
            $this->validateSuperAdminOverride($userContext, $reason);
        } else {
            $this->validateTransitionPath($lead, $fromStageId, $toStageId, $userContext, $reason, $options);
            $this->validateStageEntryRequirements($lead, $fromStageId, $toStageId, $options);
        }

        return DB::transaction(function () use ($lead, $fromStageId, $toStageId, $userContext, $options, $isOverride, $reason) {
            $oldProbability = (int) ($lead->win_probability ?? 0);
            $newProbability = $this->resolveStageProbability($toStageId);

            // Calculate duration in previous stage
            $lastChange = LeadStageHistory::query()
                ->where('lead_id', $lead->id)
                ->latest('changed_at')
                ->first();
            $since = $lastChange?->changed_at ?? $lead->created_at ?? now();
            $durationHours = max(0, (int) Carbon::parse($since)->diffInHours(now()));

            $lead->authorizeStageMutationFromGuard();
            $lead->stage_id = $toStageId;
            $lead->stage = Lead::normalizeStageUpper($toStageId);
            $lead->win_probability = $newProbability;
            $lead->weighted_value = round(((float) $lead->est_value) * ($newProbability / 100.0), 2);
            $lead->days_in_stage = 1;
            $lead->is_stale = false;
            $lead->stale_since = null;

            if ($toStageId === 'deal') {
                $lead->won_at = now();
            } elseif ($toStageId === 'reject') {
                $lead->lost_at = now();
                $rejectReasonId = $options['reject_reason_id'] ?? $lead->reject_reason_id;
                $rejectReasonCode = $options['reject_reason_code'] ?? $lead->reject_reason_code;
                $rejectNotes = $options['reject_notes'] ?? $reason ?? $lead->reject_notes;

                if ($rejectReasonId && empty($rejectReasonCode)) {
                    $rejectReasonCode = DB::table('crm_rejection_reasons')
                        ->where('id', (int) $rejectReasonId)
                        ->value('code');
                } elseif ($rejectReasonCode && empty($rejectReasonId)) {
                    $rejectReasonId = DB::table('crm_rejection_reasons')
                        ->where('code', strtoupper((string) $rejectReasonCode))
                        ->value('id');
                }

                $lead->reject_reason_id = $rejectReasonId ? (int) $rejectReasonId : null;
                $lead->reject_reason_code = $rejectReasonCode ? strtoupper((string) $rejectReasonCode) : null;
                $lead->reject_notes = $rejectNotes;
            } elseif ($fromStageId === 'reject' && $toStageId === 'follow_up') {
                $lead->reopened_at = now();
                $lead->reopened_by = isset($userContext['id']) ? (int) $userContext['id'] : null;
                $lead->reopen_reason = $reason;
            }

            $lead->save();

            $actorId = isset($userContext['id']) ? (string) $userContext['id'] : 'system';
            $actorName = isset($userContext['name']) ? (string) $userContext['name'] : null;
            $actorRole = isset($userContext['role']) ? (string) $userContext['role'] : null;

            $auditReason = $reason ?? ($toStageId === 'reject' ? ($lead->reject_notes ?? null) : null);

            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'from_stage' => $fromStageId,
                'to_stage' => $toStageId,
                'changed_by' => $actorId,
                'reason' => $auditReason,
                'is_override' => $isOverride,
                'duration_hours_in_prev_stage' => $durationHours,
                'changed_at' => now(),
            ]);

            CrmImmutableAuditLog::record(
                leadId: (int) $lead->id,
                entityType: 'lead',
                entityId: (int) $lead->id,
                fieldName: 'stage',
                oldValue: [
                    'stage_id' => $fromStageId,
                    'stage' => Lead::normalizeStageUpper($fromStageId),
                    'win_probability' => $oldProbability,
                ],
                newValue: [
                    'stage_id' => $toStageId,
                    'stage' => $lead->stage,
                    'win_probability' => $newProbability,
                    'weighted_value' => $lead->weighted_value,
                ],
                actorId: $actorId,
                actorName: $actorName,
                actorRole: $actorRole,
                isOverride: $isOverride,
                reason: $auditReason,
            );

            $lead->recordEvent(
                eventName: 'lead.stage_changed',
                payload: [
                    'lead_id' => $lead->id,
                    'code' => $lead->code,
                    'customer_id' => $lead->customer_id,
                    'from_stage' => $fromStageId,
                    'to_stage' => $toStageId,
                    'win_probability' => $newProbability,
                    'est_value' => (float) $lead->est_value,
                    'weighted_value' => (float) $lead->weighted_value,
                    'is_override' => $isOverride,
                    'reason' => $auditReason,
                    'trigger_source' => $options['trigger_source'] ?? 'api',
                    'changed_by' => $actorId,
                    'changed_at' => now()->toIso8601String(),
                ],
                aggregateType: 'Lead',
                aggregateId: (string) $lead->id,
            );

            return $lead->fresh([
                'customer',
                'contact',
                'source',
                'stageHistories',
                'activeQuotation',
                'activeRab',
                'activeTopScheme',
            ]);
        });
    }

    /**
     * Validate Super Admin Override (BR-CRM-15).
     */
    protected function validateSuperAdminOverride(array $userContext, ?string $reason): void
    {
        $role = strtolower((string) ($userContext['role'] ?? ''));
        $roles = array_map('strtolower', (array) ($userContext['roles'] ?? [$role]));
        $permissions = (array) ($userContext['permissions'] ?? []);

        $canOverride = in_array('super_admin', $roles, true)
            || in_array('super admin', $roles, true)
            || in_array('crm.stage.override', $permissions, true)
            || in_array('*', $permissions, true);

        if (!$canOverride) {
            throw new PipelineGuardException(
                message: 'Hanya Super Admin dengan izin crm.stage.override yang dapat melakukan override transisi stage.',
                guardFailureCode: 'OVERRIDE_FORBIDDEN',
                deepLinkTab: 'overview',
                httpStatus: 403,
            );
        }

        if ($reason === null || mb_strlen($reason) < 5) {
            throw new PipelineGuardException(
                message: 'Override transisi stage oleh Super Admin wajib menyertakan alasan (reason minimal 5 karakter).',
                guardFailureCode: 'OVERRIDE_REASON_REQUIRED',
                deepLinkTab: 'overview',
                httpStatus: 422,
            );
        }
    }

    /**
     * Validate transition graph (forward transitions, reopen, and backward transitions per BR-CRM-12).
     */
    protected function validateTransitionPath(
        Lead $lead,
        string $fromStageId,
        string $toStageId,
        array $userContext,
        ?string $reason,
        array $options,
    ): void {
        // 1. From DEAL: terminal stage (cannot transition backward or elsewhere without override)
        if ($fromStageId === 'deal') {
            throw new PipelineGuardException(
                message: 'Lead pada tahap Deal bersifat final dan tidak dapat dipindahkan kembali.',
                guardFailureCode: 'TRANSITION_FROM_DEAL_FORBIDDEN',
                deepLinkTab: 'overview',
                httpStatus: 422,
            );
        }

        // 2. From REJECT: only allowed to FOLLOW_UP via Reopen (Sales Manager / Super Admin + reason)
        if ($fromStageId === 'reject') {
            if ($toStageId !== 'follow_up') {
                throw new PipelineGuardException(
                    message: 'Lead pada tahap Reject hanya dapat dibuka kembali (Reopen) ke tahap Follow Up.',
                    guardFailureCode: 'INVALID_REOPEN_TARGET',
                    deepLinkTab: 'overview',
                    httpStatus: 422,
                );
            }

            $role = strtolower((string) ($userContext['role'] ?? ''));
            $roles = array_map('strtolower', (array) ($userContext['roles'] ?? [$role]));
            $permissions = (array) ($userContext['permissions'] ?? []);

            $canReopen = !empty(array_intersect($roles, ['super_admin', 'super admin', 'owner', 'direktur', 'sales_manager', 'sales manager']))
                || in_array('crm.lead.reopen', $permissions, true)
                || in_array('*', $permissions, true);

            if (!$canReopen) {
                throw new PipelineGuardException(
                    message: 'Hanya Sales Manager atau Super Admin yang dapat melakukan Reopen lead dari tahap Reject.',
                    guardFailureCode: 'REOPEN_PERMISSION_DENIED',
                    deepLinkTab: 'overview',
                    httpStatus: 403,
                );
            }

            if ($reason === null || mb_strlen($reason) < 5) {
                throw new PipelineGuardException(
                    message: 'Reopen lead dari tahap Reject wajib menyertakan alasan (reason minimal 5 karakter).',
                    guardFailureCode: 'REOPEN_REASON_REQUIRED',
                    deepLinkTab: 'overview',
                    httpStatus: 422,
                );
            }

            return;
        }

        // 3. Check if backward transition (BR-CRM-12)
        $fromSeq = self::STAGE_SEQUENCE[$fromStageId] ?? 1;
        $toSeq = self::STAGE_SEQUENCE[$toStageId] ?? 1;

        if ($toStageId !== 'reject' && $toSeq < $fromSeq) {
            // Backward transition from active stages (follow_up, requirement, quotation, negotiation)
            if ($reason === null || mb_strlen($reason) < 5) {
                throw new PipelineGuardException(
                    message: 'Transisi mundur (backward transition) wajib menyertakan alasan perubahan (BR-CRM-12).',
                    guardFailureCode: 'BACKWARD_TRANSITION_REASON_REQUIRED',
                    deepLinkTab: 'overview',
                    httpStatus: 422,
                );
            }

            return;
        }

        // 4. Check forward transition table
        $allowedTargets = self::ALLOWED_FORWARD_TRANSITIONS[$fromStageId] ?? [];
        if (!in_array($toStageId, $allowedTargets, true)) {
            throw new PipelineGuardException(
                message: sprintf(
                    'Transisi dari tahap %s ke %s tidak diizinkan tanpa melewati tahapan berurutan.',
                    strtoupper($fromStageId),
                    strtoupper($toStageId)
                ),
                guardFailureCode: 'INVALID_STAGE_TRANSITION',
                deepLinkTab: 'overview',
                httpStatus: 422,
            );
        }
    }

    /**
     * Validate stage entry requirements per PRD §6.3, BR-CRM-01, BR-CRM-02, BR-CRM-03, BR-CRM-14.
     */
    protected function validateStageEntryRequirements(
        Lead $lead,
        string $fromStageId,
        string $toStageId,
        array $options,
    ): void {
        match ($toStageId) {
            'follow_up' => $this->guardEnterFollowUp($lead, $fromStageId),
            'requirement' => $this->guardEnterRequirement($lead),
            'quotation' => $this->guardEnterQuotation($lead),
            'negotiation' => $this->guardEnterNegotiation($lead),
            'deal' => $this->guardEnterDeal($lead),
            'reject' => $this->guardEnterReject($lead, $options),
            default => null,
        };
    }

    /**
     * Entry Guard: FOLLOW_UP
     * - PIC ditetapkan (owner_id / sales_pic_id)
     * - Kontak awal tercatat / terjadwal (SLA <= 30 menit jam kerja)
     */
    protected function guardEnterFollowUp(Lead $lead, string $fromStageId): void
    {
        if (empty($lead->owner_id) && empty($lead->sales_pic_id)) {
            throw new PipelineGuardException(
                message: 'Tahap Follow Up memerlukan penetapan Sales PIC terlebih dahulu.',
                guardFailureCode: 'FOLLOW_UP_PIC_REQUIRED',
                deepLinkTab: 'overview',
            );
        }

        // If reopening from reject, PIC is sufficient (reopen reason is already validated)
        if ($fromStageId === 'reject') {
            return;
        }

        $hasActivity = $lead->activities()->exists() || (bool) $lead->has_next_step;
        if (!$hasActivity) {
            throw new PipelineGuardException(
                message: 'Tahap Follow Up memerlukan pencatatan kontak awal (SLA <= 30 menit jam kerja).',
                guardFailureCode: 'FOLLOW_UP_INITIAL_CONTACT_REQUIRED',
                deepLinkTab: 'activity',
            );
        }
    }

    /**
     * Entry Guard: REQUIREMENT
     * - Klien merespons & discovery call terjadwal/tercatat
     */
    protected function guardEnterRequirement(Lead $lead): void
    {
        $activities = $lead->activities()->get();

        $hasClientResponse = $lead->client_responded_at !== null
            || $activities->contains(fn ($a) => strtoupper((string) $a->status) === 'COMPLETED' || !empty($a->outcome));

        $hasDiscoveryCall = $activities->contains(function ($a) {
            $type = strtoupper((string) $a->activity_type);
            $title = strtolower((string) $a->title);

            return in_array($type, ['CALL', 'MEETING', 'DISCOVERY_CALL'], true)
                || str_contains($title, 'discovery')
                || str_contains($title, 'requirement');
        });

        if (!$hasClientResponse || !$hasDiscoveryCall) {
            throw new PipelineGuardException(
                message: 'Tahap Requirement memerlukan respons klien dan jadwal/catatan Discovery Call.',
                guardFailureCode: 'REQUIREMENT_DISCOVERY_REQUIRED',
                deepLinkTab: 'activity',
            );
        }
    }

    /**
     * Entry Guard: QUOTATION (BR-CRM-01, AC-CRM-11)
     * - RAB >= 1 item, total > 0, WBS selesai
     * - Exact message: "Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul)."
     */
    protected function guardEnterQuotation(Lead $lead): void
    {
        $rab = $lead->activeRab ?? $lead->rabVersions()->first();

        if ($rab === null) {
            throw new PipelineGuardException(
                message: 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).',
                guardFailureCode: 'QUOTATION_RAB_REQUIRED',
                deepLinkTab: 'rab',
            );
        }

        $itemCount = $rab->items()->count();
        if ($itemCount < 1 || (float) $rab->total <= 0) {
            throw new PipelineGuardException(
                message: 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).',
                guardFailureCode: 'QUOTATION_RAB_REQUIRED',
                deepLinkTab: 'rab',
            );
        }

        if (!$rab->wbs_completed) {
            throw new PipelineGuardException(
                message: 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul) dan struktur WBS selesai.',
                guardFailureCode: 'QUOTATION_WBS_INCOMPLETE',
                deepLinkTab: 'rab',
            );
        }
    }

    /**
     * Entry Guard: NEGOTIATION
     * - Quotation & draf MoU terkirim / ada versi SENT
     */
    protected function guardEnterNegotiation(Lead $lead): void
    {
        $hasSentQuotation = $lead->quotations()
            ->whereIn('status', ['SENT', 'VIEWED', 'NEGOTIATION', 'ACCEPTED'])
            ->exists();

        if (!$hasSentQuotation) {
            throw new PipelineGuardException(
                message: 'Tahap Negotiation memerlukan Quotation dan draf MoU yang sudah dikirim (status SENT).',
                guardFailureCode: 'NEGOTIATION_QUOTATION_MOU_REQUIRED',
                deepLinkTab: 'quotation',
            );
        }
    }

    /**
     * Entry Guard: DEAL (BR-CRM-02, AC-CRM-05, AC-CRM-12, FR-CRM-20, FR-CRM-23)
     * - RAB valid (>= 1 item, total > 0)
     * - TOP balance 100% ("Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: X%." + nominal)
     * - Quotation ACCEPTED
     * - Readiness Check PM lolos
     * - Dokumen wajib stage Deal lengkap
     */
    protected function guardEnterDeal(Lead $lead): void
    {
        // 1. Check TOP Balance 100% if a TOP scheme exists (or check before/after RAB so AC-CRM-05/12 always triggers on unbalanced TOP)
        $topScheme = $lead->activeTopScheme ?? $lead->topSchemes()->first();
        $quotation = $lead->activeQuotation ?? $lead->quotations()->orderByDesc('version')->first();

        if ($topScheme !== null) {
            $totalPct = round((float) $topScheme->total_percent, 2);
            if (abs($totalPct - 100.0) >= 0.001) {
                $remainingPct = round(100.0 - $totalPct, 2);
                $remainingPctDisplay = floor($remainingPct) == $remainingPct
                    ? (string) (int) $remainingPct
                    : (string) $remainingPct;

                $referenceTotal = (float) ($quotation?->total ?: ($lead->est_value ?: 0));
                $allocatedAmount = (float) $topScheme->total_amount;
                $remainingNominal = $referenceTotal > 0
                    ? max(0.0, round($referenceTotal * ($remainingPct / 100.0), 0))
                    : max(0.0, round($referenceTotal - $allocatedAmount, 0));
                $nominalFormatted = 'Rp ' . number_format($remainingNominal, 0, ',', '.');
                $nominalCompact = 'Rp' . number_format($remainingNominal, 0, ',', '.');

                throw new PipelineGuardException(
                    message: "Total termin harus 100%. Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: {$remainingPctDisplay}% ({$nominalCompact}). Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: {$remainingPctDisplay}%. Nominal belum teralokasi: {$nominalFormatted}.",
                    guardFailureCode: 'DEAL_TOP_NOT_BALANCED',
                    deepLinkTab: 'top',
                    extra: [
                        'total_percent' => $totalPct,
                        'remaining_percent' => $remainingPct,
                        'remaining_amount' => $remainingNominal,
                        'remaining_amount_formatted' => $nominalFormatted,
                        'ac_crm_05_message' => 'Total termin harus 100%',
                        'ac_crm_12_message' => "Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: {$remainingPctDisplay}% ({$nominalCompact}).",
                        'balance_warning' => "Sisa alokasi belum diatur: {$remainingPctDisplay}% ({$nominalCompact})",
                    ],
                );
            }
        }

        // 2. Check RAB valid
        $rab = $lead->activeRab ?? $lead->rabVersions()->first();
        if ($rab === null || $rab->items()->count() < 1 || (float) $rab->total <= 0) {
            throw new PipelineGuardException(
                message: 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).',
                guardFailureCode: 'DEAL_RAB_INVALID',
                deepLinkTab: 'rab',
            );
        }

        // 3. If TOP scheme was completely missing, block with exact TOP message (100% remaining)
        if ($topScheme === null) {
            $referenceTotal = (float) ($quotation?->total ?: ($rab->total ?: ($lead->est_value ?: 0)));
            $nominalFormatted = 'Rp ' . number_format($referenceTotal, 0, ',', '.');

            throw new PipelineGuardException(
                message: "Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: 100%. Nominal belum teralokasi: {$nominalFormatted}.",
                guardFailureCode: 'DEAL_TOP_NOT_BALANCED',
                deepLinkTab: 'top',
                extra: [
                    'total_percent' => 0,
                    'remaining_percent' => 100,
                    'remaining_amount' => $referenceTotal,
                    'remaining_amount_formatted' => $nominalFormatted,
                ],
            );
        }

        // 4. Check Quotation ACCEPTED
        $acceptedQuotation = $lead->quotations()
            ->where('status', 'ACCEPTED')
            ->first();

        if ($acceptedQuotation === null) {
            throw new PipelineGuardException(
                message: 'Tahap Deal memerlukan Quotation dengan status ACCEPTED.',
                guardFailureCode: 'DEAL_QUOTATION_NOT_ACCEPTED',
                deepLinkTab: 'quotation',
            );
        }

        // 5. Check PM Delivery Readiness Check passed
        $readiness = $lead->latestReadinessCheck;
        if ($readiness === null || !$readiness->isPassed()) {
            throw new PipelineGuardException(
                message: 'Tahap Deal memerlukan Delivery Readiness Check PM yang sudah lolos (status READY).',
                guardFailureCode: 'DEAL_READINESS_CHECK_REQUIRED',
                deepLinkTab: 'handover',
            );
        }

        // 6. Check mandatory Deal documents
        $hasMandatoryDoc = $lead->documents()
            ->where(function ($q) {
                $q->where('is_mandatory_for_deal', true)
                    ->orWhereIn('category', ['SIGNED_MOU', 'SIGNED_QUOTATION', 'PO_SPK', 'DEAL_DOCUMENT']);
            })
            ->exists();

        if (!$hasMandatoryDoc) {
            throw new PipelineGuardException(
                message: 'Tahap Deal memerlukan kelengkapan dokumen wajib stage Deal (MoU/SPK/PO bertanda tangan).',
                guardFailureCode: 'DEAL_MANDATORY_DOCUMENTS_MISSING',
                deepLinkTab: 'files',
            );
        }
    }

    /**
     * Entry Guard: REJECT (BR-CRM-03, BR-CRM-14, AC-CRM-10, AC-CRM-13)
     * - Kategori alasan wajib
     * - Catatan evaluasi minimal 10 karakter
     */
    protected function guardEnterReject(Lead $lead, array $options): void
    {
        $reasonCode = $options['reject_reason_code'] ?? $lead->reject_reason_code;
        $reasonId = $options['reject_reason_id'] ?? $lead->reject_reason_id;
        $notes = trim((string) ($options['reject_notes'] ?? $options['reason'] ?? $lead->reject_notes ?? ''));

        if (empty($reasonCode) && empty($reasonId)) {
            throw new PipelineGuardException(
                message: 'Tahap Reject memerlukan pemilihan kategori alasan penolakan (Reject Reason).',
                guardFailureCode: 'REJECT_REASON_REQUIRED',
                deepLinkTab: 'overview',
            );
        }

        if (mb_strlen($notes) < 10) {
            throw new PipelineGuardException(
                message: 'Catatan evaluasi penolakan (Reject) wajib diisi minimal 10 karakter.',
                guardFailureCode: 'REJECT_NOTES_TOO_SHORT',
                deepLinkTab: 'overview',
            );
        }
    }

    /**
     * Resolve configured probability for a stage (admin-configurable in crm_pipeline_stages, fallback to PRD defaults).
     */
    public function resolveStageProbability(string $stageId): int
    {
        $codes = match ($stageId) {
            'new_lead' => ['NEW_LEAD', 'NEW'],
            'follow_up' => ['FOLLOW_UP'],
            'requirement' => ['REQUIREMENT'],
            'quotation' => ['QUOTATION'],
            'negotiation' => ['NEGOTIATION'],
            'deal' => ['DEAL', 'DEAL_WON'],
            'reject' => ['REJECT', 'LOST'],
            default => [strtoupper($stageId)],
        };

        $configured = DB::table('crm_pipeline_stages')
            ->whereIn('code', $codes)
            ->value('win_probability_pct');

        if ($configured !== null) {
            return (int) $configured;
        }

        return self::DEFAULT_PROBABILITIES[$stageId] ?? 10;
    }
}
