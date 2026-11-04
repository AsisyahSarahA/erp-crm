<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LeadSourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_sources';

    protected $fillable = [
        'code',
        'name',
        'category',
        'channel_group',
        'sub_sources',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'sub_sources' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $source) {
            if (empty($source->channel_group) && !empty($source->category)) {
                $source->channel_group = $source->category;
            }
            if (empty($source->category) && !empty($source->channel_group)) {
                $source->category = $source->channel_group;
            }
            if (empty($source->code) && !empty($source->name)) {
                $source->code = strtoupper((string) preg_replace('/[^A-Za-z0-9]+/', '_', trim($source->name)));
            }
        });
    }

    protected static function newFactory(): LeadSourceFactory
    {
        return LeadSourceFactory::new();
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(CustomerAttribution::class, 'source_id');
    }
}

