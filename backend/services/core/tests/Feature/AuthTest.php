<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_login_successfully(): void
    {
        $user = User::create([
            'name' => 'Citra Sales',
            'email' => 'citra@example.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'citra@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', 'citra@example.com')
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('title', 'Invalid Credentials');
    }

    public function test_login_locks_out_after_five_failed_attempts(): void
    {
        $user = User::create([
            'name' => 'Lockout User',
            'email' => 'lockout@example.com',
            'role' => 'sales',
            'password' => Hash::make('correct-pass'),
            'is_active' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'lockout@example.com',
                'password' => 'bad-pass-' . $i,
            ]);
        }

        $user->refresh();
        $this->assertTrue($user->isLockedOut());

        // Subsequent attempt blocked by lockout or rate limiter
        $blockedResponse = $this->postJson('/api/auth/login', [
            'email' => 'lockout@example.com',
            'password' => 'correct-pass',
        ]);

        $this->assertContains($blockedResponse->status(), [423, 429]);
    }

    public function test_two_factor_authentication_challenge_and_verification(): void
    {
        $secret = User::generateTotpSecret();
        $user = User::create([
            'name' => 'Finance Chief',
            'email' => 'finance.lead@example.com',
            'role' => 'finance_manager', // 2FA is mandatory for finance
            'password' => Hash::make('securepass123'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'is_active' => true,
        ]);

        // Step 1: Initial login gives 2FA challenge
        $response = $this->postJson('/api/auth/login', [
            'email' => 'finance.lead@example.com',
            'password' => 'securepass123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.two_factor_required', true);

        $tempToken = $response->json('data.two_factor_token');
        $this->assertNotEmpty($tempToken);

        // Step 2: Calculate valid TOTP code
        $code = User::calculateTotpCode($secret);

        // Step 3: Verify 2FA code
        $verifyResponse = $this->postJson('/api/auth/2fa/verify', [
            'two_factor_token' => $tempToken,
            'code' => $code,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonPath('data.user.email', 'finance.lead@example.com')
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_authenticated_user_can_get_me_and_permissions(): void
    {
        $user = User::create([
            'name' => 'PM User',
            'email' => 'pm@example.com',
            'role' => 'project_manager',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.email', 'pm@example.com')
            ->assertJsonPath('data.role', 'project_manager');

        $permResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me/permissions');

        $permResponse->assertStatus(200)
            ->assertJsonPath('data.role', 'project_manager')
            ->assertJsonStructure(['data' => ['role', 'permissions']]);
    }
}

