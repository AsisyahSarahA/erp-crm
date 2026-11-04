<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\StageChecklist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stageId = strtolower((string) ($this->stage_id ?? $this->stage));
        $weightedValue = $this->weighted_value !== null
            ? (float) $this->weighted_value
            : round(((float) $this->est_value) * (((int) $this->win_probability) / 100.0), 2);

        return [
            'id' => $this->id,
            'code' => $this->code ?? $this->lead_code,
            'lead_code' => $this->lead_code ?? $this->code,
            'title' => $this->title,
            'project_type' => $this->project_type,
            'priority' => strtoupper((string) $this->priority),
            'stage' => strtoupper((string) $this->stage),
            'stage_id' => $stageId,
            'source' => $this->source,
            'source_id' => $this->source_id,
            'sub_source' => $this->sub_source,
            'utm' => $this->utm,
            'est_value' => (float) $this->est_value,
            'win_probability' => (int) $this->win_probability,
            'weighted_value' => $weightedValue,
            'budget_range' => $this->budget_range,
            'target_closing' => $this->target_closing?->toDateString(),
            'target_golive' => $this->target_golive?->toDateString(),
            'owner_id' => $this->owner_id ?? $this->sales_pic_id,
            'sales_pic_id' => $this->sales_pic_id ?? $this->owner_id,
            'sales_pic_name' => $this->sales_pic_name,
            'team_id' => $this->team_id,
            'score' => (int) $this->score,
            'score_label' => $this->score_label ?? 'WARM',
            'score_breakdown' => $this->score_breakdown,
            'days_in_stage' => (int) $this->days_in_stage,
            'is_stale' => (bool) $this->is_stale,
            'stale_since' => $this->stale_since?->toIso8601String(),
            'has_next_step' => (bool) $this->has_next_step,
            'active_quotation_id' => $this->active_quotation_id,
            'active_rab_version_id' => $this->active_rab_version_id,
            'reject_reason_code' => $this->reject_reason_code,
            'reject_reason_id' => $this->reject_reason_id,
            'reject_notes' => $this->reject_notes,
            'won_at' => $this->won_at?->toIso8601String(),
            'lost_at' => $this->lost_at?->toIso8601String(),
            'reopened_at' => $this->reopened_at?->toIso8601String(),
            'reopened_by' => $this->reopened_by,
            'reopen_reason' => $this->reopen_reason,
            'industry' => $this->industry,
            'services' => $this->services,
            'tags' => $this->tags,
            'consent' => (bool) $this->consent,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'code' => $this->customer->code ?? $this->customer->customer_code,
                'legal_name' => $this->customer->legal_name,
                'brand_name' => $this->customer->brand_name,
                'phone' => $this->customer->phone,
                'email' => $this->customer->email,
                'health_score' => $this->customer->health_score,
            ]),
            'contact_id' => $this->contact_id,
            'contact' => $this->whenLoaded('contact', fn () => [
                'id' => $this->contact->id,
                'name' => $this->contact->name,
                'role' => $this->contact->role,
                'phone_wa' => $this->contact->phone_wa,
                'email' => $this->contact->email,
            ]),
            'next_activity' => $this->whenLoaded('nextActivity', fn () => [
                'id' => $this->nextActivity->id,
                'type' => $this->nextActivity->type,
                'title' => $this->nextActivity->title,
                'start_at' => $this->nextActivity->start_at?->toIso8601String(),
                'status' => $this->nextActivity->status,
            ]),
            'stage_histories' => $this->whenLoaded('stageHistories', fn () => $this->stageHistories->map(fn ($h) => [
                'id' => $h->id,
                'from_stage' => $h->from_stage,
                'to_stage' => $h->to_stage,
                'changed_by' => $h->changed_by,
                'reason' => $h->reason,
                'is_override' => (bool) $h->is_override,
                'duration_hours_in_prev_stage' => (int) $h->duration_hours_in_prev_stage,
                'changed_at' => $h->changed_at?->toIso8601String(),
            ])),
            'stage_checklists' => $this->whenLoaded('checklistProgress', function () use ($stageId) {
                $checklists = StageChecklist::query()
                    ->where('stage_id', $stageId)
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->get();
                $progressMap = $this->checklistProgress->keyBy('checklist_id');

                return $checklists->map(function (StageChecklist $item) use ($progressMap) {
                    $prog = $progressMap->get($item->id);
                    return [
                        'id' => $item->id,
                        'stage_id' => $item->stage_id,
                        'code' => $item->code,
                        'label' => $item->label,
                        'description' => $item->description,
                        'is_mandatory' => (bool) $item->is_mandatory,
                        'is_completed' => (bool) ($prog?->is_completed ?? false),
                        'completed_by' => $prog?->completed_by,
                        'completed_at' => $prog?->completed_at?->toIso8601String(),
                    ];
                })->values();
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
