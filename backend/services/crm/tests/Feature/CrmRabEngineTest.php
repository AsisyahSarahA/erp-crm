<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Support\UserContext;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RateCardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrmRabEngineTest extends TestCase
{
    use RefreshDatabase;

    protected Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PipelineStageSeeder::class,
            RateCardSeeder::class,
            ModuleCatalogSeeder::class,
        ]);

        $source = LeadSource::firstOrCreate(
            ['code' => 'WEBSITE'],
            [
                'name' => 'Website',
                'channel_group' => 'INBOUND',
                'is_active' => true,
            ]
        );

        $customer = Customer::create([
            'code' => 'CUS-0600',
            'legal_name' => 'PT Nusantara Enterprise Tbk',
            'brand_name' => 'Nusantara',
            'type' => 'perusahaan',
            'industry' => 'Manufacturing',
            'size' => 'enterprise',
            'is_pkp' => true,
            'is_pph23_withholder' => true,
            'status' => 'prospect',
            'first_source_id' => $source->id,
            'latest_source_id' => $source->id,
        ]);

        $this->lead = Lead::create([
            'code' => 'LD-2026-0600',
            'customer_id' => $customer->id,
            'title' => 'Implementasi ERP & Gudang Pintar',
            'project_type' => 'erp_custom',
            'priority' => 'high',
            'source' => 'Website',
            'source_id' => $source->id,
            'stage' => 'requirement',
            'stage_id' => 'requirement',
            'probability' => 35,
            'est_value' => 100_000_000,
            'owner_id' => 10,
        ]);
    }

    public static function rabCalculationCasesProvider(): array
    {
        return [
            'DoD PRD Case: DPP 100jt -> PPN 11jt -> Total 111jt' => [
                'payload' => [
                    'items' => [
                        [
                            'category' => 'Transaksi',
                            'name' => 'Modul Core ERP',
                            'role' => 'BE',
                            'qty' => 50,
                            'unit' => 'Mandays',
                            'unit_price' => 2_000_000,
                            'cost_rate' => 1_000_000,
                        ],
                    ],
                    'contingency_pct' => 0.0,
                    'contingency_reason' => 'Scope sudah fixed price sesuai kontrak induk',
                    'discount_type' => 'PERCENT',
                    'discount_value' => 0.0,
                    'is_pkp' => true,
                    'vat_rate' => 11.0,
                ],
                'expected' => [
                    'subtotal' => 100_000_000.0,
                    'contingency_amount' => 0.0,
                    'discount_amount' => 0.0,
                    'dpp' => 100_000_000.0,
                    'vat_amount' => 11_000_000.0,
                    'total' => 111_000_000.0,
                    'hpp_internal' => 50_000_000.0,
                    'margin_pct' => 50.0,
                    'margin_status' => 'GREEN',
                    'terbilang_contains' => 'Seratus Sebelas Juta Rupiah',
                ],
            ],
            'Standard Case: Kontingensi 10% + Diskon 10%' => [
                'payload' => [
                    'items' => [
                        [
                            'category' => 'Master Data',
                            'name' => 'Master Data & RBAC',
                            'role' => 'BE',
                            'qty' => 25,
                            'unit' => 'Mandays',
                            'unit_price' => 2_000_000,
                            'cost_rate' => 1_000_000,
                        ],
                        [
                            'category' => 'Dashboard & KPI',
                            'name' => 'Dashboard Eksekutif',
                            'role' => 'FE',
                            'qty' => 25,
                            'unit' => 'Mandays',
                            'unit_price' => 2_000_000,
                            'cost_rate' => 1_000_000,
                        ],
                    ],
                    'contingency_pct' => 10.0,
                    'discount_type' => 'PERCENT',
                    'discount_value' => 10.0,
                    'is_pkp' => true,
                    'vat_rate' => 11.0,
                ],
                'expected' => [
                    'subtotal' => 100_000_000.0,
                    'contingency_amount' => 10_000_000.0,
                    'discount_amount' => 11_000_000.0,
                    'dpp' => 99_000_000.0,
                    'vat_amount' => 10_890_000.0,
                    'total' => 109_890_000.0,
                    'hpp_internal' => 50_000_000.0,
                    'margin_pct' => 49.49,
                    'margin_status' => 'GREEN',
                    'terbilang_contains' => 'Seratus Sembilan Juta Delapan Ratus Sembilan Puluh Ribu Rupiah',
                ],
            ],
            'Nominal Discount + Direct Costs + Yellow Margin (25-40%)' => [
                'payload' => [
                    'items' => [
                        [
                            'category' => 'Transaksi',
                            'name' => 'Modul Integrasi Payment',
                            'role' => 'BE',
                            'qty' => 40,
                            'unit' => 'Mandays',
                            'unit_price' => 2_000_000,
                            'cost_rate' => 1_200_000,
                        ],
                    ],
                    'contingency_pct' => 10.0,
                    'discount_type' => 'NOMINAL',
                    'discount_value' => 8_000_000.0,
                    'is_pkp' => true,
                    'vat_rate' => 11.0,
                    'direct_costs' => [
                        ['name' => 'Cloud Server 1 Tahun', 'category' => 'cloud', 'amount' => 4_000_000],
                    ],
                ],
                'expected' => [
                    'subtotal' => 80_000_000.0,
                    'contingency_amount' => 8_000_000.0,
                    'discount_amount' => 8_000_000.0,
                    'dpp' => 80_000_000.0,
                    'vat_amount' => 8_800_000.0,
                    'total' => 88_800_000.0,
                    'hpp_internal' => 52_000_000.0, // 48jt + 4jt cloud
                    'margin_pct' => 35.0,
                    'margin_status' => 'YELLOW',
                    'terbilang_contains' => 'Delapan Puluh Delapan Juta Delapan Ratus Ribu Rupiah',
                ],
            ],
            'Non-PKP Case: PPN = 0 + Optional & Bonus Items + Red Margin (<25%)' => [
                'payload' => [
                    'items' => [
                        [
                            'category' => 'Transaksi',
                            'name' => 'Modul Utama',
                            'role' => 'BE',
                            'qty' => 20,
                            'unit' => 'Mandays',
                            'unit_price' => 2_000_000,
                            'cost_rate' => 1_500_000,
                        ],
                        [
                            'category' => 'Lainnya',
                            'name' => 'Bonus Training Onsite',
                            'role' => 'PM',
                            'qty' => 5,
                            'unit' => 'Mandays',
                            'unit_price' => 1_500_000,
                            'cost_rate' => 1_000_000,
                            'is_bonus' => true,
                        ],
                        [
                            'category' => 'Lainnya',
                            'name' => 'Add-on AI Analytics (Opsional)',
                            'role' => 'BE',
                            'qty' => 15,
                            'unit' => 'Mandays',
                            'unit_price' => 2_500_000,
                            'cost_rate' => 1_200_000,
                            'is_optional' => true,
                        ],
                    ],
                    'contingency_pct' => 5.0,
                    'discount_type' => 'PERCENT',
                    'discount_value' => 0.0,
                    'is_pkp' => false,
                ],
                'expected' => [
                    'subtotal' => 40_000_000.0,
                    'contingency_amount' => 2_000_000.0,
                    'discount_amount' => 0.0,
                    'dpp' => 42_000_000.0,
                    'vat_amount' => 0.0,
                    'total' => 42_000_000.0,
                    'hpp_internal' => 35_000_000.0, // 30jt + 5jt bonus HPP (optional excluded)
                    'margin_pct' => 16.67,
                    'margin_status' => 'RED',
                    'terbilang_contains' => 'Empat Puluh Dua Juta Rupiah',
                ],
            ],
        ];
    }

    #[DataProvider('rabCalculationCasesProvider')]
    public function test_rab_calculation_table_cases_and_dod_dpp_100jt_ppn_11jt(array $payload, array $expected): void
    {
        $resp = $this->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/recalculate", $payload);
        $resp->assertStatus(200);

        $data = $resp->json('data');
        $this->assertEqualsWithDelta($expected['subtotal'], $data['subtotal'], 0.01);
        $this->assertEqualsWithDelta($expected['contingency_amount'], $data['contingency_amount'], 0.01);
        $this->assertEqualsWithDelta($expected['discount_amount'], $data['discount_amount'], 0.01);
        $this->assertEqualsWithDelta($expected['dpp'], $data['dpp'], 0.01);
        $this->assertEqualsWithDelta($expected['vat_amount'], $data['vat_amount'], 0.01);
        $this->assertEqualsWithDelta($expected['total'], $data['total'], 0.01);
        $this->assertEqualsWithDelta($expected['hpp_internal'], $data['hpp_internal'], 0.01);
        $this->assertEqualsWithDelta($expected['margin_pct'], $data['margin_pct'], 0.05);
        $this->assertSame($expected['margin_status'], $data['margin_status']);
        $this->assertStringContainsString($expected['terbilang_contains'], $data['total_terbilang']);
    }

    public function test_contingency_below_5_percent_without_reason_is_rejected(): void
    {
        $resp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'items' => [
                ['name' => 'Modul A', 'role' => 'BE', 'qty' => 10, 'unit_price' => 1_800_000],
            ],
            'contingency_pct' => 3.0,
            'contingency_reason' => '',
        ]);

        $resp->assertStatus(422);
        $this->assertStringContainsString('kontingensi', strtolower((string) $resp->json('detail')));
    }

    public function test_ac_crm_04_ai_item_badge_becomes_manual_when_edited_by_user(): void
    {
        // Create initial RAB with 2 AI-origin items
        $rab = RabVersion::create([
            'lead_id' => $this->lead->id,
            'version' => 1,
            'source' => 'AI',
            'status' => 'ACTIVE',
            'subtotal' => 36_000_000,
            'dpp' => 39_600_000,
            'total' => 43_956_000,
        ]);

        $item1 = RabItem::create([
            'rab_version_id' => $rab->id,
            'lead_id' => $this->lead->id,
            'sort_order' => 1,
            'category' => 'Master Data',
            'name' => 'Modul Karyawan & Organisasi',
            'role' => 'BE',
            'qty' => 10,
            'unit' => 'Mandays',
            'unit_price' => 1_800_000,
            'subtotal' => 18_000_000,
            'cost_rate' => 900_000,
            'hpp' => 9_000_000,
            'origin' => 'AI',
            'ai_confidence' => 92.0,
        ]);

        $item2 = RabItem::create([
            'rab_version_id' => $rab->id,
            'lead_id' => $this->lead->id,
            'sort_order' => 2,
            'category' => 'Transaksi',
            'name' => 'Modul Presensi GPS',
            'role' => 'MOBILE',
            'qty' => 10,
            'unit' => 'Mandays',
            'unit_price' => 1_800_000,
            'subtotal' => 18_000_000,
            'cost_rate' => 900_000,
            'hpp' => 9_000_000,
            'origin' => 'AI',
            'ai_confidence' => 89.0,
        ]);

        $this->lead->update(['active_rab_version_id' => $rab->id]);

        // User edits qty & unit_price on item1, while keeping item2 unchanged
        $updateResp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'version' => 1,
            'contingency_pct' => 10.0,
            'items' => [
                [
                    'id' => $item1->id,
                    'category' => 'Master Data',
                    'name' => 'Modul Karyawan & Organisasi',
                    'role' => 'BE',
                    'qty' => 15, // changed from 10 to 15
                    'unit' => 'Mandays',
                    'unit_price' => 2_000_000, // changed from 1.8jt to 2.0jt
                    'origin' => 'AI',
                ],
                [
                    'id' => $item2->id,
                    'category' => 'Transaksi',
                    'name' => 'Modul Presensi GPS',
                    'role' => 'MOBILE',
                    'qty' => 10, // unchanged
                    'unit' => 'Mandays',
                    'unit_price' => 1_800_000, // unchanged
                    'origin' => 'AI',
                ],
            ],
        ]);

        $updateResp->assertStatus(200);
        $items = $updateResp->json('data.items');

        $this->assertCount(2, $items);
        // Item 1 was edited -> badge/origin must flip to MANUAL (AC-CRM-04)
        $this->assertSame('MANUAL', $items[0]['origin']);
        $this->assertSame('MANUAL', $items[0]['source_badge']);
        $this->assertNull($items[0]['ai_confidence']);
        $this->assertEqualsWithDelta(30_000_000.0, $items[0]['subtotal'], 0.01);

        // Item 2 was NOT edited -> badge/origin remains AI
        $this->assertSame('AI', $items[1]['origin']);
        $this->assertSame('AI', $items[1]['source_badge']);
        $this->assertEqualsWithDelta(18_000_000.0, $items[1]['subtotal'], 0.01);

        // Total Subtotal = 30jt + 18jt = 48jt
        $this->assertEqualsWithDelta(48_000_000.0, $updateResp->json('data.subtotal'), 0.01);
    }

    public function test_ac_crm_07_and_br_crm_07_dual_view_removes_hpp_margin_and_cost_rate_for_unauthorized_roles(): void
    {
        // Save RAB as super_admin / presales first
        $saveResp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'items' => [
                [
                    'category' => 'Master Data',
                    'name' => 'Modul Akuntansi',
                    'role' => 'BE',
                    'qty' => 20,
                    'unit' => 'Mandays',
                    'unit_price' => 2_000_000,
                    'cost_rate' => 900_000,
                ],
            ],
            'direct_costs' => [
                ['name' => 'Lisensi PDF', 'category' => 'license', 'amount' => 2_000_000],
            ],
        ]);
        $saveResp->assertStatus(200);

        // 1. Authorized user (with crm.margin.view) sees HPP, margin_pct, and cost_rate
        $internalHeader = UserContext::encodeHeader([
            'id' => 2,
            'name' => 'Presales Architect',
            'role' => 'presales',
            'permissions' => ['crm.rab.view', 'crm.margin.view'],
        ]);

        $authGet = $this->withHeaders(['X-Internal-User' => $internalHeader])
            ->getJson("/api/v1/crm/leads/{$this->lead->id}/rab");

        $authGet->assertStatus(200);
        $authData = $authGet->json('data');
        $this->assertArrayHasKey('hpp_internal', $authData);
        $this->assertArrayHasKey('margin_pct', $authData);
        $this->assertArrayHasKey('margin_status', $authData);
        $this->assertArrayHasKey('cost_rate', $authData['items'][0]);
        $this->assertArrayHasKey('hpp', $authData['items'][0]);

        // 2. Unauthorized user (Sales without crm.margin.view) MUST NOT have HPP/margin/cost_rate keys in JSON
        $salesHeader = UserContext::encodeHeader([
            'id' => 10,
            'name' => 'Sales Executive',
            'role' => 'sales',
            'permissions' => ['crm.rab.view', 'crm.rab.edit'],
        ]);

        $unauthGet = $this->withHeaders(['X-Internal-User' => $salesHeader])
            ->getJson("/api/v1/crm/leads/{$this->lead->id}/rab");

        $unauthGet->assertStatus(200);
        $unauthData = $unauthGet->json('data');

        $this->assertSame('CLIENT', $unauthData['view_mode']);
        $this->assertArrayNotHasKey('hpp_internal', $unauthData);
        $this->assertArrayNotHasKey('direct_costs', $unauthData);
        $this->assertArrayNotHasKey('direct_costs_total', $unauthData);
        $this->assertArrayNotHasKey('gross_margin_nominal', $unauthData);
        $this->assertArrayNotHasKey('margin_pct', $unauthData);
        $this->assertArrayNotHasKey('gross_margin_percent', $unauthData);
        $this->assertArrayNotHasKey('margin_status', $unauthData);
        $this->assertArrayNotHasKey('margin_indicator_color', $unauthData);

        foreach ($unauthData['items'] as $item) {
            $this->assertArrayNotHasKey('cost_rate', $item);
            $this->assertArrayNotHasKey('daily_rate_cost', $item);
            $this->assertArrayNotHasKey('subtotal_cost', $item);
            $this->assertArrayNotHasKey('hpp', $item);
        }
    }

    public function test_optimistic_locking_returns_409_conflict_on_stale_version(): void
    {
        // Create v1
        $r1 = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'items' => [
                ['name' => 'Modul Inventory', 'role' => 'BE', 'qty' => 10, 'unit_price' => 2_000_000],
            ],
        ]);
        $r1->assertStatus(200);
        $this->assertSame(1, $r1->json('data.version'));

        // Update with matching version = 1 -> creates v2
        $r2 = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'version' => 1,
            'items' => [
                ['name' => 'Modul Inventory', 'role' => 'BE', 'qty' => 15, 'unit_price' => 2_000_000],
            ],
        ]);
        $r2->assertStatus(200);
        $this->assertSame(2, $r2->json('data.version'));

        // Concurrent user tries to save with stale version = 1 -> 409 Conflict
        $conflictResp = $this->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
            'version' => 1,
            'items' => [
                ['name' => 'Modul Inventory', 'role' => 'BE', 'qty' => 20, 'unit_price' => 2_000_000],
            ],
        ]);

        $conflictResp->assertStatus(409);
        $this->assertSame(1, $conflictResp->json('expected_version'));
        $this->assertSame(2, $conflictResp->json('actual_version'));
    }

    public function test_fr_crm_17_approval_tiers_and_segregation_of_duties(): void
    {
        $salesHeader = UserContext::encodeHeader([
            'id' => 10,
            'name' => 'Sales Andi',
            'role' => 'sales',
            'permissions' => ['crm.rab.edit', 'crm.margin.view'],
        ]);

        // 1. Discount 12% (>10%..15%) -> requires SALES_MANAGER approval
        $rManagerTier = $this->withHeaders(['X-Internal-User' => $salesHeader])
            ->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
                'discount_type' => 'PERCENT',
                'discount_value' => 12.0,
                'items' => [
                    ['name' => 'Modul Core', 'role' => 'BE', 'qty' => 30, 'unit_price' => 2_000_000, 'cost_rate' => 900_000],
                ],
            ]);

        $rManagerTier->assertStatus(200);
        $this->assertTrue($rManagerTier->json('data.requires_approval'));
        $this->assertSame('SALES_MANAGER', $rManagerTier->json('data.approval_tier'));
        $this->assertSame('PENDING', $rManagerTier->json('data.approval_status'));

        // Requester (id=10) cannot approve own RAB even if given sales_manager role (SoD violation -> 403)
        $selfManagerHeader = UserContext::encodeHeader([
            'id' => 10,
            'name' => 'Sales Andi',
            'role' => 'sales_manager',
            'permissions' => ['crm.rab.approve'],
        ]);

        $sodResp = $this->withHeaders(['X-Internal-User' => $selfManagerHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/approve", ['action' => 'APPROVED']);
        $sodResp->assertStatus(403);

        // Sales Manager (id=20) approves -> 200 OK
        $managerHeader = UserContext::encodeHeader([
            'id' => 20,
            'name' => 'Budi Sales Manager',
            'role' => 'sales_manager',
            'permissions' => ['crm.rab.approve', 'crm.margin.view'],
        ]);

        $mgrApprove = $this->withHeaders(['X-Internal-User' => $managerHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/approve", [
                'action' => 'APPROVED',
                'notes' => 'Diskon 12% disetujui untuk klien strategis.',
            ]);
        $mgrApprove->assertStatus(200);
        $this->assertSame('APPROVED', $mgrApprove->json('data.rab.approval_status'));

        // 2. High Value (>Rp500jt) or Discount >15% -> requires DIRECTOR approval
        $rDirectorTier = $this->withHeaders(['X-Internal-User' => $salesHeader])
            ->putJson("/api/v1/crm/leads/{$this->lead->id}/rab", [
                'discount_type' => 'PERCENT',
                'discount_value' => 18.0, // >15%
                'items' => [
                    ['name' => 'Enterprise Platform', 'role' => 'BE', 'qty' => 300, 'unit_price' => 2_500_000, 'cost_rate' => 1_000_000],
                ],
            ]);

        $rDirectorTier->assertStatus(200);
        $this->assertTrue($rDirectorTier->json('data.requires_approval'));
        $this->assertSame('DIRECTOR', $rDirectorTier->json('data.approval_tier'));

        // Sales Manager cannot approve DIRECTOR tier -> 403
        $mgrDenied = $this->withHeaders(['X-Internal-User' => $managerHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/approve", ['action' => 'APPROVED']);
        $mgrDenied->assertStatus(403);

        // Director (id=30) approves -> 200 OK
        $directorHeader = UserContext::encodeHeader([
            'id' => 30,
            'name' => 'Direktur Utama',
            'role' => 'director',
            'permissions' => ['crm.rab.approve', 'crm.margin.view'],
        ]);

        $dirApprove = $this->withHeaders(['X-Internal-User' => $directorHeader])
            ->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/approve", [
                'action' => 'APPROVED',
                'notes' => 'Disetujui Direktur.',
            ]);
        $dirApprove->assertStatus(200);
        $this->assertSame('APPROVED', $dirApprove->json('data.rab.approval_status'));
    }

    public function test_catalog_import_excel_import_export_version_diff_and_duration_derivation(): void
    {
        // 1. Import from Module Catalog (origin = TEMPLATE)
        $importCat = $this->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/import-catalog", [
            'merge' => false,
        ]);
        $importCat->assertStatus(201);
        $this->assertSame(1, $importCat->json('data.version'));
        $this->assertSame('TEMPLATE', $importCat->json('data.items.0.origin'));
        $this->assertNotEmpty($importCat->json('data.duration_derivation.roles_breakdown'));

        // 2. Import from Excel/CSV rows to create v2
        $importExcel = $this->postJson("/api/v1/crm/leads/{$this->lead->id}/rab/import-excel", [
            'merge' => true,
            'rows' => [
                [
                    'category' => 'Integrasi',
                    'name' => 'Integrasi SAP RFC',
                    'description' => 'Sinkronisasi jurnal harian',
                    'role' => 'CUSTOM_UNKNOWN_ROLE', // triggers rate card warning
                    'qty' => 12,
                    'unit' => 'Mandays',
                    'unit_price' => 2_500_000,
                ],
            ],
        ]);
        $importExcel->assertStatus(201);
        $this->assertSame(2, $importExcel->json('data.version'));
        $this->assertNotEmpty($importExcel->json('data.warnings'));

        // 3. Compare v1 and v2 diff
        $diffResp = $this->getJson("/api/v1/crm/leads/{$this->lead->id}/rab/diff?from_version=1&to_version=2");
        $diffResp->assertStatus(200);
        $this->assertEqualsWithDelta(30_000_000.0, $diffResp->json('data.summary_diff.subtotal_diff'), 0.01);

        // 4. Export Excel CSV
        $exportResp = $this->get("/api/v1/crm/leads/{$this->lead->id}/rab/export-excel");
        $exportResp->assertStatus(200);
        $csvContent = $exportResp->streamedContent();
        $this->assertStringContainsString('Integrasi SAP RFC', $csvContent);
    }
}
