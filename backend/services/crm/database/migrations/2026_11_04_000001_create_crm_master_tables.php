<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lead Sources (FR-CRM-01)
        Schema::create('crm_lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->string('category', 64)->default('INBOUND');
            $table->json('sub_sources')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Rejection Reasons (FR-CRM-10)
        Schema::create('crm_rejection_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->string('category', 64)->default('GENERAL');
            $table->boolean('requires_competitor')->default(false);
            $table->boolean('requires_evaluation_notes')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Project Types
        Schema::create('crm_project_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Industries
        Schema::create('crm_industries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Pipeline Stages & Probabilities
        Schema::create('crm_pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->unsignedInteger('sequence');
            $table->unsignedInteger('win_probability_pct')->default(10);
            $table->unsignedInteger('sla_hours')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Rate Cards role × level (FR-CRM-16)
        Schema::create('crm_rate_cards', function (Blueprint $table) {
            $table->id();
            $table->string('role', 64);
            $table->string('level', 32);
            $table->decimal('cost_rate_idr', 18, 2); // internal HPP per manday
            $table->decimal('sell_rate_idr', 18, 2); // client billing rate per manday
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['role', 'level', 'valid_from']);
        });

        // 7. Module Catalog (min. 40 modules, FR-CRM-16)
        Schema::create('crm_module_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->string('category', 64);
            $table->json('default_mandays_by_role');
            $table->unsignedInteger('total_mandays');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Document Templates (Quotation & MoU, Lampiran D, E)
        Schema::create('crm_document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('type', 32); // QUOTATION, MOU, ADDENDUM, NDA
            $table->string('title', 255);
            $table->longText('body');
            $table->json('merge_variables')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 9. Contract Clauses 22 Articles (Lampiran H)
        Schema::create('crm_contract_clauses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('clause_no')->unique(); // 1..22
            $table->string('title', 255);
            $table->longText('body');
            $table->json('variables')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Canned Responses WA / Templates (FR-CRM-11)
        Schema::create('crm_canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('channel', 32)->default('WHATSAPP');
            $table->string('category', 64)->default('GENERAL');
            $table->string('title', 255);
            $table->text('content');
            $table->json('merge_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_canned_responses');
        Schema::dropIfExists('crm_contract_clauses');
        Schema::dropIfExists('crm_document_templates');
        Schema::dropIfExists('crm_module_catalog');
        Schema::dropIfExists('crm_rate_cards');
        Schema::dropIfExists('crm_pipeline_stages');
        Schema::dropIfExists('crm_industries');
        Schema::dropIfExists('crm_project_types');
        Schema::dropIfExists('crm_rejection_reasons');
        Schema::dropIfExists('crm_lead_sources');
    }
};

