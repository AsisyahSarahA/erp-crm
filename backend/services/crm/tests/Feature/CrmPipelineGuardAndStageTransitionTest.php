<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\CrmImmutableAuditLog;
use App\Models\Customer;
use App\Models\DeliveryReadinessCheck;
use App\Models\Lead;
use App\Models\LeadDocument;
use App\Models\Quotation;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Models\StageChecklist;
use App\Models\TopMilestone;
use App\Models\TopScheme;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RejectionReasonSeeder;
use Database\Seeders\StageChecklistSeeder;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

class CrmPipelineGuardAndStageTransitionTest extends TestCase
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
            'legal_name' => 'PT Nusantara Digital Solusi',
        ]);

        return Lead::factory()->create(array_merge([
            'customer_id' => $customer->id,
            'title' => 'Implementasi ERP Modul Keuangan & HRIS',
            'project_type' => 'erp_custom',
            'priority' => 'high',
            'stage' => 'NEW_LEAD',
            'stage_id' => 'new_lead',
            'est_value' => 100_000_000,
            'owner_id' => 10,
            'sales_pic_id' => 10,
            'sales_pic_name' => 'Budi Santoso',
            'has_next_step' => true,
        ], $overrides));
    }

    /**
     * Test full happy path forward transitions from NEW_LEAD -> FOLLOW_UP -> REQUIREMENT -> QUOTATION -> NEGOTIATION -> DEAL
     * verifying probabilities (10 -> 20 -> 35 -> 55 -> 75 -> 100), weighted_value, stage history, audit log, and outbox event.
     */
    public function test_full_valid_pipeline_transitions_from_new_lead_to_deal_and_probabilities(): void
    {
        $lead = $this->createBaseLead(['est_value' => 200_000_000]);
        $this->assertEquals(10, $lead->win_probability);
        $this->assertEquals(20_000_000.0, $lead->weighted_value);

        // 1. NEW_LEAD -> FOLLOW_UP (requires PIC + initial contact activity)
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'FOLLOW_UP',
            'title' => 'Kontak awal WhatsApp <= 30 menit',
            'start_at' => now(),
            'status' => 'COMPLETED',
            'outcome' => 'Klien merespons positif, setuju jadwal Discovery Call.',
        ]);

        $res1 = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'follow_up',
        ]);
        $res1->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'follow_up')
            ->assertJsonPath('data.win_probability', 20)
            ->assertJsonPath('data.weighted_value', 40000000);

        // 2. FOLLOW_UP -> REQUIREMENT (requires client response + discovery call scheduled/recorded)
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Discovery Call & Penggalian Kebutuhan',
            'start_at' => now()->addHours(2),
            'status' => 'SCHEDULED',
        ]);

        $res2 = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'requirement',
        ]);
        $res2->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'requirement')
            ->assertJsonPath('data.win_probability', 35)
            ->assertJsonPath('data.weighted_value', 70000000);

        // 3. REQUIREMENT -> QUOTATION (requires RAB >= 1 item, total > 0, WBS completed)
        $rab = RabVersion::create([
            'lead_id' => $lead->id,
            'version' => 1,
            'status' => 'APPROVED',
            'total_mandays' => 45,
            'duration_weeks' => 12,
            'subtotal' => 180_000_000,
            'dpp' => 180_000_000,
            'vat_rate' => 11,
            'vat_amount' => 19_800_000,
            'total' => 199_800_000,
            'hpp_internal' => 95_000_000,
            'margin_pct' => 47.22,
            'margin_status' => 'GREEN',
            'wbs_completed' => true,
        ]);
        RabItem::create([
            'rab_version_id' => $rab->id,
            'lead_id' => $lead->id,
            'category' => 'Core ERP',
            'layer' => 'BACKEND',
            'name' => 'Modul General Ledger & Pajak',
            'role_code' => 'BACKEND_MID',
            'mandays' => 25,
            'daily_rate_sell' => 2_000_000,
            'daily_rate_cost' => 1_000_000,
            'subtotal_sell' => 50_000_000,
            'subtotal_cost' => 25_000_000,
        ]);

        $res3 = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
        ]);
        $res3->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'quotation')
            ->assertJsonPath('data.win_probability', 55)
            ->assertJsonPath('data.weighted_value', 110000000);

        // 4. QUOTATION -> NEGOTIATION (requires Quotation & draft MoU sent / status SENT)
        $quotation = Quotation::create([
            'lead_id' => $lead->id,
            'quotation_number' => 'QT-2026-0001',
            'version' => 1,
            'status' => 'SENT',
            'subtotal' => 180_000_000,
            'dpp' => 180_000_000,
            'vat_rate' => 11,
            'vat_amount' => 19_800_000,
            'total' => 200_000_000,
            'hpp_internal' => 95_000_000,
            'margin_pct' => 47.22,
        ]);

        $res4 = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'negotiation',
        ]);
        $res4->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'negotiation')
            ->assertJsonPath('data.win_probability', 75)
            ->assertJsonPath('data.weighted_value', 150000000);

        // 5. NEGOTIATION -> DEAL (requires Quotation ACCEPTED + TOP 100% + RAB valid + Readiness Check READY + Deal Document)
        $quotation->update(['status' => 'ACCEPTED']);

        $topScheme = TopScheme::create([
            'lead_id' => $lead->id,
            'quotation_id' => $quotation->id,
            'preset_code' => '30_40_30',
            'total_percent' => 100,
            'total_amount' => 200_000_000,
            'is_balanced' => true,
        ]);
        TopMilestone::create([
            'top_scheme_id' => $topScheme->id,
            'lead_id' => $lead->id,
            'termin_no' => 1,
            'name' => 'DP Kickoff',
            'trigger_event' => 'ON_CONTRACT_SIGNED',
            'percent' => 30,
            'amount' => 60_000_000,
        ]);
        TopMilestone::create([
            'top_scheme_id' => $topScheme->id,
            'lead_id' => $lead->id,
            'termin_no' => 2,
            'name' => 'UAT Delivery',
            'trigger_event' => 'ON_UAT_SIGNED',
            'percent' => 40,
            'amount' => 80_000_000,
        ]);
        TopMilestone::create([
            'top_scheme_id' => $topScheme->id,
            'lead_id' => $lead->id,
            'termin_no' => 3,
            'name' => 'Go-Live & BAST',
            'trigger_event' => 'ON_BAST_SIGNED',
            'percent' => 30,
            'amount' => 60_000_000,
        ]);

        DeliveryReadinessCheck::create([
            'lead_id' => $lead->id,
            'status' => 'READY',
            'capacity_available_pct' => 85,
            'notes' => 'Tim Backend & Frontend siap mulai sprint minggu depan.',
            'checked_by' => 'PM Lead',
            'checked_at' => now(),
        ]);

        LeadDocument::create([
            'lead_id' => $lead->id,
            'category' => 'SIGNED_MOU',
            'title' => 'MoU Kontrak Pengembangan ERP Signed.pdf',
            'file_path' => 'crm/deals/mou-signed.pdf',
            'is_mandatory_for_deal' => true,
        ]);

        $res5 = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'deal',
        ]);
        $res5->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'deal')
            ->assertJsonPath('data.stage', 'DEAL')
            ->assertJsonPath('data.win_probability', 100)
            ->assertJsonPath('data.weighted_value', 200000000);

        // Verify 5 outbox events `lead.stage_changed` were emitted
        $outboxCount = \Astacode\Kernel\Outbox\OutboxEvent::query()
            ->where('event_name', 'lead.stage_changed')
            ->count();
        $this->assertEquals(5, $outboxCount);

        // Verify 5 immutable audit logs for `stage`
        $auditLogs = CrmImmutableAuditLog::where('lead_id', $lead->id)
            ->where('field_name', 'stage')
            ->get();
        $this->assertCount(5, $auditLogs);
    }

    /**
     * BDD Test AC-CRM-11 & BR-CRM-01:
     * Moving a lead to Quotation without a valid RAB (>= 1 item, total > 0) is rejected with HTTP 422,
     * exact message "Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).",
     * and deep_link_tab = "rab".
     */
    public function test_ac_crm_11_transition_to_quotation_without_rab_is_blocked_with_exact_message_and_rab_deep_link(): void
    {
        $lead = $this->createBaseLead();

        // Advance to requirement first via guard
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'CALL',
            'title' => 'Discovery Call Awal',
            'start_at' => now(),
            'status' => 'COMPLETED',
            'outcome' => 'Kebutuhan dicatat',
        ]);
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'follow_up'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'requirement'])->assertOk();

        // Attempt REQUIREMENT -> QUOTATION without any RAB
        $response = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('guard_failure', 'QUOTATION_RAB_REQUIRED')
            ->assertJsonPath('deep_link_tab', 'rab')
            ->assertJsonPath('detail', 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).');

        // Also test when RAB header exists with 0 items
        RabVersion::create([
            'lead_id' => $lead->id,
            'version' => 1,
            'status' => 'DRAFT',
            'total' => 50_000_000,
            'wbs_completed' => true,
        ]);

        $responseEmptyItems = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
        ]);
        $responseEmptyItems->assertStatus(422)
            ->assertJsonPath('guard_failure', 'QUOTATION_RAB_REQUIRED')
            ->assertJsonPath('deep_link_tab', 'rab')
            ->assertJsonPath('detail', 'Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul).');
    }

    /**
     * BDD Test AC-CRM-05 & AC-CRM-12 (BR-CRM-02):
     * Moving a lead to Deal when Quotation TOP is not balanced (e.g. 80%, remaining 20%) is rejected with HTTP 422,
     * message containing "Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: 20%." + nominal Rp,
     * and deep_link_tab = "top".
     */
    public function test_ac_crm_05_and_ac_crm_12_transition_to_deal_with_unbalanced_top_is_blocked_with_exact_message_and_nominal(): void
    {
        $lead = $this->createBaseLead(['est_value' => 150_000_000]);

        // Set up prerequisites to reach NEGOTIATION
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Discovery Call',
            'start_at' => now(),
            'status' => 'COMPLETED',
            'outcome' => 'OK',
        ]);
        $rab = RabVersion::create([
            'lead_id' => $lead->id,
            'version' => 1,
            'status' => 'APPROVED',
            'total' => 150_000_000,
            'wbs_completed' => true,
        ]);
        RabItem::create([
            'rab_version_id' => $rab->id,
            'lead_id' => $lead->id,
            'category' => 'ERP',
            'name' => 'Core System',
            'role_code' => 'BACKEND_MID',
            'mandays' => 30,
            'subtotal_sell' => 150_000_000,
        ]);
        $quotation = Quotation::create([
            'lead_id' => $lead->id,
            'quotation_number' => 'QT-2026-0099',
            'version' => 1,
            'status' => 'ACCEPTED',
            'total' => 150_000_000,
        ]);

        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'follow_up'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'requirement'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'quotation'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'negotiation'])->assertOk();

        // Create unbalanced TOP (only 80% allocated = Rp 120.000.000, remaining 20% = Rp 30.000.000)
        TopScheme::create([
            'lead_id' => $lead->id,
            'quotation_id' => $quotation->id,
            'preset_code' => 'CUSTOM',
            'total_percent' => 80,
            'total_amount' => 120_000_000,
            'is_balanced' => false,
        ]);

        $response = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'deal',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('guard_failure', 'DEAL_TOP_NOT_BALANCED')
            ->assertJsonPath('deep_link_tab', 'top')
            ->assertJsonPath('remaining_percent', 20);

        $detail = (string) $response->json('detail');
        $this->assertStringContainsString(
            'Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: 20%.',
            $detail
        );
        $this->assertStringContainsString('Rp 30.000.000', $detail);
    }

    /**
     * BDD Test AC-CRM-10 & AC-CRM-13 (BR-CRM-03, BR-CRM-14):
     * Moving a lead to Reject requires a reject reason category and evaluation notes >= 10 characters.
     */
    public function test_ac_crm_10_and_ac_crm_13_reject_stage_requires_category_and_min_10_chars_note(): void
    {
        $lead = $this->createBaseLead();

        // 1. Missing reject_reason_code
        $resMissingCode = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'reject',
            'reject_notes' => 'Klien menunda proyek karena anggaran dialihkan.',
        ]);
        $resMissingCode->assertStatus(422)
            ->assertJsonPath('guard_failure', 'REJECT_REASON_REQUIRED')
            ->assertJsonPath('deep_link_tab', 'overview');

        // 2. Reject note < 10 characters
        $resShortNote = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'reject',
            'reject_reason_code' => 'PRICE_LOST',
            'reject_notes' => 'Kemahalan', // 9 chars
        ]);
        $resShortNote->assertStatus(422)
            ->assertJsonPath('guard_failure', 'REJECT_NOTES_TOO_SHORT')
            ->assertJsonPath('deep_link_tab', 'overview');

        // 3. Valid reject reason + note >= 10 characters
        $resValid = $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'reject',
            'reject_reason_code' => 'PRICE_LOST',
            'reject_notes' => 'Harga penawaran kita di atas pagu anggaran tahunan klien.',
        ]);
        $resValid->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'reject')
            ->assertJsonPath('data.win_probability', 0)
            ->assertJsonPath('data.weighted_value', 0)
            ->assertJsonPath('data.reject_reason_code', 'PRICE_LOST');
    }

    /**
     * Test invalid forward jump (e.g. NEW_LEAD -> QUOTATION or FOLLOW_UP -> DEAL) is blocked,
     * and backward transitions (BR-CRM-12) require a mandatory reason and are forbidden from DEAL/REJECT.
     */
    public function test_invalid_transitions_and_backward_transition_rules_br_crm_12(): void
    {
        $lead = $this->createBaseLead();

        // Invalid skip: NEW_LEAD -> QUOTATION
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'quotation'])
            ->assertStatus(422)
            ->assertJsonPath('guard_failure', 'INVALID_STAGE_TRANSITION');

        // Advance to FOLLOW_UP -> REQUIREMENT
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'MEETING',
            'title' => 'Discovery Call',
            'start_at' => now(),
            'status' => 'COMPLETED',
            'outcome' => 'OK',
        ]);
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'follow_up'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'requirement'])->assertOk();

        // Backward transition REQUIREMENT -> FOLLOW_UP without reason fails (BR-CRM-12)
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'follow_up',
        ])->assertStatus(422)
            ->assertJsonPath('guard_failure', 'BACKWARD_TRANSITION_REASON_REQUIRED');

        // Backward transition REQUIREMENT -> FOLLOW_UP with reason succeeds
        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'follow_up',
            'reason' => 'PIC klien berganti, perlu kualifikasi ulang dari tahap Follow Up.',
        ])->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'follow_up');
    }

    /**
     * Test Super Admin Override (BR-CRM-15):
     * - Sales rep without `crm.stage.override` gets 403 Forbidden
     * - Super Admin without `reason` gets 422
     * - Super Admin with `reason` bypasses guards and records `is_override = true` in immutable audit log.
     */
    public function test_super_admin_override_requires_permission_and_reason_and_logs_immutable_audit(): void
    {
        $lead = $this->createBaseLead(['owner_id' => 77, 'sales_pic_id' => 77]);

        // 1. Regular Sales Rep attempting override -> 403
        $resForbidden = $this->withHeaders([
            'X-User-Id' => '77',
            'X-User-Role' => 'sales',
            'X-User-Permissions' => 'crm.lead.update',
        ])->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
            'override' => true,
            'reason' => 'Coba bypass tanpa izin super admin',
        ]);
        $resForbidden->assertStatus(403)
            ->assertJsonPath('guard_failure', 'OVERRIDE_FORBIDDEN');

        // 2. Super Admin without reason -> 422
        $resNoReason = $this->withHeaders([
            'X-User-Id' => '1',
            'X-User-Role' => 'super_admin',
            'X-User-Permissions' => 'crm.stage.override',
        ])->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
            'override' => true,
        ]);
        $resNoReason->assertStatus(422)
            ->assertJsonPath('guard_failure', 'OVERRIDE_REASON_REQUIRED');

        // 3. Super Admin with valid reason -> 200 OK even without RAB
        $resSuccess = $this->withHeaders([
            'X-User-Id' => '1',
            'X-User-Name' => 'Direktur Utama',
            'X-User-Role' => 'super_admin',
            'X-User-Permissions' => 'crm.stage.override',
        ])->patchJson("/api/v1/crm/leads/{$lead->id}/stage", [
            'stage' => 'quotation',
            'override' => true,
            'reason' => 'Migrasi kontrak repeat order klien strategis sesuai persetujuan Direksi.',
        ]);
        $resSuccess->assertStatus(200)
            ->assertJsonPath('data.stage_id', 'quotation');

        // Verify immutable audit log entry has is_override = true
        $audit = CrmImmutableAuditLog::where('lead_id', $lead->id)
            ->where('field_name', 'stage')
            ->latest('id')
            ->first();
        $this->assertNotNull($audit);
        $this->assertTrue($audit->is_override);
        $this->assertEquals('Migrasi kontrak repeat order klien strategis sesuai persetujuan Direksi.', $audit->reason);
        $this->assertEquals('1', $audit->actor_id);
        $this->assertNotEmpty($audit->occurred_at_iso);
    }

    /**
     * Test Immutable Audit Log for stage, value, discount, and TOP changes,
     * and verify that updating or deleting an audit record throws LogicException.
     */
    public function test_immutable_audit_log_records_value_discount_and_top_changes_and_blocks_mutation(): void
    {
        $lead = $this->createBaseLead(['est_value' => 100_000_000]);

        // 1. Change lead est_value
        $this->patchJson("/api/v1/crm/leads/{$lead->id}", [
            'est_value' => 125_000_000,
        ])->assertOk();

        // 2. Change RAB discount
        $rab = RabVersion::create([
            'lead_id' => $lead->id,
            'version' => 1,
            'subtotal' => 125_000_000,
            'discount_pct' => 0,
            'discount_amount' => 0,
            'total' => 125_000_000,
        ]);
        $rab->update([
            'discount_pct' => 5.0,
            'discount_amount' => 6_250_000,
        ]);

        // 3. Change TOP scheme
        $top = TopScheme::create([
            'lead_id' => $lead->id,
            'preset_code' => '50_50',
            'total_percent' => 50,
            'total_amount' => 62_500_000,
        ]);
        $top->update([
            'preset_code' => '30_40_30',
            'total_percent' => 100,
            'total_amount' => 125_000_000,
        ]);

        // Fetch audit logs via endpoint
        $resLogs = $this->getJson("/api/v1/crm/leads/{$lead->id}/audit-logs");
        $resLogs->assertOk();

        $fields = collect($resLogs->json('data'))->pluck('field_name')->all();
        $this->assertContains('est_value', $fields);
        $this->assertContains('discount', $fields);
        $this->assertContains('top_scheme', $fields);

        // Verify immutability (cannot update or delete CrmImmutableAuditLog)
        $firstLog = CrmImmutableAuditLog::where('lead_id', $lead->id)->firstOrFail();

        $this->expectException(LogicException::class);
        $firstLog->update(['reason' => 'Tampered reason']);
    }

    public function test_immutable_audit_log_cannot_be_deleted(): void
    {
        $lead = $this->createBaseLead();
        $log = CrmImmutableAuditLog::record(
            leadId: $lead->id,
            entityType: 'lead',
            entityId: $lead->id,
            fieldName: 'est_value',
            oldValue: 100,
            newValue: 200,
        );

        $this->expectException(LogicException::class);
        $log->delete();
    }

    /**
     * Test Stage Mandatory Checklists & Next Best Action endpoint.
     */
    public function test_stage_checklists_and_next_best_actions_endpoint(): void
    {
        $lead = $this->createBaseLead();

        // 1. Get checklists for lead
        $resChecklists = $this->getJson("/api/v1/crm/leads/{$lead->id}/checklists?stage_id=new_lead");
        $resChecklists->assertOk();
        $checklists = $resChecklists->json('data.checklists');
        $this->assertNotEmpty($checklists);
        $firstChecklistId = $checklists[0]['id'];
        $this->assertFalse($checklists[0]['is_completed']);

        // 2. Toggle checklist completion
        $resToggle = $this->postJson("/api/v1/crm/leads/{$lead->id}/checklists/{$firstChecklistId}/toggle", [
            'is_completed' => true,
            'notes' => 'Nomor WA sudah diverifikasi aktif.',
        ]);
        $resToggle->assertOk()
            ->assertJsonPath('data.is_completed', true);

        // 3. Get Next Best Actions (rule + AI fallback)
        $resNext = $this->getJson("/api/v1/crm/leads/{$lead->id}/next-actions?include_ai=0");
        $resNext->assertOk()
            ->assertJsonPath('data.lead_id', $lead->id)
            ->assertJsonPath('data.current_stage', 'new_lead');

        $nextActions = $resNext->json('data.next_actions');
        $this->assertNotEmpty($nextActions);
        $codes = array_column($nextActions, 'code');
        $this->assertContains('FIRST_CONTACT_SLA', $codes);
    }

    /**
     * Test Admin stage probability configuration updates weighted_value on subsequent transitions.
     */
    public function test_admin_can_configure_stage_probability(): void
    {
        $this->putJson('/api/v1/crm/pipeline-stages/FOLLOW_UP', [
            'win_probability_pct' => 25,
            'sla_hours' => 24,
        ])->assertOk()
            ->assertJsonPath('data.win_probability_pct', 25);

        $lead = $this->createBaseLead(['est_value' => 100_000_000]);
        Activity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'type' => 'FOLLOW_UP',
            'title' => 'First contact',
            'start_at' => now(),
            'status' => 'COMPLETED',
        ]);

        $this->patchJson("/api/v1/crm/leads/{$lead->id}/stage", ['stage' => 'follow_up'])
            ->assertOk()
            ->assertJsonPath('data.win_probability', 25)
            ->assertJsonPath('data.weighted_value', 25000000);
    }

    /**
     * DoD Test: Direct mutation of Lead::$stage or Lead::$stage_id outside PipelineTransitionGuard
     * is strictly forbidden and throws DomainException.
     */
    public function test_dod_direct_stage_mutation_outside_pipeline_transition_guard_throws_domain_exception(): void
    {
        $lead = $this->createBaseLead();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Direct mutation of Lead stage is forbidden. All stage transitions must go through PipelineTransitionGuard.');

        $lead->stage_id = 'quotation';
        $lead->save();
    }

    /**
     * DoD Test: Static architectural scan of app/Controllers and app/Services ensuring no controller or service
     * mutates $lead->stage or $lead->stage_id bypassing PipelineTransitionGuard.
     */
    public function test_dod_codebase_scan_verifies_only_pipeline_transition_guard_mutates_lead_stage(): void
    {
        $appDir = base_path('app');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($appDir));
        $allowedFiles = [
            realpath(base_path('app/Services/PipelineTransitionGuard.php')),
            realpath(base_path('app/Models/Lead.php')),
        ];

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $realPath = realpath($file->getPathname());
            if (in_array($realPath, $allowedFiles, true)) {
                continue;
            }

            $content = (string) file_get_contents($realPath);
            $this->assertDoesNotMatchRegularExpression(
                '/\$lead\s*->\s*stage(_id)?\s*=/',
                $content,
                "File {$file->getFilename()} must not directly assign \$lead->stage or \$lead->stage_id outside PipelineTransitionGuard."
            );
        }
    }
}
