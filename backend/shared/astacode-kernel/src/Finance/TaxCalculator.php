<?php

declare(strict_types=1);

namespace Astacode\Kernel\Finance;

use Astacode\Kernel\Support\Money;
use Astacode\Kernel\Support\Terbilang;
use InvalidArgumentException;

/**
 * Class TaxCalculator
 * Implements PRD §10.4 Tax & Invoicing Rules:
 * - DPP = Nilai termin (sebelum pajak).
 * - PPN = DPP × tarif PPN (default 11%, konfigurabel).
 * - PPh 23 = DPP × 2% jika klien adalah badan pemotong pajak.
 * - Total Tagihan (Invoice Total) = DPP + PPN.
 * - Kas Bersih Diterima (Net Cash) = DPP + PPN - PPh 23.
 */
final class TaxCalculator
{
    public const DEFAULT_PPN_RATE = 11.0;
    public const DEFAULT_PPH23_RATE = 2.0;

    /**
     * Calculate tax breakdown for a given DPP.
     */
    public static function calculate(
        string|int|float $dpp,
        float $ppnRate = self::DEFAULT_PPN_RATE,
        bool $isTaxWithholder = true,
        float $pph23Rate = self::DEFAULT_PPH23_RATE
    ): TaxCalculationResult {
        if ($ppnRate < 0 || $pph23Rate < 0) {
            throw new InvalidArgumentException("Tarif pajak tidak boleh negatif.");
        }

        $moneyDpp = Money::of($dpp);

        if ($moneyDpp->lessThan(Money::zero())) {
            throw new InvalidArgumentException("Nilai DPP tidak boleh negatif.");
        }

        $ppnAmount = $moneyDpp->percentage($ppnRate);
        $pph23Amount = $isTaxWithholder ? $moneyDpp->percentage($pph23Rate) : Money::zero();

        $invoiceTotal = $moneyDpp->add($ppnAmount);
        $netCash = $invoiceTotal->subtract($pph23Amount);

        return new TaxCalculationResult(
            dpp: $moneyDpp->getAmount(),
            ppnRate: $ppnRate,
            ppnAmount: $ppnAmount->getAmount(),
            isTaxWithholder: $isTaxWithholder,
            pph23Rate: $pph23Rate,
            pph23Amount: $pph23Amount->getAmount(),
            invoiceTotal: $invoiceTotal->getAmount(),
            netCashReceivable: $netCash->getAmount(),
            terbilangInvoiceTotal: Terbilang::rupiah($invoiceTotal->getAmount()),
            terbilangNetCash: Terbilang::rupiah($netCash->getAmount())
        );
    }
}

