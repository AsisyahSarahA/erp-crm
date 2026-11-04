<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $table = 'crm_contracts';

    protected $fillable = [
        'customer_id',
        'lead_id',
        'project_id',
        'contract_number',
        'type',
        'title',
        'start_date',
        'end_date',
        'value',
        'renewal_alert_days',
        'status',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'lead_id' => 'integer',
        'project_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'float',
        'renewal_alert_days' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

