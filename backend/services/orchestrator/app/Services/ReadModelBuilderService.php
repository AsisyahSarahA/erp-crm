<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ReadModel;

class ReadModelBuilderService
{
    /**
     * Updates consolidated read model projection based on domain event.
     */
    public function project(string $eventName, array $payload, string $eventId): void
    {
        match ($eventName) {
            'lead.captured', 'lead.stage_changed' => $this->projectLead($payload, $eventId),
            'deal.won' => $this->projectDealWon($payload, $eventId),
            'invoice.issued', 'invoice.paid' => $this->projectFinance($eventName, $payload, $eventId),
            default => null,
        };
    }

    private function projectLead(array $payload, string $eventId): void
    {
        $leadId = $payload['lead_id'] ?? $payload['inquiry_id'] ?? 'unknown';

        $model = ReadModel::firstOrNew([
            'model_type' => 'lead_summary',
            'model_key' => (string) $leadId,
        ]);

        $data = $model->data ?? [];
        $data['lead_id'] = $leadId;
        $data['name'] = $payload['name'] ?? $data['name'] ?? null;
        $data['stage'] = $payload['to_stage'] ?? $payload['stage'] ?? $data['stage'] ?? 'new_lead';
        $data['updated_at'] = now()->toIso8601String();

        $model->data = $data;
        $model->version = ($model->version ?? 0) + 1;
        $model->last_event_id = $eventId;
        $model->save();
    }

    private function projectDealWon(array $payload, string $eventId): void
    {
        $dealId = $payload['deal_id'] ?? 'unknown';

        $model = ReadModel::firstOrNew([
            'model_type' => 'deal_summary',
            'model_key' => (string) $dealId,
        ]);

        $model->data = [
            'deal_id' => $dealId,
            'deal_code' => $payload['deal_code'] ?? null,
            'title' => $payload['title'] ?? null,
            'contract_value' => $payload['contract_value'] ?? '0.00',
            'customer_name' => $payload['customer']['legal_name'] ?? null,
            'status' => 'WON',
            'won_at' => $payload['won_at'] ?? now()->toIso8601String(),
        ];
        $model->version = ($model->version ?? 0) + 1;
        $model->last_event_id = $eventId;
        $model->save();
    }

    private function projectFinance(string $eventName, array $payload, string $eventId): void
    {
        $invoiceId = $payload['invoice_id'] ?? 'unknown';

        $model = ReadModel::firstOrNew([
            'model_type' => 'financial_summary',
            'model_key' => (string) $invoiceId,
        ]);

        $data = $model->data ?? [];
        $data['invoice_id'] = $invoiceId;
        $data['amount'] = $payload['amount'] ?? $data['amount'] ?? '0.00';
        $data['status'] = $eventName === 'invoice.paid' ? 'PAID' : 'ISSUED';
        $data['last_updated'] = now()->toIso8601String();

        $model->data = $data;
        $model->version = ($model->version ?? 0) + 1;
        $model->last_event_id = $eventId;
        $model->save();
    }
}

