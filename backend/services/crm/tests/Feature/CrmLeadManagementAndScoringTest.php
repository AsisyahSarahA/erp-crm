<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Support\UserContext;
use Astacode\Kernel\Outbox\OutboxEvent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrmLeadManagementAndScoringTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*/internal/audit-logs' => Http::response(['status' => 'recorded'], 201),
        ]);
    }

    private function withInternalUser(array $user): self
    {
        return $this->withHeaders([
            'X-Internal-User' => UserContext::encodeHeader($user),
        ]);
    }

    public function test_ac_crm_01_lead_creation_with_instagram_source_creates_customer_attribution_and_followup_activity(): void
    {
        $igSource = LeadSource::factory()->create([
            'code' => 'INSTAGRAM',
            'name' => 'Instagram',
            'category' => 'ORGANIC_SOCIAL',
            'channel_group' => 'ORGANIC_SOCIAL',
        ]);

        $response = $this->withInternalUser([
            'id' => 10,
            'name' => 'Citra Sales Executive',
            'role' => 'sales',
            'team_id' => 1,
        ])->postJson('/api/v1/crm/leads', [
            'title' => 'Aplikasi HRD 500 Karyawan & Mobile ESS',
            'project_type' => 'erp_custom',
            'priority' => 'high',
            'est_value' => 175000000,
            'source' => 'INSTAGRAM',
            'sub_source' => 'Instagram Direct Message',
            'utm' => [
                'source' => 'instagram',
                'medium' => 'dm',
                'campaign' => 'q4_enterprise',
            ],
            'industry' => 'TECH_SAAS',
            'company_name' => 'PT Cipta Solusi Nusantara',
            'pic_name' => 'Dharma Wangsa',
            'phone_wa' => '0812-8877-6655',
            'email' => 'dharma@ciptasolusi.co.id',
            'tags' => ['erp', 'hrd', 'urgent'],
        ]);

        $response->assertCreated();
        $data = $response->json('data');

        // 1. Verify Lead code and stage
        $this->assertMatchesRegularExpression('/^LD-\d{4}-\d{4}$/', $data['code']);
        $this->assertEquals('NEW_LEAD', $data['stage']);
        $this->assertEquals('new_lead', $data['stage_id']);
        $this->assertEquals(175000000, $data['est_value']);
        $this->assertEquals('HIGH', $data['priority']);
        $this->assertNotEmpty($data['sales_pic_name']);

        // 2. Verify Customer was automatically created with attribution "Instagram"
        $customerId = $data['customer']['id'];
        $customer = Customer::find($customerId);
        $this->assertNotNull($customer);
        $this->assertEquals('PT Cipta Solusi Nusantara', $customer->legal_name);
        $this->assertEquals('+6281288776655', $customer->phone);
        $this->assertEquals('prospect', $customer->status);
        $this->assertEquals($igSource->id, $customer->first_source_id);

        $this->assertDatabaseHas('crm_customer_attributions', [
            'customer_id' => $customerId,
            'source_id' => $igSource->id,
            'touch_type' => 'first_touch',
            'utm_source' => 'instagram',
            'utm_campaign' => 'q4_enterprise',
        ]);

        // 3. Verify Activity "Follow up pertama" with SLA ≤ 30 menit was created
        $activity = Activity::where('lead_id', $data['id'])->first();
        $this->assertNotNull($activity);
        $this->assertEquals('FOLLOW_UP', $activity->type);
        $this->assertStringContainsString('Follow up pertama', $activity->title);
        $this->assertStringContainsString('30 menit', $activity->title);
        $this->assertEquals('SCHEDULED', $activity->status);
        $this->assertNotNull($activity->sla_due_at);
        $this->assertLessThanOrEqual(31, now()->diffInMinutes($activity->sla_due_at));

        // 4. Verify Outbox Event `lead.created` was emitted
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'lead.created',
            'aggregate_type' => 'Lead',
            'aggregate_id' => (string) $data['id'],
        ]);
    }

    public function test_lead_validation_rules_and_marketing_channel_email_exception(): void
    {
        // 1. Title is required
        $this->postJson('/api/v1/crm/leads', [
            'pic_name' => 'Budi Santoso',
            'phone_wa' => '+6281234567890',
            'est_value' => 50000000,
        ])->assertStatus(422)
            ->assertJsonPath('invalid_params.title.0', 'The title field is required.');

        // 2. PIC name must be at least 2 characters
        $this->postJson('/api/v1/crm/leads', [
            'title' => 'Portal B2B',
            'pic_name' => 'A',
            'phone_wa' => '+6281234567890',
            'est_value' => 50000000,
        ])->assertStatus(422)
            ->assertJsonPath('detail', 'Nama Kontak (PIC) wajib diisi minimal 2 karakter.');

        // 3. Manual source requires WhatsApp number
        $this->postJson('/api/v1/crm/leads', [
            'title' => 'Portal B2B',
            'pic_name' => 'Budi Santoso',
            'source' => 'WEBSITE',
            'email' => 'budi@example.com',
            'est_value' => 50000000,
        ])->assertStatus(422)
            ->assertJsonPath('detail', 'Nomor WhatsApp wajib diisi dalam format Indonesia (+628...).');

        // 4. Marketing source allows Email only until Follow Up
        $mktResp = $this->postJson('/api/v1/crm/leads', [
            'title' => 'Inquiry E-Book ERP dari Kampanye TikTok Marketing',
            'pic_name' => 'Dian Marketing',
            'source' => 'TIKTOK',
            'email' => 'dian.tiktok@example.com',
            'est_value' => 60000000,
            'company_name' => 'PT TikTok Retailer',
        ]);
        $mktResp->assertCreated();
        $this->assertEquals('dian.tiktok@example.com', $mktResp->json('data.contact.email'));
        $this->assertNull($mktResp->json('data.contact.phone_wa'));
    }

    public function test_lead_scoring_algorithm_hot_warm_cold_labels(): void
    {
        // High fit + Hot priority + Enterprise budget + Referral source = HOT Lead (score >= 70)
        $hotResp = $this->postJson('/api/v1/crm/leads', [
            'title' => 'Core Banking & Fintech Payment Gateway',
            'project_type' => 'fintech',
            'priority' => 'hot',
            'est_value' => 350000000,
            'source' => 'REFERRAL',
            'industry' => 'FINANCE_BANKING',
            'company_name' => 'PT Bank Digital Sejahtera',
            'pic_name' => 'Direktur IT',
            'phone_wa' => '081199887766',
            'email' => 'it-dir@bankdigital.co.id',
            'target_closing' => now()->addDays(20)->toDateString(),
        ]);
        $hotResp->assertCreated();
        $this->assertGreaterThanOrEqual(70, $hotResp->json('data.score'));
        $this->assertEquals('HOT', $hotResp->json('data.score_label'));

        // Low fit + Low priority + Small budget + Cold source = COLD Lead (score < 40)
        $coldResp = $this->postJson('/api/v1/crm/leads', [
            'title' => 'Landing Page Profil Usaha Sablon',
            'project_type' => 'web_mobile_app',
            'priority' => 'low',
            'est_value' => 15000000,
            'source' => 'OTHER',
            'industry' => 'OTHER',
            'company_name' => 'Perorangan',
            'pic_name' => 'Joko',
            'phone_wa' => '085711223344',
            'email' => 'joko@gmail.com',
        ]);
        $coldResp->assertCreated();
        $this->assertLessThan(50, $coldResp->json('data.score'));
    }

    public function test_auto_assign_service_routes_enterprise_deals_to_sales_manager_and_standard_via_round_robin(): void
    {
        // 1. Enterprise deal (>= 300jt) -> Bambang Sales Manager (ID 1)
        $enterpriseLead = $this->postJson('/api/v1/crm/leads', [
            'title' => 'ERP Multi-Nasional 10 Pabrik',
            'est_value' => 450000000,
            'company_name' => 'PT Multi Corp',
            'pic_name' => 'Bambang GM',
            'phone_wa' => '081233445566',
        ]);
        $enterpriseLead->assertCreated();
        $this->assertEquals(1, $enterpriseLead->json('data.owner_id'));
        $this->assertEquals('Bambang Sales Manager', $enterpriseLead->json('data.sales_pic_name'));

        // 2. Standard deal (< 300jt) -> Round-robin pool (starts with Citra Sales Executive ID 10)
        $standardLead1 = $this->postJson('/api/v1/crm/leads', [
            'title' => 'Aplikasi POS Cafe Resto',
            'est_value' => 50000000,
            'company_name' => 'Cafe Kenangan',
            'pic_name' => 'Rina Owner',
            'phone_wa' => '081299112233',
        ]);
        $standardLead1->assertCreated();
        $this->assertEquals(10, $standardLead1->json('data.owner_id'));
        $this->assertEquals('Citra Sales Executive', $standardLead1->json('data.sales_pic_name'));

        // Next standard lead -> next rep in pool (Dedi Sales Representative ID 11)
        $standardLead2 = $this->postJson('/api/v1/crm/leads', [
            'title' => 'Website Profil Klinik Pratama',
            'est_value' => 40000000,
            'company_name' => 'Klinik Pratama',
            'pic_name' => 'dr. Anton',
            'phone_wa' => '081388776655',
        ]);
        $standardLead2->assertCreated();
        $this->assertEquals(11, $standardLead2->json('data.owner_id'));
        $this->assertEquals('Dedi Sales Representative', $standardLead2->json('data.sales_pic_name'));
    }

    public function test_public_lead_capture_with_utm_honeypot_and_event_emission(): void
    {
        // 1. Bot filled honeypot -> silently handled without creating lead
        $initialLeadsCount = Lead::count();
        $botResp = $this->postJson('/public/lead-capture', [
            'name' => 'Spam Bot',
            'phone' => '081234567890',
            '_hp_bio' => 'http://spam-link.ru',
        ]);
        $botResp->assertOk();
        $this->assertEquals($initialLeadsCount, Lead::count());

        // 2. Genuine website submission with UTM parameters
        $captureResp = $this->postJson('/public/lead-capture', [
            'name' => 'Faisal Rahman',
            'company_name' => 'PT Logistik Maju Sejahtera',
            'project_title' => 'Sistem Pelacakan Truk GPS & Fleet Management',
            'phone' => '0812 5566 7788',
            'email' => 'faisal@logistikmaju.id',
            'budget' => 95000000,
            'notes' => 'Membutuhkan integrasi sensor suhu dan API GPS tracker',
            'utm_source' => 'google_ads',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'fleet_iot_2026',
        ]);

        $captureResp->assertCreated()
            ->assertJsonPath('data.status', 'NEW_LEAD');

        $capturedCode = $captureResp->json('data.lead_code');
        $lead = Lead::where('code', $capturedCode)->orWhere('lead_code', $capturedCode)->first();
        $this->assertNotNull($lead);
        $this->assertEquals('NEW_LEAD', $lead->stage);
        $this->assertEquals('google_ads', $lead->utm['source'] ?? null);
        $this->assertEquals('fleet_iot_2026', $lead->utm['campaign'] ?? null);

        // Verify outbox event `lead.captured`
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'lead.captured',
            'aggregate_id' => (string) $lead->id,
        ]);
    }

    public function test_stale_lead_command_flags_stale_since_and_emits_warning_and_escalation_events(): void
    {
        // 1. Fresh lead (1 day old) with scheduled future activity -> Not stale, has next step
        $freshLead = Lead::factory()->create([
            'stage' => 'NEW_LEAD',
            'stage_id' => 'new_lead',
            'days_in_stage' => 1,
            'is_stale' => false,
            'created_at' => now()->subDay(),
        ]);
        Activity::factory()->create([
            'lead_id' => $freshLead->id,
            'status' => 'SCHEDULED',
            'start_at' => now()->addDay(),
        ]);

        // 2. Stale lead (6 days old) without future activity -> Stale warning (≥ 5 days)
        $staleLead = Lead::factory()->create([
            'stage' => 'FOLLOW_UP',
            'stage_id' => 'follow_up',
            'days_in_stage' => 6,
            'is_stale' => false,
            'sales_pic_id' => 10,
            'created_at' => now()->subDays(6),
        ]);

        // 3. Escalated lead (12 days old) without future activity -> Stale escalation (≥ 10 days)
        $escalatedLead = Lead::factory()->create([
            'stage' => 'REQUIREMENT',
            'stage_id' => 'requirement',
            'days_in_stage' => 12,
            'is_stale' => false,
            'sales_pic_id' => 11,
            'created_at' => now()->subDays(12),
        ]);

        // Run artisan command `crm:leads:stale-check`
        $exitCode = Artisan::call('crm:leads:stale-check');
        $this->assertEquals(0, $exitCode);

        // Assert fresh lead
        $freshLead->refresh();
        $this->assertFalse($freshLead->is_stale);
        $this->assertTrue($freshLead->has_next_step);

        // Assert stale lead (≥ 5 days)
        $staleLead->refresh();
        $this->assertTrue($staleLead->is_stale);
        $this->assertFalse($staleLead->has_next_step);
        $this->assertNotNull($staleLead->stale_since);
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'lead.stale_warning',
            'aggregate_id' => (string) $staleLead->id,
        ]);

        // Assert escalated lead (≥ 10 days)
        $escalatedLead->refresh();
        $this->assertTrue($escalatedLead->is_stale);
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'lead.stale_escalation',
            'aggregate_id' => (string) $escalatedLead->id,
        ]);
    }

    public function test_reopen_rejected_lead_by_sales_manager_requires_reason_and_transitions_to_follow_up(): void
    {
        $lostLead = Lead::factory()->create([
            'stage' => 'REJECT',
            'stage_id' => 'reject',
            'reject_reason_code' => 'PRICE_LOST',
            'reject_notes' => 'Klien merasa harga kemahalan.',
            'sales_pic_id' => 10,
            'owner_id' => 10,
            'team_id' => 1,
        ]);

        // 1. Sales rep cannot reopen lost lead -> 403 Forbidden
        $this->withInternalUser([
            'id' => 10,
            'role' => 'sales',
        ])->postJson("/api/v1/crm/leads/{$lostLead->id}/reopen", [
            'reason' => 'Klien ingin negosiasi ulang',
        ])->assertStatus(403);

        // 2. Sales Manager without reason -> 422
        $this->withInternalUser([
            'id' => 1,
            'name' => 'Bambang Sales Manager',
            'role' => 'sales_manager',
        ])->postJson("/api/v1/crm/leads/{$lostLead->id}/reopen", [
            'reason' => '',
        ])->assertStatus(422);

        // 3. Sales Manager with valid reason -> 200 OK & stage moves to follow_up
        $reopenResp = $this->withInternalUser([
            'id' => 1,
            'name' => 'Bambang Sales Manager',
            'role' => 'sales_manager',
        ])->postJson("/api/v1/crm/leads/{$lostLead->id}/reopen", [
            'reason' => 'Klien mengontak kembali setelah restrukturisasi budget baru',
        ]);

        $reopenResp->assertOk()
            ->assertJsonPath('data.stage', 'FOLLOW_UP')
            ->assertJsonPath('data.stage_id', 'follow_up')
            ->assertJsonPath('data.reopened_by', 1);

        $lostLead->refresh();
        $this->assertEquals('follow_up', $lostLead->stage_id);
        $this->assertNotNull($lostLead->reopened_at);

        // Verify stage history
        $this->assertDatabaseHas('crm_lead_stage_histories', [
            'lead_id' => $lostLead->id,
            'from_stage' => 'reject',
            'to_stage' => 'follow_up',
        ]);

        // Verify outbox event `lead.reopened`
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'lead.reopened',
            'aggregate_id' => (string) $lostLead->id,
        ]);
    }

    public function test_bulk_operations_and_saved_views(): void
    {
        $leads = Lead::factory()->count(3)->create(['owner_id' => 10, 'sales_pic_id' => 10]);
        $leadIds = $leads->pluck('id')->all();

        // 1. Bulk Assign to Rep 12
        $assignResp = $this->withInternalUser([
            'id' => 1,
            'role' => 'sales_manager',
        ])->postJson('/api/v1/crm/leads/bulk-assign', [
            'lead_ids' => $leadIds,
            'owner_id' => 12,
            'sales_pic_name' => 'Eko Senior Account Executive',
        ]);
        $assignResp->assertOk()
            ->assertJsonPath('data.updated_count', 3);

        $this->assertEquals(12, Lead::find($leadIds[0])->owner_id);

        // 2. Bulk Tags
        $tagsResp = $this->postJson('/api/v1/crm/leads/bulk-tags', [
            'lead_ids' => $leadIds,
            'tags' => ['q4_priority', 'telecom'],
        ]);
        $tagsResp->assertOk()
            ->assertJsonPath('data.updated_count', 3);

        // 3. Saved Views CRUD
        $viewResp = $this->withInternalUser(['id' => 10])->postJson('/api/v1/crm/saved-views', [
            'name' => 'My Hot Leads',
            'module' => 'crm_leads',
            'filters' => ['priority' => 'hot', 'is_stale' => false],
            'is_default' => true,
        ]);
        $viewResp->assertCreated();
        $viewId = $viewResp->json('data.id');

        $listViews = $this->withInternalUser(['id' => 10])->getJson('/api/v1/crm/saved-views?module=crm_leads');
        $listViews->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'My Hot Leads');

        $this->withInternalUser(['id' => 10])->deleteJson("/api/v1/crm/saved-views/{$viewId}")->assertOk();

        // 4. CSV Export
        $exportResp = $this->get('/api/v1/crm/leads/export');
        $exportResp->assertOk();
        $this->assertStringContainsString('text/csv', $exportResp->headers->get('Content-Type'));
    }
}
