<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'role' => $this->role ?? 'decision_maker',
            'title' => $this->title ?? $this->position,
            'position' => $this->position ?? $this->title,
            'email' => $this->email,
            'phone_wa' => $this->phone_wa ?? $this->phone,
            'phone' => $this->phone ?? $this->phone_wa,
            'is_primary' => (bool) $this->is_primary,
            'consent_comm' => (bool) $this->consent_comm,
            'consent_at' => $this->consent_at?->toIso8601String(),
            'preferred_channel' => $this->preferred_channel ?? 'whatsapp',
            'department' => $this->department,
            'notes' => $this->notes,
            'is_anonymized' => (bool) $this->is_anonymized,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

