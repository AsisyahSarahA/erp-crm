<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CrmApproval;
use App\Models\CrmImmutableAuditLog;
use App\Models\CrmSetting;
use App\Models\Lead;
use App\Models\RabItem;
use App\Models\RabVersion;
use Astacode\Kernel\Finance\RabCalculator;
use Astacode\Kernel\Support\Terbilang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

final class RabEngineService
{
    /**
     * Retrieves the active RAB version for a Lead, or the latest non-draft/latest version if active is not set.
     */
    public function getActiveOrLatestRab(Lead $lead): ?RabVersion
    {
        if ($lead->active_rab_version_id) {
            $active = RabVersion::with('items')->find($lead->active_rab_version_id);
            if ($active) {
                return $active;
            }
        }

        return RabVersion::with('items')
            ->where('lead_id', $lead->id)
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Authoritative Server-Side RAB Calculation (FR-CRM-06, BR-CRM-07, AC-CRM-04, FR-CRM-17).
     */
    public function calculateSummary(
        array $payload,
        ?Lead $lead = null,
        ?RabVersion $existingRab = null,
        bool $isAutosave = false
    ): array {
        $settings = CrmSetting::allMerged();

        $rawItems = (array) ($payload['items'] ?? []);
        if (!$isAutosave && count($rawItems) < 1) {
            throw new InvalidArgumentException('Estimasi RAB wajib memiliki minimal 1 item modul (FR-CRM-06).');
        }

        // Load active rate cards indexed by role and role_level
        $rateCardRows = DB::table('crm_rate_cards')
            ->where('is_active', true)
            ->get();

        $rateCardsByRole = $this->indexRateCards($rateCardRows);

        // Map existing items from DB to detect edits on AI items (AC-CRM-04)
        $existingItemsById = collect();
        $existingItemsByNameRole = collect();
        if ($existingRab !== null) {
            $loadedItems = $existingRab->relationLoaded('items')
                ? $existingRab->items
                : $existingRab->items()->get();
            $existingItemsById = $loadedItems->keyBy('id');
            $existingItemsByNameRole = $loadedItems->keyBy(
                fn (RabItem $it) => strtolower(trim($it->name)) . '|' . strtoupper(trim($it->role))
            );
        }

        $warnings = [];
        $processedItems = [];
        $kernelItems = [];
        $totalMandays = 0.0;
        $mandaysByRole = [];
        $subtotalNonOptional = 0.0;
        $itemsHppTotal = 0.0;

        foreach ($rawItems as $idx => $item) {
            $itemName = trim((string) ($item['name'] ?? 'Modul ' . ($idx + 1)));
            $role = strtoupper(trim((string) ($item['role'] ?? 'BE')));
            $level = strtoupper(trim((string) ($item['level'] ?? 'MID')));
            $unit = trim((string) ($item['unit'] ?? 'Mandays'));
            $qty = isset($item['qty']) ? (float) $item['qty'] : (float) ($item['mandays'] ?? 1.0);

            if (!$isAutosave && $qty <= 0) {
                throw new InvalidArgumentException("Quantity pada item '{$itemName}' harus lebih besar dari 0 (qty > 0).");
            }
            if ($qty <= 0) {
                $qty = 0.5;
            }

            $isOptional = (bool) ($item['is_optional'] ?? false);
            $isRecurring = (bool) ($item['is_recurring'] ?? false);
            $isBonus = (bool) ($item['is_bonus'] ?? $item['is_free'] ?? false);
            $recurringPeriod = $isRecurring ? strtoupper((string) ($item['recurring_period'] ?? 'MONTHLY')) : null;

            // Match Rate Card
            $matchedCard = $this->findRateCard($rateCardsByRole, $role, $level);
            if ($matchedCard === null && !isset($item['unit_price']) && !isset($item['cost_rate'])) {
                $warnings[] = "Role '{$role}' pada item '{$itemName}' belum memiliki tarif pada Rate Card.";
            } elseif ($matchedCard === null) {
                $warnings[] = "Role '{$role}' pada item '{$itemName}' tidak ditemukan di master Rate Card (menggunakan harga manual).";
            }

            $defaultSellRate = $matchedCard !== null
                ? (float) ($matchedCard->sell_rate_idr ?? $matchedCard->daily_rate_sell ?? 0.0)
                : 0.0;
            $defaultCostRate = $matchedCard !== null
                ? (float) ($matchedCard->cost_rate_idr ?? $matchedCard->daily_rate_cost ?? 0.0)
                : 0.0;

            $unitPrice = array_key_exists('unit_price', $item) && $item['unit_price'] !== null
                ? (float) $item['unit_price']
                : (array_key_exists('price', $item) && $item['price'] !== null
                    ? (float) $item['price']
                    : (array_key_exists('daily_rate_sell', $item) && $item['daily_rate_sell'] !== null
                        ? (float) $item['daily_rate_sell']
                        : $defaultSellRate));

            if (!$isAutosave && $unitPrice < 0) {
                throw new InvalidArgumentException("Harga satuan pada item '{$itemName}' tidak boleh negatif (harga >= 0).");
            }

            $costRate = array_key_exists('cost_rate', $item) && $item['cost_rate'] !== null
                ? (float) $item['cost_rate']
                : (array_key_exists('daily_rate_cost', $item) && $item['daily_rate_cost'] !== null
                    ? (float) $item['daily_rate_cost']
                    : $defaultCostRate);

            // Detect origin and AC-CRM-04 transition (AI -> MANUAL when edited)
            $existingItem = null;
            if (!empty($item['id'])) {
                $existingItem = $existingItemsById->get((int) $item['id']);
            }
            if (!$existingItem) {
                $existingItem = $existingItemsByNameRole->get(strtolower($itemName) . '|' . $role);
            }

            $incomingOrigin = strtoupper((string) ($item['origin'] ?? ($existingItem?->origin ?? 'MANUAL')));
            if (!in_array($incomingOrigin, ['AI', 'MANUAL', 'TEMPLATE'], true)) {
                $incomingOrigin = 'MANUAL';
            }

            $origin = $incomingOrigin;
            if (!empty($item['is_edited']) && $origin === 'AI') {
                $origin = 'MANUAL';
            } elseif ($existingItem !== null && strtoupper((string) $existingItem->origin) === 'AI') {
                $qtyChanged = abs((float) $existingItem->qty - $qty) > 0.001;
                $unitChanged = strtolower(trim((string) $existingItem->unit)) !== strtolower($unit);
                $priceChanged = abs((float) $existingItem->unit_price - $unitPrice) > 0.01;
                $nameChanged = trim((string) $existingItem->name) !== $itemName;
                $roleChanged = strtoupper(trim((string) $existingItem->role)) !== $role;
                $optChanged = (bool) $existingItem->is_optional !== $isOptional;
                $bonusChanged = (bool) $existingItem->is_bonus !== $isBonus;

                if ($qtyChanged || $unitChanged || $priceChanged || $nameChanged || $roleChanged || $optChanged || $bonusChanged) {
                    $origin = 'MANUAL';
                }
            }

            $lineSubtotalSell = ($isBonus || $isOptional)
                ? ($isBonus ? 0.0 : round($qty * $unitPrice, 2))
                : round($qty * $unitPrice, 2);

            $lineHpp = round($qty * $costRate, 2);

            if (!$isOptional) {
                if (!$isBonus) {
                    $subtotalNonOptional = round($subtotalNonOptional + $lineSubtotalSell, 2);
                }
                $itemsHppTotal = round($itemsHppTotal + $lineHpp, 2);

                if (strtolower($unit) === 'mandays') {
                    $totalMandays = round($totalMandays + $qty, 2);
                    $mandaysByRole[$role] = round(($mandaysByRole[$role] ?? 0.0) + $qty, 2);
                }
            }

            $roleCode = (string) ($item['role_code'] ?? ($role . '_' . $level));

            $processedItems[] = [
                'id' => $item['id'] ?? $existingItem?->id,
                'module_catalog_id' => $item['module_catalog_id'] ?? $existingItem?->module_catalog_id,
                'sort_order' => (int) ($item['sort_order'] ?? ($idx + 1)),
                'category' => (string) ($item['category'] ?? $existingItem?->category ?? 'Master Data'),
                'layer' => (string) ($item['layer'] ?? $existingItem?->layer ?? 'Backend Core & DB'),
                'name' => $itemName,
                'description' => (string) ($item['description'] ?? $existingItem?->description ?? ''),
                'role' => $role,
                'role_code' => $roleCode,
                'mandays' => strtolower($unit) === 'mandays' ? $qty : (float) ($item['mandays'] ?? $qty),
                'qty' => $qty,
                'unit' => $unit,
                'daily_rate_sell' => $unitPrice,
                'daily_rate_cost' => $costRate,
                'unit_price' => $unitPrice,
                'subtotal_sell' => $isBonus ? 0.0 : round($qty * $unitPrice, 2),
                'subtotal_cost' => $lineHpp,
                'subtotal' => $isBonus ? 0.0 : round($qty * $unitPrice, 2),
                'cost_rate' => $costRate,
                'hpp' => $lineHpp,
                'complexity' => strtoupper((string) ($item['complexity'] ?? $existingItem?->complexity ?? 'MEDIUM')),
                'confidence' => (int) ($item['confidence'] ?? $existingItem?->confidence ?? 85),
                'origin' => $origin,
                'source_badge' => $origin,
                'ai_confidence' => $origin === 'AI'
                    ? (isset($item['ai_confidence']) ? (float) $item['ai_confidence'] : ($existingItem?->ai_confidence ?? 85.0))
                    : null,
                'is_optional' => $isOptional,
                'is_recurring' => $isRecurring,
                'is_bonus' => $isBonus,
                'recurring_period' => $recurringPeriod,
                'notes' => (string) ($item['notes'] ?? $existingItem?->notes ?? ''),
            ];

            $kernelItems[] = [
                'name' => $itemName,
                'qty' => $qty,
                'price' => $unitPrice,
                'cost_rate' => $costRate,
                'is_optional' => $isOptional,
                'is_free' => $isBonus,
            ];
        }

        // Direct costs (cloud, license, freelance, etc.)
        $rawDirectCosts = (array) ($payload['direct_costs'] ?? $existingRab?->direct_costs ?? []);
        $normalizedDirectCosts = [];
        $directCostsAmounts = [];
        $directCostsTotal = 0.0;

        foreach ($rawDirectCosts as $dc) {
            if (is_numeric($dc)) {
                $amt = round((float) $dc, 2);
                $normalizedDirectCosts[] = [
                    'name' => 'Biaya Langsung',
                    'category' => 'other',
                    'amount' => $amt,
                ];
                $directCostsAmounts[] = $amt;
                $directCostsTotal = round($directCostsTotal + $amt, 2);
            } elseif (is_array($dc)) {
                $amt = round((float) ($dc['amount'] ?? 0), 2);
                $normalizedDirectCosts[] = [
                    'name' => (string) ($dc['name'] ?? 'Biaya Langsung'),
                    'category' => (string) ($dc['category'] ?? 'cloud'),
                    'amount' => $amt,
                ];
                $directCostsAmounts[] = $amt;
                $directCostsTotal = round($directCostsTotal + $amt, 2);
            }
        }

        // Contingency & Discount parameters
        $contingencyPct = array_key_exists('contingency_pct', $payload) && $payload['contingency_pct'] !== null
            ? (float) $payload['contingency_pct']
            : (array_key_exists('contingency_percent', $payload) && $payload['contingency_percent'] !== null
                ? (float) $payload['contingency_percent']
                : ($existingRab !== null ? (float) $existingRab->contingency_pct : (float) $settings['contingency_default_pct']));

        $contingencyReason = array_key_exists('contingency_reason', $payload)
            ? ($payload['contingency_reason'] !== null ? (string) $payload['contingency_reason'] : null)
            : $existingRab?->contingency_reason;

        $discountType = strtoupper((string) ($payload['discount_type'] ?? $existingRab?->discount_type ?? 'PERCENT'));
        if (!in_array($discountType, ['PERCENT', 'NOMINAL'], true)) {
            $discountType = 'PERCENT';
        }

        $discountValue = array_key_exists('discount_value', $payload) && $payload['discount_value'] !== null
            ? (float) $payload['discount_value']
            : null;

        $discountPctInput = 0.0;
        $discountNominalInput = null;

        if ($discountType === 'NOMINAL') {
            $discountNominalInput = $discountValue ?? (float) ($payload['discount_nominal'] ?? $payload['discount_amount'] ?? $existingRab?->discount_amount ?? 0.0);
            $discountValue = (float) $discountNominalInput;
        } else {
            $discountPctInput = $discountValue ?? (float) ($payload['discount_pct'] ?? $payload['discount_percent'] ?? $existingRab?->discount_pct ?? 0.0);
            $discountValue = (float) $discountPctInput;
        }

        // Determine PKP & VAT Rate
        $isPkp = array_key_exists('is_pkp', $payload) && $payload['is_pkp'] !== null
            ? (bool) $payload['is_pkp']
            : ($existingRab?->is_pkp ?? $lead?->customer?->is_pkp ?? (bool) ($settings['is_company_pkp'] ?? true));

        $configuredVatRate = array_key_exists('vat_rate', $payload) && $payload['vat_rate'] !== null
            ? (float) $payload['vat_rate']
            : (array_key_exists('tax_rate', $payload) && $payload['tax_rate'] !== null
                ? (float) $payload['tax_rate']
                : (float) ($settings['vat_rate'] ?? 11.0));

        $effectiveVatRate = $isPkp ? $configuredVatRate : 0.0;

        // Delegate core calculation to Astacode Kernel RabCalculator
        $kernelCalc = RabCalculator::calculate(
            items: $kernelItems,
            contingencyPercent: $contingencyPct,
            contingencyReason: $contingencyReason,
            discountPercent: $discountPctInput,
            discountNominal: $discountNominalInput,
            ppnRate: $effectiveVatRate,
            topPercentages: [],
            additionalDirectCosts: $directCostsAmounts,
            isTaxWithholder: (bool) ($lead?->customer?->is_pph23_withholder ?? true)
        );

        $subtotal = (float) $kernelCalc->subtotal;
        $contingencyAmount = (float) $kernelCalc->contingencyAmount;
        $discountPct = (float) $kernelCalc->discountPercent;
        $discountAmount = (float) $kernelCalc->discountAmount;
        $dpp = (float) $kernelCalc->dpp;
        $vatAmount = (float) $kernelCalc->ppnAmount;
        $total = (float) $kernelCalc->totalInvestasi;
        $hppInternal = (float) $kernelCalc->hppInternal;
        $grossMarginNominal = (float) $kernelCalc->grossMarginNominal;
        $marginPct = (float) $kernelCalc->grossMarginPercent;
        $terbilang = $kernelCalc->terbilang ?: Terbilang::rupiah($total);

        // Configurable margin color indicator (GREEN >= 40, YELLOW 25-40, RED < 25)
        $greenThreshold = (float) ($settings['margin_green_threshold'] ?? 40.0);
        $yellowThreshold = (float) ($settings['margin_yellow_threshold'] ?? 25.0);

        $marginStatus = 'RED';
        if ($marginPct >= $greenThreshold) {
            $marginStatus = 'GREEN';
        } elseif ($marginPct >= $yellowThreshold) {
            $marginStatus = 'YELLOW';
        }

        // Approval Tier Evaluation (FR-CRM-17)
        $approvalEval = $this->evaluateApprovalRequirement(
            discountPct: $discountPct,
            marginPct: $marginPct,
            totalInvestasi: $total,
            dpp: $dpp,
            settings: $settings
        );

        if ($marginPct < $yellowThreshold && $dpp > 0) {
            $warnings[] = "Gross Margin ({$marginPct}%) berada di bawah ambang minimum ({$yellowThreshold}%). Memerlukan persetujuan Direktur.";
        }
        if ($discountPct > (float) ($settings['discount_sales_max_pct'] ?? 10.0)) {
            $warnings[] = "Diskon ({$discountPct}%) melewati batas mandiri Sales ({$settings['discount_sales_max_pct']}%). Memerlukan persetujuan {$approvalEval['approval_tier']}.";
        }

        // Duration derivation & HRD capacity info
        $teamSizeByRole = (array) ($payload['team_size_by_role'] ?? $existingRab?->team_size_by_role ?? []);
        $durationDerivation = $this->deriveDurationAndCapacity($mandaysByRole, $teamSizeByRole);

        return [
            'subtotal' => $subtotal,
            'contingency_pct' => $contingencyPct,
            'contingency_reason' => $contingencyReason,
            'contingency_amount' => $contingencyAmount,
            'discount_type' => $discountType,
            'discount_value' => $discountType === 'NOMINAL' ? $discountAmount : $discountPct,
            'discount_pct' => $discountPct,
            'discount_amount' => $discountAmount,
            'is_pkp' => $isPkp,
            'dpp' => $dpp,
            'tax_rate' => $effectiveVatRate,
            'vat_rate' => $effectiveVatRate,
            'tax' => $vatAmount,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'total_terbilang' => $terbilang,
            'hpp_internal' => $hppInternal,
            'direct_costs' => $normalizedDirectCosts,
            'direct_costs_total' => $directCostsTotal,
            'gross_margin_nominal' => $grossMarginNominal,
            'margin_pct' => $marginPct,
            'gross_margin_percent' => $marginPct,
            'margin_status' => $marginStatus,
            'margin_indicator_color' => $marginStatus,
            'total_mandays' => $totalMandays,
            'duration_weeks' => $durationDerivation['estimated_duration_weeks'],
            'team_size_by_role' => $durationDerivation['team_size_by_role'],
            'duration_derivation' => $durationDerivation,
            'warnings' => array_values(array_unique($warnings)),
            'requires_approval' => $approvalEval['requires_approval'],
            'approval_tier' => $approvalEval['approval_tier'],
            'approval_role' => $approvalEval['approval_role'],
            'approval_reasons' => $approvalEval['reasons'],
            'items' => $processedItems,
        ];
    }

    /**
     * Evaluates FR-CRM-17 approval rules:
     * - Discount <= 10%: Sales (no extra approval if margin >= 25% and total <= Rp500jt)
     * - Discount > 10% and <= 15%: Sales Manager
     * - Discount > 15% OR Margin < 25% OR Quotation Total > Rp500jt: Director
     */
    public function evaluateApprovalRequirement(
        float $discountPct,
        float $marginPct,
        float $totalInvestasi,
        float $dpp,
        array $settings = []
    ): array {
        $salesMaxDiscount = (float) ($settings['discount_sales_max_pct'] ?? 10.0);
        $managerMaxDiscount = (float) ($settings['discount_manager_max_pct'] ?? 15.0);
        $minMarginPct = (float) ($settings['margin_yellow_threshold'] ?? 25.0);
        $directorValueThreshold = (float) ($settings['director_quotation_threshold_idr'] ?? 500_000_000.0);

        $reasons = [];
        $tier = 'NONE';
        $role = null;

        if ($discountPct > $managerMaxDiscount) {
            $tier = 'DIRECTOR';
            $role = 'director';
            $reasons[] = "Diskon {$discountPct}% melebihi batas Sales Manager ({$managerMaxDiscount}%). Wajib persetujuan Direktur.";
        } elseif ($discountPct > $salesMaxDiscount) {
            $tier = 'SALES_MANAGER';
            $role = 'sales_manager';
            $reasons[] = "Diskon {$discountPct}% berada pada rentang >{$salesMaxDiscount}% s.d. {$managerMaxDiscount}%. Wajib persetujuan Sales Manager.";
        }

        if ($dpp > 0 && $marginPct < $minMarginPct) {
            $tier = 'DIRECTOR';
            $role = 'director';
            $reasons[] = "Gross Margin ({$marginPct}%) di bawah batas minimum ({$minMarginPct}%). Wajib persetujuan Direktur.";
        }

        if ($totalInvestasi > $directorValueThreshold || $dpp > $directorValueThreshold) {
            $tier = 'DIRECTOR';
            $role = 'director';
            $reasons[] = sprintf(
                'Nilai penawaran (Rp%s) melebihi ambang Rp%s. Wajib persetujuan Direktur.',
                number_format($totalInvestasi, 0, ',', '.'),
                number_format($directorValueThreshold, 0, ',', '.')
            );
        }

        return [
            'requires_approval' => $tier !== 'NONE',
            'approval_tier' => $tier,
            'approval_role' => $role,
            'reasons' => $reasons,
        ];
    }

    /**
     * Derives project duration (in weeks) from mandays per role divided by team size,
     * and enriches with informative HRD capacity per role.
     */
    public function deriveDurationAndCapacity(array $mandaysByRole, array $teamSizeInput = []): array
    {
        $hrdCapacity = $this->fetchHrdCapacity();
        $breakdown = [];
        $normalizedTeamSize = [];
        $maxRoleDays = 0.0;

        foreach ($mandaysByRole as $role => $mandays) {
            $roleKey = strtoupper((string) $role);
            $teamSize = max(1, (int) ($teamSizeInput[$roleKey] ?? $teamSizeInput[strtolower($roleKey)] ?? 1));
            $normalizedTeamSize[$roleKey] = $teamSize;

            $workingDays = round($mandays / $teamSize, 1);
            $roleWeeks = (int) max(1, ceil($workingDays / 5.0));
            if ($workingDays > $maxRoleDays) {
                $maxRoleDays = $workingDays;
            }

            $availHeadcount = (int) ($hrdCapacity[$roleKey]['available_headcount'] ?? 3);
            $availMandaysMonth = (float) ($hrdCapacity[$roleKey]['available_mandays_per_month'] ?? ($availHeadcount * 20));

            $capacityStatus = 'AVAILABLE';
            if ($teamSize > $availHeadcount) {
                $capacityStatus = 'OVERLOADED';
            } elseif ($mandays > $availMandaysMonth) {
                $capacityStatus = 'TIGHT';
            }

            $breakdown[$roleKey] = [
                'role' => $roleKey,
                'mandays' => $mandays,
                'team_size' => $teamSize,
                'estimated_working_days' => $workingDays,
                'estimated_weeks' => $roleWeeks,
                'hrd_available_headcount' => $availHeadcount,
                'hrd_available_mandays_month' => $availMandaysMonth,
                'hrd_capacity_status' => $capacityStatus,
            ];
        }

        // Parallel overlap factor: critical path max role weeks (minimum 1 week)
        $estimatedWeeks = (int) max(1, ceil($maxRoleDays / 5.0));

        return [
            'estimated_working_days' => $maxRoleDays,
            'estimated_duration_weeks' => $estimatedWeeks,
            'team_size_by_role' => $normalizedTeamSize,
            'roles_breakdown' => $breakdown,
            'hrd_capacity_source' => $hrdCapacity['_source'] ?? 'informative_default',
        ];
    }

    /**
     * Saves/upserts RAB for a Lead with optimistic locking (`version`) and version history.
     */
    public function saveRab(Lead $lead, array $payload, array $user, bool $isAutosave = false): RabVersion
    {
        $currentRab = $this->getActiveOrLatestRab($lead);

        // Optimistic Locking check (HTTP 409 on version mismatch)
        $expectedVersion = $payload['expected_version'] ?? $payload['version'] ?? null;
        if ($currentRab !== null && $expectedVersion !== null) {
            if ((int) $expectedVersion !== (int) $currentRab->version) {
                throw new OptimisticLockException(
                    expectedVersion: (int) $expectedVersion,
                    actualVersion: (int) $currentRab->version
                );
            }
        }

        $calc = $this->calculateSummary(
            payload: $payload,
            lead: $lead,
            existingRab: $currentRab,
            isAutosave: $isAutosave
        );

        return DB::transaction(function () use ($lead, $payload, $user, $currentRab, $calc, $isAutosave) {
            $oldDiscountPct = $currentRab ? (float) $currentRab->discount_pct : 0.0;
            $oldDiscountAmt = $currentRab ? (float) $currentRab->discount_amount : 0.0;
            $oldTotal = $currentRab ? (float) $currentRab->total : (float) $lead->est_value;

            if ($isAutosave && $currentRab !== null && in_array($currentRab->status, ['DRAFT', 'ACTIVE'], true)) {
                // In-place update for autosave draft
                $targetRab = $currentRab;
                $targetRab->update([
                    'total_mandays' => $calc['total_mandays'],
                    'duration_weeks' => $calc['duration_weeks'],
                    'team_size_by_role' => $calc['team_size_by_role'],
                    'duration_derivation' => $calc['duration_derivation'],
                    'subtotal' => $calc['subtotal'],
                    'contingency_pct' => $calc['contingency_pct'],
                    'contingency_reason' => $calc['contingency_reason'],
                    'contingency_amount' => $calc['contingency_amount'],
                    'discount_type' => $calc['discount_type'],
                    'discount_value' => $calc['discount_value'],
                    'discount_pct' => $calc['discount_pct'],
                    'discount_amount' => $calc['discount_amount'],
                    'is_pkp' => $calc['is_pkp'],
                    'dpp' => $calc['dpp'],
                    'tax_rate' => $calc['tax_rate'],
                    'vat_rate' => $calc['vat_rate'],
                    'tax' => $calc['tax'],
                    'vat_amount' => $calc['vat_amount'],
                    'total' => $calc['total'],
                    'total_terbilang' => $calc['total_terbilang'],
                    'hpp_internal' => $calc['hpp_internal'],
                    'direct_costs' => $calc['direct_costs'],
                    'direct_costs_total' => $calc['direct_costs_total'],
                    'gross_margin_nominal' => $calc['gross_margin_nominal'],
                    'margin_pct' => $calc['margin_pct'],
                    'margin_status' => $calc['margin_status'],
                    'wbs_completed' => (bool) ($payload['wbs_completed'] ?? true),
                    'assumptions' => $payload['assumptions'] ?? $currentRab->assumptions ?? [],
                    'risks' => $payload['risks'] ?? $currentRab->risks ?? [],
                    'out_of_scope' => $payload['out_of_scope'] ?? $currentRab->out_of_scope ?? [],
                    'clarifying_questions' => $payload['clarifying_questions'] ?? $currentRab->clarifying_questions ?? [],
                    'tech_stack' => $payload['tech_stack'] ?? $currentRab->tech_stack ?? [],
                    'warnings' => $calc['warnings'],
                    'requires_approval' => $calc['requires_approval'],
                    'approval_tier' => $calc['approval_tier'],
                    'approval_reasons' => $calc['approval_reasons'],
                    'approval_status' => $calc['requires_approval'] ? 'PENDING' : 'NOT_REQUIRED',
                ]);

                $targetRab->items()->delete();
            } else {
                // Full save creates a new version snapshot (or v1 if none exists) & preserves history
                $maxVersion = (int) RabVersion::where('lead_id', $lead->id)->max('version');
                $nextVersion = $maxVersion + 1;

                if ($currentRab !== null && $currentRab->status === 'ACTIVE') {
                    $currentRab->update(['status' => 'SUPERSEDED']);
                }

                // Determine overall RAB source
                $origins = array_unique(array_column($calc['items'], 'origin'));
                $rabSource = count($origins) === 1 ? $origins[0] : 'MANUAL';

                $targetRab = RabVersion::create([
                    'lead_id' => $lead->id,
                    'requirement_id' => $payload['requirement_id'] ?? $currentRab?->requirement_id,
                    'ai_run_id' => $payload['ai_run_id'] ?? $currentRab?->ai_run_id,
                    'version' => $nextVersion,
                    'source' => $rabSource,
                    'status' => $calc['requires_approval'] ? 'PENDING_APPROVAL' : 'ACTIVE',
                    'total_mandays' => $calc['total_mandays'],
                    'duration_weeks' => $calc['duration_weeks'],
                    'team_size_by_role' => $calc['team_size_by_role'],
                    'duration_derivation' => $calc['duration_derivation'],
                    'subtotal' => $calc['subtotal'],
                    'contingency_pct' => $calc['contingency_pct'],
                    'contingency_reason' => $calc['contingency_reason'],
                    'contingency_amount' => $calc['contingency_amount'],
                    'discount_type' => $calc['discount_type'],
                    'discount_value' => $calc['discount_value'],
                    'discount_pct' => $calc['discount_pct'],
                    'discount_amount' => $calc['discount_amount'],
                    'is_pkp' => $calc['is_pkp'],
                    'dpp' => $calc['dpp'],
                    'tax_rate' => $calc['tax_rate'],
                    'vat_rate' => $calc['vat_rate'],
                    'tax' => $calc['tax'],
                    'vat_amount' => $calc['vat_amount'],
                    'total' => $calc['total'],
                    'total_terbilang' => $calc['total_terbilang'],
                    'hpp_internal' => $calc['hpp_internal'],
                    'direct_costs' => $calc['direct_costs'],
                    'direct_costs_total' => $calc['direct_costs_total'],
                    'gross_margin_nominal' => $calc['gross_margin_nominal'],
                    'margin_pct' => $calc['margin_pct'],
                    'margin_status' => $calc['margin_status'],
                    'wbs_completed' => (bool) ($payload['wbs_completed'] ?? true),
                    'assumptions' => $payload['assumptions'] ?? $currentRab?->assumptions ?? [],
                    'risks' => $payload['risks'] ?? $currentRab?->risks ?? [],
                    'out_of_scope' => $payload['out_of_scope'] ?? $currentRab?->out_of_scope ?? [],
                    'clarifying_questions' => $payload['clarifying_questions'] ?? $currentRab?->clarifying_questions ?? [],
                    'tech_stack' => $payload['tech_stack'] ?? $currentRab?->tech_stack ?? [],
                    'warnings' => $calc['warnings'],
                    'requires_approval' => $calc['requires_approval'],
                    'approval_tier' => $calc['approval_tier'],
                    'approval_reasons' => $calc['approval_reasons'],
                    'approval_status' => $calc['requires_approval'] ? 'PENDING' : 'NOT_REQUIRED',
                    'created_by' => (string) ($user['id'] ?? '1'),
                ]);
            }

            foreach ($calc['items'] as $idx => $itemData) {
                RabItem::create([
                    'rab_version_id' => $targetRab->id,
                    'lead_id' => $lead->id,
                    'module_catalog_id' => $itemData['module_catalog_id'] ?? null,
                    'sort_order' => (int) ($itemData['sort_order'] ?? ($idx + 1)),
                    'category' => $itemData['category'],
                    'layer' => $itemData['layer'],
                    'name' => $itemData['name'],
                    'description' => $itemData['description'],
                    'role' => $itemData['role'],
                    'role_code' => $itemData['role_code'],
                    'mandays' => $itemData['mandays'],
                    'qty' => $itemData['qty'],
                    'unit' => $itemData['unit'],
                    'daily_rate_sell' => $itemData['daily_rate_sell'],
                    'daily_rate_cost' => $itemData['daily_rate_cost'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal_sell' => $itemData['subtotal_sell'],
                    'subtotal_cost' => $itemData['subtotal_cost'],
                    'subtotal' => $itemData['subtotal'],
                    'cost_rate' => $itemData['cost_rate'],
                    'hpp' => $itemData['hpp'],
                    'complexity' => $itemData['complexity'],
                    'confidence' => $itemData['confidence'],
                    'origin' => $itemData['origin'],
                    'ai_confidence' => $itemData['ai_confidence'],
                    'is_optional' => $itemData['is_optional'],
                    'is_recurring' => $itemData['is_recurring'],
                    'is_bonus' => $itemData['is_bonus'],
                    'recurring_period' => $itemData['recurring_period'],
                    'notes' => $itemData['notes'],
                ]);
            }

            // Update lead active_rab_version_id & est_value
            $lead->update([
                'active_rab_version_id' => $targetRab->id,
                'est_value' => $calc['dpp'] > 0 ? $calc['dpp'] : $lead->est_value,
            ]);

            // Record immutable audit log if discount changed
            if (abs($oldDiscountPct - $calc['discount_pct']) > 0.001 || abs($oldDiscountAmt - $calc['discount_amount']) > 0.01) {
                CrmImmutableAuditLog::record(
                    leadId: (int) $lead->id,
                    entityType: 'rab_version',
                    entityId: (int) $targetRab->id,
                    fieldName: 'discount',
                    oldValue: ['discount_pct' => $oldDiscountPct, 'discount_amount' => $oldDiscountAmt],
                    newValue: ['discount_pct' => $calc['discount_pct'], 'discount_amount' => $calc['discount_amount']],
                    actorId: (string) ($user['id'] ?? '1'),
                    reason: $payload['discount_reason'] ?? 'Update diskon RAB'
                );
            }

            // Automatically create/sync Approval request if required (FR-CRM-17)
            if ($calc['requires_approval'] && !$isAutosave) {
                $this->createOrUpdateApprovalRequest($lead, $targetRab, $calc, $user);
            }

            return $targetRab->fresh(['items']);
        });
    }

    /**
     * Submits/creates an approval record and syncs with Core Approval Engine (FR-CRM-17).
     */
    public function createOrUpdateApprovalRequest(Lead $lead, RabVersion $rab, array $calc, array $user): CrmApproval
    {
        $requesterId = (int) ($user['id'] ?? 1);
        $requiredRole = $calc['approval_role'] ?? ($calc['approval_tier'] === 'DIRECTOR' ? 'director' : 'sales_manager');

        // Dispatch to Core Approval Engine (/api/v1/approvals) with graceful fallback
        $coreApprovalId = null;
        try {
            $coreUrl = rtrim((string) config('services.core.url', env('CORE_SERVICE_URL', 'http://127.0.0.1:8001')), '/');
            $resp = Http::timeout(2)->post("{$coreUrl}/api/v1/approvals", [
                'reference_type' => 'crm_rab_version',
                'reference_id' => (string) $rab->id,
                'approval_type' => 'RAB_DISCOUNT_MARGIN',
                'requester_id' => $requesterId,
                'steps' => [
                    ['role' => $requiredRole],
                ],
                'payload' => [
                    'lead_id' => $lead->id,
                    'lead_code' => $lead->code,
                    'rab_version_id' => $rab->id,
                    'version' => $rab->version,
                    'discount_pct' => $rab->discount_pct,
                    'margin_pct' => $rab->margin_pct,
                    'total' => $rab->total,
                    'reasons' => $calc['approval_reasons'] ?? [],
                ],
                'notes' => implode(' ', $calc['approval_reasons'] ?? []),
            ]);

            if ($resp->successful()) {
                $coreApprovalId = $resp->json('data.id');
            }
        } catch (\Throwable) {
            // Core service offline in isolated unit tests; tracked in crm_approvals
        }

        $approval = CrmApproval::create([
            'lead_id' => $lead->id,
            'rab_version_id' => $rab->id,
            'core_approval_id' => $coreApprovalId ? (int) $coreApprovalId : null,
            'approval_type' => 'RAB_DISCOUNT_MARGIN',
            'required_tier' => $calc['approval_tier'],
            'required_role' => $requiredRole,
            'status' => 'PENDING',
            'discount_pct' => $rab->discount_pct,
            'margin_pct' => $rab->margin_pct,
            'total_amount' => $rab->total,
            'reasons' => $calc['approval_reasons'] ?? [],
            'requested_by' => $requesterId,
        ]);

        if ($coreApprovalId) {
            $rab->update(['core_approval_id' => (int) $coreApprovalId]);
        }

        return $approval;
    }

    /**
     * Approves or rejects a pending RAB version (FR-CRM-17).
     */
    public function decideApproval(
        Lead $lead,
        RabVersion $rab,
        string $action,
        array $approverUser,
        ?string $notes = null
    ): array {
        $actionNorm = strtoupper(trim($action));
        if (!in_array($actionNorm, ['APPROVED', 'REJECTED', 'APPROVE', 'REJECT'], true)) {
            throw new InvalidArgumentException("Action persetujuan harus 'APPROVED' atau 'REJECTED'.");
        }
        $finalStatus = str_starts_with($actionNorm, 'APPROV') ? 'APPROVED' : 'REJECTED';

        $approverRole = strtolower((string) ($approverUser['role'] ?? 'user'));
        $approverId = (int) ($approverUser['id'] ?? 1);
        $requiredTier = strtoupper((string) ($rab->approval_tier ?: 'SALES_MANAGER'));

        // Enforce role authorization for tier
        if ($requiredTier === 'DIRECTOR' && !in_array($approverRole, ['director', 'super_admin'], true)) {
            throw new \DomainException('Persetujuan RAB ini memerlukan peran Direktur atau Super Admin (diskon >15%, margin <25%, atau nilai >Rp500 juta).');
        }
        if ($requiredTier === 'SALES_MANAGER' && !in_array($approverRole, ['sales_manager', 'director', 'super_admin'], true)) {
            throw new \DomainException('Persetujuan RAB ini memerlukan peran minimal Sales Manager (diskon >10% s.d. 15%).');
        }

        // Enforce Segregation of Duties (SoD) unless super_admin
        if ($approverRole !== 'super_admin' && (string) $rab->created_by === (string) $approverId) {
            throw new \DomainException('Pelanggaran Segregation of Duties (SoD): pembuat RAB tidak dapat menyetujui pengajuan diskon/margin sendiri.');
        }

        return DB::transaction(function () use ($lead, $rab, $finalStatus, $approverId, $notes) {
            $rab->update([
                'approval_status' => $finalStatus,
                'status' => $finalStatus === 'APPROVED' ? 'APPROVED' : 'DRAFT',
                'approved_by' => $finalStatus === 'APPROVED' ? (string) $approverId : null,
                'approved_at' => $finalStatus === 'APPROVED' ? now() : null,
            ]);

            $crmApproval = CrmApproval::where('rab_version_id', $rab->id)
                ->orderByDesc('id')
                ->first();

            if ($crmApproval) {
                $crmApproval->update([
                    'status' => $finalStatus,
                    'decided_by' => $approverId,
                    'decision_notes' => $notes,
                    'decided_at' => now(),
                ]);
            } else {
                $crmApproval = CrmApproval::create([
                    'lead_id' => $lead->id,
                    'rab_version_id' => $rab->id,
                    'approval_type' => 'RAB_DISCOUNT_MARGIN',
                    'required_tier' => $rab->approval_tier ?: 'SALES_MANAGER',
                    'required_role' => $rab->approval_tier === 'DIRECTOR' ? 'director' : 'sales_manager',
                    'status' => $finalStatus,
                    'discount_pct' => $rab->discount_pct,
                    'margin_pct' => $rab->margin_pct,
                    'total_amount' => $rab->total,
                    'reasons' => $rab->approval_reasons ?? [],
                    'requested_by' => (int) ($rab->created_by ?: 1),
                    'decided_by' => $approverId,
                    'decision_notes' => $notes,
                    'decided_at' => now(),
                ]);
            }

            return [
                'rab' => $rab->fresh(['items']),
                'approval' => $crmApproval,
            ];
        });
    }

    /**
     * Compares two RAB versions and generates a structured diff of qty, unit_price, subtotal, and summary totals.
     */
    public function compareVersions(Lead $lead, int $fromVersionNumber, int $toVersionNumber): array
    {
        $fromRab = RabVersion::with('items')
            ->where('lead_id', $lead->id)
            ->where('version', $fromVersionNumber)
            ->first();

        $toRab = RabVersion::with('items')
            ->where('lead_id', $lead->id)
            ->where('version', $toVersionNumber)
            ->first();

        if (!$fromRab || !$toRab) {
            throw new InvalidArgumentException("Versi RAB v{$fromVersionNumber} atau v{$toVersionNumber} tidak ditemukan untuk lead ini.");
        }

        $fromMap = $fromRab->items->keyBy(fn (RabItem $i) => strtolower(trim($i->name)) . '|' . strtoupper(trim($i->role)));
        $toMap = $toRab->items->keyBy(fn (RabItem $i) => strtolower(trim($i->name)) . '|' . strtoupper(trim($i->role)));

        $allKeys = array_unique(array_merge($fromMap->keys()->all(), $toMap->keys()->all()));
        $itemDiffs = [];

        foreach ($allKeys as $key) {
            /** @var RabItem|null $oldItem */
            $oldItem = $fromMap->get($key);
            /** @var RabItem|null $newItem */
            $newItem = $toMap->get($key);

            if ($oldItem === null && $newItem !== null) {
                $itemDiffs[] = [
                    'change_type' => 'ADDED',
                    'name' => $newItem->name,
                    'category' => $newItem->category,
                    'role' => $newItem->role,
                    'old_qty' => 0.0,
                    'new_qty' => (float) $newItem->qty,
                    'qty_diff' => (float) $newItem->qty,
                    'old_unit_price' => 0.0,
                    'new_unit_price' => (float) $newItem->unit_price,
                    'unit_price_diff' => (float) $newItem->unit_price,
                    'old_subtotal' => 0.0,
                    'new_subtotal' => (float) $newItem->subtotal,
                    'subtotal_diff' => (float) $newItem->subtotal,
                ];
            } elseif ($oldItem !== null && $newItem === null) {
                $itemDiffs[] = [
                    'change_type' => 'REMOVED',
                    'name' => $oldItem->name,
                    'category' => $oldItem->category,
                    'role' => $oldItem->role,
                    'old_qty' => (float) $oldItem->qty,
                    'new_qty' => 0.0,
                    'qty_diff' => -(float) $oldItem->qty,
                    'old_unit_price' => (float) $oldItem->unit_price,
                    'new_unit_price' => 0.0,
                    'unit_price_diff' => -(float) $oldItem->unit_price,
                    'old_subtotal' => (float) $oldItem->subtotal,
                    'new_subtotal' => 0.0,
                    'subtotal_diff' => -(float) $oldItem->subtotal,
                ];
            } elseif ($oldItem !== null && $newItem !== null) {
                $qtyDiff = round((float) $newItem->qty - (float) $oldItem->qty, 2);
                $priceDiff = round((float) $newItem->unit_price - (float) $oldItem->unit_price, 2);
                $subDiff = round((float) $newItem->subtotal - (float) $oldItem->subtotal, 2);
                $changed = abs($qtyDiff) > 0.001 || abs($priceDiff) > 0.01 || abs($subDiff) > 0.01;

                $itemDiffs[] = [
                    'change_type' => $changed ? 'MODIFIED' : 'UNCHANGED',
                    'name' => $newItem->name,
                    'category' => $newItem->category,
                    'role' => $newItem->role,
                    'old_qty' => (float) $oldItem->qty,
                    'new_qty' => (float) $newItem->qty,
                    'qty_diff' => $qtyDiff,
                    'old_unit_price' => (float) $oldItem->unit_price,
                    'new_unit_price' => (float) $newItem->unit_price,
                    'unit_price_diff' => $priceDiff,
                    'old_subtotal' => (float) $oldItem->subtotal,
                    'new_subtotal' => (float) $newItem->subtotal,
                    'subtotal_diff' => $subDiff,
                ];
            }
        }

        return [
            'lead_id' => $lead->id,
            'from_version' => $fromVersionNumber,
            'to_version' => $toVersionNumber,
            'summary_diff' => [
                'old_total_mandays' => (float) $fromRab->total_mandays,
                'new_total_mandays' => (float) $toRab->total_mandays,
                'mandays_diff' => round((float) $toRab->total_mandays - (float) $fromRab->total_mandays, 2),
                'old_subtotal' => (float) $fromRab->subtotal,
                'new_subtotal' => (float) $toRab->subtotal,
                'subtotal_diff' => round((float) $toRab->subtotal - (float) $fromRab->subtotal, 2),
                'old_dpp' => (float) $fromRab->dpp,
                'new_dpp' => (float) $toRab->dpp,
                'dpp_diff' => round((float) $toRab->dpp - (float) $fromRab->dpp, 2),
                'old_total' => (float) $fromRab->total,
                'new_total' => (float) $toRab->total,
                'total_diff' => round((float) $toRab->total - (float) $fromRab->total, 2),
            ],
            'items_diff' => $itemDiffs,
        ];
    }

    /**
     * Imports modules from `crm_module_catalog` into the Lead's RAB with `origin = 'TEMPLATE'`.
     */
    public function importFromCatalog(
        Lead $lead,
        array $moduleIds = [],
        ?string $category = null,
        bool $mergeWithExisting = true,
        array $user = []
    ): RabVersion {
        $query = DB::table('crm_module_catalog')->where('is_active', true);
        if (!empty($moduleIds)) {
            $query->whereIn('id', $moduleIds);
        } elseif ($category !== null && $category !== '') {
            $query->where('category', $category);
        } else {
            $query->limit(5);
        }

        $catalogRows = $query->get();
        if ($catalogRows->isEmpty()) {
            throw new InvalidArgumentException('Tidak ada modul katalog yang cocok untuk diimpor.');
        }

        $currentRab = $this->getActiveOrLatestRab($lead);
        $itemsPayload = [];

        if ($mergeWithExisting && $currentRab !== null) {
            foreach ($currentRab->items as $existing) {
                $itemsPayload[] = [
                    'id' => $existing->id,
                    'module_catalog_id' => $existing->module_catalog_id,
                    'sort_order' => $existing->sort_order,
                    'category' => $existing->category,
                    'layer' => $existing->layer,
                    'name' => $existing->name,
                    'description' => $existing->description,
                    'role' => $existing->role,
                    'qty' => (float) $existing->qty,
                    'unit' => $existing->unit,
                    'unit_price' => (float) $existing->unit_price,
                    'cost_rate' => (float) $existing->cost_rate,
                    'origin' => $existing->origin,
                    'ai_confidence' => $existing->ai_confidence,
                    'is_optional' => (bool) $existing->is_optional,
                    'is_recurring' => (bool) $existing->is_recurring,
                    'is_bonus' => (bool) $existing->is_bonus,
                    'notes' => $existing->notes,
                ];
            }
        }

        foreach ($catalogRows as $mod) {
            $mandaysByRole = is_string($mod->default_mandays_by_role)
                ? (json_decode($mod->default_mandays_by_role, true) ?: [])
                : (array) $mod->default_mandays_by_role;

            // Determine primary role and total mandays
            $primaryRole = 'BE';
            $maxMd = 0;
            foreach ($mandaysByRole as $rKey => $mdVal) {
                if ((float) $mdVal > $maxMd) {
                    $maxMd = (float) $mdVal;
                    $primaryRole = strtoupper((string) $rKey);
                }
            }
            $totalMd = (float) ($mod->total_mandays ?: array_sum($mandaysByRole) ?: 5);

            $itemsPayload[] = [
                'module_catalog_id' => $mod->id,
                'category' => (string) ($mod->category ?: 'Transaksi'),
                'layer' => 'Backend Core & DB',
                'name' => (string) $mod->name,
                'description' => (string) ($mod->description ?? ''),
                'role' => $primaryRole,
                'qty' => $totalMd,
                'unit' => 'Mandays',
                'origin' => 'TEMPLATE',
                'notes' => "Diimpor dari Katalog Modul ({$mod->code})",
            ];
        }

        return $this->saveRab(
            lead: $lead,
            payload: [
                'items' => $itemsPayload,
                'contingency_pct' => $currentRab?->contingency_pct ?? 10.0,
                'contingency_reason' => $currentRab?->contingency_reason,
                'discount_type' => $currentRab?->discount_type ?? 'PERCENT',
                'discount_value' => $currentRab?->discount_value ?? 0.0,
                'is_pkp' => $currentRab?->is_pkp ?? true,
                'direct_costs' => $currentRab?->direct_costs ?? [],
            ],
            user: $user
        );
    }

    /**
     * Indexes rate cards by normalized role and role_level.
     */
    private function indexRateCards(Collection $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $role = $this->normalizeRoleAlias((string) ($row->role ?? 'BE'));
            $level = strtoupper((string) ($row->level ?? 'MID'));
            $map["{$role}_{$level}"] = $row;
            if (!isset($map[$role]) || $level === 'MID') {
                $map[$role] = $row;
            }
        }

        return $map;
    }

    private function findRateCard(array $rateCardsByRole, string $role, string $level = 'MID'): ?object
    {
        $normRole = $this->normalizeRoleAlias($role);
        $normLevel = strtoupper($level ?: 'MID');

        return $rateCardsByRole["{$normRole}_{$normLevel}"]
            ?? $rateCardsByRole[$normRole]
            ?? null;
    }

    private function normalizeRoleAlias(string $rawRole): string
    {
        $upper = strtoupper(trim($rawRole));

        return match ($upper) {
            'BACKEND', 'BACKEND DEVELOPER', 'BE' => 'BE',
            'FRONTEND', 'FRONTEND DEVELOPER', 'FE' => 'FE',
            'FULLSTACK', 'FULLSTACK DEVELOPER', 'FS' => 'FULLSTACK',
            'MOBILE', 'MOBILE DEVELOPER', 'ANDROID', 'IOS', 'FLUTTER' => 'MOBILE',
            'UI/UX', 'UIUX', 'UI/UX DESIGNER', 'DESIGNER' => 'UIUX',
            'QA', 'QA ENGINEER', 'TESTER' => 'QA',
            'DEVOPS', 'DEVOPS ENGINEER', 'INFRA' => 'DEVOPS',
            'PM', 'PROJECT MANAGER', 'SCRUM MASTER' => 'PM',
            'SA', 'SYSTEM ANALYST', 'ARCHITECT' => 'SA',
            default => $upper,
        };
    }

    /**
     * Fetches informative capacity from HRD service, with deterministic fallback when offline.
     */
    private function fetchHrdCapacity(): array
    {
        try {
            $hrdUrl = rtrim((string) config('services.hrd.url', env('HRD_SERVICE_URL', 'http://127.0.0.1:8004')), '/');
            $resp = Http::timeout(1)->get("{$hrdUrl}/api/v1/hr/capacity");
            if ($resp->successful() && is_array($resp->json('data'))) {
                $data = $resp->json('data');
                $data['_source'] = 'hrd_service';
                return $data;
            }
        } catch (\Throwable) {
            // Fallback to default informative capacity
        }

        return [
            '_source' => 'informative_default',
            'BE' => ['available_headcount' => 4, 'available_mandays_per_month' => 80],
            'FE' => ['available_headcount' => 4, 'available_mandays_per_month' => 80],
            'FULLSTACK' => ['available_headcount' => 3, 'available_mandays_per_month' => 60],
            'MOBILE' => ['available_headcount' => 2, 'available_mandays_per_month' => 40],
            'UIUX' => ['available_headcount' => 2, 'available_mandays_per_month' => 40],
            'QA' => ['available_headcount' => 2, 'available_mandays_per_month' => 40],
            'DEVOPS' => ['available_headcount' => 1, 'available_mandays_per_month' => 20],
            'PM' => ['available_headcount' => 2, 'available_mandays_per_month' => 40],
            'SA' => ['available_headcount' => 2, 'available_mandays_per_month' => 40],
        ];
    }
}

