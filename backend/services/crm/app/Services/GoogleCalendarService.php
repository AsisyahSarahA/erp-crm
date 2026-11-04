<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\GoogleAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleCalendarService
{
    public const DEFAULT_SCOPE = 'https://www.googleapis.com/auth/calendar.events';

    public function __construct(
        private readonly ?bool $mockMode = null
    ) {}

    public function isMock(): bool
    {
        if ($this->mockMode !== null) {
            return $this->mockMode;
        }

        return (bool) env('GOOGLE_CALENDAR_MOCK', true) || app()->environment('testing', 'local');
    }

    public function getAuthUrl(int $userId, string $redirectUri = ''): string
    {
        $clientId = env('GOOGLE_CLIENT_ID', 'mock-client-id.apps.googleusercontent.com');
        $resolvedRedirect = $redirectUri ?: env('GOOGLE_REDIRECT_URI', 'http://127.0.0.1:8000/api/v1/crm/integrations/google/callback');
        $state = base64_encode(json_encode(['user_id' => $userId, 'nonce' => Str::random(16)]));

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $resolvedRedirect,
            'response_type' => 'code',
            'scope' => self::DEFAULT_SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
    }

    public function handleCallback(int $userId, string $code, string $redirectUri = ''): array
    {
        if ($this->isMock()) {
            $account = GoogleAccount::updateOrCreate(
                ['user_id' => $userId],
                [
                    'email' => "sales.user{$userId}@astacode.id",
                    'access_token' => 'mock_access_token_' . Str::random(32),
                    'refresh_token' => 'mock_refresh_token_' . Str::random(32),
                    'token_expires_at' => now()->addDays(30),
                    'calendar_id' => 'primary',
                    'channel_id' => 'channel_' . Str::random(16),
                    'resource_id' => 'res_' . Str::random(16),
                    'is_active' => true,
                ]
            );

            return [
                'success' => true,
                'email' => $account->email,
                'is_connected' => true,
                'calendar_id' => $account->calendar_id,
            ];
        }

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $resolvedRedirect = $redirectUri ?: env('GOOGLE_REDIRECT_URI', 'http://127.0.0.1:8000/api/v1/crm/integrations/google/callback');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $resolvedRedirect,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal menukarkan kode otorisasi Google: ' . $response->body());
        }

        $tokenData = $response->json();
        $accessToken = $tokenData['access_token'] ?? null;
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);

        // Fetch user email
        $userEmail = null;
        if ($accessToken) {
            $userRes = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo');
            if ($userRes->successful()) {
                $userEmail = $userRes->json('email');
            }
        }

        $account = GoogleAccount::updateOrCreate(
            ['user_id' => $userId],
            [
                'email' => $userEmail,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken ?: GoogleAccount::where('user_id', $userId)->value('refresh_token'),
                'token_expires_at' => now()->addSeconds($expiresIn),
                'calendar_id' => 'primary',
                'is_active' => true,
            ]
        );

        return [
            'success' => true,
            'email' => $account->email,
            'is_connected' => true,
            'calendar_id' => $account->calendar_id,
        ];
    }

    public function disconnect(int $userId): bool
    {
        $account = GoogleAccount::where('user_id', $userId)->first();
        if ($account) {
            $account->update([
                'is_active' => false,
                'access_token' => null,
                'refresh_token' => null,
            ]);
        }

        return true;
    }

    public function getStatus(int $userId): array
    {
        $account = GoogleAccount::where('user_id', $userId)->where('is_active', true)->first();

        return [
            'is_connected' => $account !== null,
            'email' => $account?->email,
            'calendar_id' => $account?->calendar_id ?? 'primary',
            'is_mock' => $this->isMock(),
        ];
    }

    public function createEvent(int $userId, array $eventData, bool $createMeet = false): array
    {
        $title = $eventData['title'] ?? 'Meeting Prospek';
        $startAt = isset($eventData['start_at']) ? Carbon::parse($eventData['start_at']) : now()->addHours(1);
        $endAt = isset($eventData['end_at']) ? Carbon::parse($eventData['end_at']) : $startAt->copy()->addMinutes(45);
        $notes = $eventData['notes'] ?? '';
        $attendees = (array) ($eventData['attendees'] ?? []);

        if ($this->isMock()) {
            $randomCode = strtolower(Str::random(3) . '-' . Str::random(4) . '-' . Str::random(3));
            $meetLink = $createMeet ? "https://meet.google.com/{$randomCode}" : null;
            $eventId = 'gcal_' . Str::random(24);

            return [
                'google_event_id' => $eventId,
                'meet_link' => $meetLink,
                'html_link' => "https://www.google.com/calendar/event?eid={$eventId}",
                'status' => 'confirmed',
                'location' => $meetLink ?: ($eventData['location'] ?? 'Online Meeting'),
                'attendees' => $attendees,
            ];
        }

        $account = GoogleAccount::where('user_id', $userId)->where('is_active', true)->first();
        if (!$account || empty($account->access_token)) {
            // Fallback to mock generation if user hasn't connected Google account
            $randomCode = strtolower(Str::random(3) . '-' . Str::random(4) . '-' . Str::random(3));
            $meetLink = $createMeet ? "https://meet.google.com/{$randomCode}" : null;

            return [
                'google_event_id' => 'gcal_unconnected_' . Str::random(16),
                'meet_link' => $meetLink,
                'html_link' => null,
                'status' => 'confirmed',
                'location' => $meetLink ?: ($eventData['location'] ?? 'Online Meeting'),
                'attendees' => $attendees,
            ];
        }

        $payload = [
            'summary' => $title,
            'description' => $notes,
            'start' => ['dateTime' => $startAt->toIso8601String()],
            'end' => ['dateTime' => $endAt->toIso8601String()],
            'attendees' => array_map(function ($att) {
                return [
                    'email' => is_array($att) ? ($att['email'] ?? '') : (string) $att,
                    'displayName' => is_array($att) ? ($att['name'] ?? null) : null,
                ];
            }, $attendees),
        ];

        if ($createMeet) {
            $payload['conferenceData'] = [
                'createRequest' => [
                    'requestId' => (string) Str::uuid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ];
        }

        $url = 'https://www.googleapis.com/calendar/v3/calendars/' . urlencode($account->calendar_id) . '/events?conferenceDataVersion=1';
        $response = Http::withToken($account->access_token)->post($url, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal membuat event di Google Calendar: ' . $response->body());
        }

        $data = $response->json();
        $meetLink = $data['hangoutLink'] ?? $data['conferenceData']['entryPoints'][0]['uri'] ?? null;

        return [
            'google_event_id' => $data['id'] ?? null,
            'meet_link' => $meetLink,
            'html_link' => $data['htmlLink'] ?? null,
            'status' => $data['status'] ?? 'confirmed',
            'location' => $data['location'] ?? $meetLink,
            'attendees' => $attendees,
        ];
    }

    public function updateEvent(int $userId, string $googleEventId, array $eventData): array
    {
        if ($this->isMock() || str_starts_with($googleEventId, 'gcal_')) {
            return [
                'google_event_id' => $googleEventId,
                'updated' => true,
                'title' => $eventData['title'] ?? null,
                'start_at' => $eventData['start_at'] ?? null,
                'end_at' => $eventData['end_at'] ?? null,
            ];
        }

        $account = GoogleAccount::where('user_id', $userId)->where('is_active', true)->first();
        if (!$account || empty($account->access_token)) {
            return ['google_event_id' => $googleEventId, 'updated' => false];
        }

        $payload = [];
        if (!empty($eventData['title'])) {
            $payload['summary'] = $eventData['title'];
        }
        if (!empty($eventData['notes'])) {
            $payload['description'] = $eventData['notes'];
        }
        if (!empty($eventData['start_at'])) {
            $payload['start'] = ['dateTime' => Carbon::parse($eventData['start_at'])->toIso8601String()];
        }
        if (!empty($eventData['end_at'])) {
            $payload['end'] = ['dateTime' => Carbon::parse($eventData['end_at'])->toIso8601String()];
        }

        $url = "https://www.googleapis.com/calendar/v3/calendars/{$account->calendar_id}/events/{$googleEventId}";
        $response = Http::withToken($account->access_token)->patch($url, $payload);

        return [
            'google_event_id' => $googleEventId,
            'updated' => $response->successful(),
        ];
    }

    public function deleteEvent(int $userId, string $googleEventId): bool
    {
        if ($this->isMock() || str_starts_with($googleEventId, 'gcal_')) {
            return true;
        }

        $account = GoogleAccount::where('user_id', $userId)->where('is_active', true)->first();
        if (!$account || empty($account->access_token)) {
            return true;
        }

        $url = "https://www.googleapis.com/calendar/v3/calendars/{$account->calendar_id}/events/{$googleEventId}";
        $response = Http::withToken($account->access_token)->delete($url);

        return $response->successful();
    }

    public function handleWebhook(array $headers, ?string $body = null): array
    {
        $channelId = $headers['x-goog-channel-id'] ?? $headers['X-Goog-Channel-ID'] ?? null;
        $resourceState = $headers['x-goog-resource-state'] ?? $headers['X-Goog-Resource-State'] ?? 'exists';

        if (!$channelId) {
            return ['status' => 'ignored', 'reason' => 'missing channel id'];
        }

        $account = GoogleAccount::where('channel_id', $channelId)->first();
        if (!$account) {
            return ['status' => 'ignored', 'reason' => 'channel not found'];
        }

        // Trigger synchronization for the account
        $synced = $this->syncEvents((int) $account->user_id);

        return [
            'status' => 'synced',
            'user_id' => $account->user_id,
            'state' => $resourceState,
            'synced_count' => count($synced),
        ];
    }

    public function syncEvents(int $userId): array
    {
        // Find existing activities for this user with google_event_id
        $activities = Activity::where('created_by', $userId)
            ->whereNotNull('google_event_id')
            ->get();

        return $activities->map(fn ($a) => [
            'activity_id' => $a->id,
            'google_event_id' => $a->google_event_id,
            'title' => $a->title,
            'status' => $a->status,
        ])->all();
    }
}

