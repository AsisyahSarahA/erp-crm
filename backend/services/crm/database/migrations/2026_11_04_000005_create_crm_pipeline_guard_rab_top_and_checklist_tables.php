<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_leads with weighted_value, active_rab_version_id, client_responded_at
        Schema::table('crm_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_leads', 'weighted_value')) {
                $table->decimal('weighted_value', 18, 2)->default(0)->after('est_value');
            }
            if (!Schema::hasColumn('crm_leads', 'active_rab_version_id')) {
                $table->unsignedBigInteger('active_rab_version_id')->nullable()->after('has_next_step');
            }
            if (!Schema::hasColumn('crm_leads', 'client_responded_at')) {
                $table->timestamp('client_responded_at')->nullable()->after('consent_at');
            }
        });

        // 2. RAB Versions (PRD §6.8)
        if (!Schema::hasTable('crm_rab_versions')) {
            Schema::create('crm_rab_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->unsignedInteger('version')->default(1);
                $table->string('source', 32)->default('MANUAL'); // AI, MANUAL, TEMPLATE
                $table->decimal('total_mandays', 10, 2)->default(0);
                $table->unsignedInteger('duration_weeks')->default(1);
                $table->decimal('contingency_pct', 7, 4)->default(10.0);
                $table->string('contingency_reason', 255)->nullable();
                $table->decimal('contingency_amount', 18, 2)->default(0);
                $table->string('discount_type', 16)->default('PERCENT'); // PERCENT, NOMINAL
                $table->decimal('discount_value', 18, 2)->default(0);
                $table->decimal('discount_pct', 7, 4)->default(0);
                $table->decimal('discount_amount', 18, 2)->default(0);
                $table->decimal('tax_rate', 7, 4)->default(11.0);
                $table->decimal('vat_rate', 7, 4)->default(11.0);
                $table->decimal('subtotal', 18, 2)->default(0);
                $table->decimal('dpp', 18, 2)->default(0);
                $table->decimal('tax', 18, 2)->default(0);
                $table->decimal('vat_amount', 18, 2)->default(0);
                $table->decimal('total', 18, 2)->default(0);
                $table->text('total_terbilang')->nullable();
                $table->decimal('hpp_internal', 18, 2)->default(0);
                $table->decimal('margin_pct', 7, 4)->default(0);
                $table->string('margin_status', 16)->default('GREEN');
                $table->boolean('wbs_completed')->default(true);
                $table->json('assumptions')->nullable();
                $table->json('risks')->nullable();
                $table->json('out_of_scope')->nullable();
                $table->string('status', 32)->default('DRAFT'); // DRAFT, ACTIVE, APPROVED, LOCKED, SUPERSEDED
                $table->string('created_by', 64)->nullable();
                $table->string('approved_by', 64)->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['lead_id', 'version']);
            });
        }

        // 3. RAB Items (PRD §6.8)
        if (!Schema::hasTable('crm_rab_items')) {
            Schema::create('crm_rab_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_version_id')->constrained('crm_rab_versions')->cascadeOnDelete();
                $table->unsignedBigInteger('lead_id')->nullable()->index();
                $table->unsignedBigInteger('module_catalog_id')->nullable();
                $table->unsignedInteger('sort_order')->default(1);
                $table->string('category', 64)->default('Master Data');
                $table->string('layer', 64)->nullable();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->string('role', 64)->default('BE');
                $table->string('role_code', 64)->default('BACKEND_MID');
                $table->decimal('mandays', 10, 2)->default(1);
                $table->decimal('qty', 10, 2)->default(1);
                $table->string('unit', 32)->default('Mandays');
                $table->decimal('daily_rate_sell', 18, 2)->default(0);
                $table->decimal('daily_rate_cost', 18, 2)->default(0);
                $table->decimal('unit_price', 18, 2)->default(0);
                $table->decimal('subtotal_sell', 18, 2)->default(0);
                $table->decimal('subtotal_cost', 18, 2)->default(0);
                $table->decimal('subtotal', 18, 2)->default(0);
                $table->decimal('cost_rate', 18, 2)->default(0);
                $table->decimal('hpp', 18, 2)->default(0);
                $table->string('complexity', 16)->default('MEDIUM');
                $table->unsignedInteger('confidence')->default(85);
                $table->boolean('is_optional')->default(false);
                $table->boolean('is_recurring')->default(false);
                $table->string('origin', 32)->default('MANUAL'); // AI, MANUAL, TEMPLATE
                $table->decimal('ai_confidence', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. TOP Schemes & Milestones (PRD §6.8 FR-CRM-07)
        if (!Schema::hasTable('crm_top_schemes')) {
            Schema::create('crm_top_schemes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->unsignedBigInteger('quotation_id')->nullable()->index();
                $table->string('preset_code', 64)->default('30_40_30');
                $table->string('preset_name', 64)->default('30/40/30');
                $table->decimal('total_percent', 7, 4)->default(0);
                $table->decimal('total_percentage', 7, 4)->default(0);
                $table->decimal('total_amount', 18, 2)->default(0);
                $table->boolean('is_balanced')->default(false);
                $table->string('created_by', 64)->nullable();
                $table->timestamp('locked_by_finance_at')->nullable();
                $table->unsignedBigInteger('locked_by_finance_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_top_milestones')) {
            Schema::create('crm_top_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('top_scheme_id')->constrained('crm_top_schemes')->cascadeOnDelete();
                $table->unsignedBigInteger('lead_id')->nullable()->index();
                $table->unsignedInteger('termin_no')->default(1);
                $table->unsignedInteger('term_number')->default(1);
                $table->string('name', 128)->nullable();
                $table->string('milestone_name', 128)->nullable();
                $table->string('trigger_event', 64)->default('SIGNING');
                $table->string('trigger_type', 32)->default('SIGNING');
                $table->decimal('percent', 7, 4)->default(0);
                $table->decimal('percentage', 7, 4)->default(0);
                $table->decimal('amount', 18, 2)->default(0);
                $table->decimal('amount_idr', 18, 2)->default(0);
                $table->text('terbilang_text')->nullable();
                $table->text('description')->nullable();
                $table->unsignedInteger('due_days')->default(14);
                $table->unsignedInteger('due_days_offset')->default(14);
                $table->string('project_milestone_ref', 128)->nullable();
                $table->timestamps();
            });
        }

        // 5. Delivery Readiness Checks (PRD FR-CRM-23)
        if (!Schema::hasTable('crm_delivery_readiness_checks')) {
            Schema::create('crm_delivery_readiness_checks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->unsignedBigInteger('reviewer_pm_id')->nullable();
                $table->string('status', 32)->default('READY'); // READY, READY_WITH_NOTES, NOT_READY
                $table->string('result', 32)->default('READY');
                $table->decimal('capacity_available_pct', 6, 2)->default(100);
                $table->text('notes')->nullable();
                $table->string('checked_by', 64)->nullable();
                $table->timestamp('checked_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('waived_by', 64)->nullable();
                $table->text('waiver_reason')->nullable();
                $table->text('waived_reason')->nullable();
                $table->timestamps();
            });
        }

        // 6. Lead Stage Documents (PRD FR-CRM-20)
        if (!Schema::hasTable('crm_lead_documents')) {
            Schema::create('crm_lead_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->string('stage_id', 64)->default('deal');
                $table->string('category', 64)->default('SIGNED_MOU');
                $table->string('doc_type', 64)->default('SIGNED_MOU');
                $table->string('title', 255)->nullable();
                $table->unsignedBigInteger('file_id')->nullable();
                $table->string('file_path', 512)->nullable();
                $table->string('file_url', 512)->nullable();
                $table->string('file_hash', 128)->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->boolean('is_mandatory_for_deal')->default(false);
                $table->boolean('is_required')->default(false);
                $table->string('uploaded_by', 64)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 7. Stage Checklists & Lead Progress (PRD §6.3)
        if (!Schema::hasTable('crm_stage_checklists')) {
            Schema::create('crm_stage_checklists', function (Blueprint $table) {
                $table->id();
                $table->string('stage_id', 64)->index();
                $table->string('code', 64)->unique();
                $table->string('label', 255);
                $table->string('title', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_mandatory')->default(true);
                $table->unsignedInteger('sort_order')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_lead_checklist_progress')) {
            Schema::create('crm_lead_checklist_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->foreignId('checklist_id')->constrained('crm_stage_checklists')->cascadeOnDelete();
                $table->string('stage_id', 64)->nullable();
                $table->boolean('is_completed')->default(false);
                $table->string('completed_by', 64)->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['lead_id', 'checklist_id']);
            });
        }

        // 8. Immutable CRM Audit Logs (FR-CRM-24)
        if (!Schema::hasTable('crm_immutable_audit_logs')) {
            Schema::create('crm_immutable_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lead_id')->index();
                $table->string('action', 64)->nullable()->index();
                $table->string('entity_type', 64)->default('lead');
                $table->unsignedBigInteger('entity_id')->nullable()->index();
                $table->string('field_name', 64)->index();
                $table->json('old_value')->nullable();
                $table->json('new_value')->nullable();
                $table->boolean('is_override')->default(false);
                $table->text('reason')->nullable();
                $table->string('actor_id', 64)->nullable()->index();
                $table->string('actor_name', 128)->nullable();
                $table->string('actor_role', 64)->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('occurred_at_iso', 64);
                $table->timestamp('created_at')->useCurrent();

                $table->index(['lead_id', 'field_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_immutable_audit_logs');
        Schema::dropIfExists('crm_lead_checklist_progress');
        Schema::dropIfExists('crm_stage_checklists');
        Schema::dropIfExists('crm_lead_documents');
        Schema::dropIfExists('crm_delivery_readiness_checks');
        Schema::dropIfExists('crm_top_milestones');
        Schema::dropIfExists('crm_top_schemes');
        Schema::dropIfExists('crm_rab_items');
        Schema::dropIfExists('crm_rab_versions');
    }
};
