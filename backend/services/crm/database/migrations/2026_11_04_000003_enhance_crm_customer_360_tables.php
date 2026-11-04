<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_lead_sources with channel_group
        Schema::table('crm_lead_sources', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_lead_sources', 'channel_group')) {
                $table->string('channel_group', 64)->default('INBOUND')->after('category');
            }
        });

        // 2. Enhance crm_customers for full Customer 360, encrypted NPWP, attribution, verification & PDP
        Schema::table('crm_customers', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->unique()->after('id');
            $table->string('type', 32)->default('perusahaan')->after('brand_name');
            $table->string('size', 64)->nullable()->after('industry');
            $table->string('website', 255)->nullable()->after('size');
            $table->string('logo_url', 255)->nullable()->after('website');
            $table->text('npwp')->nullable()->change();
            $table->string('npwp_hash', 64)->nullable()->index()->after('npwp');
            $table->string('nib', 64)->nullable()->after('npwp_hash');
            $table->boolean('is_pkp')->default(false)->after('nib');
            $table->boolean('is_pph23_withholder')->default(false)->after('is_pkp');
            $table->unsignedBigInteger('npwp_verified_by')->nullable()->after('is_pph23_withholder');
            $table->timestamp('npwp_verified_at')->nullable()->after('npwp_verified_by');
            $table->foreignId('first_source_id')->nullable()->after('status')->constrained('crm_lead_sources')->nullOnDelete();
            $table->foreignId('latest_source_id')->nullable()->after('first_source_id')->constrained('crm_lead_sources')->nullOnDelete();
            $table->unsignedBigInteger('owner_id')->nullable()->index()->after('latest_source_id');
            $table->unsignedBigInteger('team_id')->nullable()->index()->after('owner_id');
            $table->unsignedBigInteger('company_id')->default(1)->index()->after('team_id');
            $table->json('office_address')->nullable()->after('billing_address');
            $table->json('health_breakdown')->nullable()->after('health_score');
            $table->integer('nps_score')->nullable()->after('health_breakdown');
            $table->decimal('csat_score', 5, 2)->nullable()->after('nps_score');
            $table->json('account_plan')->nullable()->after('csat_score');
            $table->json('financial_snapshot')->nullable()->after('account_plan');
            $table->json('external_snapshots')->nullable()->after('financial_snapshot');
            $table->boolean('is_anonymized')->default(false)->after('external_snapshots');
            $table->timestamp('anonymized_at')->nullable()->after('is_anonymized');
            $table->unsignedBigInteger('anonymized_by')->nullable()->after('anonymized_at');
            $table->unsignedBigInteger('merged_into_id')->nullable()->index()->after('anonymized_by');
            $table->string('phone', 32)->nullable()->change();
        });

        // 3. Enhance crm_contacts for multi-stakeholder matrix, E.164 phone_wa, consent & preferred channel
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->string('role', 64)->default('decision_maker')->after('name');
            $table->string('title', 128)->nullable()->after('role');
            $table->string('phone_wa', 32)->nullable()->index()->after('phone');
            $table->string('phone', 32)->nullable()->change();
            $table->boolean('consent_comm')->default(true)->after('is_primary');
            $table->timestamp('consent_at')->nullable()->after('consent_comm');
            $table->string('preferred_channel', 32)->default('whatsapp')->after('consent_at');
            $table->string('department', 128)->nullable()->after('preferred_channel');
            $table->text('notes')->nullable()->after('department');
            $table->boolean('is_anonymized')->default(false)->after('notes');
            $table->softDeletes();
        });

        // 4. Customer Attributions (first/latest touch, UTM)
        Schema::create('crm_customer_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->foreignId('source_id')->constrained('crm_lead_sources')->cascadeOnDelete();
            $table->string('touch_type', 32)->default('first_touch'); // first_touch, latest_touch, multi_touch
            $table->string('sub_source', 128)->nullable();
            $table->string('campaign_name', 128)->nullable();
            $table->string('utm_source', 128)->nullable();
            $table->string('utm_medium', 128)->nullable();
            $table->string('utm_campaign', 128)->nullable();
            $table->string('utm_term', 128)->nullable();
            $table->string('utm_content', 128)->nullable();
            $table->string('referrer_url', 512)->nullable();
            $table->timestamp('touched_at')->nullable();
            $table->timestamps();
        });

        // 5. Custom Fields (Definitions + Values)
        Schema::create('crm_custom_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 64)->default('customer'); // customer, contact, lead
            $table->string('field_key', 64);
            $table->string('label', 128);
            $table->string('field_type', 32)->default('text'); // text, number, date, boolean, select, multi_select, json
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('company_id')->default(1);
            $table->timestamps();

            $table->unique(['entity_type', 'field_key', 'company_id'], 'crm_cf_def_unique');
        });

        Schema::create('crm_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('definition_id')->constrained('crm_custom_field_definitions')->cascadeOnDelete();
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id');
            $table->text('value_text')->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->unique(['definition_id', 'entity_type', 'entity_id'], 'crm_cf_val_unique');
            $table->index(['entity_type', 'entity_id']);
        });

        // 6. Customer Merge History (BR-CRM-09)
        Schema::create('crm_customer_merge_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->unsignedBigInteger('merged_customer_id')->index();
            $table->string('merged_customer_code', 32);
            $table->json('merged_customer_snapshot');
            $table->json('migrated_records_summary');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('merged_by')->nullable();
            $table->timestamp('merged_at');
            $table->timestamps();
        });

        // 7. Contracts (Project & SLA Contracts for Customer 360)
        Schema::create('crm_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->string('contract_number', 64)->unique();
            $table->string('type', 32)->default('PROJECT'); // PROJECT, SLA, MAINTENANCE
            $table->string('title', 255);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('value', 18, 2)->default(0);
            $table->unsignedInteger('renewal_alert_days')->default(60);
            $table->string('status', 32)->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contracts');
        Schema::dropIfExists('crm_customer_merge_histories');
        Schema::dropIfExists('crm_custom_field_values');
        Schema::dropIfExists('crm_custom_field_definitions');
        Schema::dropIfExists('crm_customer_attributions');
    }
};

