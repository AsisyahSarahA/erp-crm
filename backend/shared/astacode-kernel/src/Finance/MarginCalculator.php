<?php

declare(strict_types=1);

namespace Astacode\Kernel\Finance;

use Astacode\Kernel\Support\Money;
use InvalidArgumentException;

/**
 * Class MarginCalculator
 * Implements PRD §6.4 Margin Simulation & Indicators:
 * - Gross Margin % = ((DPP - HPP Internal) / DPP) * 100
 * - Color indicator:
 *   - GREEN (Hijau): >= 40.0%
 *   - YELLOW (Kuning): 25.0% <= margin < 40.0%
 *   - RED (Merah): < 25.0%
 * - Requires approval if margin < threshold (default 25.0%).
 */
final class MarginCalculator
{
    public const THRESHOLD_GREEN = 40.0;
    public const THRESHOLD_YELLOW = 25.0;

    public const COLOR_GREEN = 'GREEN';
    public const COLOR_YELLOW = 'YELLOW';
    public const COLOR_RED = 'RED';

    public static function calculate(
        string|int|float $revenueOrDpp,
        string|int|float $cogsOrHpp,
        float $minApprovalThreshold = self::THRESHOLD_YELLOW
    ): array {
        $dpp = Money::of($revenueOrDpp);
        $hpp = Money::of($cogsOrHpp);

        if ($dpp->lessThan(Money::zero()) || $hpp->lessThan(Money::zero())) {
            throw new InvalidArgumentException("DPP dan HPP tidak boleh negatif.");
        }

        $grossMarginNominal = $dpp->subtract($hpp);

        $grossMarginPercent = 0.0;
        if ($dpp->greaterThan(Money::zero())) {
            $grossMarginPercent = round(((float) $grossMarginNominal->getAmount() / (float) $dpp->getAmount()) * 100, 2);
        }

        $color = self::getIndicatorColor($grossMarginPercent);
        $requiresApproval = $grossMarginPercent < $minApprovalThreshold;

        return [
            'revenue_dpp' => $dpp->getAmount(),
            'revenue_dpp_formatted' => $dpp->formatRupiah(),
            'cogs_hpp' => $hpp->getAmount(),
            'cogs_hpp_formatted' => $hpp->formatRupiah(),
            'gross_margin_nominal' => $grossMarginNominal->getAmount(),
            'gross_margin_nominal_formatted' => $grossMarginNominal->formatRupiah(),
            'gross_margin_percent' => $grossMarginPercent,
            'indicator_color' => $color,
            'requires_approval' => $requiresApproval,
            'approval_reason' => $requiresApproval
                ? "Gross margin ({$grossMarginPercent}%) di bawah batas minimum ({$minApprovalThreshold}%)."
                : null,
        ];
    }

    public static function getIndicatorColor(float $grossMarginPercent): string
    {
        if ($grossMarginPercent >= self::THRESHOLD_GREEN) {
            return self::COLOR_GREEN;
        }

        if ($grossMarginPercent >= self::THRESHOLD_YELLOW) {
            return self::COLOR_YELLOW;
        }

        return self::COLOR_RED;
    }
}
