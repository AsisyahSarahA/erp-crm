<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadChecklistProgress extends Model
{
    protected $table = 'crm_lead_checklist_progress';

    protected $fillable = [
        'lead_id',
        'checklist_id',
        'is_completed',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'checklist_id' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(StageChecklist::class, 'checklist_id');
    }
}

