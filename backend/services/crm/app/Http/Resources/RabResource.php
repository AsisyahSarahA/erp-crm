<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RabItem;
use App\Models\RabVersion;
use App\Support\UserContext;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RabVersion
 */
class RabResource extends JsonResource
{
    public static function formatCalculationArray(array $calc, ?Request $request = null): array
    {
        $canViewMargin = UserContext::canViewMargin($request);

        if (!$canViewMargin) {
            unset(
                $calc['hpp_internal'],
                $calc['hpp_internal_formatted'],
                $calc['direct_costs'],
                $calc['direct_costs_total'],
                $calc['gross_margin_nominal'],
                $calc['gross_margin_nominal_formatted'],
                $calc['margin_pct'],
                $calc['gross_margin_percent'],
                $calc['margin_status'],
                $calc['margin_indicator_color'],
                $calc['cost_rate']
            );

            if (isset($calc['items']) && is_array($calc['items'])) {
                $calc['items'] = array_map(function (array $item): array {
                    unset(
                        $item['cost_rate'],
                        $item['daily_rate_cost'],
                        $item['subtotal_cost'],
                        $item['hpp'],
                        $item['hpp_formatted']
                    );

                    return $item;
                }, $calc['items']);
            }
        }

        return $calc;
    }

    public function toArray(Request $request): array
    {
        $canViewMargin = UserContext::canViewMargin($request);
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        $formattedItems = $items->map(function (RabItem $item) use ($canViewMargin): array {
            $row = [
                'id' => $item->id,
                'rab_version_id' => $item->rab_version_id,
                'lead_id' => $item->lead_id,
                'module_catalog_id' => $item->module_catalog_id,
                'sort_order' => (int) $item->sort_order,
                'category' => $item->category,
                'layer' => $item->layer,
                'name' => $item->name,
                'description' => $item->description,
                'role' => $item->role,
                'role_code' => $item->role_code,
                'mandays' => (float) $item->mandays,
                'qty' => (float) $item->qty,
                'unit' => $item->unit,
                'daily_rate_sell' => (float) $item->daily_rate_sell,
                'unit_price' => (float) $item->unit_price,
                'subtotal_sell' => (float) $item->subtotal_sell,
                'subtotal' => (float) $item->subtotal,
                'complexity' => $item->complexity,
                'confidence' => (int) $item->confidence,
                'origin' => $item->origin,
                'source_badge' => $item->origin, // AI | MANUAL | TEMPLATE
                'ai_confidence' => $item->ai_confidence !== null ? (float) $item->ai_confidence : null,
                'is_optional' => (bool) $item->is_optional,
                'is_recurring' => (bool) $item->is_recurring,
                'is_bonus' => (bool) $item->is_bonus,
                'recurring_period' => $item->recurring_period,
                'notes' => $item->notes,
            ];

            if ($canViewMargin) {
                $row['cost_rate'] = (float) $item->cost_rate;
                $row['daily_rate_cost'] = (float) $item->daily_rate_cost;
                $row['subtotal_cost'] = (float) $item->subtotal_cost;
                $row['hpp'] = (float) $item->hpp;
            }

            return $row;
        })->values()->all();

        // Group items by category for collapsible spreadsheet groups (FR-CRM-06)
        $groupedByCategory = [];
        foreach ($formattedItems as $fi) {
            $cat = $fi['category'] ?: 'Lainnya';
            if (!isset($groupedByCategory[$cat])) {
                $groupedByCategory[$cat] = [
                    'category' => $cat,
                    'items_count' => 0,
                    'total_qty' => 0.0,
                    'category_subtotal' => 0.0,
                    'items' => [],
                ];
            }
            $groupedByCategory[$cat]['items_count']++;
            $groupedByCategory[$cat]['total_qty'] = round($groupedByCategory[$cat]['total_qty'] + (float) $fi['qty'], 2);
            if (!$fi['is_optional'] && !$fi['is_bonus']) {
                $groupedByCategory[$cat]['category_subtotal'] = round(
                    $groupedByCategory[$cat]['category_subtotal'] + (float) $fi['subtotal'],
                    2
                );
            }
            $groupedByCategory[$cat]['items'][] = $fi;
        }

        $data = [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'requirement_id' => $this->requirement_id,
            'ai_run_id' => $this->ai_run_id,
            'version' => (int) $this->version,
            'source' => $this->source,
            'status' => $this->status,
            'view_mode' => $canViewMargin ? 'INTERNAL' : 'CLIENT',
            'total_mandays' => (float) $this->total_mandays,
            'duration_weeks' => (int) $this->duration_weeks,
            'team_size_by_role' => $this->team_size_by_role ?? [],
            'duration_derivation' => $this->duration_derivation ?? [],
            'subtotal' => (float) $this->subtotal,
            'contingency_pct' => (float) $this->contingency_pct,
            'contingency_reason' => $this->contingency_reason,
            'contingency_amount' => (float) $this->contingency_amount,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'discount_pct' => (float) $this->discount_pct,
            'discount_amount' => (float) $this->discount_amount,
            'is_pkp' => (bool) ($this->is_pkp ?? true),
            'dpp' => (float) $this->dpp,
            'tax_rate' => (float) $this->tax_rate,
            'vat_rate' => (float) $this->vat_rate,
            'tax' => (float) $this->tax,
            'vat_amount' => (float) $this->vat_amount,
            'total' => (float) $this->total,
            'total_terbilang' => $this->total_terbilang,
            'wbs_completed' => (bool) $this->wbs_completed,
            'assumptions' => $this->assumptions ?? [],
            'risks' => $this->risks ?? [],
            'out_of_scope' => $this->out_of_scope ?? [],
            'clarifying_questions' => $this->clarifying_questions ?? [],
            'tech_stack' => $this->tech_stack ?? [],
            'warnings' => $this->warnings ?? [],
            'requires_approval' => (bool) $this->requires_approval,
            'approval_tier' => $this->approval_tier ?? 'NONE',
            'approval_reasons' => $this->approval_reasons ?? [],
            'approval_status' => $this->approval_status ?? 'NOT_REQUIRED',
            'core_approval_id' => $this->core_approval_id,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'items' => $formattedItems,
            'grouped_categories' => array_values($groupedByCategory),
        ];

        if ($canViewMargin) {
            $data['hpp_internal'] = (float) $this->hpp_internal;
            $data['direct_costs'] = $this->direct_costs ?? [];
            $data['direct_costs_total'] = (float) ($this->direct_costs_total ?? 0);
            $data['gross_margin_nominal'] = (float) ($this->gross_margin_nominal ?? ($this->dpp - $this->hpp_internal));
            $data['margin_pct'] = (float) $this->margin_pct;
            $data['gross_margin_percent'] = (float) $this->margin_pct;
            $data['margin_status'] = $this->margin_status;
            $data['margin_indicator_color'] = $this->margin_status;
        }

        return $data;
    }
}

