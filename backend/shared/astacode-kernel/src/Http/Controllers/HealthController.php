<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Controller HealthController
 * Provides standard /internal/health endpoint for readiness and liveness probes.
 */
class HealthController extends Controller
{
    public function health(Request $request): JsonResponse
    {
        $dbStatus = 'UNKNOWN';
        $dbLatencyMs = null;

        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $dbLatencyMs = round((microtime(true) - $start) * 1000, 2);
            $dbStatus = 'UP';
        } catch (Exception $e) {
            $dbStatus = 'DOWN: ' . $e->getMessage();
        }

        $isOk = $dbStatus === 'UP' || app()->environment('local', 'testing');

        return response()->json([
            'status' => $isOk ? 'UP' : 'DEGRADED',
            'service' => config('app.name', 'astacode-service'),
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'database' => [
                'status' => $dbStatus,
                'latency_ms' => $dbLatencyMs,
            ],
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        ], $isOk ? 200 : 503);
    }
}

