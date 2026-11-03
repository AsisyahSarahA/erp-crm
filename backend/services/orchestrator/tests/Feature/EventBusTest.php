<?php

namespace Tests\Feature;

use App\Models\EventLog;
use App\Models\ProcessedEvent;
use App\Models\Subscription;
use Tests\TestCase;

class EventBusTest extends TestCase
{
    public function test_ingest_event_and_persist_to_event_log(): void
    {
        $payload = [
            'inquiry_id' => 'INQ-1001',
            'name' => 'Budi Client',
            'contact_channel' => 'whatsapp',
            'contact_value' => '+628123456789',
            'source' => 'web_form',
        ];

        $response = $this->postJson('/internal/events', [
            'event_name' => 'lead.captured',
            'payload' => $payload,
            'producer_service' => 'marketing-service',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.event_name', 'lead.captured')
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('event_logs', [
            'event_name' => 'lead.captured',
            'producer_service' => 'marketing-service',
        ]);
    }

    public function test_idempotency_prevents_duplicate_event_processing(): void
    {
        $eventId = 'evt-unique-test-999';
        $payload = [
            'inquiry_id' => 'INQ-1002',
            'name' => 'Dewi Client',
            'contact_channel' => 'email',
            'contact_value' => 'dewi@example.com',
            'source' => 'web_form',
        ];

        // First delivery
        $res1 = $this->postJson('/internal/events', [
            'event_id' => $eventId,
            'event_name' => 'lead.captured',
            'payload' => $payload,
            'producer_service' => 'marketing-service',
        ]);
        $res1->assertStatus(200);

        // Second delivery with identical event_id
        $res2 = $this->postJson('/internal/events', [
            'event_id' => $eventId,
            'event_name' => 'lead.captured',
            'payload' => $payload,
            'producer_service' => 'marketing-service',
        ]);
        $res2->assertStatus(200);

        // Only one record should exist in event_logs
        $this->assertEquals(1, EventLog::where('event_id', $eventId)->count());
    }

    public function test_event_dispatch_records_processed_event_idempotency(): void
    {
        // Add stub subscription
        Subscription::create([
            'event_name' => 'invoice.issued',
            'consumer_service' => 'crm-service',
            'target_endpoint' => 'stub://crm/events/invoice-issued',
            'is_active' => true,
            'max_retries' => 3,
        ]);

        $payload = [
            'invoice_id' => 'INV-001',
            'invoice_number' => 'INV/2026/11/0001',
            'customer_id' => 'CUS-001',
            'total_amount' => 50000000,
            'due_date' => '2026-11-30',
        ];

        $response = $this->postJson('/internal/events', [
            'event_id' => 'evt-inv-101',
            'event_name' => 'invoice.issued',
            'payload' => $payload,
            'producer_service' => 'finance-service',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('processed_events', [
            'event_id' => 'evt-inv-101',
            'consumer_name' => 'crm-service',
            'status' => 'SUCCESS',
        ]);
    }

    public function test_dead_letter_and_replay_workflow(): void
    {
        $eventLog = EventLog::create([
            'event_id' => 'evt-dead-letter-001',
            'event_name' => 'invoice.issued',
            'producer_service' => 'finance-service',
            'payload' => [
                'invoice_id' => 'INV-999',
                'invoice_number' => 'INV/2026/11/0999',
                'customer_id' => 'CUS-999',
                'total_amount' => 10000000,
                'due_date' => '2026-12-01',
            ],
            'status' => 'dead_letter',
            'attempts' => 5,
            'error_message' => 'Connection timeout to external webhook',
            'received_at' => now(),
        ]);

        $this->assertDatabaseHas('event_logs', [
            'id' => $eventLog->id,
            'status' => 'dead_letter',
        ]);

        // Replay endpoint
        $replayResponse = $this->postJson("/api/events/{$eventLog->id}/replay");
        $replayResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        $eventLog->refresh();
        $this->assertEquals('completed', $eventLog->status);
    }
}

