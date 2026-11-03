<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('outbox_events')) {
            Schema::create('outbox_events', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('event_name', 100)->index();
                $table->string('aggregate_type', 100)->index();
                $table->string('aggregate_id', 64)->index();
                $table->json('payload');
                $table->string('idempotency_key', 128)->unique();
                $table->string('status', 32)->default('PENDING')->index();
                $table->unsignedInteger('attempts')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('available_at')->nullable()->index();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'available_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};
