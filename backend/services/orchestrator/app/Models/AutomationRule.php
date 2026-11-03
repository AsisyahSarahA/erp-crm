<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationRule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_event',
        'trigger_config',
        'conditions',
        'actions',
        'is_active',
        'priority',
        'last_run_at',
        'execution_count',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'execution_count' => 'integer',
        'last_run_at' => 'datetime',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(AutomationRun::class, 'rule_id')->latest('executed_at');
    }
}

