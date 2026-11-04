<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadWatcher extends Model
{
    protected $table = 'crm_lead_watchers';

    protected $fillable = [
        'lead_id',
        'user_id',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

