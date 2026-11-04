<?php

declare(strict_types=1);

namespace Astacode\Kernel\Finance;

use Astacode\Kernel\Support\Money;
use Astacode\Kernel\Support\Terbilang;
use InvalidArgumentException;

/**
 * Class RabCalculator
 * Calculates full RAB estimation, contingencies, discounts, VAT, margins, and TOP terms allocation.
 * Conforms to PRD §6.4 (FR-CRM-06/07) and §10.4.
 */
final class RabCalculator
{
    public const DEFAULT_CONTINGENCY_PERCENT = 10.0;
    public const MIN_CONTINGENCY_WITHOUT_REASON = 5.0;
    public const DEFAULT_PPN_RATE = 11.0;
    public const DEFAULT_PPH23_RATE = 2.0;

    /**
     * Calculate complete RAB summary.
     *
     * @param array $items Array of items with keys: [name, qty, price, cost_rate, is_optional, is_free, ...]
     * @param float $contingencyPercent Contingency % (default 10%)
     * @param string|null $contingencyReason Reason (mandatory if contingencyPercent < 5%)
     * @param float $discountPercent Discount % (0 to 100)
     * @param string|int|float|null $discountNominal Alternative discount in IDR
     * @param float $ppnRate VAT rate (default 11%)
     * @param array $topPercentages Optional payment terms percentages (e.g. [30, 40, 30] or [50, 50])
     * @param array $additionalDirectCosts Direct expenses like cloud, licenses, etc.
     * @param bool $isTaxWithholder Whether client withholds PPh 23
     */
    public static function calculate(
        array $items,
        float $contingencyPercent = self::DEFAULT_CONTINGENCY_PERCENT,
        ?string $contingencyReason = null,
        float $discountPercent = 0.0,
        string|int|float|null $discountNominal = null,
        float $ppnRate = self::DEFAULT_PPN_RATE,
        array $topPercentages = [],
        array $additionalDirectCosts = [],
        bool $isTaxWithholder = true
    ): RabCalculationResult {
        // Validation: Contingency < 5% requires an explicit reason (PRD §6.4)
        if ($contingencyPercent < self::MIN_CONTINGENCY_WITHOUT_REASON) {
            if (empty(trim($contingencyReason ?? ''))) {
                throw new InvalidArgumentException(
                    "Alasan kontingensi wajib diisi jika persentase kontingensi di bawah 5.0% (PRD §6.4)."
                );
            }
        }

        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new InvalidArgumentException("Persentase diskon harus antara 0% dan 100%.");
        }

