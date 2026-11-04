<?php

declare(strict_types=1);

namespace Astacode\Kernel\Finance;

use Astacode\Kernel\Support\Money;

final class RabCalculationResult
{
    public function __construct(
        public readonly string $subtotal,
        public readonly float $contingencyPercent,
        public readonly string $contingencyAmount,
        public readonly ?string $contingencyReason,
        public readonly float $discountPercent,
        public readonly string $discountAmount,
        public readonly string $dpp,
        public readonly float $ppnRate,
        public readonly string $ppnAmount,
        public readonly string $totalInvestasi,
        public readonly string $terbilang,
        public readonly string $hppInternal,
        public readonly string $grossMarginNominal,
        public readonly float $grossMarginPercent,
        public readonly string $marginIndicatorColor,
        public readonly bool $requiresApproval,
        public readonly array $terms = [],
        public readonly array $items = []
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'subtotal_formatted' => Money::of($this->subtotal)->formatRupiah(),
            'contingency_percent' => $this->contingencyPercent,
            'contingency_amount' => $this->contingencyAmount,
            'contingency_amount_formatted' => Money::of($this->contingencyAmount)->formatRupiah(),
            'contingency_reason' => $this->contingencyReason,
            'discount_percent' => $this->discountPercent,
            'discount_amount' => $this->discountAmount,
            'discount_amount_formatted' => Money::of($this->discountAmount)->formatRupiah(),
            'dpp' => $this->dpp,
            'dpp_formatted' => Money::of($this->dpp)->formatRupiah(),
            'ppn_rate' => $this->ppnRate,
            'ppn_amount' => $this->ppnAmount,
            'ppn_amount_formatted' => Money::of($this->ppnAmount)->formatRupiah(),
            'total_investasi' => $this->totalInvestasi,
            'total_investasi_formatted' => Money::of($this->totalInvestasi)->formatRupiah(),
            'terbilang' => $this->terbilang,
            'hpp_internal' => $this->hppInternal,
            'hpp_internal_formatted' => Money::of($this->hppInternal)->formatRupiah(),
            'gross_margin_nominal' => $this->grossMarginNominal,
            'gross_margin_nominal_formatted' => Money::of($this->grossMarginNominal)->formatRupiah(),
            'gross_margin_percent' => $this->grossMarginPercent,
            'margin_indicator_color' => $this->marginIndicatorColor,
            'requires_approval' => $this->requiresApproval,
            'terms' => $this->terms,
            'items' => $this->items,
        ];
    }
}

