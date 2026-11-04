<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RabItem extends Model
{
    protected $table = 'crm_rab_items';

    protected $fillable = [
        'rab_version_id',
        'lead_id',
        'module_catalog_id',
        'category',
        'layer',
        'name',
        'description',
        'role',
        'role_code',
        'mandays',
        'qty',
        'unit',
        'daily_rate_sell',
        'daily_rate_cost',
        'unit_price',
        'subtotal_sell',
        'subtotal_cost',
        'subtotal',
        'cost_rate',
        'hpp',
        'complexity',
        'confidence',
        'origin',
        'ai_confidence',
        'is_optional',
        'is_recurring',
        'is_bonus',
        'recurring_period',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'rab_version_id' => 'integer',
        'lead_id' => 'integer',
        'module_catalog_id' => 'integer',
        'mandays' => 'float',
        'qty' => 'float',
        'daily_rate_sell' => 'float',
        'daily_rate_cost' => 'float',
        'unit_price' => 'float',
        'subtotal_sell' => 'float',
        'subtotal_cost' => 'float',
        'subtotal' => 'float',
        'cost_rate' => 'float',
        'hpp' => 'float',
        'confidence' => 'integer',
        'ai_confidence' => 'float',
        'is_optional' => 'boolean',
        'is_recurring' => 'boolean',
        'is_bonus' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function rabVersion(): BelongsTo
    {
        return $this->belongsTo(RabVersion::class, 'rab_version_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
