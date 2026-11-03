<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rule_id',
        'trigger_type',
        'trigger_payload',
        'matched_condition',
        'actions_executed',
        'status',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'trigger_payload' => 'array',
        'actions_executed' => 'array',
        'matched_condition' => 'boolean',
        'executed_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }
}

