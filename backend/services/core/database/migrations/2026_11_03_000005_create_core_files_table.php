<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->string('disk')->default('private');
            $table->string('mime_type', 128);
            $table->unsignedBigInteger('size_bytes');
            $table->string('hash_sha256', 64)->index();
            $table->unsignedInteger('version')->default(1);
            $table->string('virus_scan_status', 32)->default('CLEAN')->index();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('attachable');
            $table->boolean('is_private')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

