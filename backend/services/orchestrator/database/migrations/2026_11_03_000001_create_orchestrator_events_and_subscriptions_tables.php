<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Event Log (Central append-only broker log)
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 64)->unique();
            $table->string('event_name', 100)->index();
            $table->string('producer_service', 50)->index();
            $table->string('aggregate_type', 50)->nullable()->index();
            $table->string('aggregate_id', 64)->nullable()->index();
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->enum('status', ['received', 'processing', 'completed', 'partial', 'failed', 'dead_letter'])->default('received')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 2. Subscriptions (Mapping: event_name -> consumer service + endpoint)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 100)->index();
            $table->string('consumer_service', 50)->index();
            $table->string('target_endpoint', 255);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_retries')->default(5);
            $table->unsignedInteger('timeout_seconds')->default(15);
            $table->timestamps();

            $table->unique(['event_name', 'consumer_service']);
        });

        // 3. Processed Events (Idempotency tracking across consumer dispatches)
        Schema::create('processed_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 64)->index();
            $table->string('consumer_name', 64)->index();
            $table->string('idempotency_key', 128)->unique();
            $table->enum('status', ['SUCCESS', 'FAILED'])->default('SUCCESS');
            $table->text('response_summary')->nullable();
            $table->timestamp('processed_at')->useCurrent();

            $table->index(['event_id', 'consumer_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_events');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('event_logs');
    }
};

