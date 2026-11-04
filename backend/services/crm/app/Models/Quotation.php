<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $table = 'crm_quotations';

    protected $fillable = [
        'lead_id',
        'quotation_number',
        'version',
        'status',
        'subtotal',
        'contingency_pct',
        'discount_pct',
        'dpp',
        'vat_rate',
        'vat_amount',
        'total',
        'total_terbilang',
        'hpp_internal',
        'margin_pct',
        'valid_until',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'version' => 'integer',
        'subtotal' => 'float',
        'contingency_pct' => 'float',
        'discount_pct' => 'float',
        'dpp' => 'float',
        'vat_rate' => 'float',
        'vat_amount' => 'float',
        'total' => 'float',
        'hpp_internal' => 'float',
        'margin_pct' => 'float',
        'valid_until' => 'date',
    ];

    protected static function booted(): void
    {
        static::updated(function (self $quotation) {
            if ($quotation->wasChanged('discount_pct')) {
                CrmImmutableAuditLog::record(
                    leadId: (int) $quotation->lead_id,
                    entityType: 'quotation',
                    entityId: (int) $quotation->id,
                    fieldName: 'discount',
                    oldValue: (float) $quotation->getOriginal('discount_pct'),
                    newValue: (float) $quotation->discount_pct,
                );
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function topScheme(): HasOne
    {
        return $this->hasOne(TopScheme::class, 'quotation_id');
    }
}
