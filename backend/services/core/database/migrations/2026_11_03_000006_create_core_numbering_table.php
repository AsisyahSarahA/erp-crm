<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numbering_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('code_type', 64)->unique();
            $table->string('format_pattern', 128);
            $table->unsignedBigInteger('current_number')->default(0);
            $table->string('current_period', 16)->default('');
            $table->string('reset_frequency', 16)->default('yearly'); // 'monthly', 'yearly', 'never'
            $table->string('prefix', 32)->nullable();
            $table->unsignedSmallInteger('padding')->default(4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numbering_sequences');
    }
};

