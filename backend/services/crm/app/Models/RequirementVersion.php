<?php

declare(strict_types=1);

namespace App\Models;

use Astacode\Kernel\Outbox\HasOutbox;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequirementVersion extends Model
{
    use HasOutbox;

    protected $table = 'crm_requirements';

    public const STATUSES = [
        'DRAFT',
        'PROCESSING',
        'COMPLETED',
        'FAILED',
    ];

    public const GENERATION_MODES = [
        'FULL',
        'REGENERATE_FEEDBACK',
        'PARTIAL_SECTION',
    ];

    public const DETAIL_LEVELS = [
        'Ringkas',
        'Standar',
        'Rinci',
    ];

    public const FUNCTIONAL_CATEGORIES = [
        'Master Data',
        'Transaksi',
        'Laporan',
        'Dashboard & KPI',
        'Non-Fungsional',
        'Integrasi',
    ];

    public const WBS_LAYERS = [
        'Frontend UI/UX',
        'Backend Core & DB',
        'Integrasi API/Payment Gateway',
        'Mobile',
        'DevOps & Cloud Security',
        'QA & UAT',
    ];

    protected $fillable = [
        'lead_id',
        'version',
        'prompt_text',
        'status',
        'params',
        'generation_mode',
        'feedback_text',
        'target_section',
        'normalized_text',
        'token_chunks_count',
        'pii_redacted',
        'zero_retention',
        'ai_run_id',
        'rab_version_id',
        'ai_output',
        'latency_ms',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'version' => 'integer',
        'params' => 'array',
        'token_chunks_count' => 'integer',
        'pii_redacted' => 'boolean',
        'zero_retention' => 'boolean',
        'ai_run_id' => 'integer',
        'rab_version_id' => 'integer',
        'ai_output' => 'array',
        'latency_ms' => 'integer',
        'created_by' => 'integer',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RequirementFile::class, 'requirement_id');
    }

    public function aiRun(): BelongsTo
    {
        return $this->belongsTo(CrmAiRun::class, 'ai_run_id');
    }

    public function rabVersion(): BelongsTo
    {
        return $this->belongsTo(RabVersion::class, 'rab_version_id');
    }
}

