<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedView extends Model
{
    protected $table = 'crm_saved_views';

    protected $fillable = [
        'user_id',
        'module',
        'name',
        'filters',
        'sorts',
        'columns',
        'is_default',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'filters' => 'array',
        'sorts' => 'array',
        'columns' => 'array',
        'is_default' => 'boolean',
    ];
}

