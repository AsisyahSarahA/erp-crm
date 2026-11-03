<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AiRun extends Model
{
    use HasUlids;

    protected $fillable = [
        'id',
        'module',
        'purpose',
        'provider',
        'model',
        'prompt_slug',
        'prompt_version',
        'input_hash',
        'input_payload',
        'output_json',
        'tokens_in',
        'tokens_out',
        'cost',
        'latency_ms',
        'status',
        'human_decision',
        'estimated_mandays',
        'actual_mandays',
        'decided_by',
        'error_message',
    ];

    protected $casts = [
        'prompt_version' => 'integer',
        'input_payload' => 'array',
        'output_json' => 'array',
        'tokens_in' => 'integer',
        'tokens_out' => 'integer',
        'cost' => 'float',
        'latency_ms' => 'integer',
        'estimated_mandays' => 'float',
        'actual_mandays' => 'float',
    ];
}

