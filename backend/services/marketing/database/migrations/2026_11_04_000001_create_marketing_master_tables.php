<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Marketing Channels
        Schema::create('mkt_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 64);
            $table->string('handle', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Content Pillars
        Schema::create('mkt_content_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Campaigns (FR-MKT-10)
        Schema::create('mkt_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 255);
            $table->string('channel_code', 32);
            $table->decimal('budget_idr', 18, 2)->default(0);
            $table->decimal('spent_idr', 18, 2)->default(0);
            $table->unsignedInteger('leads_generated')->default(0);
            $table->string('status', 32)->default('ACTIVE'); // DRAFT, ACTIVE, COMPLETED, PAUSED
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // 4. Content Items (FR-MKT-01, FR-MKT-02)
        Schema::create('mkt_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('mkt_campaigns')->nullOnDelete();
            $table->string('title', 255);
            $table->string('pillar_code', 32);
            $table->string('content_type', 32); // CAROUSEL, REELS, BLOG, CASE_STUDY
            $table->string('status', 32)->default('DRAFT'); // DRAFT, REVIEW, APPROVED, PUBLISHED
            $table->unsignedInteger('seo_score')->default(85);
            $table->text('caption')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mkt_contents');
        Schema::dropIfExists('mkt_campaigns');
        Schema::dropIfExists('mkt_content_pillars');
        Schema::dropIfExists('mkt_channels');
    }
};

