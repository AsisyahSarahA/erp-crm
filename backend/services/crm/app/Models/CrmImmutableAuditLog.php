<?php

declare(strict_types=1);

namespace App\Models;

use Astacode\Kernel\Services\AuditClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class CrmImmutableAuditLog extends Model
{
    protected $table = 'crm_immutable_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'entity_type',
        'entity_id',
        'field_name',
        'old_value',
        'new_value',
        'is_override',
        'reason',
        'actor_id',
        'actor_name',
        'actor_role',
        'ip_address',
        'occurred_at_iso',
        'created_at',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'entity_id' => 'integer',
        'old_value' => 'array',
        'new_value' => 'array',
        'is_override' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('CrmImmutableAuditLog is immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new LogicException('CrmImmutableAuditLog is immutable and cannot be deleted.');
        });
    }

    public static function record(
        int $leadId,
        string $entityType,
        ?int $entityId,
        string $fieldName,
        mixed $oldValue,
        mixed $newValue,
        string|int|null $actorId = null,
        ?string $actorName = null,
        ?string $actorRole = null,
        bool $isOverride = false,
        ?string $reason = null,
        ?string $ipAddress = null,
    ): self {
        $now = now();
        $iso = $now->toIso8601String();

        $oldWrapped = is_array($oldValue) ? $oldValue : ['value' => $oldValue];
        $newWrapped = is_array($newValue) ? $newValue : ['value' => $newValue];

        $log = self::create([
            'lead_id' => $leadId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'field_name' => $fieldName,
            'old_value' => $oldWrapped,
            'new_value' => $newWrapped,
            'is_override' => $isOverride,
            'reason' => $reason,
            'actor_id' => $actorId !== null ? (string) $actorId : null,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,
            'ip_address' => $ipAddress ?? (app()->runningInConsole() ? '127.0.0.1' : request()?->ip()),
            'occurred_at_iso' => $iso,
            'created_at' => $now,
        ]);

        try {
            app(AuditClient::class)->log(
                action: $isOverride ? "crm.{$entityType}.{$fieldName}.override" : "crm.{$entityType}.{$fieldName}.changed",
                resourceType: $entityType,
                resourceId: (string) ($entityId ?? $leadId),
                changes: [
                    'lead_id' => $leadId,
                    'field' => $fieldName,
                    'old' => $oldValue,
                    'new' => $newValue,
                    'is_override' => $isOverride,
                    'reason' => $reason,
                    'occurred_at' => $iso,
                ],
                actorId: $actorId !== null ? (string) $actorId : null,
            );
        } catch (\Throwable) {
            // Best effort remote audit sync; local DB log is guaranteed
        }

        return $log;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

