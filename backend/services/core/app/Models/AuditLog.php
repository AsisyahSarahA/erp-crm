<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AuditLog
 * Append-only immutable audit trail for security compliance and tracking who viewed/edited what.
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'service',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'correlation_id',
        'notes',
        'is_sensitive_access',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_sensitive_access' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Strictly forbid UPDATE or DELETE on audit logs
        static::updating(function () {
            throw new Exception("Audit logs are strictly immutable and cannot be updated.");
        });

        static::deleting(function () {
            throw new Exception("Audit logs are strictly immutable and cannot be deleted.");
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSensitive(Builder $query): Builder
    {
        return $query->where('is_sensitive_access', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['service'] ?? null, fn ($q, $v) => $q->where('service', $v))
            ->when($filters['action'] ?? null, fn ($q, $v) => $q->where('action', $v))
            ->when($filters['entity_type'] ?? null, fn ($q, $v) => $q->where('entity_type', $v))
            ->when($filters['entity_id'] ?? null, fn ($q, $v) => $q->where('entity_id', $v))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when(isset($filters['sensitive']), fn ($q) => $q->where('is_sensitive_access', (bool) $filters['sensitive']));
    }
}