        // 1. Calculate items subtotal and HPP
        $subtotal = Money::zero();
        $totalHpp = Money::zero();
        $processedItems = [];

        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 1);
            $price = Money::of($item['price'] ?? 0);
            $costRate = Money::of($item['cost_rate'] ?? 0);
            $isOptional = (bool) ($item['is_optional'] ?? false);
            $isFree = (bool) ($item['is_free'] ?? false);

            $lineSubtotal = $isFree || $isOptional ? Money::zero() : $price->multiply($qty);
            $lineHpp = $costRate->multiply($qty);

            if (!$isOptional) {
                $subtotal = $subtotal->add($lineSubtotal);
                $totalHpp = $totalHpp->add($lineHpp);
            }

            $processedItems[] = [
                'name' => $item['name'] ?? 'Item',
                'qty' => $qty,
                'price' => $price->getAmount(),
                'cost_rate' => $costRate->getAmount(),
                'is_optional' => $isOptional,
                'is_free' => $isFree,
                'subtotal' => $lineSubtotal->getAmount(),
                'subtotal_formatted' => $lineSubtotal->formatRupiah(),
                'hpp' => $lineHpp->getAmount(),
                'hpp_formatted' => $lineHpp->formatRupiah(),
            ];
        }

        // Add additional direct costs to HPP (cloud, licenses, freelance)
        foreach ($additionalDirectCosts as $cost) {
            $totalHpp = $totalHpp->add(Money::of($cost));
        }

        // 2. Contingency Amount = Subtotal × %contingency
        $contingencyAmount = $subtotal->percentage($contingencyPercent);

        // Base before discount = Subtotal + Contingency
        $subtotalWithContingency = $subtotal->add($contingencyAmount);

        // 3. Discount Amount = (Subtotal + Contingency) × %discount OR nominal
        $discountAmount = Money::zero();
        if ($discountPercent > 0) {
            $discountAmount = $subtotalWithContingency->percentage($discountPercent);
        } elseif ($discountNominal !== null && (float) $discountNominal > 0) {
            $nom = Money::of($discountNominal);
            $discountAmount = $nom->greaterThan($subtotalWithContingency) ? $subtotalWithContingency : $nom;
            // derive effective discount percent
            if ($subtotalWithContingency->greaterThan(Money::zero())) {
                $discountPercent = (float) bcmul(
                    bcdiv($discountAmount->getAmount(), $subtotalWithContingency->getAmount(), 4),
                    '100',
                    2
                );
            }
        }

        // 4. DPP (Dasar Pengenaan Pajak / Total Sebelum Pajak) = Subtotal + Contingency - Discount
        $dpp = $subtotalWithContingency->subtract($discountAmount);
        if ($dpp->lessThan(Money::zero())) {
            $dpp = Money::zero();
        }

        // 5. PPN = DPP × tarif PPN
        $ppnAmount = $dpp->percentage($ppnRate);

        // 6. TOTAL INVESTASI = DPP + PPN
        $totalInvestasi = $dpp->add($ppnAmount);

        // 7. Terbilang
        $terbilang = Terbilang::rupiah($totalInvestasi->getAmount());

        // 8. Gross Margin calculation
        $marginResult = MarginCalculator::calculate($dpp->getAmount(), $totalHpp->getAmount());

        // 9. Payment Terms (TOP) with Rounding Absorption on Last Term (PRD FR-CRM-07)
        $terms = [];
        if (!empty($topPercentages)) {
            $terms = self::calculateTerms(
                $topPercentages,
                $dpp,
                $totalInvestasi,
                $ppnRate,
                $isTaxWithholder
            );
        }

        return new RabCalculationResult(
            subtotal: $subtotal->getAmount(),
            contingencyPercent: $contingencyPercent,
            contingencyAmount: $contingencyAmount->getAmount(),
            contingencyReason: $contingencyReason,
            discountPercent: $discountPercent,
            discountAmount: $discountAmount->getAmount(),
            dpp: $dpp->getAmount(),
            ppnRate: $ppnRate,
            ppnAmount: $ppnAmount->getAmount(),
            totalInvestasi: $totalInvestasi->getAmount(),
            terbilang: $terbilang,
            hppInternal: $totalHpp->getAmount(),
            grossMarginNominal: $marginResult['gross_margin_nominal'],
            grossMarginPercent: $marginResult['gross_margin_percent'],
            marginIndicatorColor: $marginResult['indicator_color'],
            requiresApproval: $marginResult['requires_approval'] || $discountPercent > 10.0,
            terms: $terms,
            items: $processedItems
        );
    }

    /**
     * Splits DPP & Total Investasi into terms, absorbing any rounding remainder into the final term.
     */
    public static function calculateTerms(
        array $percentages,
        Money $dpp,
        Money $totalInvestasi,
        float $ppnRate = self::DEFAULT_PPN_RATE,
        bool $isTaxWithholder = true
    ): array {
        // Validate total percentage == 100%
        $sumPercent = array_sum($percentages);
        if (abs($sumPercent - 100.0) > 0.05) {
            throw new InvalidArgumentException(
                "Total persentase termin (TOP) harus tepat 100.00% (saat ini: {$sumPercent}%)."
            );
        }

        $terms = [];
        $allocatedDpp = Money::zero();
        $allocatedTotal = Money::zero();
        $count = count($percentages);

        foreach ($percentages as $index => $pct) {
            $isLast = ($index === $count - 1);
            $pctVal = (float) $pct;

            if ($isLast) {
                // Last term absorbs rounding difference (PRD FR-CRM-07)
                $termDpp = $dpp->subtract($allocatedDpp);
                $termTotal = $totalInvestasi->subtract($allocatedTotal);
            } else {
                $termDpp = $dpp->percentage($pctVal);
                $termTotal = $totalInvestasi->percentage($pctVal);

                $allocatedDpp = $allocatedDpp->add($termDpp);
                $allocatedTotal = $allocatedTotal->add($termTotal);
            }

            // Calculate tax breakdown for term
            $taxRes = TaxCalculator::calculate($termDpp->getAmount(), $ppnRate, $isTaxWithholder);

            $terms[] = [
                'term_number' => $index + 1,
                'percentage' => $pctVal,
                'dpp' => $termDpp->getAmount(),
                'dpp_formatted' => $termDpp->formatRupiah(),
                'ppn' => $taxRes->ppnAmount,
                'pph23' => $taxRes->pph23Amount,
                'invoice_total' => $termTotal->getAmount(),
                'invoice_total_formatted' => $termTotal->formatRupiah(),
                'net_cash' => $taxRes->netCashReceivable,
                'net_cash_formatted' => Money::of($taxRes->netCashReceivable)->formatRupiah(),
                'terbilang' => Terbilang::rupiah($termTotal->getAmount()),
            ];
        }

        return $terms;
    }
}

