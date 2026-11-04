<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lead;
use App\Models\StageChecklist;
use Illuminate\Support\Facades\Http;

class NextBestActionService
{
    /**
     * Generate rule-based + AI-assisted Next Best Actions for a Lead.
     *
     * @param Lead $lead
     * @param bool $includeAi
     * @return array<string, mixed>
     */
    public function generate(Lead $lead, bool $includeAi = true): array
    {
        $stageId = Lead::normalizeStageId($lead->stage_id ?: $lead->stage);
        $actions = [];

        // 1. Stale & Next Step Rules (BR-CRM-08)
        if ($lead->is_stale) {
            $actions[] = [
                'code' => 'RESOLVE_STALE_LEAD',
                'priority' => 'CRITICAL',
                'title' => 'Lead Stale — Segera Lakukan Follow Up',
                'description' => "Lead berada di tahap {$lead->stage} selama {$lead->days_in_stage} hari melewati SLA stage. Hubungi PIC klien sekarang.",
                'deep_link_tab' => 'activity',
                'is_guard_blocker' => false,
                'source' => 'rule_engine',
            ];
        }

        if (!$lead->has_next_step && !in_array($stageId, ['deal', 'reject'], true)) {
            $actions[] = [
                'code' => 'SCHEDULE_NEXT_STEP',
                'priority' => 'HIGH',
                'title' => 'Jadwalkan Next Step Activity',
                'description' => 'Setiap lead aktif wajib memiliki minimal 1 aktivitas tindak lanjut terjadwal (BR-CRM-08).',
                'deep_link_tab' => 'activity',
                'is_guard_blocker' => $stageId === 'new_lead',
                'source' => 'rule_engine',
            ];
        }

        // 2. Stage-Specific Guard Readiness & Next Step Rules
        match ($stageId) {
            'new_lead' => $this->appendNewLeadActions($lead, $actions),
            'follow_up' => $this->appendFollowUpActions($lead, $actions),
            'requirement' => $this->appendRequirementActions($lead, $actions),
            'quotation' => $this->appendQuotationActions($lead, $actions),
            'negotiation' => $this->appendNegotiationActions($lead, $actions),
            'reject' => $this->appendRejectActions($lead, $actions),
            default => null,
        };

        // 3. Stage Mandatory Checklist Uncompleted Items
        $uncompletedChecklists = StageChecklist::query()
            ->where('stage_id', $stageId)
            ->where('is_active', true)
            ->where('is_mandatory', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $completedIds = $lead->checklistProgress()
            ->where('is_completed', true)
            ->pluck('checklist_id')
            ->all();

        foreach ($uncompletedChecklists as $checklist) {
            if (!in_array($checklist->id, $completedIds, true)) {
                $actions[] = [
                    'code' => 'CHECKLIST_' . strtoupper($checklist->code),
                    'priority' => 'MEDIUM',
                    'title' => 'Checklist Wajib: ' . $checklist->label,
                    'description' => $checklist->description ?? "Selesaikan aktivitas checklist wajib pada tahap {$lead->stage}.",
                    'deep_link_tab' => 'overview',
                    'is_guard_blocker' => false,
                    'source' => 'stage_checklist',
                ];
            }
        }

        // 4. AI Service Integration (with deterministic fallback)
        $aiInsight = $this->fetchAiInsight($lead, $stageId, $actions, $includeAi);

        return [
            'lead_id' => $lead->id,
            'lead_code' => $lead->code,
            'current_stage' => $stageId,
            'win_probability' => (int) $lead->win_probability,
            'weighted_value' => (float) $lead->weighted_value,
            'next_actions' => $actions,
            'ai_recommendation' => $aiInsight,
        ];
    }

    private function appendNewLeadActions(Lead $lead, array &$actions): void
    {
        if (empty($lead->owner_id) && empty($lead->sales_pic_id)) {
            $actions[] = [
                'code' => 'ASSIGN_SALES_PIC',
                'priority' => 'CRITICAL',
                'title' => 'Tetapkan Sales PIC',
                'description' => 'Syarat masuk tahap Follow Up memerlukan penetapan Sales PIC.',
                'deep_link_tab' => 'overview',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }

        if (!$lead->activities()->exists()) {
            $actions[] = [
                'code' => 'FIRST_CONTACT_SLA',
                'priority' => 'CRITICAL',
                'title' => 'Lakukan Kontak Awal (SLA <= 30 Menit)',
                'description' => 'Hubungi PIC via WhatsApp/Telepon dalam 30 menit jam kerja untuk memindahkan ke Follow Up.',
                'deep_link_tab' => 'activity',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }
    }

    private function appendFollowUpActions(Lead $lead, array &$actions): void
    {
        $hasDiscovery = $lead->activities()
            ->whereIn('activity_type', ['CALL', 'MEETING', 'DISCOVERY_CALL'])
            ->exists();

        if (!$hasDiscovery) {
            $actions[] = [
                'code' => 'SCHEDULE_DISCOVERY_CALL',
                'priority' => 'HIGH',
                'title' => 'Jadwalkan Discovery Call dengan Klien',
                'description' => 'Tahap Requirement mewajibkan respons klien dan pelaksanaan/jadwal Discovery Call.',
                'deep_link_tab' => 'activity',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }
    }

    private function appendRequirementActions(Lead $lead, array &$actions): void
    {
        $rab = $lead->activeRab ?? $lead->rabVersions()->first();
        if ($rab === null || $rab->items()->count() < 1 || (float) $rab->total <= 0) {
            $actions[] = [
                'code' => 'BUILD_RAB_ESTIMATION',
                'priority' => 'CRITICAL',
                'title' => 'Susun Estimasi RAB & WBS (Minimal 1 Modul)',
                'description' => 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).',
                'deep_link_tab' => 'rab',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        } elseif (!$rab->wbs_completed) {
            $actions[] = [
                'code' => 'COMPLETE_RAB_WBS',
                'priority' => 'HIGH',
                'title' => 'Finalisasi Struktur WBS pada RAB',
                'description' => 'Pastikan WBS modul pada RAB telah lengkap sebelum membuat Quotation.',
                'deep_link_tab' => 'rab',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }
    }

    private function appendQuotationActions(Lead $lead, array &$actions): void
    {
        $hasSent = $lead->quotations()->whereIn('status', ['SENT', 'VIEWED', 'NEGOTIATION', 'ACCEPTED'])->exists();
        if (!$hasSent) {
            $actions[] = [
                'code' => 'SEND_QUOTATION_AND_MOU_DRAFT',
                'priority' => 'HIGH',
                'title' => 'Kirim Quotation Resmi & Draf MoU ke Klien',
                'description' => 'Tahap Negotiation memerlukan Quotation dan draf MoU berstatus SENT.',
                'deep_link_tab' => 'quotation',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }
    }

    private function appendNegotiationActions(Lead $lead, array &$actions): void
    {
        $top = $lead->activeTopScheme ?? $lead->topSchemes()->first();
        if ($top === null || !$top->is_balanced) {
            $rem = $top ? $top->remainingPercent() : 100.0;
            $actions[] = [
                'code' => 'BALANCE_TOP_100',
                'priority' => 'CRITICAL',
                'title' => 'Seimbangkan Termin Pembayaran (TOP) menjadi 100%',
                'description' => "Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: {$rem}%.",
                'deep_link_tab' => 'top',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }

        $hasAccepted = $lead->quotations()->where('status', 'ACCEPTED')->exists();
        if (!$hasAccepted) {
            $actions[] = [
                'code' => 'OBTAIN_QUOTATION_ACCEPTANCE',
                'priority' => 'HIGH',
                'title' => 'Dapatkan Persetujuan Quotation (ACCEPTED)',
                'description' => 'Konfirmasi persetujuan Quotation dari Decision Maker klien sebelum Deal Won.',
                'deep_link_tab' => 'quotation',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }

        $readiness = $lead->latestReadinessCheck;
        if ($readiness === null || !$readiness->isPassed()) {
            $actions[] = [
                'code' => 'COMPLETE_PM_READINESS_CHECK',
                'priority' => 'HIGH',
                'title' => 'Lakukan Delivery Readiness Check bersama PM',
                'description' => 'Pastikan kapasitas tim delivery cukup (READY) sebelum komitmen Deal.',
                'deep_link_tab' => 'handover',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }

        $hasMandatoryDoc = $lead->documents()
            ->where(function ($q) {
                $q->where('is_mandatory_for_deal', true)
                    ->orWhereIn('category', ['SIGNED_MOU', 'SIGNED_QUOTATION', 'PO_SPK', 'DEAL_DOCUMENT']);
            })
            ->exists();

        if (!$hasMandatoryDoc) {
            $actions[] = [
                'code' => 'UPLOAD_MANDATORY_DEAL_DOCS',
                'priority' => 'HIGH',
                'title' => 'Unggah Dokumen Wajib Deal (MoU/SPK/PO)',
                'description' => 'Unggah dokumen MoU/SPK/PO bertanda tangan pada tab Files.',
                'deep_link_tab' => 'files',
                'is_guard_blocker' => true,
                'source' => 'pipeline_guard',
            ];
        }
    }

    private function appendRejectActions(Lead $lead, array &$actions): void
    {
        $actions[] = [
            'code' => 'EVALUATE_REOPEN_OPPORTUNITY',
            'priority' => 'LOW',
            'title' => 'Evaluasi Peluang Reopen ke Follow Up',
            'description' => 'Jika anggaran/kebutuhan klien kembali terbuka, Sales Manager dapat melakukan Reopen.',
            'deep_link_tab' => 'overview',
            'is_guard_blocker' => false,
            'source' => 'rule_engine',
        ];
    }

    private function fetchAiInsight(Lead $lead, string $stageId, array $actions, bool $includeAi): array
    {
        $topActionTitle = $actions[0]['title'] ?? 'Pertahankan komunikasi rutin dengan stakeholder utama.';
        $fallback = [
            'provider' => 'rule_fallback',
            'summary' => "Lead {$lead->code} ({$lead->title}) berada pada tahap " . strtoupper($stageId) . " dengan skor {$lead->score}/100. Fokus utama: {$topActionTitle}",
            'suggested_channel' => 'WHATSAPP',
            'talking_points' => [
                "Konfirmasi prioritas kebutuhan bisnis untuk proyek {$lead->project_type}.",
                "Selesaikan syarat transisi stage berikutnya ({$topActionTitle}).",
            ],
        ];

        if (!$includeAi) {
            return $fallback;
        }

        $aiBaseUrl = rtrim((string) env('AI_SERVICE_URL', 'http://127.0.0.1:8007'), '/');
        try {
            $response = Http::timeout(2)->post("{$aiBaseUrl}/api/v1/ai/lead-summary", [
                'lead_code' => $lead->code,
                'title' => $lead->title,
                'stage' => $stageId,
                'est_value' => $lead->est_value,
                'score' => $lead->score,
                'pending_actions' => array_column($actions, 'title'),
            ]);

            if ($response->successful() && is_array($response->json('data'))) {
                return array_merge($fallback, $response->json('data'), ['provider' => 'ai_gateway']);
            }
        } catch (\Throwable) {
            // Graceful fallback when AI Gateway is unreachable in local/test environment
        }

        return $fallback;
    }
}

