<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_rab_versions with requirement_id, ai_run_id, clarifying_questions, tech_stack
        Schema::table('crm_rab_versions', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_rab_versions', 'requirement_id')) {
                $table->unsignedBigInteger('requirement_id')->nullable()->after('lead_id')->index();
            }
            if (!Schema::hasColumn('crm_rab_versions', 'ai_run_id')) {
                $table->unsignedBigInteger('ai_run_id')->nullable()->after('requirement_id')->index();
            }
            if (!Schema::hasColumn('crm_rab_versions', 'clarifying_questions')) {
                $table->json('clarifying_questions')->nullable()->after('out_of_scope');
            }
            if (!Schema::hasColumn('crm_rab_versions', 'tech_stack')) {
                $table->json('tech_stack')->nullable()->after('clarifying_questions');
            }
        });

        // 2. Requirements / Requirement Versions (PRD §6.8 FR-CRM-05)
        if (!Schema::hasTable('crm_requirements')) {
            Schema::create('crm_requirements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
                $table->unsignedInteger('version')->default(1);
                $table->longText('prompt_text')->nullable();
                $table->string('status', 32)->default('DRAFT'); // DRAFT, PROCESSING, COMPLETED, FAILED
                $table->json('params')->nullable(); // platform, tech_preference, user_count, mandatory_integrations, deadline, detail_level, productivity_assumption, catalog_template
                $table->string('generation_mode', 32)->default('FULL'); // FULL, REGENERATE_FEEDBACK, PARTIAL_SECTION
                $table->text('feedback_text')->nullable();
                $table->string('target_section', 64)->nullable();
                $table->longText('normalized_text')->nullable();
                $table->unsignedInteger('token_chunks_count')->default(1);
                $table->boolean('pii_redacted')->default(false);
                $table->boolean('zero_retention')->default(false);
                $table->unsignedBigInteger('ai_run_id')->nullable()->index();
                $table->unsignedBigInteger('rab_version_id')->nullable()->index();
                $table->json('ai_output')->nullable();
                $table->unsignedInteger('latency_ms')->nullable();
                $table->text('error_message')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['lead_id', 'version']);
                $table->index(['lead_id', 'status']);
            });
        }

        // 3. Requirement Files (PRD §6.8 FR-CRM-05)
        if (!Schema::hasTable('crm_requirement_files')) {
            Schema::create('crm_requirement_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('requirement_id')->constrained('crm_requirements')->cascadeOnDelete();
                $table->unsignedBigInteger('lead_id')->index();
                $table->unsignedBigInteger('file_id')->nullable();
                $table->string('file_name', 255);
                $table->string('mime_type', 128)->nullable();
                $table->unsignedBigInteger('file_size_bytes')->default(0);
                $table->string('file_path', 512)->nullable();
                $table->string('kind', 32)->default('doc'); // doc, audio, image
                $table->string('antivirus_status', 32)->default('CLEAN'); // CLEAN, INFECTED
                $table->longText('transcript_text')->nullable();
                $table->timestamps();
            });
        }

        // 4. CRM AI Runs Log (PRD §6.8 ai_runs)
        if (!Schema::hasTable('crm_ai_runs')) {
            Schema::create('crm_ai_runs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lead_id')->nullable()->index();
                $table->unsignedBigInteger('requirement_id')->nullable()->index();
                $table->string('external_run_id', 64)->nullable();
                $table->string('module', 32)->default('crm');
                $table->string('purpose', 64)->default('rab_generation');
                $table->string('provider', 64)->default('gemini');
                $table->string('model', 64)->default('gemini-2.0-flash');
                $table->string('prompt_ref', 128)->nullable();
                $table->string('input_hash', 64)->index();
                $table->json('output')->nullable();
                $table->unsignedInteger('tokens_in')->default(0);
                $table->unsignedInteger('tokens_out')->default(0);
                $table->decimal('cost', 12, 6)->default(0);
                $table->unsignedInteger('latency_ms')->default(0);
                $table->string('status', 32)->default('SUCCESS');
                $table->string('human_decision', 32)->default('PENDING'); // PENDING, USED_AS_RAB, MERGED_TO_RAB, REGENERATED, DISCARDED
                $table->string('decided_by', 64)->nullable();
                $table->timestamp('decided_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_ai_runs');
        Schema::dropIfExists('crm_requirement_files');
        Schema::dropIfExists('crm_requirements');

        Schema::table('crm_rab_versions', function (Blueprint $table) {
            $table->dropColumn([
                'requirement_id',
                'ai_run_id',
                'clarifying_questions',
                'tech_stack',
            ]);
        });
    }
};

