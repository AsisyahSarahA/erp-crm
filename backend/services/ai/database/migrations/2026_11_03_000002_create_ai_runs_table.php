<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_runs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('module', 50)->index();
            $table->string('purpose', 100)->index();
            $table->string('provider', 50)->index();
            $table->string('model', 100);
            $table->string('prompt_slug', 100)->nullable();
            $table->unsignedInteger('prompt_version')->nullable();
            $table->string('input_hash', 64)->index();
            $table->json('input_payload');
            $table->json('output_json')->nullable();
            $table->unsignedInteger('tokens_in')->default(0);
            $table->unsignedInteger('tokens_out')->default(0);
            $table->decimal('cost', 10, 4)->default(0.0000);
            $table->unsignedInteger('latency_ms')->default(0);
            $table->enum('status', ['SUCCESS', 'FAILED', 'BUDGET_EXCEEDED', 'BLOCKED_BY_GUARDRAIL'])->default('SUCCESS')->index();
            $table->enum('human_decision', ['ACCEPTED', 'REJECTED', 'EDITED', 'PENDING'])->nullable();
            $table->decimal('estimated_mandays', 8, 2)->nullable();
            $table->decimal('actual_mandays', 8, 2)->nullable();
            $table->string('decided_by', 64)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_runs');
    }
};

