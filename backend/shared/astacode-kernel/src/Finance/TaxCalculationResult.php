<?php

declare(strict_types=1);

namespace Astacode\Kernel\Finance;

use Astacode\Kernel\Support\Money;
use Astacode\Kernel\Support\Terbilang;

final class TaxCalculationResult
{
    public function __construct(
        public readonly string $dpp,
        public readonly float $ppnRate,
        public readonly string $ppnAmount,
        public readonly bool $isTaxWithholder,
        public readonly float $pph23Rate,
        public readonly string $pph23Amount,
        public readonly string $invoiceTotal,
        public readonly string $netCashReceivable,
        public readonly string $terbilangInvoiceTotal,
        public readonly string $terbilangNetCash
    ) {}

    public function toArray(): array
    {
        return [
            'dpp' => $this->dpp,
            'dpp_formatted' => Money::of($this->dpp)->formatRupiah(),
            'ppn_rate' => $this->ppnRate,
            'ppn_amount' => $this->ppnAmount,
            'ppn_amount_formatted' => Money::of($this->ppnAmount)->formatRupiah(),
            'is_tax_withholder' => $this->isTaxWithholder,
            'pph23_rate' => $this->pph23Rate,
            'pph23_amount' => $this->pph23Amount,
            'pph23_amount_formatted' => Money::of($this->pph23Amount)->formatRupiah(),
            'invoice_total' => $this->invoiceTotal,
            'invoice_total_formatted' => Money::of($this->invoiceTotal)->formatRupiah(),
            'net_cash_receivable' => $this->netCashReceivable,
            'net_cash_receivable_formatted' => Money::of($this->netCashReceivable)->formatRupiah(),
            'terbilang_invoice_total' => $this->terbilangInvoiceTotal,
            'terbilang_net_cash' => $this->terbilangNetCash,
        ];
    }
}

