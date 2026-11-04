<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\TopMilestone;
use App\Models\TopScheme;
use App\Services\TopService;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TopController extends Controller
{
    public function __construct(
        private readonly TopService $topService
    ) {
    }

    /**
     * GET /api/v1/crm/leads/{id}/top
     * Retrieves the TOP scheme, milestones, balance status, and preset catalog for a Lead.
     */
    public function show(int $id): JsonResponse
    {
        $lead = Lead::with(['customer', 'activeRab', 'activeQuotation'])->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $scheme = $this->topService->getForLead($lead);
        if (!$scheme) {
            return ApiResponse::rfcProblem('Not Found', 404, "Belum ada skema TOP untuk Lead #{$id}.");
        }

        return ApiResponse::success(
            $this->formatScheme($lead, $scheme),
            'Data skema TOP berhasil diambil.'
        );
    }

    /**
     * PUT /api/v1/crm/leads/{id}/top
     * Upserts TOP scheme and milestones with preset support, percent<->nominal sync,
     * rounding absorption on final term, balance check, and Finance lock protection (BR-CRM-18).
     */
    public function upsert(Request $request, int $id): JsonResponse
    {
        $lead = Lead::with(['customer', 'activeRab', 'activeQuotation'])->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);

        try {
            $scheme = $this->topService->saveTopScheme($lead, $request->all(), $user);

            return ApiResponse::success(
                $this->formatScheme($lead, $scheme),
                $scheme->is_balanced
                    ? 'Skema TOP berhasil disimpan (Balance 100%).'
                    : 'Draf skema TOP disimpan (Belum balance 100%: simpan final & ekspor dokumen dikunci).'
            );
        } catch (DomainException $e) {
            $existing = $this->topService->getForLead($lead);

            return ApiResponse::rfcProblem(
                title: 'TOP Locked by Finance',
                status: 422,
                detail: $e->getMessage(),
                invalidParams: ['top' => [$e->getMessage()]],
                extra: [
                    'code' => 'TOP_LOCKED_BY_FINANCE',
                    'approval_required' => true,
                    'unlock_approval_status' => $existing?->unlock_approval_status ?? 'PENDING',
                    'locked_by_finance_at' => $existing?->locked_by_finance_at?->toIso8601String(),
                ]
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem(
                title: 'Validation Error',
                status: 422,
                detail: $e->getMessage(),
                invalidParams: ['top' => [$e->getMessage()]]
            );
        }
    }

    /**
     * POST /api/v1/crm/leads/{id}/top/lock
     * POST /api/v1/crm/top/lock
     * Finance AR locks the TOP scheme (BR-CRM-18) and checks Customer NPWP verification.
     */
    public function lock(Request $request, ?int $id = null): JsonResponse
    {
        $leadId = $id ?? (int) $request->input('lead_id', 0);
        $lead = Lead::with('customer')->find($leadId);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$leadId} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);
        $notes = $request->filled('notes') ? (string) $request->input('notes') : null;

        try {
            $result = $this->topService->lockByFinance($lead, $user, $notes);

            $formatted = $this->formatScheme($lead, $result['scheme']);
            $formatted['npwp_verified'] = $result['npwp_verified'];
            $formatted['dp_invoice_ready'] = $result['dp_invoice_ready'];
            $formatted['npwp_warning'] = $result['npwp_warning'];

            return ApiResponse::success(
                $formatted,
                'Skema TOP berhasil dikunci oleh Finance AR.'
            );
        } catch (DomainException $e) {
            return ApiResponse::rfcProblem('Forbidden', 403, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    /**
     * POST /api/v1/crm/leads/{id}/top/approve-change
     * POST /api/v1/crm/top/approve-change
     * Finance approves or rejects a modification to a Finance-locked TOP scheme (BR-CRM-18).
     */
    public function approveChange(Request $request, ?int $id = null): JsonResponse
    {
        $leadId = $id ?? (int) $request->input('lead_id', 0);
        $lead = Lead::with('customer')->find($leadId);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$leadId} tidak ditemukan.");
        }

        $user = UserContext::resolve($request);
        $action = (string) $request->input('action', 'APPROVED');
        $notes = $request->filled('notes') ? (string) $request->input('notes') : null;

        try {
            $res = $this->topService->decideLockedChangeApproval($lead, $action, $user, $notes);

            return ApiResponse::success([
                'top' => $this->formatScheme($lead, $res['scheme']),
                'approval' => $res['approval'],
            ], 'Keputusan Finance atas perubahan TOP berhasil disimpan.');
        } catch (DomainException $e) {
            return ApiResponse::rfcProblem('Forbidden', 403, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    /**
     * GET /api/v1/crm/leads/{id}/top/receivable-schedule
     * Generates the Receivable Schedule structure for Finance upon Deal Won.
     */
    public function receivableSchedule(int $id): JsonResponse
    {
        $lead = Lead::with('customer')->find($id);
        if (!$lead) {
            return ApiResponse::rfcProblem('Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        try {
            $schedule = $this->topService->buildReceivableSchedule($lead);

            return ApiResponse::success($schedule, 'Jadwal piutang (Receivable Schedule) berhasil disusun.');
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Validation Error', 422, $e->getMessage());
        }
    }

    private function formatScheme(Lead $lead, TopScheme $scheme): array
    {
        $milestones = $scheme->relationLoaded('milestones')
            ? $scheme->milestones
            : $scheme->milestones()->get();

        $customer = $lead->customer;
        $npwpVerified = $customer !== null && $customer->npwp_verified_at !== null;
        $isBalanced = (bool) $scheme->is_balanced;

        return [
            'id' => $scheme->id,
            'lead_id' => $scheme->lead_id,
            'quotation_id' => $scheme->quotation_id,
            'rab_version_id' => $scheme->rab_version_id,
            'preset_code' => $scheme->preset_code,
            'preset_name' => $scheme->preset_name,
            'reference_dpp' => (float) $scheme->reference_dpp,
            'reference_total' => (float) $scheme->reference_total,
            'vat_rate' => (float) $scheme->vat_rate,
            'pph23_rate' => (float) $scheme->pph23_rate,
            'is_pkp' => (bool) $scheme->is_pkp,
            'is_pph23_withholder' => (bool) $scheme->is_pph23_withholder,
            'total_percent' => (float) $scheme->total_percent,
            'total_percentage' => (float) $scheme->total_percentage,
            'total_dpp' => (float) $scheme->total_dpp,
            'total_ppn' => (float) $scheme->total_ppn,
            'total_pph23' => (float) $scheme->total_pph23,
            'total_amount' => (float) $scheme->total_amount,
            'total_invoice_idr' => round((float) $scheme->total_dpp + (float) $scheme->total_ppn, 2),
            'total_net_cash' => (float) $scheme->total_net_cash,
            'total_terbilang' => $scheme->total_terbilang,
            'is_balanced' => $isBalanced,
            'can_finalize' => $isBalanced,
            'can_export_document' => $isBalanced,
            'remaining_percent' => (float) $scheme->remaining_percent,
            'remaining_amount_idr' => (float) $scheme->remaining_amount_idr,
            'balance_warning' => $scheme->balance_warning,
            'is_locked_by_finance' => $scheme->locked_by_finance_at !== null,
            'locked_by_finance_at' => $scheme->locked_by_finance_at?->toIso8601String(),
            'locked_by_finance_by' => $scheme->locked_by_finance_by,
            'finance_lock_notes' => $scheme->finance_lock_notes,
            'unlock_approval_status' => $scheme->unlock_approval_status ?? 'NONE',
            'npwp_verified' => $npwpVerified,
            'dp_invoice_ready' => $isBalanced && $npwpVerified,
            'milestones' => $milestones->map(fn (TopMilestone $m) => [
                'id' => $m->id,
                'term_number' => (int) ($m->term_number ?: $m->termin_no),
                'termin_no' => (int) ($m->termin_no ?: $m->term_number),
                'name' => $m->name ?: $m->milestone_name,
                'milestone_name' => $m->milestone_name ?: $m->name,
                'trigger_type' => $m->trigger_type,
                'trigger_event' => $m->trigger_event,
                'percentage' => (float) ($m->percentage ?: $m->percent),
                'percent' => (float) ($m->percent ?: $m->percentage),
                'dpp_amount' => (float) $m->dpp_amount,
                'ppn_amount' => (float) $m->ppn_amount,
                'pph23_amount' => (float) $m->pph23_amount,
                'invoice_total_idr' => round((float) $m->dpp_amount + (float) $m->ppn_amount, 2),
                'amount_idr' => (float) ($m->amount_idr ?: $m->amount),
                'amount' => (float) ($m->amount ?: $m->amount_idr),
                'net_cash_idr' => (float) $m->net_cash_idr,
                'terbilang_text' => $m->terbilang_text,
                'description' => $m->description,
                'due_days_offset' => (int) ($m->due_days_offset ?: $m->due_days ?: 14),
                'due_days' => (int) ($m->due_days ?: $m->due_days_offset ?: 14),
                'due_date' => $m->due_date?->toDateString(),
                'project_milestone_ref' => $m->project_milestone_ref,
                'is_retention' => (bool) $m->is_retention,
            ])->values()->all(),
        ];
    }
}

