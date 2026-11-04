<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CrmApproval;
use App\Models\CrmSetting;
use App\Models\Lead;
use App\Models\TopMilestone;
use App\Models\TopScheme;
use Astacode\Kernel\Support\Terbilang;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

final class TopService
{
    public const VALID_TRIGGERS = [
        'signing',
        'date',
        'milestone',
        'uat_passed',
        'go_live',
        'bast',
        'retention_end',
    ];

    public const PRESET_DEFINITIONS = [
        '30_40_30' => [
            'preset_code' => '30_40_30',
            'preset_name' => '30/40/30',
            'milestones' => [
                [
                    'term_number' => 1,
                    'name' => 'DP',
                    'percentage' => 30.0,
                    'trigger_type' => 'signing',
                    'description' => 'Pembayaran Uang Muka (DP) 30% saat penandatanganan MoU/SPK',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 2,
                    'name' => 'Termin 1',
                    'percentage' => 40.0,
                    'trigger_type' => 'milestone',
                    'description' => 'Pembayaran Termin 1 (40%) setelah progres Backend & Frontend Staging selesai',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 3,
                    'name' => 'UAT',
                    'percentage' => 30.0,
                    'trigger_type' => 'bast',
                    'description' => 'Pelunasan 30% setelah UAT lulus & BAST ditandatangani',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
            ],
        ],
        '50_50' => [
            'preset_code' => '50_50',
            'preset_name' => '50/50',
            'milestones' => [
                [
                    'term_number' => 1,
                    'name' => 'DP',
                    'percentage' => 50.0,
                    'trigger_type' => 'signing',
                    'description' => 'Pembayaran Uang Muka (DP) 50% saat penandatanganan MoU/SPK',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 2,
                    'name' => 'Pelunasan',
                    'percentage' => 50.0,
                    'trigger_type' => 'bast',
                    'description' => 'Pelunasan 50% saat Serah Terima Pekerjaan (BAST)',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
            ],
        ],
        '20_30_30_20' => [
            'preset_code' => '20_30_30_20',
            'preset_name' => '20/30/30/20',
            'milestones' => [
                [
                    'term_number' => 1,
                    'name' => 'DP',
                    'percentage' => 20.0,
                    'trigger_type' => 'signing',
                    'description' => 'Uang Muka (DP) 20% saat penandatanganan kontrak',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 2,
                    'name' => 'Termin 1',
                    'percentage' => 30.0,
                    'trigger_type' => 'milestone',
                    'description' => 'Termin 1 (30%) setelah persetujuan Prototype UI/UX',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 3,
                    'name' => 'Termin 2',
                    'percentage' => 30.0,
                    'trigger_type' => 'uat_passed',
                    'description' => 'Termin 2 (30%) setelah Staging Demo & UAT',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 4,
                    'name' => 'Pelunasan',
                    'percentage' => 20.0,
                    'trigger_type' => 'go_live',
                    'description' => 'Pelunasan 20% saat Go-Live & masa retensi',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
            ],
        ],
        '40_30_20_10_RETENTION' => [
            'preset_code' => '40_30_20_10_RETENTION',
            'preset_name' => '40/30/20/10 dengan Retensi',
            'milestones' => [
                [
                    'term_number' => 1,
                    'name' => 'DP',
                    'percentage' => 40.0,
                    'trigger_type' => 'signing',
                    'description' => 'Uang Muka (DP) 40% saat penandatanganan kontrak',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 2,
                    'name' => 'Termin 1',
                    'percentage' => 30.0,
                    'trigger_type' => 'milestone',
                    'description' => 'Termin 1 (30%) penyelesaian pengembangan modul inti',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 3,
                    'name' => 'UAT',
                    'percentage' => 20.0,
                    'trigger_type' => 'uat_passed',
                    'description' => 'Termin UAT (20%) setelah UAT disetujui',
                    'due_days_offset' => 14,
                    'is_retention' => false,
                ],
                [
                    'term_number' => 4,
                    'name' => 'Retensi',
                    'percentage' => 10.0,
                    'trigger_type' => 'retention_end',
                    'description' => 'Retensi 10% dibayarkan setelah masa garansi/retensi berakhir',
                    'due_days_offset' => 14,
                    'is_retention' => true,
                ],
            ],
        ],
    ];

    /**
     * Retrieves active or latest TOP scheme for a Lead.
     */
    public function getForLead(Lead $lead): ?TopScheme
    {
        return TopScheme::with('milestones')
            ->where('lead_id', $lead->id)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Normalizes preset alias (e.g. "30/40/30" -> "30_40_30", "40/30/20/10" -> "40_30_20_10_RETENTION").
     */
    public function normalizePresetCode(?string $raw): string
    {
        $clean = strtoupper(trim((string) $raw));
        $clean = str_replace(['/', '-', ' '], '_', $clean);

        return match ($clean) {
            '30_40_30' => '30_40_30',
            '50_50' => '50_50',
            '20_30_30_20' => '20_30_30_20',
            '40_30_20_10', '40_30_20_10_RETENTION', '40_30_20_10_DENGAN_RETENSI' => '40_30_20_10_RETENTION',
            'MONTHLY', 'RECURRING', 'MONTHLY_RECURRING', 'BULANAN', 'BULANAN_RECURRING' => 'MONTHLY_RECURRING',
            default => 'CUSTOM',
        };
    }

    /**
     * Upserts TOP Scheme & Milestones for a Lead (PUT /leads/{id}/top).
     */
    public function saveTopScheme(Lead $lead, array $payload, array $user): TopScheme
    {
        $existingScheme = $this->getForLead($lead);

        // Enforce BR-CRM-18: If locked by Finance, modification requires Finance approval
        if ($existingScheme !== null && $existingScheme->locked_by_finance_at !== null) {
            $userRole = strtolower((string) ($user['role'] ?? 'user'));
            $isFinanceOverride = in_array($userRole, ['finance', 'finance_ar', 'finance_manager', 'super_admin'], true)
                && !empty($payload['finance_override']);

            if ($existingScheme->unlock_approval_status !== 'APPROVED' && !$isFinanceOverride) {
                // Validate that proposed change is balanced 100% (BR-CRM-18: total termin tetap 100%)
                $calcPreview = $this->calculateScheme($lead, $payload, $existingScheme);
                if (!$calcPreview['is_balanced']) {
                    throw new InvalidArgumentException(
                        'Perubahan pada TOP yang telah dikunci Finance wajib tetap memiliki total termin tepat 100.00% (BR-CRM-18). ' .
                        ($calcPreview['balance_warning'] ?? '')
                    );
                }

                // Store pending change payload & create Finance approval request
                $approval = $this->createLockedChangeApproval($lead, $existingScheme, $payload, $user);

                throw new DomainException(
                    "Skema TOP telah dikunci oleh Finance AR pada {$existingScheme->locked_by_finance_at->toIso8601String()}. " .
                    "Perubahan setelah dikunci memerlukan persetujuan Finance (BR-CRM-18). ID Permohonan Approval: #{$approval->id}."
                );
            }
        }

        $calc = $this->calculateScheme($lead, $payload, $existingScheme);

        $finalize = (bool) ($payload['finalize'] ?? $payload['strict_balance'] ?? false);
        if ($finalize && !$calc['is_balanced']) {
            throw new InvalidArgumentException($calc['strict_error_message']);
        }

        return DB::transaction(function () use ($lead, $payload, $user, $existingScheme, $calc) {
            $schemeData = [
                'lead_id' => $lead->id,
                'quotation_id' => $payload['quotation_id'] ?? $existingScheme?->quotation_id ?? $lead->activeQuotation?->id,
                'rab_version_id' => $payload['rab_version_id'] ?? $existingScheme?->rab_version_id ?? $lead->active_rab_version_id,
                'preset_code' => $calc['preset_code'],
                'preset_name' => $calc['preset_name'],
                'reference_dpp' => $calc['reference_dpp'],
                'reference_total' => $calc['reference_total'],
                'vat_rate' => $calc['vat_rate'],
                'pph23_rate' => $calc['pph23_rate'],
                'is_pkp' => $calc['is_pkp'],
                'is_pph23_withholder' => $calc['is_pph23_withholder'],
                'total_percent' => $calc['total_percent'],
                'total_percentage' => $calc['total_percent'],
                'total_dpp' => $calc['total_dpp'],
                'total_ppn' => $calc['total_ppn'],
                'total_pph23' => $calc['total_pph23'],
                'total_amount' => $calc['total_amount'],
                'total_net_cash' => $calc['total_net_cash'],
                'total_terbilang' => $calc['total_terbilang'],
                'is_balanced' => $calc['is_balanced'],
                'remaining_percent' => $calc['remaining_percent'],
                'remaining_amount_idr' => $calc['remaining_amount_idr'],
                'balance_warning' => $calc['balance_warning'],
                'created_by' => (string) ($user['id'] ?? '1'),
                // If an approved change was just applied on a locked TOP, re-lock & reset approval status
                'unlock_approval_status' => 'NONE',
                'pending_change_payload' => null,
            ];

            if ($existingScheme !== null) {
                $existingScheme->update($schemeData);
                $scheme = $existingScheme;
                $scheme->milestones()->delete();
            } else {
                $scheme = TopScheme::create($schemeData);
            }

            foreach ($calc['milestones'] as $m) {
                TopMilestone::create([
                    'top_scheme_id' => $scheme->id,
                    'lead_id' => $lead->id,
                    'termin_no' => $m['term_number'],
                    'term_number' => $m['term_number'],
                    'name' => $m['name'],
                    'milestone_name' => $m['name'],
                    'trigger_event' => strtoupper($m['trigger_type']),
                    'trigger_type' => strtolower($m['trigger_type']),
                    'percent' => $m['percentage'],
                    'percentage' => $m['percentage'],
                    'dpp_amount' => $m['dpp_amount'],
                    'ppn_amount' => $m['ppn_amount'],
                    'pph23_amount' => $m['pph23_amount'],
                    'amount' => $m['amount_idr'],
                    'amount_idr' => $m['amount_idr'],
                    'net_cash_idr' => $m['net_cash_idr'],
                    'terbilang_text' => $m['terbilang_text'],
                    'description' => $m['description'],
                    'due_days' => $m['due_days_offset'],
                    'due_days_offset' => $m['due_days_offset'],
                    'due_date' => $m['due_date'],
                    'project_milestone_ref' => $m['project_milestone_ref'],
                    'is_retention' => $m['is_retention'],
                ]);
            }

            return $scheme->fresh(['milestones']);
        });
    }

    /**
     * Calculates TOP scheme & milestone breakdown (DPP, PPN, PPh 23, Terbilang, percent<->nominal sync, last-term rounding absorption).
     */
    public function calculateScheme(Lead $lead, array $payload, ?TopScheme $existingScheme = null): array
    {
        $lead->loadMissing(['customer', 'activeRab', 'activeQuotation']);
        $rab = $lead->activeRab ?? $lead->rabVersions()->orderByDesc('version')->first();
        $quo = $lead->activeQuotation ?? $lead->quotations()->orderByDesc('version')->first();
        $customer = $lead->customer;

        $isPkp = array_key_exists('is_pkp', $payload) && $payload['is_pkp'] !== null
            ? (bool) $payload['is_pkp']
            : ($rab?->is_pkp ?? $customer?->is_pkp ?? (bool) CrmSetting::getValue('is_company_pkp', true));

        $vatRate = $isPkp
            ? (float) ($payload['vat_rate'] ?? $rab?->vat_rate ?? CrmSetting::getValue('vat_rate', 11.0))
            : 0.0;

        $isPph23Withholder = array_key_exists('is_pph23_withholder', $payload) && $payload['is_pph23_withholder'] !== null
            ? (bool) $payload['is_pph23_withholder']
            : (bool) ($customer?->is_pph23_withholder ?? true);

        $pph23Rate = $isPph23Withholder
            ? (float) ($payload['pph23_rate'] ?? 2.0)
            : 0.0;

        // Determine Reference DPP and Reference Total
        $referenceDpp = isset($payload['reference_dpp']) && (float) $payload['reference_dpp'] > 0
            ? (float) $payload['reference_dpp']
            : (float) ($rab?->dpp ?: ($quo?->dpp ?: ($lead->est_value ?: 100_000_000.0)));

        $referenceTotal = isset($payload['reference_total']) && (float) $payload['reference_total'] > 0
            ? (float) $payload['reference_total']
            : ($rab && (float) $rab->total > 0 && !isset($payload['reference_dpp'])
                ? (float) $rab->total
                : round($referenceDpp * (1.0 + ($vatRate / 100.0)), 2));

        $referencePpn = round($referenceTotal - $referenceDpp, 2);
        $referencePph23 = $isPph23Withholder ? round($referenceDpp * ($pph23Rate / 100.0), 2) : 0.0;

        // Resolve Preset vs Custom milestones
        $rawPreset = $payload['preset_code'] ?? $payload['preset_name'] ?? $payload['preset'] ?? null;
        $presetCode = $rawPreset !== null
            ? $this->normalizePresetCode((string) $rawPreset)
            : ($existingScheme?->preset_code ?? 'CUSTOM');

        $rawMilestones = (array) ($payload['milestones'] ?? $payload['terms'] ?? []);

        if (empty($rawMilestones) && $presetCode !== 'CUSTOM') {
            if ($presetCode === 'MONTHLY_RECURRING') {
                $months = max(1, (int) ($payload['months'] ?? $payload['recurring_months'] ?? 3));
                $presetName = "Bulanan/Recurring ({$months} Bulan)";
                $basePct = floor((100.0 / $months) * 100) / 100;
                $accumPct = 0.0;
                for ($i = 1; $i <= $months; $i++) {
                    $pct = ($i === $months) ? round(100.0 - $accumPct, 2) : $basePct;
                    $accumPct = round($accumPct + $pct, 2);
                    $rawMilestones[] = [
                        'term_number' => $i,
                        'name' => "Termin Bulan {$i}",
                        'percentage' => $pct,
                        'trigger_type' => 'date',
                        'description' => "Pembayaran rutin/berkala bulan ke-{$i} sesuai SLA",
                        'due_days_offset' => 14,
                        'is_retention' => false,
                    ];
                }
            } elseif (isset(self::PRESET_DEFINITIONS[$presetCode])) {
                $def = self::PRESET_DEFINITIONS[$presetCode];
                $presetName = $def['preset_name'];
                $rawMilestones = $def['milestones'];
            } else {
                $presetName = 'Custom';
            }
        } else {
            $presetName = isset(self::PRESET_DEFINITIONS[$presetCode])
                ? self::PRESET_DEFINITIONS[$presetCode]['preset_name']
                : ($presetCode === 'MONTHLY_RECURRING' ? 'Bulanan/Recurring' : 'Custom');
        }

        if (empty($rawMilestones)) {
            throw new InvalidArgumentException('Skema TOP wajib memiliki minimal 1 termin pembayaran.');
        }

        // First pass: resolve percentage <-> nominal synchronization
        $normalizedInput = [];
        $sumPct = 0.0;

        foreach ($rawMilestones as $idx => $item) {
            $termNo = (int) ($item['term_number'] ?? $item['termin_no'] ?? ($idx + 1));
            $name = trim((string) ($item['name'] ?? $item['milestone_name'] ?? ($idx === 0 ? 'DP' : "Termin {$idx}")));
            $triggerRaw = strtolower(trim((string) ($item['trigger_type'] ?? $item['trigger_event'] ?? 'milestone')));
            if (!in_array($triggerRaw, self::VALID_TRIGGERS, true)) {
                $triggerRaw = 'milestone';
            }

            $dueDays = isset($item['due_days_offset'])
                ? (int) $item['due_days_offset']
                : (isset($item['due_days']) ? (int) $item['due_days'] : 14);
            if ($dueDays <= 0) {
                $dueDays = 14;
            }

            // Bidirectional sync: percentage <-> nominal
            $hasExplicitPct = (isset($item['percentage']) && $item['percentage'] !== null && $item['percentage'] !== '')
                || (isset($item['percent']) && $item['percent'] !== null && $item['percent'] !== '');
            $hasExplicitNominal = (isset($item['amount_idr']) && $item['amount_idr'] !== null && $item['amount_idr'] !== '')
                || (isset($item['amount']) && $item['amount'] !== null && $item['amount'] !== '');

            $explicitNominal = $hasExplicitNominal
                ? (float) ($item['amount_idr'] ?? $item['amount'])
                : null;

            if ($hasExplicitPct) {
                $pct = round((float) ($item['percentage'] ?? $item['percent']), 2);
            } elseif ($explicitNominal !== null && $referenceDpp > 0) {
                // Sync nominal -> percentage (compare against referenceDpp or referenceTotal depending on match)
                $pctFromDpp = round(($explicitNominal / $referenceDpp) * 100.0, 2);
                $pctFromTotal = $referenceTotal > 0 ? round(($explicitNominal / $referenceTotal) * 100.0, 2) : $pctFromDpp;
                $pct = ($pctFromDpp <= 100.0) ? $pctFromDpp : $pctFromTotal;
            } else {
                $pct = 0.0;
            }

            $sumPct = round($sumPct + $pct, 2);

            $normalizedInput[] = [
                'term_number' => $termNo,
                'name' => $name,
                'trigger_type' => $triggerRaw,
                'percentage' => $pct,
                'explicit_nominal' => $explicitNominal,
                'has_explicit_pct' => $hasExplicitPct,
                'description' => (string) ($item['description'] ?? "Pembayaran {$name} ({$pct}%)"),
                'due_days_offset' => $dueDays,
                'due_date' => $item['due_date'] ?? null,
                'project_milestone_ref' => $item['project_milestone_ref'] ?? null,
                'is_retention' => (bool) ($item['is_retention'] ?? ($triggerRaw === 'retention_end' || stripos($name, 'retensi') !== false)),
            ];
        }

        // Check if balanced (within 0.05% rounding tolerance -> absorb remainder on last term)
        $isBalanced = abs($sumPct - 100.0) <= 0.05;
        $count = count($normalizedInput);

        $allocatedPct = 0.0;
        $allocatedDpp = 0.0;
        $allocatedPpn = 0.0;
        $allocatedPph23 = 0.0;
        $allocatedTotal = 0.0;
        $processedMilestones = [];

        foreach ($normalizedInput as $idx => $m) {
            $isLast = ($idx === $count - 1);

            if ($isBalanced && $isLast) {
                // Final term absorbs rounding difference (FR-CRM-07)
                $termPct = round(100.0 - $allocatedPct, 2);
                $termDpp = round($referenceDpp - $allocatedDpp, 2);
                $termPpn = $isPkp ? round($referencePpn - $allocatedPpn, 2) : 0.0;
                $termPph23 = $isPph23Withholder ? round($referencePph23 - $allocatedPph23, 2) : 0.0;
                $termTotal = round($referenceTotal - $allocatedTotal, 2);
            } else {
                $termPct = $m['percentage'];
                $termDpp = (!$m['has_explicit_pct'] && $m['explicit_nominal'] !== null && abs($m['explicit_nominal'] - ($referenceDpp * $termPct / 100.0)) < 100)
                    ? round((float) $m['explicit_nominal'], 2)
                    : round($referenceDpp * ($termPct / 100.0), 2);
                $termPpn = $isPkp ? round($termDpp * ($vatRate / 100.0), 2) : 0.0;
                $termPph23 = $isPph23Withholder ? round($termDpp * ($pph23Rate / 100.0), 2) : 0.0;
                $termTotal = round($termDpp + $termPpn, 2);

                $allocatedPct = round($allocatedPct + $termPct, 2);
                $allocatedDpp = round($allocatedDpp + $termDpp, 2);
                $allocatedPpn = round($allocatedPpn + $termPpn, 2);
                $allocatedPph23 = round($allocatedPph23 + $termPph23, 2);
                $allocatedTotal = round($allocatedTotal + $termTotal, 2);
            }

            $netCash = round($termTotal - $termPph23, 2);
            // Amount IDR synced with DPP/Total (if non-PKP or DPP-based, termDpp == termTotal; for invoice amount we expose both dpp_amount and amount_idr)
            $amountIdr = $termDpp;

            $processedMilestones[] = [
                'term_number' => $m['term_number'],
                'termin_no' => $m['term_number'],
                'name' => $m['name'],
                'milestone_name' => $m['name'],
                'trigger_type' => $m['trigger_type'],
                'trigger_event' => strtoupper($m['trigger_type']),
                'percentage' => $termPct,
                'percent' => $termPct,
                'dpp_amount' => $termDpp,
                'ppn_amount' => $termPpn,
                'pph23_amount' => $termPph23,
                'invoice_total_idr' => $termTotal,
                'amount_idr' => $amountIdr,
                'amount' => $amountIdr,
                'net_cash_idr' => $netCash,
                'terbilang_text' => Terbilang::rupiah($amountIdr),
                'terbilang_invoice_total' => Terbilang::rupiah($termTotal),
                'description' => $m['description'],
                'due_days_offset' => $m['due_days_offset'],
                'due_days' => $m['due_days_offset'],
                'due_date' => $m['due_date'],
                'project_milestone_ref' => $m['project_milestone_ref'],
                'is_retention' => $m['is_retention'],
            ];
        }

        $finalTotalPct = $isBalanced ? 100.0 : $sumPct;
        $totalDpp = round((float) array_sum(array_column($processedMilestones, 'dpp_amount')), 2);
        $totalPpn = round((float) array_sum(array_column($processedMilestones, 'ppn_amount')), 2);
        $totalPph23 = round((float) array_sum(array_column($processedMilestones, 'pph23_amount')), 2);
        $totalAmount = $totalDpp;
        $totalInvoice = round($totalDpp + $totalPpn, 2);
        $totalNetCash = round($totalInvoice - $totalPph23, 2);

        $remainingPct = $isBalanced ? 0.0 : round(100.0 - $finalTotalPct, 2);
        $remainingPctDisplay = floor($remainingPct) == $remainingPct
            ? (string) (int) $remainingPct
            : (string) $remainingPct;

        $remainingAmountIdr = $isBalanced
            ? 0.0
            : max(0.0, round($referenceDpp * (abs($remainingPct) / 100.0), 0));

        $nominalCompact = 'Rp' . number_format($remainingAmountIdr, 0, ',', '.');
        $nominalSpaced = 'Rp ' . number_format($remainingAmountIdr, 0, ',', '.');

        $balanceWarning = $isBalanced
            ? null
            : "Sisa alokasi belum diatur: {$remainingPctDisplay}% ({$nominalCompact})";

        $strictErrorMessage = "Total termin harus 100%. Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: {$remainingPctDisplay}% ({$nominalCompact}). Sisa alokasi belum diatur: {$remainingPctDisplay}% ({$nominalCompact}). Nominal belum teralokasi: {$nominalSpaced}.";

        return [
            'preset_code' => $presetCode,
            'preset_name' => $presetName,
            'reference_dpp' => $referenceDpp,
            'reference_total' => $referenceTotal,
            'vat_rate' => $vatRate,
            'pph23_rate' => $pph23Rate,
            'is_pkp' => $isPkp,
            'is_pph23_withholder' => $isPph23Withholder,
            'total_percent' => $finalTotalPct,
            'total_dpp' => $totalDpp,
            'total_ppn' => $totalPpn,
            'total_pph23' => $totalPph23,
            'total_amount' => $totalAmount,
            'total_invoice_idr' => $totalInvoice,
            'total_net_cash' => $totalNetCash,
            'total_terbilang' => Terbilang::rupiah($totalAmount),
            'is_balanced' => $isBalanced,
            'can_finalize' => $isBalanced,
            'can_export_document' => $isBalanced,
            'remaining_percent' => $remainingPct,
            'remaining_amount_idr' => $remainingAmountIdr,
            'balance_warning' => $balanceWarning,
            'strict_error_message' => $strictErrorMessage,
            'milestones' => $processedMilestones,
        ];
    }

    /**
     * Reviews and locks TOP scheme by Finance AR (`locked_by_finance_at`).
     * Also checks customer NPWP verification readiness for DP invoice issuance.
     */
    public function lockByFinance(Lead $lead, array $user, ?string $notes = null): array
    {
        $role = strtolower((string) ($user['role'] ?? 'user'));
        $permissions = array_map('strtolower', (array) ($user['permissions'] ?? []));

        $isAuthorized = in_array($role, ['finance', 'finance_ar', 'finance_manager', 'director', 'super_admin'], true)
            || in_array('crm.top.lock', $permissions, true)
            || in_array('finance.ar.manage', $permissions, true)
            || in_array('*', $permissions, true);

        if (!$isAuthorized) {
            throw new DomainException('Hanya Finance AR Specialist / Finance Manager yang berwenang mengunci skema TOP.');
        }

        $scheme = $this->getForLead($lead);
        if (!$scheme) {
            throw new InvalidArgumentException("Skema TOP belum dibuat untuk Lead #{$lead->id}.");
        }

        if (!$scheme->is_balanced || abs((float) $scheme->total_percent - 100.0) >= 0.01) {
            throw new InvalidArgumentException(
                "Total termin harus 100% sebelum dapat dikunci oleh Finance. " . ($scheme->balance_warning ?? '')
            );
        }

        $scheme->update([
            'locked_by_finance_at' => now(),
            'locked_by_finance_by' => isset($user['id']) ? (int) $user['id'] : 1,
            'finance_lock_notes' => $notes ?? 'Dikunci oleh Finance AR.',
            'unlock_approval_status' => 'NONE',
            'pending_change_payload' => null,
        ]);

        $customer = $lead->customer;
        $npwpVerified = $customer !== null && $customer->npwp_verified_at !== null;

        return [
            'scheme' => $scheme->fresh(['milestones']),
            'npwp_verified' => $npwpVerified,
            'dp_invoice_ready' => $npwpVerified && $scheme->is_balanced,
            'npwp_warning' => !$npwpVerified
                ? 'NPWP Customer (16 digit) wajib diverifikasi oleh Finance sebelum Invoice DP dapat diterbitkan (FR-CRM-07).'
                : null,
        ];
    }

    /**
     * Creates a pending Finance approval when a user tries to modify a Finance-locked TOP scheme (BR-CRM-18).
     */
    public function createLockedChangeApproval(
        Lead $lead,
        TopScheme $scheme,
        array $proposedPayload,
        array $user
    ): CrmApproval {
        $requesterId = (int) ($user['id'] ?? 1);

        $coreApprovalId = null;
        try {
            $coreUrl = rtrim((string) config('services.core.url', env('CORE_SERVICE_URL', 'http://127.0.0.1:8001')), '/');
            $resp = Http::timeout(2)->post("{$coreUrl}/api/v1/approvals", [
                'reference_type' => 'crm_top_scheme',
                'reference_id' => (string) $scheme->id,
                'approval_type' => 'TOP_LOCKED_CHANGE',
                'requester_id' => $requesterId,
                'steps' => [
                    ['role' => 'finance'],
                ],
                'payload' => [
                    'lead_id' => $lead->id,
                    'top_scheme_id' => $scheme->id,
                    'proposed_payload' => $proposedPayload,
                ],
                'notes' => $proposedPayload['change_reason'] ?? 'Permohonan perubahan TOP setelah dikunci Finance (BR-CRM-18).',
            ]);
            if ($resp->successful()) {
                $coreApprovalId = $resp->json('data.id');
            }
        } catch (\Throwable) {
            // Tracked locally in crm_approvals
        }

        $scheme->update([
            'unlock_approval_status' => 'PENDING',
            'pending_change_payload' => $proposedPayload,
        ]);

        return CrmApproval::create([
            'lead_id' => $lead->id,
            'core_approval_id' => $coreApprovalId ? (int) $coreApprovalId : null,
            'approval_type' => 'TOP_LOCKED_CHANGE',
            'required_tier' => 'FINANCE',
            'required_role' => 'finance',
            'status' => 'PENDING',
            'total_amount' => $scheme->total_amount,
            'reasons' => [$proposedPayload['change_reason'] ?? 'Perubahan skema TOP setelah dikunci oleh Finance AR (BR-CRM-18).'],
            'requested_by' => $requesterId,
        ]);
    }

    /**
     * Finance approves or rejects a change to a locked TOP scheme (BR-CRM-18).
     */
    public function decideLockedChangeApproval(
        Lead $lead,
        string $action,
        array $financeUser,
        ?string $notes = null
    ): array {
        $role = strtolower((string) ($financeUser['role'] ?? 'user'));
        $permissions = array_map('strtolower', (array) ($financeUser['permissions'] ?? []));

        $isAuthorized = in_array($role, ['finance', 'finance_ar', 'finance_manager', 'director', 'super_admin'], true)
            || in_array('crm.top.lock', $permissions, true)
            || in_array('finance.ar.manage', $permissions, true)
            || in_array('*', $permissions, true);

        if (!$isAuthorized) {
            throw new DomainException('Hanya Finance yang dapat menyetujui perubahan atas skema TOP yang telah dikunci (BR-CRM-18).');
        }

        $scheme = $this->getForLead($lead);
        if (!$scheme) {
            throw new InvalidArgumentException("Skema TOP tidak ditemukan untuk Lead #{$lead->id}.");
        }

        $normAction = strtoupper(trim($action));
        $finalStatus = str_starts_with($normAction, 'APPROV') ? 'APPROVED' : 'REJECTED';
        $approverId = (int) ($financeUser['id'] ?? 1);

        $approval = CrmApproval::where('lead_id', $lead->id)
            ->where('approval_type', 'TOP_LOCKED_CHANGE')
            ->orderByDesc('id')
            ->first();

        // Enforce Segregation of Duties unless super_admin
        if ($approval && $role !== 'super_admin' && (int) $approval->requested_by === $approverId) {
            throw new DomainException('Pelanggaran Segregation of Duties (SoD): pemohon perubahan TOP tidak dapat menyetujui permohonannya sendiri.');
        }

        return DB::transaction(function () use ($lead, $scheme, $approval, $finalStatus, $approverId, $notes, $financeUser) {
            if ($approval) {
                $approval->update([
                    'status' => $finalStatus,
                    'decided_by' => $approverId,
                    'decision_notes' => $notes,
                    'decided_at' => now(),
                ]);
            }

            if ($finalStatus === 'APPROVED') {
                $pendingPayload = $scheme->pending_change_payload;
                $scheme->update(['unlock_approval_status' => 'APPROVED']);

                if (is_array($pendingPayload) && !empty($pendingPayload)) {
                    $pendingPayload['finance_override'] = true;
                    $scheme = $this->saveTopScheme($lead, $pendingPayload, $financeUser);
                    // Re-lock after applying the Finance-approved change
                    $scheme->update([
                        'locked_by_finance_at' => now(),
                        'locked_by_finance_by' => $approverId,
                        'finance_lock_notes' => $notes ?? 'Perubahan TOP disetujui dan dikunci kembali oleh Finance.',
                    ]);
                }
            } else {
                $scheme->update([
                    'unlock_approval_status' => 'REJECTED',
                    'pending_change_payload' => null,
                ]);
            }

            return [
                'scheme' => $scheme->fresh(['milestones']),
                'approval' => $approval,
            ];
        });
    }

    /**
     * Generates the Receivable Schedule structure for Finance upon Deal Won (FR-CRM-07, FR-CRM-09).
     */
    public function buildReceivableSchedule(Lead $lead): array
    {
        $lead->loadMissing(['customer']);
        $scheme = $this->getForLead($lead);
        if (!$scheme) {
            throw new InvalidArgumentException("Skema TOP belum tersedia untuk Lead #{$lead->id}.");
        }

        $customer = $lead->customer;
        $npwpVerified = $customer !== null && $customer->npwp_verified_at !== null;

        $scheduleItems = $scheme->milestones->map(function (TopMilestone $m) use ($scheme, $npwpVerified): array {
            $isDp = (int) $m->term_number === 1 || strtoupper((string) $m->name) === 'DP' || $m->trigger_type === 'signing';

            return [
                'term_number' => (int) $m->term_number,
                'milestone_name' => $m->name,
                'percentage' => (float) $m->percentage,
                'dpp_idr' => (float) $m->dpp_amount,
                'ppn_rate' => (float) $scheme->vat_rate,
                'ppn_idr' => (float) $m->ppn_amount,
                'pph23_rate' => (float) $scheme->pph23_rate,
                'pph23_idr' => (float) $m->pph23_amount,
                'invoice_total_idr' => round((float) $m->dpp_amount + (float) $m->ppn_amount, 2),
                'net_cash_idr' => (float) $m->net_cash_idr,
                'terbilang_text' => $m->terbilang_text,
                'trigger_type' => $m->trigger_type,
                'description' => $m->description,
                'due_days_offset' => (int) $m->due_days_offset,
                'due_date' => $m->due_date?->toDateString(),
                'project_milestone_ref' => $m->project_milestone_ref,
                'is_retention' => (bool) $m->is_retention,
                'is_dp_term' => $isDp,
                'can_issue_invoice' => $isDp ? ($scheme->is_balanced && $npwpVerified) : $scheme->is_balanced,
                'status' => 'SCHEDULED_DRAFT',
            ];
        })->values()->all();

        return [
            'lead_id' => $lead->id,
            'lead_code' => $lead->code,
            'customer_id' => $customer?->id,
            'customer_name' => $customer?->legal_name,
            'npwp_verified' => $npwpVerified,
            'top_scheme_id' => $scheme->id,
            'preset_code' => $scheme->preset_code,
            'is_balanced' => (bool) $scheme->is_balanced,
            'locked_by_finance_at' => $scheme->locked_by_finance_at?->toIso8601String(),
            'dp_invoice_ready' => (bool) $scheme->is_balanced && $npwpVerified,
            'total_dpp_idr' => (float) $scheme->total_dpp,
            'total_ppn_idr' => (float) $scheme->total_ppn,
            'total_pph23_idr' => (float) $scheme->total_pph23,
            'total_invoice_idr' => round((float) $scheme->total_dpp + (float) $scheme->total_ppn, 2),
            'total_net_cash_idr' => (float) $scheme->total_net_cash,
            'total_terbilang' => $scheme->total_terbilang,
            'schedule' => $scheduleItems,
        ];
    }
}

