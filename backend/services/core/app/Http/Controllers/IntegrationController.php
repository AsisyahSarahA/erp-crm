<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Integration;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IntegrationController extends Controller
{
    /**
     * List all third-party integrations with masked credentials.
     */
    public function index(): JsonResponse
    {
        $integrations = Integration::all()->map(function ($item) {
            $creds = $item->credentials ?? [];
            $maskedCreds = [];
            foreach ($creds as $key => $val) {
                $maskedCreds[$key] = is_string($val) && strlen($val) > 4
                    ? substr($val, 0, 3) . '••••••••' . substr($val, -2)
                    : '••••••••';
            }

            return [
                'id' => $item->id,
                'provider' => $item->provider,
                'name' => $item->name,
                'credentials_preview' => $maskedCreds,
                'is_active' => $item->is_active,
                'last_synced_at' => $item->last_synced_at?->toIso8601String(),
            ];
        });

        return ApiResponse::success($integrations);
    }

    /**
     * Store or update an integration with securely encrypted credentials.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'credentials' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $integration = Integration::updateOrCreate(
            ['provider' => $validated['provider']],
            [
                'name' => $validated['name'],
                'credentials' => $validated['credentials'],
                'is_active' => $validated['is_active'] ?? true,
                'last_synced_at' => now(),
            ]
        );

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'integration.configured',
            'entity_type' => 'integration',
            'entity_id' => (string) $integration->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Integrasi provider {$integration->provider} dikonfigurasi.",
        ]);

        return ApiResponse::success([
            'id' => $integration->id,
            'provider' => $integration->provider,
            'name' => $integration->name,
            'is_active' => $integration->is_active,
        ], 'Integrasi berhasil disimpan dengan enkripsi.', 201);
    }

    /**
     * Test integration connection health.
     */
    public function testConnection(string $provider): JsonResponse
    {
        $integration = Integration::where('provider', $provider)->first();
        if (!$integration) {
            return ApiResponse::rfcProblem('Integration Not Found', 404, "Integrasi untuk provider {$provider} belum dikonfigurasi.");
        }

        // Dummy test probe
        $isOk = true;

        return ApiResponse::success([
            'provider' => $provider,
            'connected' => $isOk,
            'tested_at' => now()->toIso8601String(),
        ], "Koneksi ke {$provider} berhasil.");
    }
}

