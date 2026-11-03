<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saga extends Model
{
    use HasUlids;

    protected $fillable = [
        'id',
        'saga_name',
        'correlation_id',
        'reference_type',
        'reference_id',
        'status',
        'total_steps',
        'completed_steps',
        'context',
        'readiness_check',
        'kickoff_gate_status',
        'failure_reason',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_steps' => 'integer',
        'completed_steps' => 'integer',
        'context' => 'array',
        'readiness_check' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(SagaStep::class)->orderBy('step_order');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    public function isPartial(): bool
    {
        return $this->status === 'PARTIAL';
    }

    public function isFailed(): bool
    {
        return $this->status === 'FAILED';
    }

    /**
     * Checks whether Readiness Check passes for Kickoff Gate (FR-CRM-23).
     * Requirements: DP Invoice Paid (or explicitly waived), MoU Signed, Scope Locked.
     */
    public function checkKickoffReadiness(): bool
    {
        $checks = $this->readiness_check ?? [];
        $dpSatisfied = ($checks['dp_paid'] ?? false) || ($checks['dp_waived'] ?? false);
        $contractSigned = $checks['contract_signed'] ?? false;
        $scopeLocked = $checks['scope_locked'] ?? false;

        return $dpSatisfied && $contractSigned && $scopeLocked;
    }
}

