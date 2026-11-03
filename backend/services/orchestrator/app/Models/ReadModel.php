<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadModel extends Model
{
    protected $fillable = [
        'model_type',
        'model_key',
        'data',
        'version',
        'last_event_id',
    ];

    protected $casts = [
        'data' => 'array',
        'version' => 'integer',
    ];
}

