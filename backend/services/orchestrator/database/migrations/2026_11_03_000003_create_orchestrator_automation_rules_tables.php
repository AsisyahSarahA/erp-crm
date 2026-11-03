<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Automation Rules (No-code trigger-condition-action definitions)
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['event', 'idle', 'overdue', 'cron'])->index();
            $table->string('trigger_event', 100)->nullable()->index();
            $table->json('trigger_config')->nullable(); // e.g. {"idle_days": 5} or {"cron": "0 8 * * *"}
            $table->json('conditions'); // JSON-Logic syntax
            $table->json('actions'); // Array of actions to execute
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('priority')->default(10);
            $table->timestamp('last_run_at')->nullable();
            $table->unsignedInteger('execution_count')->default(0);
            $table->timestamps();
        });

        // 2. Automation Runs (Audit trail of executions)
        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('automation_rules')->onDelete('cascade');
            $table->string('trigger_type', 50);
            $table->json('trigger_payload')->nullable();
            $table->boolean('matched_condition')->default(true);
            $table->json('actions_executed')->nullable();
            $table->enum('status', ['SUCCESS', 'FAILED', 'PARTIAL', 'SKIPPED'])->default('SUCCESS')->index();
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_runs');
        Schema::dropIfExists('automation_rules');
    }
};

