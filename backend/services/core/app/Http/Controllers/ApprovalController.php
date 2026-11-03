<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\AuditLog;
use App\Services\ApprovalEngineService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Support\AssertDifferentUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApprovalController extends Controller
{
    private ApprovalEngineService $approvalService;

    public function __construct(ApprovalEngineService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * List approvals filtered by status or user role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Approval::with(['requester:id,name,email,role', 'steps']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_type')) {
            $query->where('approval_type', $request->approval_type);
        }

        // Filter pending items where the current step matches user or user role
        if ($request->boolean('my_pending') && $user) {
            $query->where('status', 'pending')
                  ->whereHas('steps', function ($q) use ($user) {
                      $q->where('status', 'pending')
                        ->where(function ($sub) use ($user) {
                            $sub->where('approver_id', $user->id)
                                ->orWhere('approver_role', $user->role);
                        });
                  });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $approvals = $query->latest()->paginate($perPage);

        return ApiResponse::paginated($approvals);
    }

    /**
     * Submit a new approval workflow (called by Frontend or other Microservices).
     */
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference_type' => 'required|string',
            'reference_id' => 'required|string',
            'approval_type' => 'required|string',
            'requester_id' => 'nullable|integer',
            'steps' => 'required|array|min:1',
            'steps.*.role' => 'nullable|string',
            'steps.*.user_id' => 'nullable|integer',
            'payload' => 'nullable|array',
            'notes' => 'nullable|string',
            'sla_hours' => 'nullable|integer|min:1',
        ]);

        $requesterId = $validated['requester_id'] ?? $request->user()?->id ?? 1;

        $approval = $this->approvalService->submit(
            referenceType: $validated['reference_type'],
            referenceId: $validated['reference_id'],
            approvalType: $validated['approval_type'],
            requesterId: (int) $requesterId,
            stepsDefinition: $validated['steps'],
            payload: $validated['payload'] ?? [],
            notes: $validated['notes'] ?? null,
            slaHours: (int) ($validated['sla_hours'] ?? 24)
        );

        AuditLog::create([
            'user_id' => (string) $requesterId,
            'service' => 'core-service',
            'action' => 'approval.submitted',
            'entity_type' => 'approval',
            'entity_id' => (string) $approval->id,
            'new_values' => ['type' => $approval->approval_type, 'ref' => "{$approval->reference_type}:{$approval->reference_id}"],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Pengajuan approval {$approval->approval_type} dibuat.",
        ]);

        return ApiResponse::success($approval->load('steps'), 'Permohonan persetujuan berhasil dibuat.', 201);
    }

    /**
     * View detailed approval request.
     */
    public function show(string $id): JsonResponse
    {
        $approval = Approval::with(['requester:id,name,email,role', 'steps.approver:id,name,email,role'])->find($id);
        if (!$approval) {
            return ApiResponse::rfcProblem('Approval Not Found', 404, "Approval dengan ID {$id} tidak ditemukan.");
        }

        return ApiResponse::success($approval);
    }

    /**
     * Decide approval step (Approved / Rejected) with Segregation of Duties check.
     */
    public function decide(Request $request, string $id): JsonResponse
    {
        $approval = Approval::with('steps')->find($id);
        if (!$approval) {
            return ApiResponse::rfcProblem('Approval Not Found', 404, "Approval dengan ID {$id} tidak ditemukan.");
        }

        if ($approval->status !== 'pending') {
            return ApiResponse::rfcProblem('Invalid State', 422, "Approval sudah berstatus '{$approval->status}' dan tidak dapat diproses lagi.");
        }

        $request->validate([
            'action' => 'required|string|in:approved,rejected',
            'comments' => 'nullable|string',
            'approver_id' => 'nullable|integer',
        ]);

        $approverId = (int) ($request->approver_id ?? $request->user()?->id ?? 1);

        try {
            $updated = $this->approvalService->decide(
                approval: $approval,
                approverId: $approverId,
                action: $request->action,
                comments: $request->comments
            );

            AuditLog::create([
                'user_id' => (string) $approverId,
                'service' => 'core-service',
                'action' => "approval.{$request->action}",
                'entity_type' => 'approval',
                'entity_id' => (string) $approval->id,
                'new_values' => ['decision' => $request->action, 'comments' => $request->comments],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'notes' => "Approval {$approval->id} ({$approval->approval_type}) diputuskan: {$request->action}.",
            ]);

            return ApiResponse::success($updated, "Keputusan persetujuan berhasil dicatat ({$request->action}).");
        } catch (\InvalidArgumentException $e) {
            // SoD violation
            return ApiResponse::rfcProblem(
                title: 'Segregation of Duties Violation',
                status: 403,
                detail: $e->getMessage(),
                type: 'https://astacode.com/errors/sod-violation'
            );
        } catch (Exception $e) {
            return ApiResponse::rfcProblem('Decision Error', 422, $e->getMessage());
        }
    }
}

