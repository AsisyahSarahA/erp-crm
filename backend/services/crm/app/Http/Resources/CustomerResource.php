<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Policies\CustomerPolicy;
use App\Support\UserContext;
use Astacode\Kernel\Security\FieldMasker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userContext = UserContext::resolve($request);
        $policy = new CustomerPolicy();
        $canViewUnmasked = $policy->canViewUnmaskedNpwp($userContext);

        $rawNpwp = $this->npwp;
        $maskedNpwp = FieldMasker::npwp($rawNpwp);

        return [
            'id' => $this->id,
            'code' => $this->code ?? $this->customer_code,
            'customer_code' => $this->customer_code ?? $this->code,
            'legal_name' => $this->legal_name,
            'brand_name' => $this->brand_name,
            'type' => $this->type ?? strtolower((string) $this->customer_type),
            'customer_type' => $this->customer_type ?? strtoupper((string) $this->type),
            'industry' => $this->industry,
            'size' => $this->size,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'npwp' => $canViewUnmasked ? $rawNpwp : $maskedNpwp,
            'npwp_masked' => $maskedNpwp,
            'npwp_is_masked' => !$canViewUnmasked && $rawNpwp !== null,
            'nib' => $this->nib,
            'is_pkp' => (bool) $this->is_pkp,
            'is_pph23_withholder' => (bool) $this->is_pph23_withholder,
            'npwp_verified_by' => $this->npwp_verified_by,
            'npwp_verified_at' => $this->npwp_verified_at?->toIso8601String(),
            'is_npwp_verified' => $this->npwp_verified_at !== null,
            'status' => strtolower((string) $this->status),
            'first_source_id' => $this->first_source_id,
            'latest_source_id' => $this->latest_source_id,
            'first_source' => $this->whenLoaded('firstSource', fn () => [
                'id' => $this->firstSource->id,
                'code' => $this->firstSource->code,
                'name' => $this->firstSource->name,
                'channel_group' => $this->firstSource->channel_group,
            ]),
            'latest_source' => $this->whenLoaded('latestSource', fn () => [
                'id' => $this->latestSource->id,
                'code' => $this->latestSource->code,
                'name' => $this->latestSource->name,
                'channel_group' => $this->latestSource->channel_group,
            ]),
            'owner_id' => $this->owner_id,
            'team_id' => $this->team_id,
            'company_id' => $this->company_id ?? 1,
            'health_score' => (int) ($this->health_score ?? 100),
            'health_breakdown' => $this->health_breakdown,
            'billing_address' => $this->billing_address,
            'office_address' => $this->office_address,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_anonymized' => (bool) $this->is_anonymized,
            'anonymized_at' => $this->anonymized_at?->toIso8601String(),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'primary_contact' => new ContactResource($this->whenLoaded('primaryContact')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

