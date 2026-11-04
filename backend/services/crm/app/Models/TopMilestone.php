<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopMilestone extends Model
{
    protected $table = 'crm_top_milestones';

    protected $fillable = [
        'top_scheme_id',
        'lead_id',
        'termin_no',
        'term_number',
        'name',
        'milestone_name',
        'trigger_event',
        'trigger_type',
        'percent',
        'percentage',
        'dpp_amount',
        'ppn_amount',
        'pph23_amount',
        'amount',
        'amount_idr',
        'net_cash_idr',
        'terbilang_text',
        'description',
        'due_days',
        'due_days_offset',
        'due_date',
        'project_milestone_ref',
        'is_retention',
    ];

    protected $casts = [
        'top_scheme_id' => 'integer',
        'lead_id' => 'integer',
        'termin_no' => 'integer',
        'term_number' => 'integer',
        'percent' => 'float',
        'percentage' => 'float',
        'dpp_amount' => 'float',
        'ppn_amount' => 'float',
        'pph23_amount' => 'float',
        'amount' => 'float',
        'amount_idr' => 'float',
        'net_cash_idr' => 'float',
        'due_days' => 'integer',
        'due_days_offset' => 'integer',
        'due_date' => 'date',
        'is_retention' => 'boolean',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(TopScheme::class, 'top_scheme_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
