<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponse
 * Provides standardized JSON response formatting for ASTACODE ERP:
 * - Happy Path: { data: ..., meta: ..., links: ... }
 * - Error Path: RFC 7807 Problem Details { type, title, status, detail, instance, invalid_params }
 */
final class ApiResponse
{
    /**
     * Standard success response envelope.
     */
    public static function success(mixed $data = null, string|array $meta = [], array|int $links = [], int $status = 200): JsonResponse
    {
        if (is_int($links)) {
            $status = $links;
            $links = [];
        }

        if (is_string($meta)) {
            $meta = ['message' => $meta];
        }

        $payload = [
            'data' => $data,
        ];

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        if (!empty($links)) {
            $payload['links'] = $links;
        }

        return response()->json($payload, $status);
    }

    /**
     * Standard paginated response envelope.
     */
    public static function paginated(mixed $data, ?int $currentPage = null, ?int $perPage = null, ?int $total = null, array $links = []): JsonResponse
    {
        if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator || $data instanceof \Illuminate\Contracts\Pagination\Paginator) {
            $items = $data->items();
            $curPage = $data->currentPage();
            $pPage = $data->perPage();
            $tot = method_exists($data, 'total') ? $data->total() : count($items);
            $lPage = method_exists($data, 'lastPage') ? $data->lastPage() : 1;

            $meta = [
                'current_page' => $curPage,
                'per_page' => $pPage,
                'total' => $tot,
                'last_page' => $lPage,
            ];

            return self::success($items, $meta, $links, 200);
        }

        $lastPage = ($perPage ?? 1) > 0 ? (int) ceil(($total ?? 0) / ($perPage ?? 1)) : 1;

        $meta = [
            'current_page' => $currentPage ?? 1,
            'per_page' => $perPage ?? 15,
            'total' => $total ?? 0,
            'last_page' => $lastPage,
        ];

        return self::success($data, $meta, $links, 200);
    }

    /**
     * RFC 7807 Problem Details for HTTP APIs.
     */
    public static function rfcProblem(
        string $title,
        int $status = 400,
        ?string $detail = null,
        ?string $type = null,
        array $invalidParams = [],
        array $extra = []
    ): JsonResponse {
        $problem = [
            'type' => $type ?? 'https://astacode.com/errors/' . self::statusSlug($status),
            'title' => $title,
            'status' => $status,
            'detail' => $detail ?? $title,
            'instance' => request()->fullUrl(),
            'timestamp' => now()->toIso8601String(),
            'correlation_id' => request()->header('X-Correlation-Id') ?? (app()->bound('correlation_id') ? app('correlation_id') : null),
        ];

        if (!empty($invalidParams)) {
            $problem['invalid_params'] = $invalidParams;
        }

        foreach ($extra as $key => $value) {
            $problem[$key] = $value;
        }

        return response()->json($problem, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }

    private static function statusSlug(int $status): string
    {
        return match ($status) {
            400 => 'bad-request',
            401 => 'unauthorized',
            403 => 'forbidden',
            404 => 'not-found',
            409 => 'conflict',
            422 => 'validation-error',
            429 => 'rate-limit-exceeded',
            500 => 'internal-server-error',
            502 => 'bad-gateway',
            503 => 'service-unavailable',
            default => 'http-error',
        };
    }
}

