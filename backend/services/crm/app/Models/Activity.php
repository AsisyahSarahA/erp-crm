<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'crm_activities';

    public const TYPES = [
        'MEETING',
        'CALL',
        'FOLLOW_UP',
        'EMAIL',
        'WHATSAPP',
        'DEMO',
        'SITE_VISIT',
        'TASK',
        'NOTE',
    ];

    public const STATUSES = [
        'SCHEDULED',
        'COMPLETED',
        'CANCELLED',
        'OVERDUE',
        'RESCHEDULED',
    ];

    public const OUTCOMES = [
        'SUCCESS',
        'RESCHEDULE',
        'NO_RESPONSE',
        'CANCELLED',
    ];

    public const OUTCOME_MAP = [
        'berhasil' => 'SUCCESS',
        'success' => 'SUCCESS',
        'reschedule' => 'RESCHEDULE',
        'rescheduled' => 'RESCHEDULE',
        'tidak_merespons' => 'NO_RESPONSE',
        'tidak merespons' => 'NO_RESPONSE',
        'no_response' => 'NO_RESPONSE',
        'cancelled' => 'CANCELLED',
        'batal' => 'CANCELLED',
    ];

    protected $fillable = [
        'lead_id',
        'customer_id',
        'type',
        'title',
        'notes',
        'start_at',
        'end_at',
        'sla_due_at',
        'status',
        'outcome',
        'outcome_notes',
        'next_step',
        'location',
        'link',
        'google_event_id',
        'meet_link',
        'attendees',
        'attachments',
        'recording_url',
        'transcript_text',
        'ai_summary',
        'reminder_h1_sent_at',
        'reminder_1h_sent_at',
        'created_by',
    ];

    protected $casts = [
        'lead_id' => 'integer',
        'customer_id' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'attendees' => 'array',
        'attachments' => 'array',
        'ai_summary' => 'array',
        'reminder_h1_sent_at' => 'datetime',
        'reminder_1h_sent_at' => 'datetime',
        'created_by' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $activity) {
            if (!empty($activity->type)) {
                $activity->type = strtoupper(trim($activity->type));
            }
            if (!empty($activity->status)) {
                $activity->status = strtoupper(trim($activity->status));
            }
            if (!empty($activity->outcome)) {
                $rawOutcome = strtolower(trim($activity->outcome));
                $activity->outcome = self::OUTCOME_MAP[$rawOutcome] ?? strtoupper($activity->outcome);
            }
        });

        static::saved(function (self $activity) {
            if ($activity->lead_id) {
                self::syncLeadHasNextStep((int) $activity->lead_id);
            }
        });

        static::deleted(function (self $activity) {
            if ($activity->lead_id) {
                self::syncLeadHasNextStep((int) $activity->lead_id);
            }
        });
    }

    public static function syncLeadHasNextStep(int $leadId): void
    {
        $lead = Lead::find($leadId);
        if (!$lead) {
            return;
        }

        $hasNext = self::query()
            ->where('lead_id', $leadId)
            ->where('status', 'SCHEDULED')
            ->where('start_at', '>=', now())
            ->exists();

        if ($lead->has_next_step !== $hasNext) {
            $lead->has_next_step = $hasNext;
            $lead->saveQuietly();
        }
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function isScheduled(): bool
    {
        return $this->status === 'SCHEDULED';
    }

    public function isFuture(): bool
    {
        return $this->isScheduled() && $this->start_at !== null && $this->start_at->isFuture();
    }

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }
}
