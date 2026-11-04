<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_leads with full PRD §6.8 columns
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->unique()->after('id');
            $table->foreignId('contact_id')->nullable()->after('customer_id')->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('source_id')->nullable()->after('source')->constrained('crm_lead_sources')->nullOnDelete();
            $table->json('utm')->nullable()->after('sub_source');
            $table->string('stage_id', 64)->default('new_lead')->after('stage');
            $table->string('budget_range', 128)->nullable()->after('est_value');
            $table->date('target_golive')->nullable()->after('target_closing');
            $table->unsignedBigInteger('owner_id')->nullable()->index()->after('sales_pic_id');
            $table->unsignedBigInteger('team_id')->nullable()->index()->after('owner_id');
            $table->unsignedInteger('score')->default(0)->after('win_probability');
            $table->json('score_breakdown')->nullable()->after('score');
            $table->string('score_label', 16)->default('WARM')->after('score_breakdown'); // HOT, WARM, COLD
            $table->timestamp('stale_since')->nullable()->after('days_in_stage');
            $table->boolean('is_stale')->default(false)->after('stale_since');
            $table->boolean('has_next_step')->default(false)->after('is_stale');
            $table->unsignedBigInteger('active_quotation_id')->nullable()->after('has_next_step');
            $table->unsignedBigInteger('reject_reason_id')->nullable()->after('reject_reason_code');
            $table->timestamp('won_at')->nullable()->after('reject_notes');
            $table->timestamp('lost_at')->nullable()->after('won_at');
            $table->timestamp('reopened_at')->nullable()->after('lost_at');
            $table->unsignedBigInteger('reopened_by')->nullable()->after('reopened_at');
            $table->text('reopen_reason')->nullable()->after('reopened_by');
            $table->string('industry', 64)->nullable()->after('reopen_reason');
            $table->json('services')->nullable()->after('industry');
            $table->json('tags')->nullable()->after('services');
            $table->boolean('consent')->default(true)->after('tags');
            $table->timestamp('consent_at')->nullable()->after('consent');
            $table->string('created_by', 64)->nullable()->after('consent_at');
        });

        // 2. Lead Stage History (FR-CRM-03, FR-CRM-24)
        Schema::create('crm_lead_stage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->string('from_stage', 64);
            $table->string('to_stage', 64);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['lead_id', 'changed_at']);
        });

        // 3. Lead Watchers
        Schema::create('crm_lead_watchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();

            $table->unique(['lead_id', 'user_id']);
        });

        // 4. Activities (FR-CRM-04)
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('crm_customers')->cascadeOnDelete();
            $table->string('type', 32); // MEETING, CALL, FOLLOW_UP, EMAIL, WHATSAPP, DEMO, SITE_VISIT, TASK, NOTE
            $table->string('title', 255);
            $table->text('notes')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamp('sla_due_at')->nullable();
            $table->string('status', 32)->default('SCHEDULED'); // SCHEDULED, COMPLETED, CANCELLED, OVERDUE
            $table->string('outcome', 64)->nullable(); // SUCCESS, RESCHEDULE, NO_RESPONSE
            $table->text('outcome_notes')->nullable();
            $table->string('google_event_id', 255)->nullable();
            $table->string('meet_link', 512)->nullable();
            $table->json('attendees')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lead_id', 'status', 'start_at']);
            $table->index(['customer_id', 'status']);
        });

        // 5. Saved Views (FR-CRM-02)
        Schema::create('crm_saved_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('module', 64)->default('crm_leads');
            $table->string('name', 128);
            $table->json('filters')->nullable();
            $table->json('sorts')->nullable();
            $table->json('columns')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_saved_views');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_lead_watchers');
        Schema::dropIfExists('crm_lead_stage_histories');
    }
};

