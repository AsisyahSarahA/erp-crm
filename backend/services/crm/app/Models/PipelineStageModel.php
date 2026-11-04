<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStageModel extends Model
{
    protected $table = 'crm_pipeline_stages';

    protected $fillable = [
        'code',
        'name',
        'sequence',
        'win_probability_pct',
        'sla_hours',
        'is_active',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'win_probability_pct' => 'integer',
        'sla_hours' => 'integer',
        'is_active' => 'boolean',
    ];
}

