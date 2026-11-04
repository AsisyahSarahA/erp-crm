<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStageHistory extends Model
{
    protected $table = 'crm_lead_stage_histories';

    protected $fillable = [
        'lead_id',
        'from_stage',
        'to_stage',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

