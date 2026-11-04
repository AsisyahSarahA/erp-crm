<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAttribution extends Model
{
    protected $table = 'crm_customer_attributions';

    protected $fillable = [
        'customer_id',
        'lead_id',
        'source_id',
        'touch_type',
        'sub_source',
        'campaign_name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'referrer_url',
        'touched_at',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'lead_id' => 'integer',
        'source_id' => 'integer',
        'touched_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $attribution) {
            if ($attribution->touched_at === null) {
                $attribution->touched_at = now();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }
}

