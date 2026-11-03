<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('idempotency_keys')) {
            Schema::create('idempotency_keys', function (Blueprint $table) {
                $table->id();
                $table->string('key', 128)->unique();
                $table->string('route', 255);
                $table->string('request_hash', 64);
                $table->string('status', 32)->default('PROCESSING')->index();
                $table->unsignedSmallInteger('response_code')->nullable();
                $table->json('response_body')->nullable();
                $table->string('user_id', 64)->nullable()->index();
                $table->timestamp('locked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
