<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberingSequence extends Model
{
    protected $table = 'numbering_sequences';

    protected $fillable = [
        'code_type',
        'format_pattern',
        'current_number',
        'current_period',
        'reset_frequency',
        'prefix',
        'padding',
    ];

    protected $casts = [
        'current_number' => 'integer',
        'padding' => 'integer',
    ];
}

