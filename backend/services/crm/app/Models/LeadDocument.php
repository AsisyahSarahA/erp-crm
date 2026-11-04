<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDocument extends Model
{
    use SoftDeletes;

    protected $table = 'crm_lead_documents';

    protected $fillable = [
        'lead_id',
        'category',
        'title',
        'file_path',
        'file_hash',
        'stage_id',
        'is_mandatory_for_deal',
        'uploaded_by',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'is_mandatory_for_deal' => 'boolean',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

