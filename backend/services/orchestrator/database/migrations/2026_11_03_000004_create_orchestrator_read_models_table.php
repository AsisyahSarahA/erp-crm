<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('read_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 64)->index();
            $table->string('model_key', 128)->index();
            $table->json('data');
            $table->unsignedInteger('version')->default(1);
            $table->string('last_event_id', 64)->nullable();
            $table->timestamps();

            $table->unique(['model_type', 'model_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('read_models');
    }
};

