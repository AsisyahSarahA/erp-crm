<?php

namespace Tests\Feature;

use Database\Seeders\CannedResponseSeeder;
use Database\Seeders\ContractClauseSeeder;
use Database\Seeders\CrmDemoSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\IndustrySeeder;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\ProjectTypeSeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RejectionReasonSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmMasterAndDemoSeederTest extends TestCase
{
    public function test_crm_master_seeders_run_and_are_idempotent(): void
    {
        // First run
        $this->seed(DatabaseSeeder::class);

        $initialLeadSources = DB::table('crm_lead_sources')->count();
        $initialReasons = DB::table('crm_rejection_reasons')->count();
        $initialModules = DB::table('crm_module_catalog')->count();
        $initialClauses = DB::table('crm_contract_clauses')->count();

        $this->assertGreaterThanOrEqual(12, $initialLeadSources);
        $this->assertEquals(8, $initialReasons);
        $this->assertGreaterThanOrEqual(40, $initialModules);
        $this->assertEquals(22, $initialClauses);

        // Second run to verify idempotency (no duplicate errors, counts remain exact)
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals($initialLeadSources, DB::table('crm_lead_sources')->count());
        $this->assertEquals($initialReasons, DB::table('crm_rejection_reasons')->count());
        $this->assertEquals($initialModules, DB::table('crm_module_catalog')->count());
        $this->assertEquals($initialClauses, DB::table('crm_contract_clauses')->count());
    }

    public function test_lead_sources_cover_all_prd_fr_crm_01_channels(): void
    {
        $this->seed(LeadSourceSeeder::class);

        $requiredSources = [
            'INSTAGRAM', 'WEBSITE', 'TIKTOK', 'LINKEDIN', 'FACEBOOK',
            'WHATSAPP_MARKETING', 'GOOGLE_ADS', 'META_ADS', 'REFERRAL',
            'EVENT_WEBINAR', 'COLD_OUTREACH', 'MARKETPLACE', 'OTHER'
        ];

        foreach ($requiredSources as $code) {
            $this->assertDatabaseHas('crm_lead_sources', ['code' => $code]);
        }
    }

    public function test_rejection_reasons_cover_all_prd_fr_crm_10_categories(): void
    {
        $this->seed(RejectionReasonSeeder::class);

        $requiredCodes = [
            'PRICE_LOST', 'CLIENT_CANCELLED', 'COMPETITOR', 'SCOPE_MISMATCH',
            'TIMELINE_MISMATCH', 'UNRESPONSIVE', 'BUDGET_UNAVAILABLE', 'OTHER'
        ];

        foreach ($requiredCodes as $code) {
            $this->assertDatabaseHas('crm_rejection_reasons', ['code' => $code]);
        }
    }

    public function test_module_catalog_has_at_least_40_modules_with_valid_mandays(): void
    {
        $this->seed(ModuleCatalogSeeder::class);

        $modules = DB::table('crm_module_catalog')->get();
        $this->assertGreaterThanOrEqual(40, $modules->count());

        foreach ($modules as $module) {
            $this->assertNotEmpty($module->name);
            $this->assertNotEmpty($module->category);
            $this->assertGreaterThan(0, $module->total_mandays);

            $mandays = json_decode($module->default_mandays_by_role, true);
            $this->assertIsArray($mandays);
            $this->assertNotEmpty($mandays);
        }
    }

    public function test_contract_clauses_has_exactly_22_articles_from_lampiran_h(): void
    {
        $this->seed(ContractClauseSeeder::class);

        $clauses = DB::table('crm_contract_clauses')->orderBy('clause_no')->get();
        $this->assertCount(22, $clauses);

        for ($i = 1; $i <= 22; $i++) {
            $clause = $clauses->firstWhere('clause_no', $i);
            $this->assertNotNull($clause, "Pasal {$i} harus terdaftar di contract_clauses");
            $this->assertNotEmpty($clause->title);
            $this->assertNotEmpty($clause->body);
        }

        // Specific articles check
        $this->assertEquals('Ketentuan Umum & Definisi', $clauses->firstWhere('clause_no', 1)->title);
        $this->assertEquals('Nilai Kontrak & Termin (TOP)', $clauses->firstWhere('clause_no', 6)->title);
        $this->assertEquals('Hak Cipta & Kepemilikan Source Code', $clauses->firstWhere('clause_no', 11)->title);
        $this->assertEquals('Keabsahan Tanda Tangan Elektronik & e-Meterai', $clauses->firstWhere('clause_no', 22)->title);
    }

    public function test_crm_demo_seeder_creates_30_leads_and_10_customers(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(CrmDemoSeeder::class);

        $this->assertEquals(10, DB::table('crm_customers')->count());
        $this->assertEquals(30, DB::table('crm_leads')->count());

        // Check leads exist across all 7 stages
        $stages = DB::table('crm_leads')->pluck('stage')->unique()->toArray();
        $this->assertContains('NEW_LEAD', $stages);
        $this->assertContains('FOLLOW_UP', $stages);
        $this->assertContains('REQUIREMENT', $stages);
        $this->assertContains('QUOTATION', $stages);
        $this->assertContains('NEGOTIATION', $stages);
        $this->assertContains('DEAL_WON', $stages);
        $this->assertContains('LOST', $stages);

        // Check quotations exist for DEAL_WON
        $dealWonLeads = DB::table('crm_leads')->where('stage', 'DEAL_WON')->count();
        $this->assertEquals(5, $dealWonLeads);
        $this->assertGreaterThanOrEqual(5, DB::table('crm_quotations')->count());
    }
}

