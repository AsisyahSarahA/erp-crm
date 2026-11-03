<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->index();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('module', 50)->index();
            $table->unsignedInteger('version')->default(1);
            $table->text('system_prompt');
            $table->text('user_template');
            $table->string('schema_name', 50)->nullable();
            $table->enum('status', ['draft', 'active', 'deprecated'])->default('active')->index();
            $table->string('model_preference', 50)->default('accurate'); // 'fast', 'cheap', 'accurate'
            $table->decimal('temperature', 3, 2)->default(0.20);
            $table->string('ab_test_group', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['slug', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};

