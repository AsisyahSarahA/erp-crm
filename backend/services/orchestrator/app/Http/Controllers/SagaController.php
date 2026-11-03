<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Saga;
use App\Services\DealWonSagaService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SagaController extends Controller
{
    private DealWonSagaService $sagaService;

    public function __construct(DealWonSagaService $sagaService)
    {
        $this->sagaService = $sagaService;
    }

    /**
     * List all sagas with step status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Saga::with('steps');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('saga_name')) {
            $query->where('saga_name', $request->saga_name);
        }

        $sagas = $query->latest('started_at')->paginate(min((int) $request->get('per_page', 15), 100));

        return ApiResponse::paginated($sagas);
    }

    /**
     * Show detailed saga state machine and steps (Handover Tracker & Project Transition Hub FR-CRM-22).
     */
    public function show(string $id): JsonResponse
    {
        $saga = Saga::with('steps')->find($id);
        if (!$saga) {
            return ApiResponse::rfcProblem('Saga Not Found', 404, "Saga dengan ID {$id} tidak ditemukan.");
        }

        return ApiResponse::success([
            'saga' => $saga,
            'is_ready_for_kickoff' => $saga->checkKickoffReadiness(),
            'steps_summary' => $saga->steps->map(fn ($s) => [
                'step' => $s->step_order,
                'name' => $s->step_name,
                'target' => $s->target_service,
                'status' => $s->status,
                'attempts' => $s->attempts,
                'idempotency_key' => $s->idempotency_key,
                'error' => $s->error_message,
            ]),
        ]);
    }

    /**
     * Retry a specific failed handover step (Endpoint: Retry Handover per langkah).
     */
    public function retryStep(string $id, int $stepOrder): JsonResponse
    {
        $saga = Saga::with('steps')->find($id);
        if (!$saga) {
            return ApiResponse::rfcProblem('Saga Not Found', 404);
        }

        try {
            $updated = $this->sagaService->retryStep($saga, $stepOrder);
            return ApiResponse::success($updated, "Langkah ke-{$stepOrder} berhasil diproses ulang.");
        } catch (Exception $e) {
            return ApiResponse::rfcProblem('Retry Step Failed', 422, $e->getMessage());
        }
    }

    /**
     * Update Readiness Check values (DP paid, Contract signed, Scope agreed).
     */
    public function updateReadiness(Request $request, string $id): JsonResponse
    {
        $saga = Saga::find($id);
        if (!$saga) {
            return ApiResponse::rfcProblem('Saga Not Found', 404);
        }

        $validated = $request->validate([
            'dp_paid' => 'nullable|boolean',
            'contract_signed' => 'nullable|boolean',
            'scope_locked' => 'nullable|boolean',
        ]);

        $updated = $this->sagaService->updateReadiness($saga, $validated);

        return ApiResponse::success($updated, 'Readiness Check berhasil diperbarui.');
    }

    /**
     * Waive Kickoff Gate (FR-CRM-23) by Authorized Director.
     */
    public function waiveKickoff(Request $request, string $id): JsonResponse
    {
        $saga = Saga::find($id);
        if (!$saga) {
            return ApiResponse::rfcProblem('Saga Not Found', 404);
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $waivedBy = $request->user()?->name ?? 'Direktur Utama';
        $updated = $this->sagaService->waiveKickoffGate($saga, $waivedBy, $request->reason);

        return ApiResponse::success($updated, 'Kickoff Gate berhasil di-waive.');
    }

    /**
     * Compensate (roll back) Saga if deal cancelled before kickoff.
     */
    public function compensate(Request $request, string $id): JsonResponse
    {
        $saga = Saga::find($id);
        if (!$saga) {
            return ApiResponse::rfcProblem('Saga Not Found', 404);
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $updated = $this->sagaService->compensate($saga, $request->reason);

        return ApiResponse::success($updated, 'Saga berhasil dikompensasi / dibatalkan.');
    }
}

