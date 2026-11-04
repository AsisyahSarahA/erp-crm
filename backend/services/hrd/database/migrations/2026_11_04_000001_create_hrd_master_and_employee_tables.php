<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Leave Types (FR-HRD-05)
        Schema::create('hrd_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 128);
            $table->unsignedInteger('default_quota_days')->default(0);
            $table->boolean('requires_document')->default(false);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Work Shifts & Tolerances (FR-HRD-02, FR-HRD-04)
        Schema::create('hrd_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 128);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('break_minutes')->default(60);
            $table->unsignedInteger('grace_period_minutes')->default(15);
            $table->boolean('is_night_shift')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. PKL / Internship Quotas per Department (BR-HRD-03, FR-HRD-08)
        Schema::create('hrd_pkl_quotas', function (Blueprint $table) {
            $table->id();
            $table->string('department', 64);
            $table->unsignedInteger('period_year')->default(2026);
            $table->unsignedInteger('max_quota')->default(5);
            $table->unsignedInteger('current_accepted')->default(0);
            $table->string('mentor_name', 255)->nullable();
            $table->boolean('is_accepting')->default(true);
            $table->timestamps();

            $table->unique(['department', 'period_year']);
        });

        // 4. Employees & Contractors (FR-HRD-01)
        Schema::create('hrd_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 32)->unique(); // EMP-NNNN
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('full_name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 32);
            $table->string('employment_status', 32); // TETAP, KONTRAK, FREELANCE, PKL
            $table->string('job_title', 128);
            $table->string('department', 64);
            $table->string('technical_role', 64); // PM, BE, FE, FULLSTACK, QA, UIUX, DEVOPS, HR, FINANCE, SALES
            $table->decimal('base_salary', 18, 2)->default(0);
            $table->decimal('allowance', 18, 2)->default(0);
            $table->decimal('hourly_rate', 18, 2)->default(0); // For freelance
            $table->date('join_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Attendances (FR-HRD-02)
        Schema::create('hrd_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrd_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in');
            $table->time('check_out')->nullable();
            $table->string('mode', 16)->default('ONSITE'); // ONSITE, WFH
            $table->string('status', 32)->default('ON_TIME'); // ON_TIME, LATE, LEAVE, SICK, ABSENT
            $table->timestamps();
        });

        // 6. Daily Logs (FR-HRD-03)
        Schema::create('hrd_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrd_employees')->cascadeOnDelete();
            $table->date('date');
            $table->string('task_ref', 128); // e.g. PRJ-001-05
            $table->text('progress_summary');
            $table->decimal('hours_spent', 4, 2)->default(8.0);
            $table->boolean('is_synced_to_pm')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrd_daily_logs');
        Schema::dropIfExists('hrd_attendances');
        Schema::dropIfExists('hrd_employees');
        Schema::dropIfExists('hrd_pkl_quotas');
        Schema::dropIfExists('hrd_shifts');
        Schema::dropIfExists('hrd_leave_types');
    }
};

