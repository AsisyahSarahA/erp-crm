<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'is_active',
        'failed_login_attempts',
        'lockout_until',
        'company_id',
        'customer_id',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'lockout_until' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
        ];
    }

    // ==========================================
    // RBAC & Permissions Logic
    // ==========================================

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions', 'model_id', 'permission_id');
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        $checkRoles = is_array($roles) ? $roles : [$roles];

        if (in_array($this->role, $checkRoles, true)) {
            return true;
        }

        return $this->roles()->whereIn('name', $checkRoles)->exists();
    }

    public function hasPermissionTo(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Check direct model permission
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check permission via assigned roles
        if ($this->roles()->whereHas('permissions', fn ($q) => $q->where('name', $permission))->exists()) {
            return true;
        }

        // Check permission via role attribute
        if ($this->role) {
            $roleModel = Role::where('name', $this->role)->first();
            if ($roleModel && $roleModel->permissions()->where('name', $permission)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function givePermissionTo(...$permissions): self
    {
        $perms = collect($permissions)->flatten()->map(function ($p) {
            return $p instanceof Permission ? $p : Permission::firstOrCreate(['name' => $p], ['display_name' => $p, 'module' => 'general']);
        });

        $this->permissions()->syncWithoutDetaching($perms->pluck('id'));
        return $this;
    }

    public function assignRole(...$roles): self
    {
        $roleModels = collect($roles)->flatten()->map(function ($r) {
            return $r instanceof Role ? $r : Role::firstOrCreate(['name' => $r], ['display_name' => ucfirst($r)]);
        });

        $this->roles()->syncWithoutDetaching($roleModels->pluck('id'));
        return $this;
    }

    public function getAllPermissions(): Collection
    {
        if ($this->hasRole('super_admin')) {
            return Permission::all();
        }

        $directPermissions = $this->permissions;
        $rolePermissions = $this->roles->load('permissions')->pluck('permissions')->flatten();

        return $directPermissions->merge($rolePermissions)->unique('id')->values();
    }

    // ==========================================
    // Lockout & Brute-force Defense
    // ==========================================

    public function isLockedOut(): bool
    {
        if ($this->lockout_until && $this->lockout_until->isFuture()) {
            return true;
        }

        return false;
    }

    public function recordFailedLogin(): void
    {
        $this->increment('failed_login_attempts');

        // Lockout after 5 failed attempts for 15 minutes
        if ($this->failed_login_attempts >= 5) {
            $this->update([
                'lockout_until' => now()->addMinutes(15),
            ]);
        }
    }

    public function resetFailedLogins(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'lockout_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    // ==========================================
    // Two-Factor Authentication (TOTP RFC 6238)
    // ==========================================

    public function isTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_secret) {
            return false;
        }

        // Test mode / demo bypass
        if (app()->environment('local', 'testing') && $code === '123456') {
            return true;
        }

        // Verify standard RFC 6238 TOTP
        return self::verifyTotp($this->two_factor_secret, $code);
    }

    public static function generateTotpSecret(int $length = 16): string
    {
        $b32 = '234567QWERTYUIOPASDFGHJKLZXCVBNM';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $b32[random_int(0, 31)];
        }
        return $secret;
    }

    private static function verifyTotp(string $secret, string $code): bool
    {
        $timeSlice = (int) floor(time() / 30);
        // Check window: previous, current, next 30s slice
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = self::calculateTotpCode($secret, $timeSlice + $i);
            if (hash_equals($calculatedCode, trim($code))) {
                return true;
            }
        }
        return false;
    }

    public static function calculateTotpCode(string $secret, ?int $timeSlice = null): string
    {
        $timeSlice = $timeSlice ?? (int) floor(time() / 30);
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hm = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1] & 0x7FFFFFFF;
        $modulo = $value % 1000000;
        return str_pad((string) $modulo, 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $b32): string
    {
        $lut = array(
            "A" => 0,  "B" => 1,  "C" => 2,  "D" => 3,
            "E" => 4,  "F" => 5,  "G" => 6,  "H" => 7,
            "I" => 8,  "J" => 9,  "K" => 10, "L" => 11,
            "M" => 12, "N" => 13, "O" => 14, "P" => 15,
            "Q" => 16, "R" => 17, "S" => 18, "T" => 19,
            "U" => 20, "V" => 21, "W" => 22, "X" => 23,
            "Y" => 24, "Z" => 25, "2" => 26, "3" => 27,
            "4" => 28, "5" => 29, "6" => 30, "7" => 31
        );
        $b32 = strtoupper(rtrim($b32, '='));
        $l = strlen($b32);
        $n = 0;
        $j = 0;
        $binary = "";
        for ($i = 0; $i < $l; $i++) {
            $n = $n << 5;
            $n = $n + $lut[$b32[$i]];
            $j += 5;
            if ($j >= 8) {
                $j -= 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
        return $binary;
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }
}
