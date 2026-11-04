<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmApproval extends Model
{
    protected $table = 'crm_approvals';

    protected $fillable = [
        'lead_id',
        'rab_version_id',
        'quotation_id',
        'core_approval_id',
        'approval_type',
        'required_tier',
        'required_role',
        'status',
        'discount_pct',
        'margin_pct',
        'total_amount',
        'reasons',
        'requested_by',
        'decided_by',
        'decision_notes',
        'decided_at',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'rab_version_id' => 'integer',
        'quotation_id' => 'integer',
        'core_approval_id' => 'integer',
        'discount_pct' => 'float',
        'margin_pct' => 'float',
        'total_amount' => 'float',
        'reasons' => 'array',
        'requested_by' => 'integer',
        'decided_by' => 'integer',
        'decided_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function rabVersion(): BelongsTo
    {
        return $this->belongsTo(RabVersion::class, 'rab_version_id');
    }
}

