<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\GoogleAccount;
use App\Models\Lead;
use Astacode\Kernel\Outbox\OutboxEvent;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RejectionReasonSeeder;
use Database\Seeders\StageChecklistSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CrmActivitiesAndGoogleMeetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            PipelineStageSeeder::class,
            RejectionReasonSeeder::class,
            StageChecklistSeeder::class,
        ]);
    }

    private function createBaseLead(array $overrides = []): Lead
    {
        $customer = Customer::factory()->create([
            'legal_name' => 'PT Surya Mahardika Tech',
        ]);

        return Lead::factory()->create(array_merge([
            'customer_id' => $customer->id,
            'title' => 'Pengembangan Sistem ERP & WMS',
            'project_type' => 'erp_custom',
            'priority' => 'high',
            'stage' => 'NEW_LEAD',
            'stage_id' => 'new_lead',
            'est_value' => 150_000_000,
            'owner_id' => 10,
            'sales_pic_id' => 10,
            'sales_pic_name' => 'Budi Santoso',
            'has_next_step' => true,
        ], $overrides));
    }

    /**
     * DoD: Membuat meeting dengan opsi Meet menghasilkan meet_link & undangan (mock) dan tersinkron.
     */
    public function test_dod_create_meeting_with_google_meet_option_generates_meet_link_and_syncs(): void
    {
        $lead = $this->createBaseLead();

        $startAt = now()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0);
        $endAt = $startAt->copy()->addMinutes(45);

        $payload = [
            'type' => 'MEETING',
            'title' => 'Discovery Call & Demo Fitur ERP',
            'notes' => 'Diskusi scope WBS bersama stakeholder dan Tech Lead klien.',
            'start_at' => $startAt->toIso8601String(),
            'end_at' => $endAt->toIso8601String(),
            'create_meet' => true,
            'attendees' => [
                ['name' => 'Budi Santoso', 'email' => 'budi@astacode.id', 'type' => 'INTERNAL'],
                ['name' => 'Pak Hendra (CTO)', 'email' => 'hendra@suryamdp.com', 'type' => 'CLIENT'],
            ],
        ];

        $response = $this->withHeaders([
            'X-User-Id' => '10',
            'X-User-Role' => 'sales',
        ])->postJson("/api/v1/crm/leads/{$lead->id}/activities", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'MEETING')
            ->assertJsonPath('data.title', 'Discovery Call & Demo Fitur ERP')
            ->assertJsonPath('data.status', 'SCHEDULED')
            ->assertJsonPath('data.location', 'Google Meet');

        $meetLink = $response->json('data.meet_link');
        $googleEventId = $response->json('data.google_event_id');

        $this->assertNotEmpty($meetLink, 'Google Meet link harus terbuat.');
        $this->assertStringStartsWith('https://meet.google.com/', $meetLink);
        $this->assertNotEmpty($googleEventId, 'Google Event ID harus tercatat.');

        // Verify lead has_next_step is true
        $lead->refresh();
        $this->assertTrue($lead->has_next_step);

        // Verify database entry
        $this->assertDatabaseHas('crm_activities', [
            'lead_id' => $lead->id,
            'type' => 'MEETING',
            'status' => 'SCHEDULED',
            'google_event_id' => $googleEventId,
        ]);
    }

    /**
     * Test completing an activity requires outcome (FR-CRM-04),
     * and supports auto-creating the next activity.
     */
    public function test_complete_activity_requires_outcome_and_allows_auto_creating_next_activity(): void
    {
        $lead = $this->createBaseLead();

        $activity = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'CALL',
            'title' => 'Follow up pertama via telepon',
            'start_at' => now()->subHours(1),
            'end_at' => now()->subMinutes(45),
            'status' => 'SCHEDULED',
            'created_by' => 10,
        ]);

        // 1. Completing without outcome fails with 422
        $resWithoutOutcome = $this->postJson("/api/v1/crm/activities/{$activity->id}/complete", []);
        $resWithoutOutcome->assertStatus(422)
            ->assertJsonPath('detail', 'Hasil aktivitas (outcome) wajib diisi setelah meeting/aktivitas selesai (FR-CRM-04).');

        // 2. Completing with outcome and auto-creating next activity
        $nextDate = now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0);
        $resWithNext = $this->postJson("/api/v1/crm/activities/{$activity->id}/complete", [
            'outcome' => 'berhasil',
            'outcome_notes' => 'Klien menyetujui discovery call lanjutan untuk perincian modul akuntansi.',
            'next_step' => 'Kirim undangan Discovery Call via Google Meet.',
            'next_activity' => [
                'type' => 'MEETING',
                'title' => 'Discovery Call Lanjutan Akuntansi',
                'start_at' => $nextDate->toIso8601String(),
                'notes' => 'Membahas WBS modul GL dan Pajak.',
            ],
        ]);

        $resWithNext->assertStatus(200)
            ->assertJsonPath('data.activity.status', 'COMPLETED')
            ->assertJsonPath('data.activity.outcome', 'SUCCESS')
            ->assertJsonPath('data.next_activity.type', 'MEETING')
            ->assertJsonPath('data.next_activity.title', 'Discovery Call Lanjutan Akuntansi')
            ->assertJsonPath('data.lead_has_next_step', true);

        // Verify next activity was persisted
        $this->assertDatabaseHas('crm_activities', [
            'lead_id' => $lead->id,
            'type' => 'MEETING',
            'title' => 'Discovery Call Lanjutan Akuntansi',
            'status' => 'SCHEDULED',
        ]);

        $lead->refresh();
        $this->assertTrue($lead->has_next_step);
    }

    /**
     * Test Rule "No Next Step" & Stale (BR-CRM-08):
     * When an activity is completed and there are no other future scheduled activities,
     * lead->has_next_step becomes false.
     */
    public function test_no_next_step_rule_flags_lead_has_next_step_false_when_no_future_activities(): void
    {
        $lead = $this->createBaseLead(['has_next_step' => true]);

        $activity = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'FOLLOW_UP',
            'title' => 'Diskusi Penawaran Harga',
            'start_at' => now()->subMinutes(30),
            'end_at' => now(),
            'status' => 'SCHEDULED',
            'created_by' => 10,
        ]);

        $this->postJson("/api/v1/crm/activities/{$activity->id}/complete", [
            'outcome' => 'tidak_merespons',
            'outcome_notes' => 'PIC belum merespons pesan WhatsApp.',
        ])->assertStatus(200);

        $lead->refresh();
        $this->assertFalse($lead->has_next_step, 'Lead has_next_step harus false bila tidak ada aktivitas terjadwal berikutnya.');
    }

    /**
     * Test Activity Rescheduling.
     */
    public function test_reschedule_activity_updates_schedule_and_keeps_has_next_step_true(): void
    {
        $lead = $this->createBaseLead();

        $activity = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Meeting Demo Solusi',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'status' => 'SCHEDULED',
            'google_event_id' => 'gcal_reschedule_test_123',
            'created_by' => 10,
        ]);

        $newStart = now()->addDays(5)->setHour(11)->setMinute(0)->setSecond(0);
        $newEnd = $newStart->copy()->addMinutes(45);

        $res = $this->postJson("/api/v1/crm/activities/{$activity->id}/reschedule", [
            'start_at' => $newStart->toIso8601String(),
            'end_at' => $newEnd->toIso8601String(),
            'reason' => 'PIC klien sedang dinas luar kota.',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'SCHEDULED')
            ->assertJsonPath('data.outcome', 'RESCHEDULE');

        $activity->refresh();
        $this->assertEquals($newStart->toIso8601String(), $activity->start_at?->toIso8601String());
        $this->assertStringContainsString('PIC klien sedang dinas luar kota.', $activity->notes);

        $lead->refresh();
        $this->assertTrue($lead->has_next_step);
    }

    /**
     * Test /activities/calendar endpoint with date range and scope filtering.
     */
    public function test_activities_calendar_endpoint_returns_events_and_supports_scope(): void
    {
        $lead = $this->createBaseLead();

        $act1 = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Meeting Arsitektur Solusi',
            'start_at' => now()->addDays(1)->setHour(9)->setMinute(0),
            'end_at' => now()->addDays(1)->setHour(10)->setMinute(0),
            'status' => 'SCHEDULED',
            'meet_link' => 'https://meet.google.com/test-cal-1',
            'created_by' => 10,
        ]);

        $act2 = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'CALL',
            'title' => 'Call Konfirmasi PIC',
            'start_at' => now()->addDays(2)->setHour(15)->setMinute(0),
            'end_at' => now()->addDays(2)->setHour(15)->setMinute(30),
            'status' => 'SCHEDULED',
            'created_by' => 99, // Another user
        ]);

        // 1. Calendar endpoint without filter
        $resAll = $this->getJson('/api/v1/crm/activities/calendar?start_date=' . now()->toDateString() . '&end_date=' . now()->addDays(7)->toDateString());
        $resAll->assertStatus(200)
            ->assertJsonPath('data.total_events', 2);

        // 2. Calendar endpoint with scope = mine (User ID = 10)
        $resMine = $this->withHeaders([
            'X-User-Id' => '10',
            'X-User-Role' => 'sales',
        ])->getJson('/api/v1/crm/activities/calendar?scope=mine&start_date=' . now()->toDateString() . '&end_date=' . now()->addDays(7)->toDateString());

        $resMine->assertStatus(200);
        $events = $resMine->json('data.events');
        $this->assertNotEmpty($events);
        $titles = array_column($events, 'title');
        $this->assertContains('Meeting Arsitektur Solusi', $titles);
    }

    /**
     * Test Meeting Recording, AI Meeting Notes, and Push to Requirement (FR-CRM-04).
     */
    public function test_meeting_recording_ai_notes_and_push_to_requirement(): void
    {
        $lead = $this->createBaseLead();

        $activity = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Discovery Meeting Modul ERP',
            'start_at' => now()->subHours(2),
            'end_at' => now()->subHours(1),
            'status' => 'COMPLETED',
            'created_by' => 10,
        ]);

        // 1. Upload recording / transcript
        $transcriptText = "Klien menginginkan modul presensi GPS berbasis foto selfie dan payroll otomatis dengan PPh 21 TER. Database harus terintegrasi dengan accounting.";
        $this->postJson("/api/v1/crm/activities/{$activity->id}/recording", [
            'recording_url' => 'https://storage.astacode.id/recordings/meeting_123.mp4',
            'transcript_text' => $transcriptText,
        ])->assertStatus(200);

        $activity->refresh();
        $this->assertEquals($transcriptText, $activity->transcript_text);
        $this->assertEquals('https://storage.astacode.id/recordings/meeting_123.mp4', $activity->recording_url);

        // 2. Generate AI Meeting Notes
        $resAi = $this->postJson("/api/v1/crm/activities/{$activity->id}/ai-notes", [
            'transcript_text' => $transcriptText,
        ]);
        $resAi->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'activity_id',
                    'ai_summary' => [
                        'executive_summary',
                        'core_needs',
                        'technical_scope',
                        'decisions',
                        'action_items',
                    ],
                ],
            ]);

        // 3. Push to Requirement
        $resPush = $this->postJson("/api/v1/crm/activities/{$activity->id}/push-to-requirement");
        $resPush->assertStatus(200)
            ->assertJsonPath('data.lead_id', $lead->id);

        $lead->refresh();
        $this->assertNotNull($lead->client_responded_at, 'lead->client_responded_at harus terisi saat rangkuman meeting dikirim ke requirement.');
    }

    /**
     * Test Scheduler Reminders Command for H-1 and 1-hour reminders.
     */
    public function test_activity_reminders_command_triggers_h1_and_1hour_reminders(): void
    {
        $lead = $this->createBaseLead();

        // 1. Activity due in 24 hours (H-1 window)
        $actH1 = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Meeting Presentasi MoU',
            'start_at' => now()->addHours(24),
            'end_at' => now()->addHours(25),
            'status' => 'SCHEDULED',
            'meet_link' => 'https://meet.google.com/rem-h1-test',
            'created_by' => 10,
        ]);

        // 2. Activity due in 60 minutes (1-hour window)
        $act1H = Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'CALL',
            'title' => 'Telepon Konfirmasi Direktur',
            'start_at' => now()->addMinutes(60),
            'end_at' => now()->addMinutes(90),
            'status' => 'SCHEDULED',
            'created_by' => 10,
        ]);

        // Run the command
        $exitCode = Artisan::call('crm:activities:reminders');
        $this->assertEquals(0, $exitCode);

        $actH1->refresh();
        $act1H->refresh();

        $this->assertNotNull($actH1->reminder_h1_sent_at, 'reminder_h1_sent_at harus terisi.');
        $this->assertNotNull($act1H->reminder_1h_sent_at, 'reminder_1h_sent_at harus terisi.');

        // Verify outbox events were recorded
        $outboxH1 = OutboxEvent::where('aggregate_type', 'Activity')
            ->where('aggregate_id', (string) $actH1->id)
            ->first();
        $this->assertNotNull($outboxH1);
        $this->assertEquals('activity.reminder_triggered', $outboxH1->event_name);
        $this->assertEquals('H_MINUS_1', $outboxH1->payload['reminder_type']);

        $outbox1H = OutboxEvent::where('aggregate_type', 'Activity')
            ->where('aggregate_id', (string) $act1H->id)
            ->first();
        $this->assertNotNull($outbox1H);
        $this->assertEquals('ONE_HOUR', $outbox1H->payload['reminder_type']);

        // Running command again should be idempotent (no duplicate outbox records)
        Artisan::call('crm:activities:reminders');
        $count = OutboxEvent::where('event_name', 'activity.reminder_triggered')->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test Google Calendar OAuth Connect, Status, Callback, and Disconnect endpoints.
     */
    public function test_google_calendar_oauth_flow_and_status(): void
    {
        // 1. Check status before connecting
        $resInitial = $this->withHeaders(['X-User-Id' => '25'])->getJson('/api/v1/crm/integrations/google/status');
        $resInitial->assertStatus(200)
            ->assertJsonPath('data.is_connected', false);

        // 2. Get auth URL
        $resAuthUrl = $this->withHeaders(['X-User-Id' => '25'])->getJson('/api/v1/crm/integrations/google/auth-url');
        $resAuthUrl->assertStatus(200)
            ->assertJsonStructure(['data' => ['auth_url', 'scope']]);
        $this->assertStringContainsString('accounts.google.com', $resAuthUrl->json('data.auth_url'));

        // 3. Callback mock exchange
        $resCallback = $this->withHeaders(['X-User-Id' => '25'])->postJson('/api/v1/crm/integrations/google/callback', [
            'code' => 'auth_code_mock_xyz',
        ]);
        $resCallback->assertStatus(200)
            ->assertJsonPath('data.is_connected', true);

        $account = GoogleAccount::where('user_id', 25)->first();
        $this->assertNotNull($account);
        $this->assertTrue($account->is_active);

        // 4. Status after connecting
        $resConnected = $this->withHeaders(['X-User-Id' => '25'])->getJson('/api/v1/crm/integrations/google/status');
        $resConnected->assertStatus(200)
            ->assertJsonPath('data.is_connected', true)
            ->assertJsonPath('data.email', $account->email);

        // 5. Disconnect
        $this->withHeaders(['X-User-Id' => '25'])->postJson('/api/v1/crm/integrations/google/disconnect')
            ->assertStatus(200)
            ->assertJsonPath('data.disconnected', true);

        $resDisconnected = $this->withHeaders(['X-User-Id' => '25'])->getJson('/api/v1/crm/integrations/google/status');
        $resDisconnected->assertStatus(200)
            ->assertJsonPath('data.is_connected', false);
    }
}
