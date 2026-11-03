<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_catalog_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('module_key', 100)->index();
            $table->string('role', 50)->index();
            $table->decimal('estimated_mandays', 8, 2);
            $table->decimal('actual_mandays', 8, 2);
            $table->decimal('variance_pct', 8, 2);
            $table->string('project_id', 64)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_catalog_feedbacks');
    }
};

