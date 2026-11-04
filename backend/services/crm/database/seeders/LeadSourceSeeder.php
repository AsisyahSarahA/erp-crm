<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'code' => 'INSTAGRAM',
                'name' => 'Instagram',
                'category' => 'ORGANIC_SOCIAL',
                'sub_sources' => json_encode(['Instagram DM', 'Instagram Bio Link', 'Story Swipe Up']),
                'sort_order' => 1,
            ],
            [
                'code' => 'WEBSITE',
                'name' => 'Website Inbound Form',
                'category' => 'ORGANIC_WEB',
                'sub_sources' => json_encode(['Contact Form', 'Pricing Calculator', 'Live Chat Widget']),
                'sort_order' => 2,
            ],
            [
                'code' => 'TIKTOK',
                'name' => 'TikTok',
                'category' => 'ORGANIC_SOCIAL',
                'sub_sources' => json_encode(['TikTok Direct Message', 'TikTok Profile Link']),
                'sort_order' => 3,
            ],
            [
                'code' => 'LINKEDIN',
                'name' => 'LinkedIn',
                'category' => 'PROFESSIONAL_NETWORK',
                'sub_sources' => json_encode(['InMail Outreach', 'Company Page Lead Form', 'Post Engagement']),
                'sort_order' => 4,
            ],
            [
                'code' => 'FACEBOOK',
                'name' => 'Facebook',
                'category' => 'ORGANIC_SOCIAL',
                'sub_sources' => json_encode(['Messenger Inbound', 'Group Post']),
                'sort_order' => 5,
            ],
            [
                'code' => 'WHATSAPP_MARKETING',
                'name' => 'WhatsApp Marketing',
                'category' => 'DIRECT_CHAT',
                'sub_sources' => json_encode(['Broadcast Campaign', 'Direct Click to Chat']),
                'sort_order' => 6,
            ],
            [
                'code' => 'GOOGLE_ADS',
                'name' => 'Google Ads (Search & Display)',
                'category' => 'PAID_SEARCH',
                'sub_sources' => json_encode(['Search Intent ERP', 'Display Retargeting']),
                'sort_order' => 7,
            ],
            [
                'code' => 'META_ADS',
                'name' => 'Meta Ads (FB/IG Paid)',
                'category' => 'PAID_SOCIAL',
                'sub_sources' => json_encode(['Lead Generation Form Ads', 'Traffic Campaign to WhatsApp']),
                'sort_order' => 8,
            ],
            [
                'code' => 'REFERRAL',
                'name' => 'Referral / Relasi Bisnis',
                'category' => 'PARTNERSHIP',
                'sub_sources' => json_encode(['Client Referral', 'Partner Software House', 'Keluarga/Alumni']),
                'sort_order' => 9,
            ],
            [
                'code' => 'EVENT_WEBINAR',
                'name' => 'Event / Webinar Tech',
                'category' => 'EVENTS',
                'sub_sources' => json_encode(['Workshop IT Architecture', 'Tech Meetup Expo']),
                'sort_order' => 10,
            ],
            [
                'code' => 'COLD_OUTREACH',
                'name' => 'Cold Outreach / B2B Prospecting',
                'category' => 'OUTBOUND',
                'sub_sources' => json_encode(['Cold Email Sequence', 'Cold Call Phone']),
                'sort_order' => 11,
            ],
            [
                'code' => 'MARKETPLACE',
                'name' => 'B2B Marketplace & Tender',
                'category' => 'PORTAL',
                'sub_sources' => json_encode(['LPSE Tender', 'Fastwork B2B', 'Sribulancer']),
                'sort_order' => 12,
            ],
            [
                'code' => 'OTHER',
                'name' => 'Lainnya / Walk-in',
                'category' => 'GENERAL',
                'sub_sources' => json_encode(['Walk-in Office', 'Media Liputan']),
                'sort_order' => 99,
            ],
        ];

        foreach ($sources as $source) {
            $source['channel_group'] = $source['channel_group'] ?? $source['category'];
            DB::table('crm_lead_sources')->updateOrInsert(
                ['code' => $source['code']],
                array_merge($source, ['is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}

