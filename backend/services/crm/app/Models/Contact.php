<?php

declare(strict_types=1);

namespace App\Models;

use Astacode\Kernel\Support\PhoneNormalizer;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'crm_contacts';

    public const ROLES = [
        'decision_maker',
        'technical_lead',
        'finance_lead',
        'influencer',
    ];

    public const CHANNELS = [
        'whatsapp',
        'email',
        'phone',
        'meet',
    ];

    protected $fillable = [
        'customer_id',
        'name',
        'role',
        'title',
        'position',
        'phone',
        'phone_wa',
        'email',
        'is_primary',
        'consent_comm',
        'consent_at',
        'preferred_channel',
        'department',
        'notes',
        'is_anonymized',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'is_primary' => 'boolean',
        'consent_comm' => 'boolean',
        'consent_at' => 'datetime',
        'is_anonymized' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $contact) {
            // Sync phone_wa <-> phone and normalize to E.164
            $rawPhone = $contact->phone_wa ?? $contact->phone;
            if (!empty($rawPhone) && !$contact->is_anonymized) {
                $normalized = PhoneNormalizer::toE164($rawPhone);
                $contact->phone_wa = $normalized;
                $contact->phone = $normalized;
            } elseif (!empty($rawPhone)) {
                $contact->phone_wa = $rawPhone;
                $contact->phone = $rawPhone;
            }

            // Sync title <-> position
            if (!empty($contact->title) && empty($contact->position)) {
                $contact->position = $contact->title;
            } elseif (!empty($contact->position) && empty($contact->title)) {
                $contact->title = $contact->position;
            }

            // Normalize role
            if (!empty($contact->role)) {
                $contact->role = strtolower($contact->role);
            } else {
                $contact->role = 'decision_maker';
            }

            if ($contact->consent_comm && $contact->consent_at === null) {
                $contact->consent_at = now();
            }

            if (empty($contact->preferred_channel)) {
                $contact->preferred_channel = 'whatsapp';
            }
        });
    }

    protected static function newFactory(): ContactFactory
    {
        return ContactFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

