<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance crm_activities
        Schema::table('crm_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_activities', 'location')) {
                $table->string('location', 255)->nullable()->after('meet_link');
            }
            if (!Schema::hasColumn('crm_activities', 'link')) {
                $table->string('link', 512)->nullable()->after('location');
            }
            if (!Schema::hasColumn('crm_activities', 'next_step')) {
                $table->text('next_step')->nullable()->after('outcome_notes');
            }
            if (!Schema::hasColumn('crm_activities', 'attachments')) {
                $table->json('attachments')->nullable()->after('attendees');
            }
            if (!Schema::hasColumn('crm_activities', 'recording_url')) {
                $table->string('recording_url', 512)->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('crm_activities', 'transcript_text')) {
                $table->longText('transcript_text')->nullable()->after('recording_url');
            }
            if (!Schema::hasColumn('crm_activities', 'ai_summary')) {
                $table->json('ai_summary')->nullable()->after('transcript_text');
            }
            if (!Schema::hasColumn('crm_activities', 'reminder_h1_sent_at')) {
                $table->timestamp('reminder_h1_sent_at')->nullable()->after('ai_summary');
            }
            if (!Schema::hasColumn('crm_activities', 'reminder_1h_sent_at')) {
                $table->timestamp('reminder_1h_sent_at')->nullable()->after('reminder_h1_sent_at');
            }
        });

        // 2. Google OAuth Accounts per User (PRD §14, FR-CRM-04)
        if (!Schema::hasTable('crm_google_accounts')) {
            Schema::create('crm_google_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('email', 255)->nullable();
                $table->text('access_token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->timestamp('token_expires_at')->nullable();
                $table->string('calendar_id', 255)->default('primary');
                $table->string('channel_id', 255)->nullable();
                $table->string('resource_id', 255)->nullable();
                $table->string('sync_token', 512)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_google_accounts');

        Schema::table('crm_activities', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'link',
                'next_step',
                'attachments',
                'recording_url',
                'transcript_text',
                'ai_summary',
                'reminder_h1_sent_at',
                'reminder_1h_sent_at',
            ]);
        });
    }
};

