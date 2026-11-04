<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StageChecklist extends Model
{
    protected $table = 'crm_stage_checklists';

    protected $fillable = [
        'stage_id',
        'code',
        'label',
        'description',
        'is_mandatory',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function progress(): HasMany
    {
        return $this->hasMany(LeadChecklistProgress::class, 'checklist_id');
    }
}

