<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'version',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'json',
        'version' => 'integer',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value, ?string $category = 'general', ?int $userId = null): self
    {
        Cache::forget("setting:{$key}");

        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->category = $category ?? $setting->category ?? 'general';
        $setting->version = ($setting->version ?? 0) + 1;
        $setting->updated_by = $userId ?? auth()->id();
        $setting->save();

        return $setting;
    }
}

