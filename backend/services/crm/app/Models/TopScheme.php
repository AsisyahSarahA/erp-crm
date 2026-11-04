<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopScheme extends Model
{
    protected $table = 'crm_top_schemes';

    protected $fillable = [
        'lead_id',
        'quotation_id',
        'rab_version_id',
        'preset_code',
        'preset_name',
        'reference_dpp',
        'reference_total',
        'vat_rate',
        'pph23_rate',
        'is_pkp',
        'is_pph23_withholder',
        'total_percent',
        'total_percentage',
        'total_dpp',
        'total_ppn',
        'total_pph23',
        'total_amount',
        'total_net_cash',
        'total_terbilang',
        'is_balanced',
        'remaining_percent',
        'remaining_amount_idr',
        'balance_warning',
        'created_by',
        'locked_by_finance_at',
        'locked_by_finance_by',
        'finance_lock_notes',
        'unlock_approval_status',
        'pending_change_payload',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'quotation_id' => 'integer',
        'rab_version_id' => 'integer',
        'reference_dpp' => 'float',
        'reference_total' => 'float',
        'vat_rate' => 'float',
        'pph23_rate' => 'float',
        'is_pkp' => 'boolean',
        'is_pph23_withholder' => 'boolean',
        'total_percent' => 'float',
        'total_percentage' => 'float',
        'total_dpp' => 'float',
        'total_ppn' => 'float',
        'total_pph23' => 'float',
        'total_amount' => 'float',
        'total_net_cash' => 'float',
        'is_balanced' => 'boolean',
        'remaining_percent' => 'float',
        'remaining_amount_idr' => 'float',
        'locked_by_finance_at' => 'datetime',
        'locked_by_finance_by' => 'integer',
        'pending_change_payload' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $scheme) {
            if ($scheme->total_percentage === null || (float) $scheme->total_percentage === 0.0) {
                $scheme->total_percentage = (float) $scheme->total_percent;
            }
            if ($scheme->total_percent === null || (float) $scheme->total_percent === 0.0) {
                $scheme->total_percent = (float) $scheme->total_percentage;
            }
            $scheme->is_balanced = abs(((float) $scheme->total_percent) - 100.0) < 0.001;
        });

        static::updated(function (self $scheme) {
            if ($scheme->wasChanged(['preset_code', 'total_percent', 'total_amount', 'is_balanced', 'locked_by_finance_at'])) {
                CrmImmutableAuditLog::record(
                    leadId: (int) $scheme->lead_id,
                    entityType: 'top_scheme',
                    entityId: (int) $scheme->id,
                    fieldName: 'top_scheme',
                    oldValue: [
                        'preset_code' => $scheme->getOriginal('preset_code'),
                        'total_percent' => (float) $scheme->getOriginal('total_percent'),
                        'total_amount' => (float) $scheme->getOriginal('total_amount'),
                        'is_balanced' => (bool) $scheme->getOriginal('is_balanced'),
                        'locked_by_finance_at' => $scheme->getOriginal('locked_by_finance_at'),
                    ],
                    newValue: [
                        'preset_code' => $scheme->preset_code,
                        'total_percent' => (float) $scheme->total_percent,
                        'total_amount' => (float) $scheme->total_amount,
                        'is_balanced' => (bool) $scheme->is_balanced,
                        'locked_by_finance_at' => $scheme->locked_by_finance_at?->toIso8601String(),
                    ],
                    actorId: $scheme->created_by,
                );
            }
        });
    }

    public function remainingPercent(): float
    {
        return round(100.0 - (float) $this->total_percent, 2);
    }

    public function recalculateFromMilestones(): self
    {
        $milestones = $this->milestones()->get();
        $this->total_percent = round((float) $milestones->sum('percent'), 2);
        $this->total_percentage = $this->total_percent;
        $this->total_amount = round((float) $milestones->sum('amount'), 2);
        $this->is_balanced = abs($this->total_percent - 100.0) < 0.001;
        $this->save();

        return $this;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function rabVersion(): BelongsTo
    {
        return $this->belongsTo(RabVersion::class, 'rab_version_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(TopMilestone::class, 'top_scheme_id')->orderBy('termin_no', 'asc');
    }
}
