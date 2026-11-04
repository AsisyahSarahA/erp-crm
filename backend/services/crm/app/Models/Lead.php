<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\PipelineTransitionGuard;
use Astacode\Kernel\Outbox\HasOutbox;
use Database\Factories\LeadFactory;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    use HasFactory;
    use HasOutbox;
    use SoftDeletes;

    protected $table = 'crm_leads';

    private bool $stageMutationAuthorized = false;

    public const PROJECT_TYPES = [
        'web_mobile_app',
        'erp_custom',
        'fintech',
        'iot_telematics',
        'ecommerce',
        'lainnya',
    ];

    public const PRIORITIES = [
        'hot',
        'high',
        'medium',
        'low',
    ];

    public const STAGES = [
        'new_lead',
        'follow_up',
        'requirement',
        'quotation',
        'negotiation',
        'deal',
        'reject',
    ];

    public const DEFAULT_STAGE_PROBABILITIES = [
        'new_lead' => 10,
        'follow_up' => 20,
        'requirement' => 35,
        'quotation' => 55,
        'negotiation' => 75,
        'deal' => 100,
        'deal_won' => 100,
        'reject' => 0,
        'lost' => 0,
    ];

    protected $fillable = [
        'code',
        'lead_code',
        'customer_id',
        'contact_id',
        'title',
        'project_type',
        'priority',
        'stage',
        'stage_id',
        'source',
        'source_id',
        'sub_source',
        'utm',
        'est_value',
        'win_probability',
        'weighted_value',
        'budget_range',
        'target_closing',
        'target_golive',
        'sales_pic_id',
        'owner_id',
        'team_id',
        'sales_pic_name',
        'score',
        'score_breakdown',
        'score_label',
        'days_in_stage',
        'stale_since',
        'is_stale',
        'has_next_step',
        'active_quotation_id',
        'active_rab_version_id',
        'client_responded_at',
        'reject_reason_code',
        'reject_reason_id',
        'reject_notes',
        'won_at',
        'lost_at',
        'reopened_at',
        'reopened_by',
        'reopen_reason',
        'industry',
        'services',
        'tags',
        'consent',
        'consent_at',
        'created_by',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'contact_id' => 'integer',
        'source_id' => 'integer',
        'utm' => 'array',
        'est_value' => 'float',
        'win_probability' => 'integer',
        'weighted_value' => 'float',
        'target_closing' => 'date',
        'target_golive' => 'date',
        'sales_pic_id' => 'integer',
        'owner_id' => 'integer',
        'team_id' => 'integer',
        'score' => 'integer',
        'score_breakdown' => 'array',
        'days_in_stage' => 'integer',
        'stale_since' => 'datetime',
        'is_stale' => 'boolean',
        'has_next_step' => 'boolean',
        'active_quotation_id' => 'integer',
        'active_rab_version_id' => 'integer',
        'client_responded_at' => 'datetime',
        'reject_reason_id' => 'integer',
        'won_at' => 'datetime',
        'lost_at' => 'datetime',
        'reopened_at' => 'datetime',
        'reopened_by' => 'integer',
        'services' => 'array',
        'tags' => 'array',
        'consent' => 'boolean',
        'consent_at' => 'datetime',
    ];

    /**
     * Authorize stage mutation exclusively from PipelineTransitionGuard.
     */
    public function authorizeStageMutationFromGuard(): void
    {
        $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        $authorizedCaller = false;
        foreach ($frames as $frame) {
            if (($frame['class'] ?? null) === PipelineTransitionGuard::class) {
                $authorizedCaller = true;
                break;
            }
        }

        if (!$authorizedCaller) {
            throw new DomainException('Direct mutation of Lead stage is forbidden. All stage transitions must go through PipelineTransitionGuard.');
        }

        $this->stageMutationAuthorized = true;
    }

    public static function normalizeStageId(?string $stage): string
    {
        $val = strtolower(trim((string) $stage));

        return match ($val) {
            'new', 'new_lead' => 'new_lead',
            'follow_up', 'followup' => 'follow_up',
            'requirement', 'requirements' => 'requirement',
            'quotation', 'quote' => 'quotation',
            'negotiation', 'nego' => 'negotiation',
            'deal', 'deal_won', 'won' => 'deal',
            'reject', 'rejected', 'lost' => 'reject',
            default => $val !== '' ? $val : 'new_lead',
        };
    }

    public static function normalizeStageUpper(string $stageId): string
    {
        return match ($stageId) {
            'deal' => 'DEAL',
            'reject' => 'REJECT',
            default => strtoupper($stageId),
        };
    }

    protected static function booted(): void
    {
        static::saving(function (self $lead) {
            // Sync code <-> lead_code (LD-YYYY-NNNN)
            if (empty($lead->code) && !empty($lead->lead_code)) {
                $lead->code = $lead->lead_code;
            } elseif (!empty($lead->code) && empty($lead->lead_code)) {
                $lead->lead_code = $lead->code;
            } elseif (empty($lead->code) && empty($lead->lead_code)) {
                $code = self::generateNextCode();
                $lead->code = $code;
                $lead->lead_code = $code;
            }

            // Sync sales_pic_id <-> owner_id
            if (empty($lead->owner_id) && !empty($lead->sales_pic_id)) {
                $lead->owner_id = $lead->sales_pic_id;
            } elseif (!empty($lead->owner_id) && empty($lead->sales_pic_id)) {
                $lead->sales_pic_id = $lead->owner_id;
            }

            // Enforce PipelineTransitionGuard on existing leads if stage or stage_id is mutated
            if ($lead->exists && ($lead->isDirty('stage') || $lead->isDirty('stage_id'))) {
                $originalStageId = self::normalizeStageId(
                    (string) ($lead->getOriginal('stage_id') ?: $lead->getOriginal('stage'))
                );
                $targetRaw = $lead->isDirty('stage_id')
                    ? (string) $lead->stage_id
                    : (string) $lead->stage;
                $targetStageId = self::normalizeStageId($targetRaw);

                if ($originalStageId !== $targetStageId && !$lead->stageMutationAuthorized) {
                    throw new DomainException(
                        'Direct mutation of Lead stage is forbidden. All stage transitions must go through PipelineTransitionGuard.'
                    );
                }

                $lead->stage_id = $targetStageId;
                $lead->stage = self::normalizeStageUpper($targetStageId);
            } else {
                // Sync stage <-> stage_id for new leads
                if (!empty($lead->stage_id)) {
                    $lead->stage_id = self::normalizeStageId($lead->stage_id);
                    if (empty($lead->stage)) {
                        $lead->stage = self::normalizeStageUpper($lead->stage_id);
                    } else {
                        $lead->stage = strtoupper($lead->stage);
                    }
                } elseif (!empty($lead->stage)) {
                    $lead->stage_id = self::normalizeStageId($lead->stage);
                    $lead->stage = strtoupper($lead->stage);
                } else {
                    $lead->stage_id = 'new_lead';
                    $lead->stage = 'NEW_LEAD';
                }
            }

            // Standardize project_type & priority
            if (!empty($lead->project_type)) {
                $lead->project_type = strtolower($lead->project_type);
            } else {
                $lead->project_type = 'web_mobile_app';
            }

            if (!empty($lead->priority)) {
                $lead->priority = strtolower($lead->priority);
            } else {
                $lead->priority = 'medium';
            }

            if ($lead->win_probability === null) {
                $lead->win_probability = self::DEFAULT_STAGE_PROBABILITIES[$lead->stage_id] ?? 10;
            }

            // Calculate weighted_value
            $lead->weighted_value = round(
                ((float) ($lead->est_value ?? 0)) * (((int) ($lead->win_probability ?? 0)) / 100.0),
                2
            );

            if ($lead->consent && $lead->consent_at === null) {
                $lead->consent_at = now();
            }
        });

        static::saved(function (self $lead) {
            $lead->stageMutationAuthorized = false;
        });

        static::updated(function (self $lead) {
            if ($lead->wasChanged('est_value')) {
                CrmImmutableAuditLog::record(
                    leadId: (int) $lead->id,
                    entityType: 'lead',
                    entityId: (int) $lead->id,
                    fieldName: 'est_value',
                    oldValue: (float) $lead->getOriginal('est_value'),
                    newValue: (float) $lead->est_value,
                    actorId: $lead->owner_id ?? $lead->created_by,
                );
            }
        });
    }

    public static function generateNextCode(): string
    {
        $year = date('Y');
        $prefix = "LD-{$year}-";

        $lastCode = DB::table('crm_leads')
            ->where(function ($q) use ($prefix) {
                $q->where('lead_code', 'like', "{$prefix}%")
                    ->orWhere('code', 'like', "{$prefix}%");
            })
            ->selectRaw("MAX(CAST(SUBSTRING(COALESCE(lead_code, code), 9) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $next = ((int) $lastCode) + 1;
        return sprintf('LD-%s-%04d', $year, $next);
    }

    protected static function newFactory(): LeadFactory
    {
        return LeadFactory::new();
    }

    /**
     * Scope for Policy enforcement: Sales can only view own + team leads.
     */
    public function scopeAccessibleBy(Builder $query, array $userContext): Builder
    {
        $role = strtolower((string) ($userContext['role'] ?? ''));
        $roles = array_map('strtolower', (array) ($userContext['roles'] ?? [$role]));
        $permissions = (array) ($userContext['permissions'] ?? []);

        $unrestrictedRoles = [
            'super_admin',
            'super admin',
            'owner',
            'direktur',
            'sales_manager',
            'sales manager',
            'presales',
            'finance',
            'finance_ar',
            'finance ar specialist',
            'pm',
            'project_manager',
        ];

        if (
            !empty(array_intersect($roles, $unrestrictedRoles))
            || in_array('crm.lead.view_all', $permissions, true)
            || in_array('*', $permissions, true)
        ) {
            return $query;
        }

        $userId = isset($userContext['id']) ? (int) $userContext['id'] : null;
        $teamId = isset($userContext['team_id']) ? (int) $userContext['team_id'] : null;
        $teamMemberIds = array_map('intval', (array) ($userContext['team_member_ids'] ?? []));

        return $query->where(function (Builder $q) use ($userId, $teamId, $teamMemberIds) {
            if ($userId !== null) {
                $q->where('owner_id', $userId)
                    ->orWhere('sales_pic_id', $userId)
                    ->orWhere('created_by', (string) $userId)
                    ->orWhereHas('watchers', fn (Builder $wq) => $wq->where('user_id', $userId));
            }
            if ($teamId !== null) {
                $q->orWhere('team_id', $teamId);
            }
            if (!empty($teamMemberIds)) {
                $q->orWhereIn('owner_id', $teamMemberIds);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'lead_id')->orderBy('start_at', 'asc');
    }

    public function nextActivity(): HasOne
    {
        return $this->hasOne(Activity::class, 'lead_id')
            ->where('status', 'SCHEDULED')
            ->where('start_at', '>=', now())
            ->orderBy('start_at', 'asc');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(Activity::class, 'lead_id')->latestOfMany('start_at');
    }

    public function stageHistories(): HasMany
    {
        return $this->hasMany(LeadStageHistory::class, 'lead_id')->orderBy('changed_at', 'asc');
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(LeadWatcher::class, 'lead_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }

    public function activeQuotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'active_quotation_id');
    }

    public function rabVersions(): HasMany
    {
        return $this->hasMany(RabVersion::class, 'lead_id')->orderBy('version', 'desc');
    }

    public function activeRab(): BelongsTo
    {
        return $this->belongsTo(RabVersion::class, 'active_rab_version_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(RequirementVersion::class, 'lead_id')->orderBy('version', 'desc');
    }

    public function latestRequirement(): HasOne
    {
        return $this->hasOne(RequirementVersion::class, 'lead_id')->latestOfMany('version');
    }

    public function aiRuns(): HasMany
    {
        return $this->hasMany(CrmAiRun::class, 'lead_id')->orderBy('id', 'desc');
    }

    public function topSchemes(): HasMany
    {
        return $this->hasMany(TopScheme::class, 'lead_id')->orderBy('id', 'desc');
    }

    public function activeTopScheme(): HasOne
    {
        return $this->hasOne(TopScheme::class, 'lead_id')->latestOfMany('id');
    }

    public function deliveryReadinessChecks(): HasMany
    {
        return $this->hasMany(DeliveryReadinessCheck::class, 'lead_id')->orderBy('id', 'desc');
    }

    public function latestReadinessCheck(): HasOne
    {
        return $this->hasOne(DeliveryReadinessCheck::class, 'lead_id')->latestOfMany('id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadDocument::class, 'lead_id');
    }

    public function checklistProgress(): HasMany
    {
        return $this->hasMany(LeadChecklistProgress::class, 'lead_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(CrmImmutableAuditLog::class, 'lead_id')->orderBy('id', 'desc');
    }
}
