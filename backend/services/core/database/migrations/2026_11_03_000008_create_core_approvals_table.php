<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reference_type', 64)->index();
            $table->string('reference_id', 64)->index();
            $table->string('approval_type', 64)->index();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('current_stage')->default(1);
            $table->unsignedSmallInteger('total_stages')->default(1);
            $table->string('status', 32)->default('pending')->index(); // pending, approved, rejected, escalated, cancelled
            $table->unsignedSmallInteger('sla_hours')->default(24);
            $table->timestamp('deadline_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('approval_id')->constrained('approvals')->cascadeOnDelete();
            $table->unsignedSmallInteger('stage_order')->default(1);
            $table->string('approver_role', 64)->nullable()->index();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('pending')->index();
            $table->text('comments')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approvals');
    }
};

