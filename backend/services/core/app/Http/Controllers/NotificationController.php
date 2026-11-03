<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\UserNotificationPreference;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    /**
     * List user notifications with unread count.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        if (!$userId) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $query = Notification::where('user_id', $userId);

        if ($request->boolean('unread_only')) {
            $query->whereNull('read_at');
        }

        $unreadCount = Notification::where('user_id', $userId)->whereNull('read_at')->count();

        $perPage = min((int) ($request->get('per_page', 20)), 100);
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $userId = $request->user()?->id;
        $notification = Notification::where('id', $id)->where('user_id', $userId)->first();

        if (!$notification) {
            return ApiResponse::rfcProblem('Notification Not Found', 404);
        }

        $notification->markAsRead();

        return ApiResponse::success($notification, 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications for user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        Notification::where('user_id', $userId)->whereNull('read_at')->update(['read_at' => now()]);

        return ApiResponse::success(null, 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Get user's notification preferences.
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $prefs = UserNotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            [
                'in_app_enabled' => true,
                'email_enabled' => true,
                'whatsapp_enabled' => false,
                'telegram_enabled' => false,
                'daily_digest_enabled' => false,
            ]
        );

        return ApiResponse::success($prefs);
    }

    /**
     * Update user's notification preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $validated = $request->validate([
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'whatsapp_enabled' => 'boolean',
            'telegram_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|string',
            'quiet_hours_end' => 'nullable|string',
            'daily_digest_enabled' => 'boolean',
        ]);

        $prefs = UserNotificationPreference::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );

        return ApiResponse::success($prefs, 'Preferensi notifikasi berhasil diperbarui.');
    }

    /**
     * Dispatch notification internally from other services.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'channel' => 'nullable|string|in:in_app,email,whatsapp,telegram',
            'data' => 'nullable|array',
        ]);

        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'channel' => $validated['channel'] ?? 'in_app',
            'title' => $validated['title'],
            'body' => $validated['body'],
            'data' => $validated['data'] ?? [],
            'created_at' => now(),
        ]);

        return ApiResponse::success($notification, 'Notifikasi berhasil dikirim.', 201);
    }

    /**
     * SSE (Server-Sent Events) live notification stream for Frontend client.
     */
    public function stream(Request $request): StreamedResponse
    {
        $userId = $request->user()?->id ?? (int) $request->get('user_id', 1);

        return new StreamedResponse(function () use ($userId) {
            echo "event: connected\n";
            echo 'data: ' . json_encode(['status' => 'connected', 'user_id' => $userId, 'time' => now()->toIso8601String()]) . "\n\n";
            ob_flush();
            flush();

            // Fetch recent unread
            $recent = Notification::where('user_id', $userId)
                ->whereNull('read_at')
                ->latest('created_at')
                ->take(5)
                ->get();

            foreach ($recent as $item) {
                echo "event: notification\n";
                echo 'data: ' . json_encode($item) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}

