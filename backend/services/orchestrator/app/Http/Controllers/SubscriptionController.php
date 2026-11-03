<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subscription;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subs = Subscription::all();
        return ApiResponse::success($subs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_name' => 'required|string',
            'consumer_service' => 'required|string',
            'target_endpoint' => 'required|string',
            'is_active' => 'boolean',
            'max_retries' => 'nullable|integer|min:1|max:10',
            'timeout_seconds' => 'nullable|integer|min:1|max:60',
        ]);

        $sub = Subscription::updateOrCreate(
            [
                'event_name' => $validated['event_name'],
                'consumer_service' => $validated['consumer_service'],
            ],
            [
                'target_endpoint' => $validated['target_endpoint'],
                'is_active' => $validated['is_active'] ?? true,
                'max_retries' => $validated['max_retries'] ?? 5,
                'timeout_seconds' => $validated['timeout_seconds'] ?? 15,
            ]
        );

        return ApiResponse::success($sub, 'Subscription berhasil disimpan.', 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $sub = Subscription::find($id);
        if (!$sub) {
            return ApiResponse::rfcProblem('Subscription Not Found', 404);
        }

        $sub->delete();
        return ApiResponse::success(null, 'Subscription berhasil dihapus.');
    }
}

