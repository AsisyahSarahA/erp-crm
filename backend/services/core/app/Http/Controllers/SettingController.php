<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get all active system settings grouped by category (cached).
     */
    public function index(Request $request): JsonResponse
    {
        $category = $request->query('category');

        $cacheKey = $category ? "settings:cat:{$category}" : 'settings:all';

        $settings = Cache::remember($cacheKey, 3600, function () use ($category) {
            $query = Setting::query();
            if ($category) {
                $query->where('category', $category);
            }
            return $query->get();
        });

        return ApiResponse::success($settings);
    }

    /**
     * Get single setting value by key.
     */
    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return ApiResponse::rfcProblem('Setting Not Found', 404, "Konfigurasi '{$key}' tidak ditemukan.");
        }

        return ApiResponse::success($setting);
    }

    /**
     * Update a setting value (bumps version, invalidates cache, creates audit log).
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $request->validate([
            'value' => 'required',
            'category' => 'nullable|string',
        ]);

        $setting = Setting::where('key', $key)->first();
        $oldValue = $setting?->value;

        $userId = $request->user()?->id;
        $updatedSetting = Setting::set($key, $request->input('value'), $request->input('category'), $userId);

        Cache::forget('settings:all');
        if ($updatedSetting->category) {
            Cache::forget("settings:cat:{$updatedSetting->category}");
        }

        AuditLog::create([
            'user_id' => (string) ($userId ?? 'system'),
            'service' => 'core-service',
            'action' => 'settings.updated',
            'entity_type' => 'setting',
            'entity_id' => (string) $updatedSetting->id,
            'old_values' => ['value' => $oldValue],
            'new_values' => ['value' => $updatedSetting->value, 'version' => $updatedSetting->version],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Pengaturan {$key} dinaikkan ke versi {$updatedSetting->version}.",
        ]);

        return ApiResponse::success($updatedSetting, "Pengaturan '{$key}' berhasil diperbarui.");
    }
}

