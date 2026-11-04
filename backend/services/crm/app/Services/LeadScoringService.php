<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Lead;

final class LeadScoringService
{
    private const HIGH_FIT_INDUSTRIES = [
        'TECH_SAAS',
        'FINANCE_BANKING',
        'HEALTHCARE',
        'MFG_LOGISTICS',
        'RETAIL_ECOMMERCE',
        'PROPERTY_CONSTR',
    ];

    /**
     * Compute Rule-based Lead Score (0-100) per PRD §6.4 FR-CRM-12.
     * Evaluates Fit (30%), Engagement (30%), Intent (25%), and Source (15%).
     */
    public function calculate(Lead $lead, ?Customer $customer = null): array
    {
        $cust = $customer ?? $lead->customer;

        // 1. FIT (Max 30 points)
        $industry = strtoupper((string) ($lead->industry ?? $cust?->industry ?? ''));
        $industryScore = in_array($industry, self::HIGH_FIT_INDUSTRIES, true) ? 10.0 : (empty($industry) ? 4.0 : 7.0);

        $size = strtolower((string) ($cust?->size ?? ''));
        $sizeScore = match (true) {
            str_contains($size, '500') || str_contains($size, 'korporat') => 10.0,
            str_contains($size, '201') || str_contains($size, 'enterprise') => 8.0,
            str_contains($size, '51') => 6.0,
            str_contains($size, '11') => 4.0,
            default => 3.0,
        };

        $val = (float) $lead->est_value;
        $budgetScore = match (true) {
            $val >= 300000000 => 10.0,
            $val >= 150000000 => 8.0,
            $val >= 75000000 => 6.0,
            $val >= 30000000 => 4.0,
            default => 2.0,
        };
        $fitTotal = min(30.0, $industryScore + $sizeScore + $budgetScore);

        // 2. ENGAGEMENT (Max 30 points)
        $hasWa = !empty($cust?->phone) || !empty($lead->contact?->phone_wa);
        $waScore = $hasWa ? 10.0 : 2.0;

        $hasEmail = !empty($cust?->email) || !empty($lead->contact?->email);
        $emailScore = $hasEmail ? 10.0 : 3.0;

        $activityCount = $lead->relationLoaded('activities')
            ? $lead->activities->count()
            : $lead->activities()->count();
        $activityScore = match (true) {
            $activityCount >= 3 => 10.0,
            $activityCount >= 1 => 8.0,
            default => 4.0,
        };
        $engagementTotal = min(30.0, $waScore + $emailScore + $activityScore);

        // 3. INTENT (Max 25 points)
        $priority = strtolower((string) $lead->priority);
        $priorityScore = match ($priority) {
            'hot' => 15.0,
            'high' => 11.0,
            'medium' => 7.0,
            default => 3.0,
        };

        $targetClosing = $lead->target_closing;
        $urgencyScore = 4.0;
        if ($targetClosing !== null) {
            $daysToClose = now()->diffInDays($targetClosing, false);
            if ($daysToClose >= 0 && $daysToClose <= 30) {
                $urgencyScore = 10.0;
            } elseif ($daysToClose > 30 && $daysToClose <= 60) {
                $urgencyScore = 7.0;
            }
        }
        $intentTotal = min(25.0, $priorityScore + $urgencyScore);

        // 4. SOURCE CONVERSION (Max 15 points)
        $source = strtoupper((string) ($lead->source ?? ''));
        $sourceScore = match (true) {
            str_contains($source, 'REFERRAL') || str_contains($source, 'RELASI') => 15.0,
            str_contains($source, 'WEBSITE') || str_contains($source, 'ORGANIC_WEB') => 13.0,
            str_contains($source, 'GOOGLE') => 11.0,
            str_contains($source, 'LINKEDIN') || str_contains($source, 'WHATSAPP') => 10.0,
            str_contains($source, 'META') || str_contains($source, 'FACEBOOK') => 8.0,
            str_contains($source, 'INSTAGRAM') || str_contains($source, 'TIKTOK') => 7.0,
            str_contains($source, 'EVENT') || str_contains($source, 'WEBINAR') => 9.0,
            default => 5.0,
        };
        $sourceTotal = min(15.0, $sourceScore);

        $totalScore = (int) round(min(100.0, max(0.0, $fitTotal + $engagementTotal + $intentTotal + $sourceTotal)));

        $label = match (true) {
            $totalScore >= 70 => 'HOT',
            $totalScore >= 40 => 'WARM',
            default => 'COLD',
        };

        $breakdown = [
            'fit' => [
                'score' => round($fitTotal, 1),
                'max' => 30,
                'industry' => round($industryScore, 1),
                'company_size' => round($sizeScore, 1),
                'budget_value' => round($budgetScore, 1),
            ],
            'engagement' => [
                'score' => round($engagementTotal, 1),
                'max' => 30,
                'whatsapp' => round($waScore, 1),
                'email' => round($emailScore, 1),
                'activities' => round($activityScore, 1),
            ],
            'intent' => [
                'score' => round($intentTotal, 1),
                'max' => 25,
                'priority' => round($priorityScore, 1),
                'urgency' => round($urgencyScore, 1),
            ],
            'source' => [
                'score' => round($sourceTotal, 1),
                'max' => 15,
                'channel' => round($sourceScore, 1),
            ],
            'total_score' => $totalScore,
            'label' => $label,
            'calculated_at' => now()->toIso8601String(),
        ];

        return [
            'score' => $totalScore,
            'label' => $label,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Phase 2 Hook for predictive ML scoring based on historical win/loss conversion data.
     */
    public function predictiveScoreHook(Lead $lead): ?float
    {
        // Reserved for Phase 2 ML model service call
        return null;
    }
}

