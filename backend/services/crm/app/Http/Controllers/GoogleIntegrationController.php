<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleIntegrationController extends Controller
{
    public function __construct(
        private readonly GoogleCalendarService $googleCalendarService = new GoogleCalendarService()
    ) {}

    public function status(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = (int) ($userContext['id'] ?? 1);

        $status = $this->googleCalendarService->getStatus($userId);

        return ApiResponse::success($status);
    }

    public function authUrl(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = (int) ($userContext['id'] ?? 1);
        $redirect = (string) $request->query('redirect_uri', '');

        $url = $this->googleCalendarService->getAuthUrl($userId, $redirect);

        return ApiResponse::success([
            'auth_url' => $url,
            'scope' => GoogleCalendarService::DEFAULT_SCOPE,
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = (int) ($userContext['id'] ?? 1);
        $code = (string) ($request->input('code') ?? 'mock_code_12345');
        $redirect = (string) $request->input('redirect_uri', '');

        $result = $this->googleCalendarService->handleCallback($userId, $code, $redirect);

        return ApiResponse::success($result, ['message' => 'Akun Google Calendar berhasil dihubungkan.']);
    }

    public function disconnect(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = (int) ($userContext['id'] ?? 1);

        $this->googleCalendarService->disconnect($userId);

        return ApiResponse::success(['disconnected' => true], ['message' => 'Integrasi Google Calendar diputus.']);
    }

    public function webhook(Request $request): JsonResponse
    {
        $headers = $request->headers->all();
        $flatHeaders = [];
        foreach ($headers as $k => $v) {
            $flatHeaders[$k] = is_array($v) ? ($v[0] ?? '') : (string) $v;
        }

        $result = $this->googleCalendarService->handleWebhook($flatHeaders, $request->getContent());

        return response()->json($result);
    }
}

