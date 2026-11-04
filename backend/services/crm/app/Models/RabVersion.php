<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RabVersion extends Model
{
    use SoftDeletes;

    protected $table = 'crm_rab_versions';

    protected $fillable = [
        'lead_id',
        'requirement_id',
        'ai_run_id',
        'version',
        'source',
        'status',
        'total_mandays',
        'duration_weeks',
        'subtotal',
        'contingency_pct',
        'contingency_reason',
        'contingency_amount',
        'discount_type',
        'discount_value',
        'discount_pct',
        'discount_amount',
        'is_pkp',
        'dpp',
        'tax_rate',
        'vat_rate',
        'tax',
        'vat_amount',
        'total',
        'total_terbilang',
        'hpp_internal',
        'direct_costs',
        'direct_costs_total',
        'gross_margin_nominal',
        'margin_pct',
        'margin_status',
        'team_size_by_role',
        'duration_derivation',
        'wbs_completed',
        'assumptions',
        'risks',
        'out_of_scope',
        'clarifying_questions',
        'tech_stack',
        'warnings',
        'requires_approval',
        'approval_tier',
        'approval_reasons',
        'approval_status',
        'core_approval_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'requirement_id' => 'integer',
        'ai_run_id' => 'integer',
        'version' => 'integer',
        'total_mandays' => 'float',
        'duration_weeks' => 'integer',
        'subtotal' => 'float',
        'contingency_pct' => 'float',
        'contingency_amount' => 'float',
        'discount_value' => 'float',
        'discount_pct' => 'float',
        'discount_amount' => 'float',
        'is_pkp' => 'boolean',
        'dpp' => 'float',
        'tax_rate' => 'float',
        'vat_rate' => 'float',
        'tax' => 'float',
        'vat_amount' => 'float',
        'total' => 'float',
        'hpp_internal' => 'float',
        'direct_costs' => 'array',
        'direct_costs_total' => 'float',
        'gross_margin_nominal' => 'float',
        'margin_pct' => 'float',
        'team_size_by_role' => 'array',
        'duration_derivation' => 'array',
        'wbs_completed' => 'boolean',
        'assumptions' => 'array',
        'risks' => 'array',
        'out_of_scope' => 'array',
        'clarifying_questions' => 'array',
        'tech_stack' => 'array',
        'warnings' => 'array',
        'requires_approval' => 'boolean',
        'approval_reasons' => 'array',
        'core_approval_id' => 'integer',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updated(function (self $rab) {
            if ($rab->wasChanged(['discount_pct', 'discount_amount', 'discount_value'])) {
                CrmImmutableAuditLog::record(
                    leadId: (int) $rab->lead_id,
                    entityType: 'rab_version',
                    entityId: (int) $rab->id,
                    fieldName: 'discount',
                    oldValue: [
                        'discount_pct' => $rab->getOriginal('discount_pct'),
                        'discount_amount' => $rab->getOriginal('discount_amount'),
                    ],
                    newValue: [
                        'discount_pct' => $rab->discount_pct,
                        'discount_amount' => $rab->discount_amount,
                    ],
                    actorId: $rab->created_by,
                );
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(RequirementVersion::class, 'requirement_id');
    }

    public function aiRun(): BelongsTo
    {
        return $this->belongsTo(CrmAiRun::class, 'ai_run_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RabItem::class, 'rab_version_id')->orderBy('sort_order', 'asc');
    }
}
