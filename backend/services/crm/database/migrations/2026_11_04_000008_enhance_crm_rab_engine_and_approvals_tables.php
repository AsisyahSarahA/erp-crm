<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_rab_versions for full FR-CRM-06 & FR-CRM-17 capabilities
        Schema::table('crm_rab_versions', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_rab_versions', 'is_pkp')) {
                $table->boolean('is_pkp')->default(true)->after('discount_amount');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'direct_costs')) {
                $table->json('direct_costs')->nullable()->after('hpp_internal');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'direct_costs_total')) {
                $table->decimal('direct_costs_total', 18, 2)->default(0)->after('direct_costs');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'gross_margin_nominal')) {
                $table->decimal('gross_margin_nominal', 18, 2)->default(0)->after('direct_costs_total');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'team_size_by_role')) {
                $table->json('team_size_by_role')->nullable()->after('duration_weeks');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'duration_derivation')) {
                $table->json('duration_derivation')->nullable()->after('team_size_by_role');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'warnings')) {
                $table->json('warnings')->nullable()->after('tech_stack');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'requires_approval')) {
                $table->boolean('requires_approval')->default(false)->after('status');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'approval_tier')) {
                $table->string('approval_tier', 32)->default('NONE')->after('requires_approval'); // NONE, SALES, SALES_MANAGER, DIRECTOR
            }
            if (!Schema::hasColumn('crm_rab_versions', 'approval_reasons')) {
                $table->json('approval_reasons')->nullable()->after('approval_tier');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'approval_status')) {
                $table->string('approval_status', 32)->default('NOT_REQUIRED')->after('approval_reasons'); // NOT_REQUIRED, PENDING, APPROVED, REJECTED
            }
            if (!Schema::hasColumn('crm_rab_versions', 'core_approval_id')) {
                $table->unsignedBigInteger('core_approval_id')->nullable()->after('approval_status');
            }
        });

        // 2. Enhance crm_rab_items with is_bonus and recurring_period (FR-CRM-06)
        Schema::table('crm_rab_items', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_rab_items', 'is_bonus')) {
                $table->boolean('is_bonus')->default(false)->after('is_recurring');
            }
            if (!Schema::hasColumn('crm_rab_items', 'recurring_period')) {
                $table->string('recurring_period', 32)->nullable()->after('is_bonus'); // MONTHLY, QUARTERLY, YEARLY
            }
        });

        // 3. CRM Settings table for configurable thresholds (VAT, contingency, margin colors, approval tiers)
        if (!Schema::hasTable('crm_settings')) {
            Schema::create('crm_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 128)->unique();
                $table->json('value');
                $table->string('description', 255)->nullable();
                $table->string('updated_by', 64)->nullable();
                $table->timestamps();
            });
        }

        // 4. CRM Approvals Log (tracks discount/margin/high-value approvals & syncs with Core Approval Engine)
        if (!Schema::hasTable('crm_approvals')) {
            Schema::create('crm_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->unsignedBigInteger('rab_version_id')->nullable()->index();
                $table->unsignedBigInteger('quotation_id')->nullable()->index();
                $table->unsignedBigInteger('core_approval_id')->nullable()->index();
                $table->string('approval_type', 64)->default('RAB_DISCOUNT_MARGIN'); // RAB_DISCOUNT_MARGIN, QUOTATION_HIGH_VALUE
                $table->string('required_tier', 32)->default('SALES_MANAGER'); // SALES_MANAGER, DIRECTOR
                $table->string('required_role', 64)->default('sales_manager'); // sales_manager, director
                $table->string('status', 32)->default('PENDING'); // PENDING, APPROVED, REJECTED
                $table->decimal('discount_pct', 7, 4)->default(0);
                $table->decimal('margin_pct', 7, 4)->default(0);
                $table->decimal('total_amount', 18, 2)->default(0);
                $table->json('reasons')->nullable();
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->unsignedBigInteger('decided_by')->nullable();
                $table->text('decision_notes')->nullable();
                $table->timestamp('decided_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_approvals');
        Schema::dropIfExists('crm_settings');

        Schema::table('crm_rab_items', function (Blueprint $table) {
            $table->dropColumn(['is_bonus', 'recurring_period']);
        });

        Schema::table('crm_rab_versions', function (Blueprint $table) {
            $table->dropColumn([
                'is_pkp',
                'direct_costs',
                'direct_costs_total',
                'gross_margin_nominal',
                'team_size_by_role',
                'duration_derivation',
                'warnings',
                'requires_approval',
                'approval_tier',
                'approval_reasons',
                'approval_status',
                'core_approval_id',
            ]);
        });
    }
};

