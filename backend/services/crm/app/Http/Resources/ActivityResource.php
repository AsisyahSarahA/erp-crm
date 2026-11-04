<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $durationMinutes = null;
        if ($this->start_at && $this->end_at) {
            $durationMinutes = max(0, (int) $this->start_at->diffInMinutes($this->end_at));
        }

        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'lead' => $this->whenLoaded('lead', fn () => [
                'id' => $this->lead->id,
                'code' => $this->lead->code ?? $this->lead->lead_code,
                'title' => $this->lead->title,
                'stage' => $this->lead->stage,
                'stage_id' => $this->lead->stage_id,
                'est_value' => (float) $this->lead->est_value,
            ]),
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'legal_name' => $this->customer->legal_name,
                'brand_name' => $this->customer->brand_name,
                'phone' => $this->customer->phone,
                'email' => $this->customer->email,
            ]),
            'type' => strtoupper((string) $this->type),
            'title' => $this->title,
            'notes' => $this->notes,
            'start_at' => $this->start_at?->toIso8601String(),
            'end_at' => $this->end_at?->toIso8601String(),
            'duration_minutes' => $durationMinutes,
            'sla_due_at' => $this->sla_due_at?->toIso8601String(),
            'status' => strtoupper((string) $this->status),
            'outcome' => $this->outcome ? strtoupper((string) $this->outcome) : null,
            'outcome_notes' => $this->outcome_notes,
            'next_step' => $this->next_step,
            'location' => $this->location,
            'link' => $this->link,
            'google_event_id' => $this->google_event_id,
            'meet_link' => $this->meet_link,
            'attendees' => (array) ($this->attendees ?? []),
            'attachments' => (array) ($this->attachments ?? []),
            'recording_url' => $this->recording_url,
            'transcript_text' => $this->transcript_text,
            'ai_summary' => $this->ai_summary,
            'reminder_h1_sent_at' => $this->reminder_h1_sent_at?->toIso8601String(),
            'reminder_1h_sent_at' => $this->reminder_1h_sent_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

