<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFieldValue extends Model
{
    protected $table = 'crm_custom_field_values';

    protected $fillable = [
        'definition_id',
        'entity_type',
        'entity_id',
        'value_text',
        'value_json',
    ];

    protected $casts = [
        'definition_id' => 'integer',
        'entity_id' => 'integer',
        'value_json' => 'array',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(CustomFieldDefinition::class, 'definition_id');
    }
}

