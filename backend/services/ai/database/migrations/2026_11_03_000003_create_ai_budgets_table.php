<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50)->unique();
            $table->decimal('monthly_budget_usd', 10, 2)->default(100.00);
            $table->decimal('current_spend_usd', 10, 4)->default(0.0000);
            $table->unsignedInteger('rate_limit_rpm')->default(60);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_budgets');
    }
};

