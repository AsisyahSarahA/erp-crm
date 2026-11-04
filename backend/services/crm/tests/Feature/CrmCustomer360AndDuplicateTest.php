<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\CustomFieldDefinition;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Quotation;
use App\Support\UserContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrmCustomer360AndDuplicateTest extends TestCase
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

    public function test_customer_creation_encrypts_npwp_at_rest_and_normalizes_phone_to_e164(): void
    {
        $igSource = LeadSource::factory()->create([
            'code' => 'INSTAGRAM',
            'name' => 'Instagram',
            'channel_group' => 'ORGANIC_SOCIAL',
        ]);

        CustomFieldDefinition::create([
            'entity_type' => 'customer',
            'field_key' => 'erp_legacy_vendor',
            'label' => 'Vendor ERP Sebelumnya',
            'field_type' => 'text',
            'is_required' => false,
        ]);

        $response = $this->withInternalUser([
            'id' => 10,
            'name' => 'Andi Sales',
            'role' => 'sales',
            'team_id' => 1,
        ])->postJson('/api/v1/crm/customers', [
            'legal_name' => 'PT Nusantara Siber Solusi',
            'brand_name' => 'NusaCyber',
            'type' => 'perusahaan',
            'industry' => 'TECH_SAAS',
            'size' => '51-200',
            'npwp' => '01.442.123.4-012.0000',
            'nib' => '9120301234567',
            'is_pkp' => true,
            'is_pph23_withholder' => true,
            'phone' => '0812-9988-7766',
            'email' => 'info@nusacyber.co.id',
            'first_source_id' => $igSource->id,
            'utm' => [
                'source' => 'instagram',
                'medium' => 'social',
                'campaign' => 'erp_q4_launch',
            ],
            'billing_address' => [
                'street' => 'Jl. Jend. Sudirman Kav. 52',
                'city' => 'Jakarta Selatan',
                'postal_code' => '12190',
            ],
            'contacts' => [
                [
                    'name' => 'Rizky Pratama',
                    'role' => 'decision_maker',
                    'title' => 'Chief Information Officer',
                    'phone_wa' => '0812 9988 7766',
                    'email' => 'rizky@nusacyber.co.id',
                    'is_primary' => true,
                    'consent_comm' => true,
                    'preferred_channel' => 'whatsapp',
                ],
                [
                    'name' => 'Sari Kusuma',
                    'role' => 'finance_lead',
                    'title' => 'Head of Finance & Tax',
                    'phone_wa' => '0813-1122-3344',
                    'email' => 'sari.tax@nusacyber.co.id',
                    'is_primary' => false,
                ],
            ],
            'custom_fields' => [
                'erp_legacy_vendor' => 'SAP Business One',
            ],
        ]);

        $response->assertCreated();
        $data = $response->json('data');

        $this->assertMatchesRegularExpression('/^CUS-\d{4}$/', $data['code']);
        $this->assertEquals('+6281299887766', $data['phone']);
        $this->assertCount(2, $data['contacts']);
        $this->assertEquals('+6281299887766', $data['contacts'][0]['phone_wa']);
        $this->assertEquals('decision_maker', $data['contacts'][0]['role']);

        // Verify NPWP is encrypted at rest in raw database row
        $rawRow = DB::table('crm_customers')->where('id', $data['id'])->first();
        $this->assertNotNull($rawRow->npwp);
        $this->assertNotEquals('01.442.123.4-012.0000', $rawRow->npwp);
        $this->assertNotNull($rawRow->npwp_hash);

        // Verify Eloquent model transparently decrypts NPWP
        $customerModel = Customer::find($data['id']);
        $this->assertEquals('01.442.123.4-012.0000', $customerModel->npwp);

        // Verify Attribution & UTM were recorded
        $this->assertDatabaseHas('crm_customer_attributions', [
            'customer_id' => $data['id'],
            'source_id' => $igSource->id,
            'touch_type' => 'first_touch',
            'utm_source' => 'instagram',
            'utm_campaign' => 'erp_q4_launch',
        ]);
    }

    public function test_ac_crm_02_duplicate_detection_by_whatsapp_email_and_fuzzy_company_name(): void
    {
        $existing = Customer::factory()->create([
            'legal_name' => 'PT Megah Nusantara Teknologi',
            'brand_name' => 'Megah Tech',
            'phone' => '+6281234567890',
            'email' => 'procurement@megahnusantara.co.id',
        ]);

        Contact::factory()->create([
            'customer_id' => $existing->id,
            'name' => 'Budi Santoso',
            'role' => 'decision_maker',
            'phone_wa' => '+6281234567890',
            'email' => 'budi@megahnusantara.co.id',
            'is_primary' => true,
        ]);

        // 1. Check via /customers/duplicates-check with local format 0812-3456-7890
        $dupResponse = $this->postJson('/api/v1/crm/customers/duplicates-check', [
            'phone_wa' => '0812-3456-7890',
        ]);

        $dupResponse->assertOk()
            ->assertJsonPath('data.has_duplicates', true)
            ->assertJsonPath('data.dialog_prompt', 'Customer sudah ada: Tautkan / Buat baru / Gabung')
            ->assertJsonPath('data.available_actions', ['LINK_EXISTING', 'CREATE_NEW', 'MERGE'])
            ->assertJsonPath('data.matches.0.customer_id', $existing->id)
            ->assertJsonPath('data.matches.0.match_reasons.0.type', 'PHONE_WA_MATCH');

        // 2. Check fuzzy company name >= 85% ("Megah Nusantara PT" vs "PT Megah Nusantara Teknologi")
        $fuzzyResponse = $this->postJson('/api/v1/crm/customers/duplicates-check', [
            'legal_name' => 'Megah Nusantara PT',
        ]);

        $fuzzyResponse->assertOk()
            ->assertJsonPath('data.has_duplicates', true)
            ->assertJsonPath('data.matches.0.customer_id', $existing->id);
        $this->assertGreaterThanOrEqual(85.0, $fuzzyResponse->json('data.matches.0.match_score'));

        // 3. AC-CRM-02: Submitting POST /customers with existing WA number triggers 409 duplicate dialog
        $conflictResp = $this->postJson('/api/v1/crm/customers', [
            'legal_name' => 'PT Baru Namun Nomor Sama',
            'phone' => '081234567890',
        ]);

        $conflictResp->assertStatus(409)
            ->assertJsonPath('detail', 'Customer sudah ada: Tautkan / Buat baru / Gabung')
            ->assertJsonPath('duplicate_check.has_duplicates', true)
            ->assertJsonPath('duplicate_check.available_actions', ['LINK_EXISTING', 'CREATE_NEW', 'MERGE']);

        // 4. Choosing LINK_EXISTING links to existing customer without creating duplicate
        $linkResp = $this->postJson('/api/v1/crm/customers', [
            'legal_name' => 'PT Baru Namun Nomor Sama',
            'phone' => '081234567890',
            'duplicate_resolution' => 'LINK_EXISTING',
        ]);

        $linkResp->assertOk()
            ->assertJsonPath('data.id', $existing->id)
            ->assertJsonPath('meta.resolution', 'LINKED_EXISTING');
    }

    public function test_br_crm_09_merge_customer_preserves_history_leads_contacts_and_attributions(): void
    {
        $sourceWeb = LeadSource::factory()->create(['code' => 'WEBSITE', 'name' => 'Website']);
        $sourceEvent = LeadSource::factory()->create(['code' => 'EVENT_WEBINAR', 'name' => 'Tech Event']);

        $master = Customer::factory()->create([
            'code' => 'CUS-0101',
            'legal_name' => 'PT Sinar Logistik Utama',
            'brand_name' => null,
            'first_source_id' => $sourceWeb->id,
            'latest_source_id' => $sourceWeb->id,
            'status' => 'prospect',
        ]);

        $secondary = Customer::factory()->create([
            'code' => 'CUS-0102',
            'legal_name' => 'Sinar Logistik Utama PT',
            'brand_name' => 'SinarLog',
            'first_source_id' => $sourceEvent->id,
            'latest_source_id' => $sourceEvent->id,
            'status' => 'active',
        ]);

        Contact::factory()->create([
            'customer_id' => $master->id,
            'name' => 'Antonius (Master PIC)',
            'phone_wa' => '+628111000111',
            'is_primary' => true,
        ]);

        $secondaryContact = Contact::factory()->create([
            'customer_id' => $secondary->id,
            'name' => 'Bella (Finance PIC)',
            'role' => 'finance_lead',
            'phone_wa' => '+628111000222',
            'is_primary' => true,
        ]);

        CustomerAttribution::create([
            'customer_id' => $master->id,
            'source_id' => $sourceWeb->id,
            'touch_type' => 'first_touch',
            'touched_at' => now()->subDays(10),
        ]);

        CustomerAttribution::create([
            'customer_id' => $secondary->id,
            'source_id' => $sourceEvent->id,
            'touch_type' => 'latest_touch',
            'touched_at' => now()->subDays(2),
        ]);

        $leadOnSecondary = Lead::create([
            'lead_code' => 'LD-2026-0901',
            'customer_id' => $secondary->id,
            'title' => 'Sistem WMS Gudang Otomatis',
            'project_type' => 'ERP_CUSTOM',
            'priority' => 'HIGH',
            'stage' => 'QUOTATION',
            'source' => 'EVENT_WEBINAR',
            'est_value' => 450000000,
            'win_probability' => 55,
        ]);

        $contractOnSecondary = Contract::create([
            'customer_id' => $secondary->id,
            'contract_number' => 'CTR/2026/11/0099',
            'type' => 'SLA',
            'title' => 'Kontrak SLA Maintenance Server',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'value' => 120000000,
            'status' => 'ACTIVE',
        ]);

        $mergeResponse = $this->withInternalUser([
            'id' => 1,
            'name' => 'Sales Manager',
            'role' => 'sales_manager',
        ])->postJson("/api/v1/crm/customers/{$master->id}/merge", [
            'source_customer_id' => $secondary->id,
            'reason' => 'Duplikasi entri dari pameran dan form web',
        ]);

        $mergeResponse->assertOk()
            ->assertJsonPath('data.master_customer.id', $master->id)
            ->assertJsonPath('data.master_customer.brand_name', 'SinarLog')
            ->assertJsonPath('data.master_customer.status', 'active')
            ->assertJsonPath('data.merge_history.merged_customer_id', $secondary->id)
            ->assertJsonPath('data.merge_history.merged_customer_code', 'CUS-0102');

        // Verify Lead, Contact, Attribution, and Contract were migrated to Master
        $this->assertEquals($master->id, $leadOnSecondary->fresh()->customer_id);
        $this->assertEquals($master->id, $secondaryContact->fresh()->customer_id);
        $this->assertFalse($secondaryContact->fresh()->is_primary); // Master already had primary contact
        $this->assertEquals($master->id, $contractOnSecondary->fresh()->customer_id);

        // Verify Master has both first_source (Website) and latest_source (Event)
        $masterFresh = $master->fresh();
        $this->assertEquals($sourceWeb->id, $masterFresh->first_source_id);
        $this->assertEquals($sourceEvent->id, $masterFresh->latest_source_id);

        // Verify Secondary Customer is soft-deleted and points to master
        $secondaryTrashed = Customer::withTrashed()->find($secondary->id);
        $this->assertTrue($secondaryTrashed->trashed());
        $this->assertEquals($master->id, $secondaryTrashed->merged_into_id);
    }

    public function test_npwp_masking_in_resource_and_verification_permission_by_finance_ar(): void
    {
        $customer = Customer::factory()->create([
            'legal_name' => 'PT FinTech Amanah',
            'npwp' => '01.234.567.8-901.0000',
            'owner_id' => 10,
            'team_id' => 1,
        ]);

        // 1. Sales user sees masked NPWP
        $salesView = $this->withInternalUser([
            'id' => 10,
            'name' => 'Sales Rep',
            'role' => 'sales',
            'team_id' => 1,
        ])->getJson("/api/v1/crm/customers/{$customer->id}");

        $salesView->assertOk()
            ->assertJsonPath('data.npwp', '01.234.***.*-***.000')
            ->assertJsonPath('data.npwp_is_masked', true);

        // 2. Sales user CANNOT verify NPWP -> 403 Forbidden
        $salesVerify = $this->withInternalUser([
            'id' => 10,
            'name' => 'Sales Rep',
            'role' => 'sales',
            'team_id' => 1,
        ])->postJson("/api/v1/crm/customers/{$customer->id}/verify-npwp", [
            'is_pkp' => true,
            'is_pph23_withholder' => true,
        ]);

        $salesVerify->assertStatus(403);

        // 3. Finance AR Specialist views UNMASKED NPWP and CAN verify NPWP
        $financeVerify = $this->withInternalUser([
            'id' => 55,
            'name' => 'Finance AR Specialist',
            'role' => 'finance_ar',
            'permissions' => ['finance.npwp.verify', 'finance.tax.view'],
        ])->postJson("/api/v1/crm/customers/{$customer->id}/verify-npwp", [
            'is_pkp' => true,
            'is_pph23_withholder' => true,
            'notes' => 'Sesuai SKPKP dan kartu NPWP 16 digit',
        ]);

        $financeVerify->assertOk()
            ->assertJsonPath('data.npwp', '01.234.567.8-901.0000')
            ->assertJsonPath('data.npwp_is_masked', false)
            ->assertJsonPath('data.is_npwp_verified', true)
            ->assertJsonPath('data.npwp_verified_by', 55);
    }

    public function test_policy_sales_can_only_access_own_and_team_customers(): void
    {
        $ownCustomer = Customer::factory()->create([
            'legal_name' => 'PT Milik Sales Sendiri',
            'owner_id' => 10,
            'team_id' => 1,
        ]);

        $teamCustomer = Customer::factory()->create([
            'legal_name' => 'PT Milik Rekan Satu Tim',
            'owner_id' => 11,
            'team_id' => 1,
        ]);

        $otherTeamCustomer = Customer::factory()->create([
            'legal_name' => 'PT Milik Tim Lain',
            'owner_id' => 99,
            'team_id' => 2,
        ]);

        $salesUser = [
            'id' => 10,
            'name' => 'Sales A',
            'role' => 'sales',
            'team_id' => 1,
            'team_member_ids' => [10, 11],
        ];

        // 1. Index only returns own + team customers (2 customers, excluding team 2)
        $listResp = $this->withInternalUser($salesUser)->getJson('/api/v1/crm/customers');
        $listResp->assertOk();
        $returnedIds = collect($listResp->json('data'))->pluck('id')->all();
        $this->assertContains($ownCustomer->id, $returnedIds);
        $this->assertContains($teamCustomer->id, $returnedIds);
        $this->assertNotContains($otherTeamCustomer->id, $returnedIds);

        // 2. Show own & team customer succeeds
        $this->withInternalUser($salesUser)->getJson("/api/v1/crm/customers/{$ownCustomer->id}")->assertOk();
        $this->withInternalUser($salesUser)->getJson("/api/v1/crm/customers/{$teamCustomer->id}")->assertOk();

        // 3. Show or update other team's customer is 403 Forbidden
        $this->withInternalUser($salesUser)->getJson("/api/v1/crm/customers/{$otherTeamCustomer->id}")->assertStatus(403);
        $this->withInternalUser($salesUser)->putJson("/api/v1/crm/customers/{$otherTeamCustomer->id}", [
            'brand_name' => 'Unauthorized Edit',
        ])->assertStatus(403);
    }

    public function test_customer_360_endpoint_and_health_score_calculation(): void
    {
        $source = LeadSource::factory()->create(['code' => 'LINKEDIN', 'name' => 'LinkedIn']);
        $customer = Customer::factory()->create([
            'legal_name' => 'PT Enterprise Korporasi Raya',
            'first_source_id' => $source->id,
            'latest_source_id' => $source->id,
            'owner_id' => 10,
            'team_id' => 1,
            'nps_score' => 80,
            'csat_score' => 4.5,
            'financial_snapshot' => [
                'total_contract_value' => 850000000,
                'outstanding_ar' => 150000000,
                'overdue_ar' => 0,
                'dso_days' => 12,
                'collectibility_ratio' => 100.0,
                'on_time_payment_rate' => 100.0,
                'overdue_invoices_count' => 0,
            ],
            'external_snapshots' => [
                'projects_count' => 3,
                'days_since_last_activity' => 2,
                'open_tickets_count' => 0,
                'critical_tickets_count' => 0,
                'projects' => [
                    ['id' => 1, 'code' => 'PRJ-2026-0001', 'name' => 'ERP Core Implementation', 'status' => 'ACTIVE'],
                ],
                'invoices' => [
                    ['id' => 10, 'number' => 'INV/2026/11/0001', 'amount' => 255000000, 'status' => 'PAID'],
                ],
            ],
        ]);

        Contact::factory()->create([
            'customer_id' => $customer->id,
            'name' => 'Ir. Hendrawan',
            'role' => 'decision_maker',
            'is_primary' => true,
        ]);

        Contact::factory()->create([
            'customer_id' => $customer->id,
            'name' => 'Bambang Tech',
            'role' => 'technical_lead',
            'is_primary' => false,
        ]);

        $lead = Lead::create([
            'lead_code' => 'LD-2026-0500',
            'customer_id' => $customer->id,
            'title' => 'ERP Core Implementation',
            'project_type' => 'ERP_CUSTOM',
            'priority' => 'HOT',
            'stage' => 'DEAL_WON',
            'source' => 'LINKEDIN',
            'est_value' => 850000000,
            'win_probability' => 100,
            'sales_pic_id' => 10,
        ]);

        Quotation::create([
            'lead_id' => $lead->id,
            'quotation_number' => 'QT/2026/11/0500-R1',
            'version' => 1,
            'status' => 'ACCEPTED',
            'dpp' => 765765765,
            'total' => 850000000,
        ]);

        // Recalculate health score with high performance metrics
        $healthResp = $this->postJson("/api/v1/crm/customers/{$customer->id}/health-score");
        $healthResp->assertOk()
            ->assertJsonPath('data.health_score', 99) // 40 (payment) + 20 (3 projects) + 15 (activity) + 15 (0 tickets) + 9 (CSAT 4.5/5) = 99
            ->assertJsonPath('data.breakdown.status_label', 'HEALTHY');

        // Degrade payment timeliness & add overdue tickets -> verify score drops proportionally
        $degradedResp = $this->postJson("/api/v1/crm/customers/{$customer->id}/health-score", [
            'financial_snapshot' => [
                'on_time_payment_rate' => 40.0,
                'overdue_invoices_count' => 2,
                'dso_days' => 65,
            ],
            'open_tickets_count' => 3,
            'critical_tickets_count' => 1,
            'days_since_last_activity' => 45,
        ]);
        $degradedResp->assertOk();
        $this->assertLessThan(50, $degradedResp->json('data.health_score'));
        $this->assertEquals('CRITICAL', $degradedResp->json('data.breakdown.status_label'));

        // Fetch full Customer 360
        $resp360 = $this->getJson("/api/v1/crm/customers/{$customer->id}/360");
        $resp360->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'code',
                    'profile',
                    'legal_and_tax',
                    'attribution',
                    'stakeholder_matrix' => [
                        'primary_contact',
                        'by_role' => ['decision_maker', 'technical_lead', 'finance_lead', 'influencer'],
                        'all_contacts',
                    ],
                    'history' => ['leads', 'quotations', 'deals', 'projects', 'contracts', 'tickets', 'invoices'],
                    'financials' => ['total_contract_value', 'outstanding_ar', 'overdue_ar', 'dso_days', 'collectibility_ratio', 'payment_rating'],
                    'health' => ['score', 'status_label', 'breakdown'],
                    'communications',
                    'account_plan',
                    'custom_fields',
                    'merge_history',
                ],
            ])
            ->assertJsonPath('data.financials.total_contract_value', 850000000)
            ->assertJsonCount(1, 'data.stakeholder_matrix.by_role.decision_maker')
            ->assertJsonCount(1, 'data.stakeholder_matrix.by_role.technical_lead')
            ->assertJsonCount(1, 'data.history.deals');
    }

    public function test_pdp_anonymization_scrambles_pii_and_records_audit(): void
    {
        $customer = Customer::factory()->create([
            'legal_name' => 'PT Privasi Data Nusantara',
            'npwp' => '09.888.777.6-555.0000',
            'email' => 'direksi@privasidata.co.id',
            'phone' => '+6281999888777',
        ]);

        Contact::factory()->create([
            'customer_id' => $customer->id,
            'name' => 'Rahasia Subjek Data',
            'email' => 'rahasia@privasidata.co.id',
            'phone_wa' => '+6281999888777',
            'consent_comm' => true,
        ]);

        // Sales cannot anonymize
        $this->withInternalUser([
            'id' => 10,
            'role' => 'sales',
        ])->postJson("/api/v1/crm/customers/{$customer->id}/anonymize", [
            'reason' => 'Permintaan penghapusan UU PDP',
        ])->assertStatus(403);

        // Legal Counsel / Super Admin can anonymize
        $anonResp = $this->withInternalUser([
            'id' => 2,
            'name' => 'Legal Counsel',
            'role' => 'legal_counsel',
        ])->postJson("/api/v1/crm/customers/{$customer->id}/anonymize", [
            'reason' => 'Permintaan resmi penghapusan data pribadi UU PDP Pasal 43',
        ]);

        $anonResp->assertOk()
            ->assertJsonPath('data.is_anonymized', true)
            ->assertJsonPath('data.npwp', null);

        $fresh = $customer->fresh(['contacts']);
        $this->assertStringStartsWith('ANONYMIZED-', $fresh->legal_name);
        $this->assertNull($fresh->npwp);
        $this->assertTrue($fresh->is_anonymized);
        $this->assertStringStartsWith('REDACTED-CONTACT-', $fresh->contacts->first()->name);
        $this->assertFalse($fresh->contacts->first()->consent_comm);
    }

    public function test_csv_import_and_export_customers(): void
    {
        $csvContent = "legal_name,brand_name,type,industry,phone,email,primary_contact_name,primary_contact_phone_wa\n"
            . "\"PT Impor Digital Satu\",\"ImporSatu\",perusahaan,TECH_SAAS,081277778888,contact@imporsatu.id,\"Deni Impor\",081277778888\n";

        $importResp = $this->postJson('/api/v1/crm/customers/import', [
            'csv_content' => $csvContent,
        ]);

        $importResp->assertOk()
            ->assertJsonPath('data.imported_count', 1)
            ->assertJsonPath('data.skipped_duplicates_count', 0);

        // Importing the exact same CSV again detects duplicate and skips it
        $duplicateImportResp = $this->postJson('/api/v1/crm/customers/import', [
            'csv_content' => $csvContent,
        ]);

        $duplicateImportResp->assertOk()
            ->assertJsonPath('data.imported_count', 0)
            ->assertJsonPath('data.skipped_duplicates_count', 1);

        // Export CSV
        $exportResp = $this->get('/api/v1/crm/customers/export');
        $exportResp->assertOk();
        $this->assertStringContainsString('PT Impor Digital Satu', $exportResp->getContent());
    }
}

