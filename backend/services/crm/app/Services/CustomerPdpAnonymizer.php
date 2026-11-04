<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use Astacode\Kernel\Services\AuditClient;
use Illuminate\Support\Facades\DB;

final class CustomerPdpAnonymizer
{
    public function __construct(
        private readonly AuditClient $auditClient = new AuditClient()
    ) {}

    /**
     * Anonymize Customer & Contact PII upon UU PDP Right-to-Erasure request.
     * Scrambles sensitive columns and logs immutable audit trail.
     */
    public function anonymize(Customer $customer, ?int $actorId = null, string $reason = 'Permintaan penghapusan data pribadi UU PDP'): Customer
    {
        return DB::transaction(function () use ($customer, $actorId, $reason) {
            $customer->load('contacts');
            $code = $customer->code ?? $customer->customer_code ?? ('ID-' . $customer->id);
            $randomToken = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $oldSummary = [
                'code' => $code,
                'had_npwp' => $customer->npwp !== null,
                'contacts_count' => $customer->contacts->count(),
            ];

            $customer->legal_name = "ANONYMIZED-{$code}-{$randomToken}";
            $customer->brand_name = "ANONYMIZED-{$randomToken}";
            $customer->npwp = null;
            $customer->nib = null;
            $customer->email = "anon-{$customer->id}-" . strtolower($randomToken) . '@anonymized.invalid';
            $customer->phone = '+62800000' . str_pad((string) $customer->id, 5, '0', STR_PAD_LEFT);
            $customer->website = null;
            $customer->billing_address = [
                'redacted' => true,
                'notice' => 'Data dianonimkan sesuai UU Perlindungan Data Pribadi (UU PDP)',
            ];
            $customer->office_address = null;
            $customer->status = 'inactive';
            $customer->is_anonymized = true;
            $customer->anonymized_at = now();
            $customer->anonymized_by = $actorId;
            $customer->save();

            foreach ($customer->contacts as $contact) {
                $cToken = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
                $contact->name = "REDACTED-CONTACT-{$contact->id}-{$cToken}";
                $contact->email = "redacted-{$contact->id}-" . strtolower($cToken) . '@anonymized.invalid';
                $contact->phone_wa = '+62800009' . str_pad((string) $contact->id, 5, '0', STR_PAD_LEFT);
                $contact->phone = $contact->phone_wa;
                $contact->title = 'REDACTED';
                $contact->position = 'REDACTED';
                $contact->notes = null;
                $contact->consent_comm = false;
                $contact->is_anonymized = true;
                $contact->save();
            }

            $this->auditClient->log(
                action: 'customer.pdp_anonymized',
                entityType: 'customer',
                entityId: (string) $customer->id,
                oldValues: $oldSummary,
                newValues: [
                    'is_anonymized' => true,
                    'anonymized_at' => $customer->anonymized_at?->toIso8601String(),
                    'anonymized_by' => $actorId,
                ],
                notes: $reason,
                userId: $actorId ? (string) $actorId : null
            );

            return $customer->fresh(['contacts']);
        });
    }
}

