<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * User login with rate limiting, brute-force lockout, and 2FA support.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $email = strtolower(trim($request->email));
        $throttleKey = 'login:' . $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return ApiResponse::rfcProblem(
                title: 'Too Many Login Attempts',
                status: 429,
                detail: "Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$seconds} detik.",
                type: 'https://astacode.com/errors/too-many-requests'
            );
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);

            if ($user) {
                $user->recordFailedLogin();
                AuditLog::create([
                    'user_id' => (string) $user->id,
                    'service' => 'core-service',
                    'action' => 'auth.login_failed',
                    'entity_type' => 'user',
                    'entity_id' => (string) $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'notes' => 'Invalid password submission',
                ]);
            }

            return ApiResponse::rfcProblem(
                title: 'Invalid Credentials',
                status: 401,
                detail: 'Kombinasi email dan kata sandi tidak cocok.',
                type: 'https://astacode.com/errors/invalid-credentials'
            );
        }

        if (!$user->is_active) {
            return ApiResponse::rfcProblem(
                title: 'Account Disabled',
                status: 403,
                detail: 'Akun Anda dinonaktifkan oleh administrator.',
                type: 'https://astacode.com/errors/account-disabled'
            );
        }

        if ($user->isLockedOut()) {
            return ApiResponse::rfcProblem(
                title: 'Account Locked Out',
                status: 423,
                detail: 'Akun terkunci sementara karena 5x percobaan gagal. Silakan coba 15 menit lagi.',
                type: 'https://astacode.com/errors/account-locked'
            );
        }

        RateLimiter::clear($throttleKey);
        $user->resetFailedLogins();

        // Check if 2FA TOTP is required
        $is2faMandatoryRole = in_array($user->role, ['director', 'finance_manager', 'hrd_manager', 'super_admin'], true);
        if ($user->isTwoFactorEnabled() || $is2faMandatoryRole) {
            if (!$user->isTwoFactorEnabled()) {
                // Auto generate 2FA secret for mandatory roles on first login
                $user->update(['two_factor_secret' => User::generateTotpSecret()]);
            }

            $tempToken = Str::random(64);
            cache()->put("2fa_temp:{$tempToken}", $user->id, 300); // 5 min TTL

            return ApiResponse::success([
                'two_factor_required' => true,
                'two_factor_token' => $tempToken,
                'message' => 'Verifikasi kode 2FA (TOTP) 6 digit diperlukan.',
            ], status: 200);
        }

        $deviceName = $request->device_name ?? 'Web Browser';
        $token = $user->createToken($deviceName)->plainTextToken;

        AuditLog::create([
            'user_id' => (string) $user->id,
            'service' => 'core-service',
            'action' => 'auth.login_success',
            'entity_type' => 'user',
            'entity_id' => (string) $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Login sukses dari {$deviceName}",
        ]);

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'customer_id' => $user->customer_id,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Verifies 2FA TOTP code.
     */
    public function verify2fa(Request $request): JsonResponse
    {
        $request->validate([
            'two_factor_token' => 'required|string',
            'code' => 'required|string|size:6',
            'device_name' => 'nullable|string',
        ]);

        $userId = cache()->get("2fa_temp:{$request->two_factor_token}");
        if (!$userId) {
            return ApiResponse::rfcProblem(
                title: 'Invalid or Expired 2FA Session',
                status: 401,
                detail: 'Sesi verifikasi 2FA telah kedaluwarsa. Silakan login kembali.',
                type: 'https://astacode.com/errors/expired-2fa-session'
            );
        }

        $user = User::findOrFail($userId);

        if (!$user->verifyTwoFactorCode($request->code)) {
            return ApiResponse::rfcProblem(
                title: 'Invalid 2FA Code',
                status: 422,
                detail: 'Kode autentikasi 2FA salah. Periksa aplikasi authenticator Anda.',
                type: 'https://astacode.com/errors/invalid-2fa-code'
            );
        }

        cache()->forget("2fa_temp:{$request->two_factor_token}");
        if (!$user->two_factor_confirmed_at) {
            $user->update(['two_factor_confirmed_at' => now()]);
        }

        $deviceName = $request->device_name ?? 'Web Browser (2FA)';
        $token = $user->createToken($deviceName)->plainTextToken;

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Revoke current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return ApiResponse::success(['message' => 'Berhasil logout dan sesi ditutup.']);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'company_id' => $user->company_id,
            'customer_id' => $user->customer_id,
            'is_two_factor_enabled' => $user->isTwoFactorEnabled(),
            'created_at' => $user->created_at->toIso8601String(),
        ]);
    }

    /**
     * Get authenticated user's permission slugs for Frontend permission guards.
     */
    public function permissions(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $permissions = $user->getAllPermissions()->pluck('name')->values()->toArray();

        return ApiResponse::success([
            'role' => $user->role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Setup 2FA TOTP secret and QR provisioning URI.
     */
    public function setup2fa(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $secret = User::generateTotpSecret();
        $user->update(['two_factor_secret' => $secret]);

        $otpauthUri = "otpauth://totp/ASTACODE:{$user->email}?secret={$secret}&issuer=ASTACODE";

        return ApiResponse::success([
            'secret' => $secret,
            'otpauth_uri' => $otpauthUri,
            'message' => 'Pindai QR code di aplikasi Authenticator Anda dan masukkan kode 6-digit untuk konfirmasi.',
        ]);
    }

    /**
     * List user's active login sessions/devices.
     */
    public function sessions(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $tokens = $user->tokens()->get(['id', 'name', 'last_used_at', 'created_at']);

        return ApiResponse::success($tokens);
    }

    /**
     * Revoke a specific personal access token.
     */
    public function revokeSession(Request $request, string $tokenId): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $user->tokens()->where('id', $tokenId)->delete();

        return ApiResponse::success(['message' => "Sesi dengan ID {$tokenId} telah dicabut."]);
    }
}
