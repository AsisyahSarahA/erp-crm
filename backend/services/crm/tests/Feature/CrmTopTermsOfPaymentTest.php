<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Quotation;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Support\UserContext;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RateCardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTopTermsOfPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected Lead $lead;
    protected RabVersion $rab;
    protected Quotation $quotation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PipelineStageSeeder::class,
            RateCardSeeder::class,
        ]);

        $source = LeadSource::firstOrCreate(
            ['code' => 'WEBSITE'],
            [
                'name' => 'Website',
                'channel_group' => 'INBOUND',
                'is_active' => true,
            ]
        );

        $this->customer = Customer::create([
            'code' => 'CUS-0700',
            'legal_name' => 'PT Sinar Logistik Indonesia',
            'brand_name' => 'SinarLog',
            'type' => 'perusahaan',
            'industry' => 'Logistics',
            'size' => 'enterprise',
            'npwp' => '0123456789012345',
            'is_pkp' => true,
            'is_pph23_withholder' => true,
            'status' => 'prospect',
            'first_source_id' => $source->id,
            'latest_source_id' => $source->id,
        ]);

        $this->lead = Lead::create([
            'code' => 'LD-2026-0700',
            'customer_id' => $this->customer->id,
            'title' => 'Sistem Manajemen Armada & Telematika',
            'project_type' => 'iot_telematics',
            'priority' => 'hot',
            'source' => 'Website',
            'source_id' => $source->id,
            'stage' => 'requirement',
            'stage_id' => 'requirement',
            'probability' => 35,
            'est_value' => 300_000_000,
            'owner_id' => 10,
        ]);

        $this->rab = RabVersion::create([
            'lead_id' => $this->lead->id,
            'version' => 1,
            'source' => 'MANUAL',
            'status' => 'ACTIVE',
            'total_mandays' => 100,
            'duration_weeks' => 12,
            'subtotal' => 300_000_000,
            'contingency_pct' => 0,
            'contingency_reason' => 'Fixed scope',
            'dpp' => 300_000_000,
            'is_pkp' => true,
            'vat_rate' => 11.0,
            'vat_amount' => 33_000_000,
            'total' => 333_000_000,
            'hpp_internal' => 150_000_000,
            'margin_pct' => 50.0,
            'margin_status' => 'GREEN',
            'wbs_completed' => true,
        ]);

        RabItem::create([
            'rab_version_id' => $this->rab->id,
            'lead_id' => $this->lead->id,
            'sort_order' => 1,
            'category' => 'Transaksi',
            'name' => 'Core Telematics Platform',
            'role' => 'BE',
            'qty' => 100,
            'unit' => 'Mandays',
            'unit_price' => 3_000_000,
            'subtotal' => 300_000_000,
            'cost_rate' => 1_500_000,
            'hpp' => 150_000_000,
            'origin' => 'MANUAL',
        ]);

        $this->lead->update(['active_rab_version_id' => $this->rab->id]);
    }

    public function test_presets_30_40_30_50_50_20_30_30_20_retention_and_monthly_rounding_absorption(): void
    {
        // 1. Preset 30/40/30 on DPP 300.000.000
        $r1 = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => '30_40_30',
        ]);
        $r1->assertStatus(200);
        $this->assertTrue($r1->json('data.is_balanced'));
        $this->assertTrue($r1->json('data.can_finalize'));
        $this->assertCount(3, $r1->json('data.milestones'));
        $this->assertEqualsWithDelta(90_000_000.0, $r1->json('data.milestones.0.dpp_amount'), 0.01);
        $this->assertEqualsWithDelta(9_900_000.0, $r1->json('data.milestones.0.ppn_amount'), 0.01);
        $this->assertEqualsWithDelta(1_800_000.0, $r1->json('data.milestones.0.pph23_amount'), 0.01);
        $this->assertStringContainsString('Sembilan Puluh Juta Rupiah', $r1->json('data.milestones.0.terbilang_text'));

        // 2. Preset 40/30/20/10 dengan Retensi
        $r2 = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => '40/30/20/10',
        ]);
        $r2->assertStatus(200);
        $this->assertSame('40_30_20_10_RETENTION', $r2->json('data.preset_code'));
        $this->assertCount(4, $r2->json('data.milestones'));
        $this->assertTrue($r2->json('data.milestones.3.is_retention'));
        $this->assertSame('retention_end', $r2->json('data.milestones.3.trigger_type'));

        // 3. Preset Bulanan/Recurring (3 months on DPP 100.000.000 -> 33.33%, 33.33%, 33.34% absorbs remainder)
        $r3 = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => 'MONTHLY_RECURRING',
            'months' => 3,
            'reference_dpp' => 100_000_000,
        ]);
        $r3->assertStatus(200);
        $this->assertTrue($r3->json('data.is_balanced'));
        $this->assertEqualsWithDelta(100.0, $r3->json('data.total_percent'), 0.001);
        $this->assertEqualsWithDelta(33.33, $r3->json('data.milestones.0.percentage'), 0.01);
        $this->assertEqualsWithDelta(33.33, $r3->json('data.milestones.1.percentage'), 0.01);
        $this->assertEqualsWithDelta(33.34, $r3->json('data.milestones.2.percentage'), 0.01);
        $this->assertEqualsWithDelta(100_000_000.0, $r3->json('data.total_dpp'), 0.01);
    }

    public function test_bidirectional_nominal_to_percentage_sync_and_custom_terms(): void
    {
        // Pass nominal amount_idr without percentage on a Rp300.000.000 DPP RAB
        $resp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => 'CUSTOM',
            'milestones' => [
                [
                    'term_number' => 1,
                    'name' => 'DP',
                    'amount_idr' => 90_000_000, // 30% of 300jt
                    'trigger_type' => 'signing',
                    'due_days_offset' => 14,
                ],
                [
                    'term_number' => 2,
                    'name' => 'Pelunasan UAT',
                    'amount_idr' => 210_000_000, // 70% of 300jt
                    'trigger_type' => 'uat_passed',
                    'due_days_offset' => 14,
                    'project_milestone_ref' => 'EPIC-UAT-01',
                ],
            ],
        ]);

        $resp->assertStatus(200);
        $this->assertTrue($resp->json('data.is_balanced'));
        $this->assertEqualsWithDelta(30.0, $resp->json('data.milestones.0.percentage'), 0.01);
        $this->assertEqualsWithDelta(70.0, $resp->json('data.milestones.1.percentage'), 0.01);
        $this->assertSame('EPIC-UAT-01', $resp->json('data.milestones.1.project_milestone_ref'));
    }

    public function test_ac_crm_05_and_ac_crm_12_unbalanced_top_90_percent_locks_export_and_blocks_deal_with_exact_messages(): void
    {
        // Save unbalanced 90% TOP (DP 30% + Termin 1 60% on Rp300.000.000 RAB)
        $draftResp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => 'CUSTOM',
            'milestones' => [
                ['term_number' => 1, 'name' => 'DP', 'percentage' => 30.0, 'trigger_type' => 'signing'],
                ['term_number' => 2, 'name' => 'Termin 1', 'percentage' => 60.0, 'trigger_type' => 'milestone'],
            ],
        ]);

        $draftResp->assertStatus(200);
        $this->assertFalse($draftResp->json('data.is_balanced'));
        $this->assertFalse($draftResp->json('data.can_finalize'));
        $this->assertFalse($draftResp->json('data.can_export_document'));
        $this->assertSame(
            'Sisa alokasi belum diatur: 10% (Rp30.000.000)',
            $draftResp->json('data.balance_warning')
        );

        // Attempting final save (`finalize = true`) when unbalanced -> rejected with 422
        $finalResp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'finalize' => true,
            'preset_code' => 'CUSTOM',
            'milestones' => [
                ['term_number' => 1, 'name' => 'DP', 'percentage' => 30.0, 'trigger_type' => 'signing'],
                ['term_number' => 2, 'name' => 'Termin 1', 'percentage' => 60.0, 'trigger_type' => 'milestone'],
            ],
        ]);
        $finalResp->assertStatus(422);
        $this->assertStringContainsString('Total termin harus 100%', (string) $finalResp->json('detail'));
        $this->assertStringContainsString('Sisa alokasi belum diatur: 10% (Rp30.000.000)', (string) $finalResp->json('detail'));

        // Advance Lead to NEGOTIATION and attempt transition to DEAL (AC-CRM-05 & AC-CRM-12)
        Activity::create([
            'lead_id' => $this->lead->id,
            'customer_id' => $this->customer->id,
            'type' => 'MEETING',
            'title' => 'Discovery Call',
            'start_at' => now(),
            'status' => 'COMPLETED',
            'outcome' => 'SUCCESS',
        ]);

        $this->quotation = Quotation::create([
            'lead_id' => $this->lead->id,
            'quotation_number' => 'QT/2026/11/0700',
            'version' => 1,
            'status' => 'ACCEPTED',
            'subtotal' => 300_000_000,
            'dpp' => 300_000_000,
            'vat_rate' => 11.0,
            'vat_amount' => 33_000_000,
            'total' => 300_000_000,
        ]);

        $this->patchJson("/api/v1/crm/leads/{$this->lead->id}/stage", ['stage' => 'quotation'])->assertOk();
        $this->patchJson("/api/v1/crm/leads/{$this->lead->id}/stage", ['stage' => 'negotiation'])->assertOk();

        $dealResp = $this->patchJson("/api/v1/crm/leads/{$this->lead->id}/stage", [
            'stage' => 'deal',
        ]);

        $dealResp->assertStatus(422)
            ->assertJsonPath('guard_failure', 'DEAL_TOP_NOT_BALANCED')
            ->assertJsonPath('deep_link_tab', 'top');

        $detail = (string) $dealResp->json('detail');
        // AC-CRM-05 exact message
        $this->assertStringContainsString('Total termin harus 100%', $detail);
        // AC-CRM-12 exact message
        $this->assertStringContainsString(
            'Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: 10% (Rp30.000.000).',
            $detail
        );
    }

    public function test_br_crm_18_finance_lock_npwp_verification_and_approval_for_changes_after_lock(): void
    {
        // 1. Save balanced 30/40/30 TOP
        $this->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
            'preset_code' => '30_40_30',
        ])->assertStatus(200);

        $salesHeader = UserContext::encodeHeader([
            'id' => 10,
            'name' => 'Sales PIC',
            'role' => 'sales',
            'permissions' => ['crm.top.edit'],
        ]);

        // 2. Sales cannot lock TOP -> 403
        $this->withHeaders(['X-Internal-User' => $salesHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/top/lock")
            ->assertStatus(403);

        // 3. Finance AR locks TOP -> 200 OK (initially npwp_verified = false)
        $financeHeader = UserContext::encodeHeader([
            'id' => 50,
            'name' => 'Finance AR Specialist',
            'role' => 'finance_ar',
            'permissions' => ['crm.top.lock', 'finance.ar.manage'],
        ]);

        $lockResp = $this->withHeaders(['X-Internal-User' => $financeHeader])
            ->postJson('/api/v1/crm/top/lock', [
                'lead_id' => $this->lead->id,
                'notes' => 'Skema 30/40/30 telah sesuai SOP.',
            ]);

        $lockResp->assertStatus(200);
        $this->assertTrue($lockResp->json('data.is_locked_by_finance'));
        $this->assertNotNull($lockResp->json('data.locked_by_finance_at'));
        $this->assertFalse($lockResp->json('data.npwp_verified'));
        $this->assertNotEmpty($lockResp->json('data.npwp_warning'));

        // 4. Finance verifies Customer NPWP -> Receivable Schedule shows dp_invoice_ready = true
        $this->withHeaders(['X-Internal-User' => $financeHeader])
            ->postJson("/api/v1/crm/customers/{$this->customer->id}/verify-npwp", [
                'npwp' => '0123456789012345',
            ])->assertStatus(200);

        $schedResp = $this->getJson("/api/v1/crm/leads/{$this->lead->id}/top/receivable-schedule");
        $schedResp->assertStatus(200);
        $this->assertTrue($schedResp->json('data.npwp_verified'));
        $this->assertTrue($schedResp->json('data.dp_invoice_ready'));
        $this->assertCount(3, $schedResp->json('data.schedule'));

        // 5. Sales attempts to change locked TOP to 50/50 -> blocked with 422 (TOP_LOCKED_BY_FINANCE) & creates pending approval
        $changeAttempt = $this->withHeaders(['X-Internal-User' => $salesHeader])
            ->putJson("/api/v1/crm/leads/{$this->lead->id}/top", [
                'preset_code' => '50_50',
                'change_reason' => 'Klien meminta pembayaran 2 tahap 50/50.',
            ]);

        $changeAttempt->assertStatus(422)
            ->assertJsonPath('code', 'TOP_LOCKED_BY_FINANCE')
            ->assertJsonPath('approval_required', true)
            ->assertJsonPath('unlock_approval_status', 'PENDING');

        // Active TOP is still 30_40_30 until Finance approves
        $currentTop = $this->getJson("/api/v1/crm/leads/{$this->lead->id}/top");
        $this->assertSame('30_40_30', $currentTop->json('data.preset_code'));

        // 6. Finance approves the locked TOP change -> automatically applies 50_50 and re-locks!
        $approveResp = $this->withHeaders(['X-Internal-User' => $financeHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/top/approve-change", [
                'action' => 'APPROVED',
                'notes' => 'Disetujui perubahan ke 50/50 oleh Finance AR.',
            ]);

        $approveResp->assertStatus(200);
        $this->assertSame('50_50', $approveResp->json('data.top.preset_code'));
        $this->assertTrue($approveResp->json('data.top.is_balanced'));
        $this->assertCount(2, $approveResp->json('data.top.milestones'));
        $this->assertTrue($approveResp->json('data.top.is_locked_by_finance'));
    }
}

