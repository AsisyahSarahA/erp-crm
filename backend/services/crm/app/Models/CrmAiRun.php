<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAiRun extends Model
{
    protected $table = 'crm_ai_runs';

    protected $fillable = [
        'lead_id',
        'requirement_id',
        'external_run_id',
        'module',
        'purpose',
        'provider',
        'model',
        'prompt_ref',
        'input_hash',
        'output',
        'tokens_in',
        'tokens_out',
        'cost',
        'latency_ms',
        'status',
        'human_decision',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'requirement_id' => 'integer',
        'output' => 'array',
        'tokens_in' => 'integer',
        'tokens_out' => 'integer',
        'cost' => 'float',
        'latency_ms' => 'integer',
        'decided_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(RequirementVersion::class, 'requirement_id');
    }
}

