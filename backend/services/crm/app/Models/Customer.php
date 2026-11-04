<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\EncryptedNpwpCast;
use App\Models\Casts\FlexibleJsonCast;
use Astacode\Kernel\Support\PhoneNormalizer;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'crm_customers';

    public const TYPES = [
        'perusahaan',
        'perorangan',
        'instansi',
        'startup',
        'umkm',
    ];

    public const STATUSES = [
        'prospect',
        'active',
        'inactive',
        'churned',
        'blacklist',
    ];

    protected $fillable = [
        'code',
        'customer_code',
        'legal_name',
        'brand_name',
        'type',
        'customer_type',
        'industry',
        'size',
        'website',
        'logo_url',
        'npwp',
        'npwp_hash',
        'nib',
        'is_pkp',
        'is_pph23_withholder',
        'npwp_verified_by',
        'npwp_verified_at',
        'billing_address',
        'office_address',
        'phone',
        'email',
        'status',
        'first_source_id',
        'latest_source_id',
        'owner_id',
        'team_id',
        'company_id',
        'health_score',
        'health_breakdown',
        'nps_score',
        'csat_score',
        'account_plan',
        'financial_snapshot',
        'external_snapshots',
        'is_anonymized',
        'anonymized_at',
        'anonymized_by',
        'merged_into_id',
        'created_by',
    ];

    protected $casts = [
        'npwp' => EncryptedNpwpCast::class,
        'billing_address' => FlexibleJsonCast::class,
        'office_address' => FlexibleJsonCast::class,
        'is_pkp' => 'boolean',
        'is_pph23_withholder' => 'boolean',
        'npwp_verified_by' => 'integer',
        'npwp_verified_at' => 'datetime',
        'first_source_id' => 'integer',
        'latest_source_id' => 'integer',
        'owner_id' => 'integer',
        'team_id' => 'integer',
        'company_id' => 'integer',
        'health_score' => 'integer',
        'health_breakdown' => 'array',
        'nps_score' => 'integer',
        'csat_score' => 'float',
        'account_plan' => 'array',
        'financial_snapshot' => 'array',
        'external_snapshots' => 'array',
        'is_anonymized' => 'boolean',
        'anonymized_at' => 'datetime',
        'anonymized_by' => 'integer',
        'merged_into_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $customer) {
            // Sync code <-> customer_code (CUS-NNNN)
            if (empty($customer->code) && !empty($customer->customer_code)) {
                $customer->code = $customer->customer_code;
            } elseif (!empty($customer->code) && empty($customer->customer_code)) {
                $customer->customer_code = $customer->code;
            } elseif (empty($customer->code) && empty($customer->customer_code)) {
                $generated = self::generateNextCode();
                $customer->code = $generated;
                $customer->customer_code = $generated;
            }

            // Sync type <-> customer_type
            if (!empty($customer->type)) {
                $customer->type = strtolower($customer->type);
                $customer->customer_type = strtoupper($customer->type);
            } elseif (!empty($customer->customer_type)) {
                $customer->type = strtolower($customer->customer_type);
                $customer->customer_type = strtoupper($customer->customer_type);
            } else {
                $customer->type = 'perusahaan';
                $customer->customer_type = 'PERUSAHAAN';
            }

            // Normalize status
            if (!empty($customer->status)) {
                $customer->status = strtolower($customer->status);
            } else {
                $customer->status = 'prospect';
            }

            // Normalize phone to E.164 if present
            if (!empty($customer->phone) && !$customer->is_anonymized) {
                $customer->phone = PhoneNormalizer::toE164($customer->phone);
            }

            if (empty($customer->company_id)) {
                $customer->company_id = 1;
            }
        });
    }

    public static function generateNextCode(): string
    {
        $lastCode = DB::table('crm_customers')
            ->selectRaw("MAX(CAST(SUBSTRING(COALESCE(code, customer_code), 5) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $next = ((int) $lastCode) + 1;
        return sprintf('CUS-%04d', $next);
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    /**
     * Scope for Policy enforcement: Sales only sees own + team customers.
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
            'finance',
            'finance_ar',
            'finance ar specialist',
            'legal_counsel',
            'legal counsel',
            'pm',
            'project_manager',
        ];

        if (
            !empty(array_intersect($roles, $unrestrictedRoles))
            || in_array('crm.customer.view_all', $permissions, true)
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
                    ->orWhere('created_by', (string) $userId)
                    ->orWhereHas('leads', fn (Builder $lq) => $lq->where('sales_pic_id', $userId));
            }
            if ($teamId !== null) {
                $q->orWhere('team_id', $teamId);
            }
            if (!empty($teamMemberIds)) {
                $q->orWhereIn('owner_id', $teamMemberIds);
            }
        });
    }

    /**
     * Recalculate Customer Health Score (0-100) per PRD §6.4 FR-CRM-13:
     * - Payment timeliness (Ketepatan bayar): weight 40% (max 40 pts)
     * - Repeat projects (Frekuensi repeat project): weight 20% (max 20 pts)
     * - Recent activity (Aktivitas komunikasi): weight 15% (max 15 pts)
     * - Open tickets (Tiket terbuka): weight 15% (max 15 pts)
     * - NPS / CSAT: weight 10% (max 10 pts)
     */
    public function calculateHealthScore(array $overrides = []): array
    {
        $fin = array_merge($this->financial_snapshot ?? [], $overrides['financials'] ?? []);
        $ext = array_merge($this->external_snapshots ?? [], $overrides['external'] ?? []);

        // 1. Payment Timeliness (Weight 40% -> max 40 points)
        $onTimePaymentRate = (float) ($fin['on_time_payment_rate'] ?? 100.0);
        $overdueInvoicesCount = (int) ($fin['overdue_invoices_count'] ?? 0);
        $dsoDays = (int) ($fin['dso_days'] ?? 14);

        $paymentRatio = max(0.0, min(100.0, $onTimePaymentRate)) / 100.0;
        $paymentScore = $paymentRatio * 40.0;
        if ($overdueInvoicesCount > 0) {
            $paymentScore -= min(20.0, $overdueInvoicesCount * 7.0);
        }
        if ($dsoDays > 30) {
            $paymentScore -= min(10.0, (($dsoDays - 30) / 10.0) * 2.5);
        }
        $paymentScore = max(0.0, min(40.0, round($paymentScore, 2)));

        // 2. Repeat Projects (Weight 20% -> max 20 points)
        $repeatCount = $overrides['repeat_projects_count']
            ?? $ext['projects_count']
            ?? ($this->relationLoaded('leads')
                ? $this->leads->whereIn('stage', ['DEAL_WON', 'DEAL', 'deal'])->count()
                : $this->leads()->whereIn('stage', ['DEAL_WON', 'DEAL', 'deal'])->count());

        $repeatScore = match (true) {
            $repeatCount >= 3 => 20.0,
            $repeatCount === 2 => 17.0,
            $repeatCount === 1 => 14.0,
            default => 10.0, // Prospect baseline
        };

        // 3. Activity Recency (Weight 15% -> max 15 points)
        $daysSinceActivity = (int) ($overrides['days_since_last_activity'] ?? $ext['days_since_last_activity'] ?? 3);
        $activityScore = match (true) {
            $daysSinceActivity <= 7 => 15.0,
            $daysSinceActivity <= 14 => 12.0,
            $daysSinceActivity <= 30 => 8.0,
            $daysSinceActivity <= 60 => 4.0,
            default => 1.0,
        };

        // 4. Open Tickets (Weight 15% -> max 15 points)
        $openTicketsCount = (int) ($overrides['open_tickets_count'] ?? $ext['open_tickets_count'] ?? 0);
        $criticalTicketsCount = (int) ($overrides['critical_tickets_count'] ?? $ext['critical_tickets_count'] ?? 0);
        $ticketPenalty = ($openTicketsCount * 3.0) + ($criticalTicketsCount * 5.0);
        $ticketScore = max(0.0, min(15.0, 15.0 - $ticketPenalty));

        // 5. NPS / CSAT (Weight 10% -> max 10 points)
        $csat = $overrides['csat_score'] ?? $this->csat_score; // 1.0 - 5.0
        $nps = $overrides['nps_score'] ?? $this->nps_score;    // -100 to 100 or 0 to 10
        if ($csat !== null) {
            $npsCsatScore = max(0.0, min(10.0, ((float) $csat / 5.0) * 10.0));
        } elseif ($nps !== null) {
            $npsFloat = (float) $nps;
            if ($npsFloat >= 0 && $npsFloat <= 10) {
                $npsCsatScore = $npsFloat;
            } else {
                // -100..100 normalized to 0..10
                $npsCsatScore = max(0.0, min(10.0, (($npsFloat + 100.0) / 200.0) * 10.0));
            }
        } else {
            $npsCsatScore = 8.0; // Default neutral-good when no survey yet
        }

        $totalScore = (int) round(max(0.0, min(100.0, $paymentScore + $repeatScore + $activityScore + $ticketScore + $npsCsatScore)));

        $breakdown = [
            'payment_timeliness' => [
                'score' => round($paymentScore, 2),
                'max_score' => 40,
                'weight_pct' => 40,
                'on_time_rate_pct' => $onTimePaymentRate,
                'overdue_invoices_count' => $overdueInvoicesCount,
                'dso_days' => $dsoDays,
            ],
            'repeat_projects' => [
                'score' => round($repeatScore, 2),
                'max_score' => 20,
                'weight_pct' => 20,
                'projects_count' => (int) $repeatCount,
            ],
            'activity_engagement' => [
                'score' => round($activityScore, 2),
                'max_score' => 15,
                'weight_pct' => 15,
                'days_since_last_activity' => $daysSinceActivity,
            ],
            'open_tickets' => [
                'score' => round($ticketScore, 2),
                'max_score' => 15,
                'weight_pct' => 15,
                'open_tickets_count' => $openTicketsCount,
                'critical_tickets_count' => $criticalTicketsCount,
            ],
            'nps_csat' => [
                'score' => round($npsCsatScore, 2),
                'max_score' => 10,
                'weight_pct' => 10,
                'nps_score' => $nps,
                'csat_score' => $csat,
            ],
            'total_score' => $totalScore,
            'status_label' => match (true) {
                $totalScore >= 75 => 'HEALTHY',
                $totalScore >= 50 => 'AT_RISK',
                default => 'CRITICAL',
            },
            'calculated_at' => now()->toIso8601String(),
        ];

        $this->health_score = $totalScore;
        $this->health_breakdown = $breakdown;

        return $breakdown;
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id');
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(Contact::class, 'customer_id')->where('is_primary', true);
    }

    public function firstSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'first_source_id');
    }

    public function latestSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'latest_source_id');
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(CustomerAttribution::class, 'customer_id')->orderBy('touched_at');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'customer_id');
    }

    public function quotations(): HasManyThrough
    {
        return $this->hasManyThrough(Quotation::class, Lead::class, 'customer_id', 'lead_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'customer_id');
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'entity_id')
            ->where('entity_type', 'customer');
    }

    public function mergeHistories(): HasMany
    {
        return $this->hasMany(CustomerMergeHistory::class, 'master_customer_id');
    }
}

