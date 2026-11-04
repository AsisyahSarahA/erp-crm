<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CrmSetting extends Model
{
    protected $table = 'crm_settings';

    protected $fillable = [
        'key',
        'value',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public const DEFAULTS = [
        'vat_rate' => 11.0,
        'is_company_pkp' => true,
        'contingency_default_pct' => 10.0,
        'contingency_min_without_reason_pct' => 5.0,
        'margin_green_threshold' => 40.0,
        'margin_yellow_threshold' => 25.0,
        'discount_sales_max_pct' => 10.0,
        'discount_manager_max_pct' => 15.0,
        'director_quotation_threshold_idr' => 500_000_000.0,
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $fallback = $default ?? (self::DEFAULTS[$key] ?? null);

        if (!Schema::hasTable('crm_settings')) {
            return $fallback;
        }

        $record = static::where('key', $key)->first();
        if (!$record) {
            return $fallback;
        }

        $val = $record->value;
        if (is_array($val) && array_key_exists('val', $val)) {
            return $val['val'];
        }

        return $val ?? $fallback;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null, ?string $updatedBy = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => ['val' => $value],
                'description' => $description,
                'updated_by' => $updatedBy,
            ]
        );
    }

    public static function allMerged(): array
    {
        $result = self::DEFAULTS;

        if (!Schema::hasTable('crm_settings')) {
            return $result;
        }

        foreach (static::all() as $row) {
            $val = $row->value;
            $result[$row->key] = (is_array($val) && array_key_exists('val', $val)) ? $val['val'] : $val;
        }

        return $result;
    }
}

