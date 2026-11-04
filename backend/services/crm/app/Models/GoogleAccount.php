<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class GoogleAccount extends Model
{
    protected $table = 'crm_google_accounts';

    protected $fillable = [
        'user_id',
        'email',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'calendar_id',
        'channel_id',
        'resource_id',
        'sync_token',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'token_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function setAccessTokenAttribute(?string $value): void
    {
        $this->attributes['access_token'] = $value !== null && $value !== '' ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value !== null && $value !== '' ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function isTokenExpired(): bool
    {
        if ($this->token_expires_at === null) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }
}

