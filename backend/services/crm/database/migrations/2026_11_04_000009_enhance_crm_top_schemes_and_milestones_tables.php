<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_top_schemes (FR-CRM-07, BR-CRM-02, BR-CRM-18)
        Schema::table('crm_top_schemes', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_top_schemes', 'rab_version_id')) {
                $table->unsignedBigInteger('rab_version_id')->nullable()->after('quotation_id')->index();
            }
            if (!Schema::hasColumn('crm_top_schemes', 'reference_dpp')) {
                $table->decimal('reference_dpp', 18, 2)->default(0)->after('preset_name');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'reference_total')) {
                $table->decimal('reference_total', 18, 2)->default(0)->after('reference_dpp');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'vat_rate')) {
                $table->decimal('vat_rate', 7, 4)->default(11.0)->after('reference_total');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'pph23_rate')) {
                $table->decimal('pph23_rate', 7, 4)->default(2.0)->after('vat_rate');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'is_pkp')) {
                $table->boolean('is_pkp')->default(true)->after('pph23_rate');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'is_pph23_withholder')) {
                $table->boolean('is_pph23_withholder')->default(true)->after('is_pkp');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'total_dpp')) {
                $table->decimal('total_dpp', 18, 2)->default(0)->after('total_percentage');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'total_ppn')) {
                $table->decimal('total_ppn', 18, 2)->default(0)->after('total_dpp');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'total_pph23')) {
                $table->decimal('total_pph23', 18, 2)->default(0)->after('total_ppn');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'total_net_cash')) {
                $table->decimal('total_net_cash', 18, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'total_terbilang')) {
                $table->text('total_terbilang')->nullable()->after('total_net_cash');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'remaining_percent')) {
                $table->decimal('remaining_percent', 7, 4)->default(0)->after('is_balanced');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'remaining_amount_idr')) {
                $table->decimal('remaining_amount_idr', 18, 2)->default(0)->after('remaining_percent');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'balance_warning')) {
                $table->string('balance_warning', 255)->nullable()->after('remaining_amount_idr');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'finance_lock_notes')) {
                $table->text('finance_lock_notes')->nullable()->after('locked_by_finance_by');
            }
            if (!Schema::hasColumn('crm_top_schemes', 'unlock_approval_status')) {
                $table->string('unlock_approval_status', 32)->default('NONE')->after('finance_lock_notes'); // NONE, PENDING, APPROVED, REJECTED
            }
            if (!Schema::hasColumn('crm_top_schemes', 'pending_change_payload')) {
                $table->json('pending_change_payload')->nullable()->after('unlock_approval_status');
            }
        });

        // 2. Enhance crm_top_milestones with tax breakdown (DPP, PPN, PPh 23, Net Cash, Retention flag)
        Schema::table('crm_top_milestones', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_top_milestones', 'dpp_amount')) {
                $table->decimal('dpp_amount', 18, 2)->default(0)->after('percentage');
            }
            if (!Schema::hasColumn('crm_top_milestones', 'ppn_amount')) {
                $table->decimal('ppn_amount', 18, 2)->default(0)->after('dpp_amount');
            }
            if (!Schema::hasColumn('crm_top_milestones', 'pph23_amount')) {
                $table->decimal('pph23_amount', 18, 2)->default(0)->after('ppn_amount');
            }
            if (!Schema::hasColumn('crm_top_milestones', 'net_cash_idr')) {
                $table->decimal('net_cash_idr', 18, 2)->default(0)->after('amount_idr');
            }
            if (!Schema::hasColumn('crm_top_milestones', 'due_date')) {
                $table->date('due_date')->nullable()->after('due_days_offset');
            }
            if (!Schema::hasColumn('crm_top_milestones', 'is_retention')) {
                $table->boolean('is_retention')->default(false)->after('project_milestone_ref');
            }
        });
    }

    public function down(): void
    {
        Schema::table('crm_top_milestones', function (Blueprint $table) {
            $table->dropColumn([
                'dpp_amount',
                'ppn_amount',
                'pph23_amount',
                'net_cash_idr',
                'due_date',
                'is_retention',
            ]);
        });

        Schema::table('crm_top_schemes', function (Blueprint $table) {
            $table->dropColumn([
                'rab_version_id',
                'reference_dpp',
                'reference_total',
                'vat_rate',
                'pph23_rate',
                'is_pkp',
                'is_pph23_withholder',
                'total_dpp',
                'total_ppn',
                'total_pph23',
                'total_net_cash',
                'total_terbilang',
                'remaining_percent',
                'remaining_amount_idr',
                'balance_warning',
                'finance_lock_notes',
                'unlock_approval_status',
                'pending_change_payload',
            ]);
        });
    }
};

