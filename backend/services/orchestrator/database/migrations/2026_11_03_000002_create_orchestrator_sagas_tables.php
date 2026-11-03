<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sagas State Machine (Distributed Transactions)
        Schema::create('sagas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('saga_name', 100)->index();
            $table->string('correlation_id', 64)->index();
            $table->string('reference_type', 50)->index();
            $table->string('reference_id', 64)->index();
            $table->enum('status', [
                'PENDING',
                'IN_PROGRESS',
                'COMPLETED',
                'PARTIAL',
                'FAILED',
                'COMPENSATING',
                'COMPENSATED'
            ])->default('PENDING')->index();
            $table->unsignedInteger('total_steps')->default(4);
            $table->unsignedInteger('completed_steps')->default(0);
            $table->json('context'); // Global payload across steps
            $table->json('readiness_check')->nullable(); // Readiness checks (DP, Contract, Scope)
            $table->enum('kickoff_gate_status', ['LOCKED', 'PASSED', 'WAIVED'])->default('LOCKED');
            $table->text('failure_reason')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['saga_name', 'reference_id']);
        });

        // 2. Saga Steps
        Schema::create('saga_steps', function (Blueprint $table) {
            $table->id();
            $table->ulid('saga_id');
            $table->unsignedInteger('step_order');
            $table->string('step_name', 100);
            $table->string('target_service', 50);
            $table->enum('status', [
                'PENDING',
                'RUNNING',
                'COMPLETED',
                'FAILED',
                'SKIPPED',
                'COMPENSATING',
                'COMPENSATED'
            ])->default('PENDING')->index();
            $table->string('idempotency_key', 128)->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->foreign('saga_id')->references('id')->on('sagas')->onDelete('cascade');
            $table->unique(['saga_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saga_steps');
        Schema::dropIfExists('sagas');
    }
};

