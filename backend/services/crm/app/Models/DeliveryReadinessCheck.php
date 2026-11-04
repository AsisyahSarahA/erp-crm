<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReadinessCheck extends Model
{
    protected $table = 'crm_delivery_readiness_checks';

    protected $fillable = [
        'lead_id',
        'status',
        'capacity_available_pct',
        'notes',
        'checked_by',
        'checked_at',
        'waived_by',
        'waiver_reason',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'capacity_available_pct' => 'float',
        'checked_at' => 'datetime',
    ];

    public function isPassed(): bool
    {
        $status = strtoupper((string) $this->status);
        if (in_array($status, ['READY', 'READY_WITH_NOTES', 'PASSED', 'APPROVED'], true)) {
            return true;
        }

        if (!empty($this->waived_by) && !empty($this->waiver_reason)) {
            return true;
        }

        return false;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

