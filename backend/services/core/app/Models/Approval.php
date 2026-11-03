<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approval extends Model
{
    use HasUlids;

    protected $fillable = [
        'reference_type',
        'reference_id',
        'approval_type',
        'requester_id',
        'current_stage',
        'total_stages',
        'status',
        'sla_hours',
        'deadline_at',
        'notes',
        'payload',
    ];

    protected $casts = [
        'current_stage' => 'integer',
        'total_stages' => 'integer',
        'sla_hours' => 'integer',
        'deadline_at' => 'datetime',
        'payload' => 'array',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('stage_order');
    }

    public function currentStep(): ?ApprovalStep
    {
        return $this->steps()->where('stage_order', $this->current_stage)->first();
    }
}

