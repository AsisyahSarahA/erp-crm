<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'module',
        'version',
        'system_prompt',
        'user_template',
        'schema_name',
        'status',
        'model_preference',
        'temperature',
        'ab_test_group',
        'is_active',
    ];

    protected $casts = [
        'version' => 'integer',
        'temperature' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Renders prompt user message by replacing {{variable}} placeholders with values.
     */
    public function render(array $variables = []): string
    {
        $rendered = $this->user_template;
        foreach ($variables as $key => $val) {
            $replacement = is_array($val) ? json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $val;
            $rendered = str_replace('{{' . $key . '}}', $replacement, $rendered);
        }
        return $rendered;
    }
}

