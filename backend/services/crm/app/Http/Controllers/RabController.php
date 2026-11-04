<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RabResource;
use App\Models\Lead;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Services\OptimisticLockException;
use App\Services\RabEngineService;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RabController extends Controller
{
    public function __construct(
        private readonly RabEngineService $rabEngine
    ) {
    }

    /**
     * GET /api/v1/crm/leads/{id}/rab
     * Retrieves active RAB (or specific ?version=N) with Dual-View field filtering (BR-CRM-07, AC-CRM-07).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if ($request->filled('version')) {
            $rab = RabVersion::with('items')
                ->where('lead_id', $lead->id)
                ->where('version', (int) $request->query('version'))
                ->first();
        } else {
            $rab = $this->rabEngine->getActiveOrLatestRab($lead);
        }

        if (!$rab) {
            return ApiResponse::rfcProblem('Not Found', 404, "Belum ada RAB untuk Lead #{$id}.");
        }

        $resourceData = (new RabResource($rab))->toArray($request);

        return ApiResponse::success($resourceData, 'Data RAB berhasil diambil.');
    }

    /**
     * PUT /api/v1/crm/leads/{id}/rab
     * Bulk upsert RAB items & parameters with optimistic locking (`version`),
     * authoritative calculation, AI->MANUAL badge transition (AC-CRM-04), and approval evaluation (FR-CRM-17).
     */
    public function upsert(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);
        $isAutosave = $request->boolean('is_autosave', false);

        try {
            $savedRab = $this->rabEngine->saveRab(
                lead: $lead,
                payload: $request->all(),
                user: $user,
                isAutosave: $isAutosave
            );

            $resourceData = (new RabResource($savedRab))->toArray($request);

            return ApiResponse::success(
                $resourceData,
                $isAutosave ? 'Draf RAB tersimpan otomatis.' : "RAB versi v{$savedRab->version} berhasil disimpan."
            );
        } catch (OptimisticLockException $e) {
            return response()->json([
                'type' => 'https://astacode.id/errors/optimistic-lock-conflict',
                'title' => 'Optimistic Lock Conflict',
                'status' => 409,
                'detail' => $e->getMessage(),
                'expected_version' => $e->expectedVersion,
                'actual_version' => $e->actualVersion,
            ], 409, ['Content-Type' => 'application/problem+json']);
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem(
                title: 'Validation Error',
                status: 422,
                detail: $e->getMessage(),
                invalidParams: ['rab' => [$e->getMessage()]]
            );
        }
    }

    /**
     * PATCH /api/v1/crm/leads/{id}/rab/autosave
     * Auto-save draft in-place without bumping version number.
     */
    public function autosave(Request $request, int $id): JsonResponse
    {
        $request->merge(['is_autosave' => true]);

        return $this->upsert($request, $id);
    }

    /**
     * POST /api/v1/crm/rab/recalculate
     * POST /api/v1/crm/leads/{id}/rab/recalculate
     * Authoritative stateless recalculation endpoint.
     */
    public function recalculate(Request $request, ?int $id = null): JsonResponse
    {
        $lead = null;
        $existingRab = null;

        $leadId = $id ?? ($request->filled('lead_id') ? (int) $request->input('lead_id') : null);
        if ($leadId !== null) {
            $lead = Lead::with('customer')->find($leadId);
            if ($lead) {
                $existingRab = $this->rabEngine->getActiveOrLatestRab($lead);
            }
        }

        try {
            $calc = $this->rabEngine->calculateSummary(
                payload: $request->all(),
                lead: $lead,
                existingRab: $existingRab,
                isAutosave: false
            );

            $filtered = RabResource::formatCalculationArray($calc, $request);

            return ApiResponse::success($filtered, 'Kalkulasi RAB berhasil dihitung.');
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem(
                title: 'Validation Error',
                status: 422,
                detail: $e->getMessage(),
                invalidParams: ['rab' => [$e->getMessage()]]
            );
        }
    }

    /**
     * GET /api/v1/crm/leads/{id}/rab/versions
     * Lists version history of RAB for a Lead.
     */
    public function versions(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $versions = RabVersion::with('items')
            ->where('lead_id', $lead->id)
            ->orderByDesc('version')
            ->get()
            ->map(fn (RabVersion $v) => (new RabResource($v))->toArray($request))
            ->values()
            ->all();

        return ApiResponse::success($versions, 'Riwayat versi RAB berhasil diambil.');
    }

    /**
     * GET /api/v1/crm/leads/{id}/rab/diff?from_version=1&to_version=2
     * Compares two RAB versions (qty, unit_price, subtotal, and totals).
     */
    public function diff(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $fromVersion = (int) $request->query('from_version', 1);
        $toVersion = (int) $request->query('to_version', 2);

        try {
            $diffData = $this->rabEngine->compareVersions($lead, $fromVersion, $toVersion);

            return ApiResponse::success($diffData, "Perbandingan RAB v{$fromVersion} dan v{$toVersion} berhasil dihitung.");
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    /**
     * POST /api/v1/crm/leads/{id}/rab/import-catalog
     * Imports standard modules from `crm_module_catalog` with `origin = 'TEMPLATE'`.
     */
    public function importCatalog(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);
        $moduleIds = array_map('intval', (array) $request->input('module_ids', []));
        $category = $request->filled('category') ? (string) $request->input('category') : null;
        $merge = $request->boolean('merge', true);

        try {
            $rab = $this->rabEngine->importFromCatalog($lead, $moduleIds, $category, $merge, $user);

            return ApiResponse::success(
                (new RabResource($rab))->toArray($request),
                'Modul katalog berhasil diimpor ke RAB.',
                201
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    /**
     * POST /api/v1/crm/leads/{id}/rab/import-excel
     * Imports RAB items from uploaded CSV/Excel file or structured rows array.
     */
    public function importExcel(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);
        $parsedItems = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $content = (string) file_get_contents($file->getRealPath());
            $lines = array_values(array_filter(preg_split('/\r\n|\r|\n/', trim($content)) ?: []));

            foreach ($lines as $idx => $line) {
                $cols = str_getcsv($line);
                if ($idx === 0 && isset($cols[0]) && stripos((string) $cols[0], 'kategori') !== false) {
                    continue; // Skip header row
                }
                if (count($cols) < 2) {
                    continue;
                }
                $parsedItems[] = [
                    'category' => trim((string) ($cols[0] ?? 'Master Data')),
                    'name' => trim((string) ($cols[1] ?? 'Modul Impor')),
                    'description' => trim((string) ($cols[2] ?? '')),
                    'role' => strtoupper(trim((string) ($cols[3] ?? 'BE'))),
                    'qty' => (float) ($cols[4] ?? 1),
                    'unit' => trim((string) ($cols[5] ?? 'Mandays')),
                    'unit_price' => isset($cols[6]) && $cols[6] !== '' ? (float) $cols[6] : null,
                    'origin' => 'MANUAL',
                ];
            }
        } elseif (is_array($request->input('rows'))) {
            foreach ($request->input('rows') as $row) {
                $parsedItems[] = [
                    'category' => (string) ($row['category'] ?? 'Master Data'),
                    'name' => (string) ($row['name'] ?? 'Modul Impor'),
                    'description' => (string) ($row['description'] ?? ''),
                    'role' => strtoupper((string) ($row['role'] ?? 'BE')),
                    'qty' => (float) ($row['qty'] ?? 1),
                    'unit' => (string) ($row['unit'] ?? 'Mandays'),
                    'unit_price' => isset($row['unit_price']) ? (float) $row['unit_price'] : null,
                    'origin' => 'MANUAL',
                ];
            }
        }

        if (empty($parsedItems)) {
            return ApiResponse::rfcProblem('Validation Error', 422, 'File atau baris impor RAB kosong atau tidak valid.');
        }

        $currentRab = $this->rabEngine->getActiveOrLatestRab($lead);
        $merge = $request->boolean('merge', false);
        $existingItems = [];

        if ($merge && $currentRab !== null) {
            foreach ($currentRab->items as $it) {
                $existingItems[] = [
                    'id' => $it->id,
                    'category' => $it->category,
                    'layer' => $it->layer,
                    'name' => $it->name,
                    'description' => $it->description,
                    'role' => $it->role,
                    'qty' => (float) $it->qty,
                    'unit' => $it->unit,
                    'unit_price' => (float) $it->unit_price,
                    'cost_rate' => (float) $it->cost_rate,
                    'origin' => $it->origin,
                    'is_optional' => (bool) $it->is_optional,
                    'is_recurring' => (bool) $it->is_recurring,
                    'is_bonus' => (bool) $it->is_bonus,
                ];
            }
        }

        try {
            $savedRab = $this->rabEngine->saveRab(
                lead: $lead,
                payload: [
                    'items' => array_merge($existingItems, $parsedItems),
                    'contingency_pct' => $currentRab?->contingency_pct ?? 10.0,
                    'contingency_reason' => $currentRab?->contingency_reason,
                    'discount_type' => $currentRab?->discount_type ?? 'PERCENT',
                    'discount_value' => $currentRab?->discount_value ?? 0.0,
                    'is_pkp' => $currentRab?->is_pkp ?? true,
                ],
                user: $user
            );

            return ApiResponse::success(
                (new RabResource($savedRab))->toArray($request),
                'Item RAB berhasil diimpor dari Excel/CSV.',
                201
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    /**
     * GET /api/v1/crm/leads/{id}/rab/export-excel
     * Exports RAB to CSV/Excel stream (respecting Dual-View margin permissions).
     */
    public function exportExcel(Request $request, int $id): StreamedResponse|JsonResponse
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $rab = $this->rabEngine->getActiveOrLatestRab($lead);
        if (!$rab) {
            return ApiResponse::rfcProblem('Not Found', 404, "Belum ada RAB untuk diekspor pada Lead #{$id}.");
        }

        $canViewMargin = UserContext::canViewMargin($request);
        $fileName = sprintf('RAB_%s_v%d.csv', $lead->code ?: "LEAD_{$lead->id}", $rab->version);

        return response()->streamDownload(function () use ($rab, $canViewMargin) {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }

            $headers = ['No', 'Kategori', 'Modul/Item', 'Deskripsi', 'Role', 'Qty', 'Satuan', 'Harga Satuan', 'Sub Total', 'Sumber', 'Optional', 'Gratis/Bonus', 'Recurring'];
            if ($canViewMargin) {
                $headers[] = 'Cost Rate Internal';
                $headers[] = 'HPP Internal';
            }
            fputcsv($out, $headers);

            foreach ($rab->items as $idx => $item) {
                $row = [
                    $idx + 1,
                    $item->category,
                    $item->name,
                    $item->description,
                    $item->role,
                    $item->qty,
                    $item->unit,
                    $item->unit_price,
                    $item->subtotal,
                    $item->origin,
                    $item->is_optional ? 'Ya' : 'Tidak',
                    $item->is_bonus ? 'Ya' : 'Tidak',
                    $item->is_recurring ? 'Ya' : 'Tidak',
                ];
                if ($canViewMargin) {
                    $row[] = $item->cost_rate;
                    $row[] = $item->hpp;
                }
                fputcsv($out, $row);
            }

            fputcsv($out, []);
            fputcsv($out, ['Subtotal Pekerjaan', $rab->subtotal]);
            fputcsv($out, ["Kontingensi ({$rab->contingency_pct}%)", $rab->contingency_amount]);
            fputcsv($out, ["Diskon ({$rab->discount_pct}%)", $rab->discount_amount]);
            fputcsv($out, ['DPP', $rab->dpp]);
            fputcsv($out, ["PPN ({$rab->vat_rate}%)", $rab->vat_amount]);
            fputcsv($out, ['TOTAL INVESTASI', $rab->total]);
            fputcsv($out, ['Terbilang', $rab->total_terbilang]);

            if ($canViewMargin) {
                fputcsv($out, ['HPP Internal', $rab->hpp_internal]);
                fputcsv($out, ['Gross Margin (%)', $rab->margin_pct . '%']);
                fputcsv($out, ['Status Margin', $rab->margin_status]);
            }

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * POST /api/v1/crm/leads/{id}/rab/items/{itemId}/duplicate
     * Duplicates a row in the active RAB and recalculates.
     */
    public function duplicateItem(Request $request, int $id, int $itemId): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $rab = $this->rabEngine->getActiveOrLatestRab($lead);
        if (!$rab) {
            return ApiResponse::rfcProblem('Not Found', 404, "RAB tidak ditemukan pada Lead #{$id}.");
        }

        $targetItem = $rab->items->firstWhere('id', $itemId);
        if (!$targetItem) {
            return ApiResponse::rfcProblem('Not Found', 404, "Item RAB #{$itemId} tidak ditemukan.");
        }

        $itemsPayload = [];
        foreach ($rab->items as $it) {
            $itemsPayload[] = $it->toArray();
            if ((int) $it->id === $itemId) {
                $clone = $it->toArray();
                unset($clone['id']);
                $clone['name'] = $it->name . ' (Copy)';
                $clone['origin'] = 'MANUAL';
                $itemsPayload[] = $clone;
            }
        }

        $updatedRab = $this->rabEngine->saveRab(
            lead: $lead,
            payload: [
                'items' => $itemsPayload,
                'contingency_pct' => $rab->contingency_pct,
                'contingency_reason' => $rab->contingency_reason,
                'discount_type' => $rab->discount_type,
                'discount_value' => $rab->discount_value,
                'is_pkp' => $rab->is_pkp,
                'direct_costs' => $rab->direct_costs,
            ],
            user: UserContext::resolve($request)
        );

        return ApiResponse::success(
            (new RabResource($updatedRab))->toArray($request),
            'Baris item RAB berhasil diduplikasi.',
            201
        );
    }

    /**
     * POST /api/v1/crm/leads/{id}/rab/reorder
     * Reorders items in the active RAB according to `item_ids` array.
     */
    public function reorderItems(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $rab = $this->rabEngine->getActiveOrLatestRab($lead);
        if (!$rab) {
            return ApiResponse::rfcProblem('Not Found', 404, "RAB tidak ditemukan pada Lead #{$id}.");
        }

        $orderedIds = array_map('intval', (array) $request->input('item_ids', []));
        if (empty($orderedIds)) {
            return ApiResponse::rfcProblem('Validation Error', 422, 'Daftar item_ids wajib diisi untuk pengurutan ulang.');
        }

        foreach ($orderedIds as $index => $itemId) {
            RabItem::where('rab_version_id', $rab->id)
                ->where('id', $itemId)
                ->update(['sort_order' => $index + 1]);
        }

        return ApiResponse::success(
            (new RabResource($rab->fresh(['items'])))->toArray($request),
            'Urutan baris RAB berhasil diperbarui.'
        );
    }

    /**
     * POST /api/v1/crm/leads/{id}/rab/approve
     * Approves or rejects a RAB version requiring discount/margin/high-value approval (FR-CRM-17).
     */
    public function decideApproval(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $rab = $request->filled('rab_version_id')
            ? RabVersion::with('items')->where('lead_id', $lead->id)->find((int) $request->input('rab_version_id'))
            : $this->rabEngine->getActiveOrLatestRab($lead);

        if (!$rab) {
            return ApiResponse::rfcProblem('Not Found', 404, "RAB tidak ditemukan untuk Lead #{$id}.");
        }

        $action = (string) $request->input('action', 'APPROVED');
        $notes = $request->filled('notes') ? (string) $request->input('notes') : null;
        $user = UserContext::resolve($request);

        try {
            $result = $this->rabEngine->decideApproval($lead, $rab, $action, $user, $notes);

            return ApiResponse::success([
                'rab' => (new RabResource($result['rab']))->toArray($request),
                'approval' => $result['approval'],
            ], "Keputusan persetujuan RAB ({$result['approval']->status}) berhasil disimpan.");
        } catch (\DomainException $e) {
            return ApiResponse::rfcProblem('Forbidden / Policy Violation', 403, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }
}
