<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Project Statuses
        Schema::create('pm_project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 64);
            $table->string('badge_color', 32)->default('blue');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Task Statuses (Kanban workflow)
        Schema::create('pm_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 64);
            $table->unsignedInteger('sequence')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 3. Projects (FR-PM-01, PRD §9.5)
        Schema::create('pm_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 32)->unique(); // PRJ-NNN
            $table->string('customer_id', 64)->index();
            $table->string('customer_name', 255);
            $table->string('deal_id', 64)->nullable()->index();
            $table->string('title', 255);
            $table->string('project_type', 64);
            $table->string('status', 32)->default('ACTIVE'); // PROVISIONED, ACTIVE, IN_UAT, MAINTENANCE, COMPLETED, ON_HOLD
            $table->string('health', 16)->default('GREEN'); // GREEN, YELLOW, RED
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->unsignedInteger('budget_mandays')->default(0);
            $table->decimal('consumed_mandays', 8, 2)->default(0);
            $table->date('start_date');
            $table->date('target_end_date');
            $table->unsignedBigInteger('pm_user_id')->nullable();
            $table->string('pm_user_name', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Milestones
        Schema::create('pm_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pm_projects')->cascadeOnDelete();
            $table->string('name', 255);
            $table->unsignedInteger('term_number')->default(1);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('status', 32)->default('PENDING'); // PENDING, IN_PROGRESS, UAT_READY, APPROVED
            $table->timestamp('uat_approved_at')->nullable();
            $table->timestamps();
        });

        // 5. Tasks (Backlog & Sprint tasks, FR-PM-02)
        Schema::create('pm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pm_projects')->cascadeOnDelete();
            $table->string('task_key', 32)->unique(); // PRJ-NNN-XX
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 32)->default('BACKLOG');
            $table->string('priority', 32)->default('MEDIUM'); // CRITICAL, HIGH, MEDIUM, LOW
            $table->string('role_needed', 64)->nullable(); // BE, FE, QA, UIUX, etc.
            $table->decimal('estimated_mandays', 4, 2)->default(1.0);
            $table->decimal('actual_hours', 6, 2)->default(0);
            $table->string('assignee_name', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_tasks');
        Schema::dropIfExists('pm_milestones');
        Schema::dropIfExists('pm_projects');
        Schema::dropIfExists('pm_task_statuses');
        Schema::dropIfExists('pm_project_statuses');
    }
};

