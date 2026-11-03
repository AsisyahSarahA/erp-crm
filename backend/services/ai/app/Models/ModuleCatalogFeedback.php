<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleCatalogFeedback extends Model
{
    protected $table = 'module_catalog_feedbacks';

    protected $fillable = [
        'module_key',
        'role',
        'estimated_mandays',
        'actual_mandays',
        'variance_pct',
        'project_id',
    ];

    protected $casts = [
        'estimated_mandays' => 'float',
        'actual_mandays' => 'float',
        'variance_pct' => 'float',
    ];
}

