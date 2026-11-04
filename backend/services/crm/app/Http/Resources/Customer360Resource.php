<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Policies\CustomerPolicy;
use App\Support\UserContext;
use Astacode\Kernel\Security\FieldMasker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Customer360Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userContext = UserContext::resolve($request);
        $policy = new CustomerPolicy();
        $canViewUnmasked = $policy->canViewUnmaskedNpwp($userContext);

        $rawNpwp = $this->npwp;
        $maskedNpwp = FieldMasker::npwp($rawNpwp);

        $contacts = $this->contacts ?? collect();
        $leads = $this->leads ?? collect();
        $quotations = $this->quotations ?? collect();
        $contracts = $this->contracts ?? collect();
        $attributions = $this->attributions ?? collect();

        $deals = $leads->filter(fn ($lead) => in_array(strtoupper((string) $lead->stage), ['DEAL_WON', 'DEAL'], true))->values();

        // Compute financial summary from snapshot or derived from won deals & contracts
        $finSnapshot = $this->financial_snapshot ?? [];
        $derivedContractTotal = (float) ($contracts->sum('value') ?: $deals->sum('est_value'));
        $totalContractValue = (float) ($finSnapshot['total_contract_value'] ?? $derivedContractTotal);
        $outstandingAr = (float) ($finSnapshot['outstanding_ar'] ?? 0.0);
        $overdueAr = (float) ($finSnapshot['overdue_ar'] ?? 0.0);
        $dsoDays = (int) ($finSnapshot['dso_days'] ?? 14);
        $collectibilityRatio = (float) ($finSnapshot['collectibility_ratio'] ?? ($overdueAr > 0 && $totalContractValue > 0
            ? round((1 - ($overdueAr / $totalContractValue)) * 100, 2)
            : 100.0));
        $paymentRating = $finSnapshot['payment_rating'] ?? match (true) {
            $collectibilityRatio >= 95 && $dsoDays <= 21 => 'A (Lancar)',
            $collectibilityRatio >= 80 => 'B (Baik)',
            $collectibilityRatio >= 60 => 'C (Dalam Perhatian)',
            default => 'D (Menunggak)',
        };

        // Ensure health breakdown is computed
        $healthBreakdown = $this->health_breakdown ?? $this->calculateHealthScore();
        $extSnapshots = $this->external_snapshots ?? [];

        // Custom fields key-value map
        $customFields = ($this->customFieldValues ?? collect())->map(function ($val) {
            return [
                'definition_id' => $val->definition_id,
                'key' => $val->definition?->field_key,
                'label' => $val->definition?->label,
                'type' => $val->definition?->field_type,
                'value' => $val->value_json ?? $val->value_text,
            ];
        })->values();

        return [
            'id' => $this->id,
            'code' => $this->code ?? $this->customer_code,
            'profile' => [
                'id' => $this->id,
                'code' => $this->code ?? $this->customer_code,
                'legal_name' => $this->legal_name,
                'brand_name' => $this->brand_name,
                'type' => $this->type ?? strtolower((string) $this->customer_type),
                'industry' => $this->industry,
                'size' => $this->size,
                'website' => $this->website,
                'logo_url' => $this->logo_url,
                'phone' => $this->phone,
                'email' => $this->email,
                'office_address' => $this->office_address,
                'billing_address' => $this->billing_address,
                'status' => strtolower((string) $this->status),
                'owner_id' => $this->owner_id,
                'team_id' => $this->team_id,
                'company_id' => $this->company_id ?? 1,
                'is_anonymized' => (bool) $this->is_anonymized,
                'created_at' => $this->created_at?->toIso8601String(),
            ],
            'legal_and_tax' => [
                'legal_name' => $this->legal_name,
                'brand_name' => $this->brand_name,
                'npwp' => $canViewUnmasked ? $rawNpwp : $maskedNpwp,
                'npwp_masked' => $maskedNpwp,
                'npwp_is_masked' => !$canViewUnmasked && $rawNpwp !== null,
                'nib' => $this->nib,
                'is_pkp' => (bool) $this->is_pkp,
                'is_pph23_withholder' => (bool) $this->is_pph23_withholder,
                'size' => $this->size,
                'npwp_verified_by' => $this->npwp_verified_by,
                'npwp_verified_at' => $this->npwp_verified_at?->toIso8601String(),
                'is_npwp_verified' => $this->npwp_verified_at !== null,
            ],
            'attribution' => [
                'first_source' => $this->firstSource ? [
                    'id' => $this->firstSource->id,
                    'code' => $this->firstSource->code,
                    'name' => $this->firstSource->name,
                    'channel_group' => $this->firstSource->channel_group,
                ] : null,
                'latest_source' => $this->latestSource ? [
                    'id' => $this->latestSource->id,
                    'code' => $this->latestSource->code,
                    'name' => $this->latestSource->name,
                    'channel_group' => $this->latestSource->channel_group,
                ] : null,
                'first_touch_at' => $attributions->first()?->touched_at?->toIso8601String() ?? $this->created_at?->toIso8601String(),
                'touches' => $attributions->map(fn ($attr) => [
                    'id' => $attr->id,
                    'touch_type' => $attr->touch_type,
                    'source_id' => $attr->source_id,
                    'source_name' => $attr->source?->name,
                    'sub_source' => $attr->sub_source,
                    'campaign_name' => $attr->campaign_name,
                    'utm' => [
                        'source' => $attr->utm_source,
                        'medium' => $attr->utm_medium,
                        'campaign' => $attr->utm_campaign,
                        'term' => $attr->utm_term,
                        'content' => $attr->utm_content,
                    ],
                    'touched_at' => $attr->touched_at?->toIso8601String(),
                ])->values(),
            ],
            'stakeholder_matrix' => [
                'primary_contact' => $contacts->firstWhere('is_primary', true)
                    ? new ContactResource($contacts->firstWhere('is_primary', true))
                    : ($contacts->first() ? new ContactResource($contacts->first()) : null),
                'by_role' => [
                    'decision_maker' => ContactResource::collection($contacts->where('role', 'decision_maker')->values()),
                    'technical_lead' => ContactResource::collection($contacts->where('role', 'technical_lead')->values()),
                    'finance_lead' => ContactResource::collection($contacts->where('role', 'finance_lead')->values()),
                    'influencer' => ContactResource::collection($contacts->where('role', 'influencer')->values()),
                ],
                'all_contacts' => ContactResource::collection($contacts),
            ],
            'history' => [
                'leads' => $leads->map(fn ($lead) => [
                    'id' => $lead->id,
                    'lead_code' => $lead->lead_code,
                    'title' => $lead->title,
                    'project_type' => $lead->project_type,
                    'stage' => $lead->stage,
                    'est_value' => (float) $lead->est_value,
                    'win_probability' => (int) $lead->win_probability,
                    'sales_pic_id' => $lead->sales_pic_id,
                    'sales_pic_name' => $lead->sales_pic_name,
                    'created_at' => $lead->created_at?->toIso8601String(),
                ])->values(),
                'quotations' => $quotations->map(fn ($quo) => [
                    'id' => $quo->id,
                    'lead_id' => $quo->lead_id,
                    'quotation_number' => $quo->quotation_number,
                    'version' => $quo->version,
                    'status' => $quo->status,
                    'dpp' => (float) $quo->dpp,
                    'total' => (float) $quo->total,
                    'valid_until' => $quo->valid_until?->toDateString(),
                ])->values(),
                'deals' => $deals->map(fn ($deal) => [
                    'id' => $deal->id,
                    'lead_code' => $deal->lead_code,
                    'title' => $deal->title,
                    'value' => (float) $deal->est_value,
                    'stage' => $deal->stage,
                ])->values(),
                'projects' => $extSnapshots['projects'] ?? [],
                'contracts' => $contracts->map(fn ($c) => [
                    'id' => $c->id,
                    'contract_number' => $c->contract_number,
                    'type' => $c->type,
                    'title' => $c->title,
                    'start_date' => $c->start_date?->toDateString(),
                    'end_date' => $c->end_date?->toDateString(),
                    'value' => (float) $c->value,
                    'status' => $c->status,
                ])->values(),
                'tickets' => $extSnapshots['tickets'] ?? [],
                'invoices' => $extSnapshots['invoices'] ?? [],
            ],
            'financials' => [
                'total_contract_value' => $totalContractValue,
                'outstanding_ar' => $outstandingAr,
                'overdue_ar' => $overdueAr,
                'dso_days' => $dsoDays,
                'collectibility_ratio' => $collectibilityRatio,
                'payment_rating' => $paymentRating,
            ],
            'health' => [
                'score' => (int) ($this->health_score ?? $healthBreakdown['total_score'] ?? 100),
                'status_label' => $healthBreakdown['status_label'] ?? 'HEALTHY',
                'breakdown' => $healthBreakdown,
            ],
            'communications' => $extSnapshots['communications'] ?? [],
            'account_plan' => $this->account_plan ?? [
                'account_owner_id' => $this->owner_id,
                'upsell_opportunities' => [],
                'visit_plan' => [],
                'strategic_notes' => null,
            ],
            'custom_fields' => $customFields,
            'merge_history' => ($this->mergeHistories ?? collect())->map(fn ($mh) => [
                'id' => $mh->id,
                'merged_customer_id' => $mh->merged_customer_id,
                'merged_customer_code' => $mh->merged_customer_code,
                'migrated_records_summary' => $mh->migrated_records_summary,
                'reason' => $mh->reason,
                'merged_by' => $mh->merged_by,
                'merged_at' => $mh->merged_at?->toIso8601String(),
            ])->values(),
        ];
    }
}

