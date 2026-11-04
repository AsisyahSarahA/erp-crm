<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\CustomerMergeHistory;
use App\Models\CustomFieldValue;
use App\Models\Lead;
use Astacode\Kernel\Services\AuditClient;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

final class CustomerMerger
{
    public function __construct(
        private readonly AuditClient $auditClient = new AuditClient()
    ) {}

    /**
     * Merge $sourceCustomer into $masterCustomer while preserving full history (BR-CRM-09).
     */
    public function merge(
        Customer $masterCustomer,
        Customer $sourceCustomer,
        ?int $actorId = null,
        ?string $reason = null
    ): CustomerMergeHistory {
        if ($masterCustomer->id === $sourceCustomer->id) {
            throw new InvalidArgumentException('Tidak dapat menggabungkan customer dengan dirinya sendiri.');
        }

        return DB::transaction(function () use ($masterCustomer, $sourceCustomer, $actorId, $reason) {
            $sourceCustomer->load(['contacts', 'leads', 'attributions', 'customFieldValues', 'contracts']);
            $masterCustomer->load(['contacts', 'attributions']);

            // 1. Capture complete snapshot before modifying
            $snapshot = [
                'customer' => $sourceCustomer->toArray(),
                'raw_npwp_present' => $sourceCustomer->npwp !== null,
                'contacts' => $sourceCustomer->contacts->toArray(),
                'leads' => $sourceCustomer->leads->toArray(),
                'attributions' => $sourceCustomer->attributions->toArray(),
                'custom_fields' => $sourceCustomer->customFieldValues->toArray(),
                'contracts' => $sourceCustomer->contracts->toArray(),
            ];

            $migratedContactsIds = [];
            $deduplicatedContactsIds = [];

            // 2. Migrate Contacts
            $hasMasterPrimary = $masterCustomer->contacts()->where('is_primary', true)->exists();
            foreach ($sourceCustomer->contacts as $contact) {
                $existingOnMaster = $masterCustomer->contacts()
                    ->where(function ($q) use ($contact) {
                        $phone = $contact->phone_wa ?? $contact->phone;
                        if ($phone) {
                            $q->where('phone_wa', $phone)->orWhere('phone', $phone);
                        }
                        if ($contact->email) {
                            $q->orWhere('email', $contact->email);
                        }
                    })
                    ->first();

                if ($existingOnMaster) {
                    // Enrich master contact if missing fields
                    if (empty($existingOnMaster->email) && !empty($contact->email)) {
                        $existingOnMaster->email = $contact->email;
                    }
                    if (empty($existingOnMaster->title) && !empty($contact->title)) {
                        $existingOnMaster->title = $contact->title;
                    }
                    $existingOnMaster->save();
                    $deduplicatedContactsIds[] = $contact->id;
                    $contact->delete();
                } else {
                    if ($hasMasterPrimary && $contact->is_primary) {
                        $contact->is_primary = false;
                    } elseif (!$hasMasterPrimary && $contact->is_primary) {
                        $hasMasterPrimary = true;
                    }
                    $contact->customer_id = $masterCustomer->id;
                    $contact->save();
                    $migratedContactsIds[] = $contact->id;
                }
            }

            // 3. Migrate Leads
            $leadIds = $sourceCustomer->leads->pluck('id')->all();
            if (!empty($leadIds)) {
                Lead::whereIn('id', $leadIds)->update(['customer_id' => $masterCustomer->id]);
            }

            // 4. Migrate Attributions & Recalculate first_source_id / latest_source_id
            $attributionIds = $sourceCustomer->attributions->pluck('id')->all();
            if (!empty($attributionIds)) {
                CustomerAttribution::whereIn('id', $attributionIds)->update(['customer_id' => $masterCustomer->id]);
            }

            $allAttributions = CustomerAttribution::where('customer_id', $masterCustomer->id)
                ->orderBy('touched_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($allAttributions->isNotEmpty()) {
                $masterCustomer->first_source_id = $allAttributions->first()->source_id;
                $masterCustomer->latest_source_id = $allAttributions->last()->source_id;
            } else {
                if (empty($masterCustomer->first_source_id) && !empty($sourceCustomer->first_source_id)) {
                    $masterCustomer->first_source_id = $sourceCustomer->first_source_id;
                }
                if (!empty($sourceCustomer->latest_source_id)) {
                    $masterCustomer->latest_source_id = $sourceCustomer->latest_source_id;
                }
            }

            // 5. Migrate Custom Field Values
            $migratedCustomFieldIds = [];
            foreach ($sourceCustomer->customFieldValues as $cfVal) {
                $existsOnMaster = CustomFieldValue::where('definition_id', $cfVal->definition_id)
                    ->where('entity_type', 'customer')
                    ->where('entity_id', $masterCustomer->id)
                    ->exists();

                if (!$existsOnMaster) {
                    $cfVal->entity_id = $masterCustomer->id;
                    $cfVal->save();
                    $migratedCustomFieldIds[] = $cfVal->id;
                } else {
                    $cfVal->delete();
                }
            }

            // 6. Migrate Contracts
            $contractIds = $sourceCustomer->contracts->pluck('id')->all();
            if (!empty($contractIds)) {
                Contract::whereIn('id', $contractIds)->update(['customer_id' => $masterCustomer->id]);
            }

            // 7. Enrich Master Customer blank fields from Source Customer
            $enrichableFields = ['brand_name', 'industry', 'size', 'website', 'npwp', 'nib', 'billing_address', 'office_address', 'phone', 'email'];
            foreach ($enrichableFields as $field) {
                if (empty($masterCustomer->{$field}) && !empty($sourceCustomer->{$field})) {
                    $masterCustomer->{$field} = $sourceCustomer->{$field};
                }
            }

            if ($sourceCustomer->status === 'active' && $masterCustomer->status === 'prospect') {
                $masterCustomer->status = 'active';
            }

            // Recalculate master health score
            $masterCustomer->unsetRelation('leads');
            $masterCustomer->calculateHealthScore();
            $masterCustomer->save();

            // 8. Record Merge History
            $summary = [
                'migrated_contacts_ids' => $migratedContactsIds,
                'deduplicated_contacts_ids' => $deduplicatedContactsIds,
                'migrated_leads_ids' => $leadIds,
                'migrated_attributions_ids' => $attributionIds,
                'migrated_custom_fields_ids' => $migratedCustomFieldIds,
                'migrated_contracts_ids' => $contractIds,
            ];

            $mergeHistory = CustomerMergeHistory::create([
                'master_customer_id' => $masterCustomer->id,
                'merged_customer_id' => $sourceCustomer->id,
                'merged_customer_code' => $sourceCustomer->code ?? $sourceCustomer->customer_code ?? 'UNKNOWN',
                'merged_customer_snapshot' => $snapshot,
                'migrated_records_summary' => $summary,
                'reason' => $reason ?? 'Penggabungan duplikat data customer (BR-CRM-09)',
                'merged_by' => $actorId,
                'merged_at' => now(),
            ]);

            // 9. Mark Source Customer as merged and soft-delete
            $sourceCustomer->merged_into_id = $masterCustomer->id;
            $sourceCustomer->status = 'inactive';
            $sourceCustomer->save();
            $sourceCustomer->delete();

            // 10. Audit Log
            $this->auditClient->log(
                action: 'customer.merged',
                entityType: 'customer',
                entityId: (string) $masterCustomer->id,
                oldValues: ['merged_customer_id' => $sourceCustomer->id, 'merged_code' => $mergeHistory->merged_customer_code],
                newValues: ['master_customer_id' => $masterCustomer->id, 'summary' => $summary],
                notes: $reason ?? 'Merged duplicate customer',
                userId: $actorId ? (string) $actorId : null
            );

            return $mergeHistory;
        });
    }
}

