<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiRun;
use App\Services\AiGatewayService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AiRunController extends Controller
{
    private AiGatewayService $gateway;

    public function __construct(AiGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Executes AI generation through standard Gateway pipeline.
     * POST /internal/ai/run
     */
    public function run(Request $request): JsonResponse
    {
        $request->validate([
            'module' => 'required|string',
            'purpose' => 'required|string',
            'payload' => 'required|array',
            'prompt_slug' => 'nullable|string',
            'options' => 'nullable|array',
        ]);

        $aiRun = $this->gateway->run(
            module: $request->module,
            purpose: $request->purpose,
            payload: $request->payload,
            promptSlug: $request->prompt_slug,
            options: $request->options ?? []
        );

        if ($aiRun->status === 'BLOCKED_BY_GUARDRAIL') {
            return ApiResponse::rfcProblem(
                title: 'AI Guardrail Violation',
                status: 400,
                detail: $aiRun->error_message ?? 'Permintaan diblokir oleh sistem keamanan guardrail.',
                type: 'https://astacode.com/errors/ai-guardrail-blocked'
            );
        }

        if ($aiRun->status === 'BUDGET_EXCEEDED') {
            return ApiResponse::rfcProblem(
                title: 'AI Monthly Budget Exceeded',
                status: 429,
                detail: $aiRun->error_message ?? 'Batas anggaran AI bulanan untuk modul ini telah tercapai.',
                type: 'https://astacode.com/errors/ai-budget-exceeded'
            );
        }

        return ApiResponse::success($aiRun, 'AI generation completed successfully.');
    }

    /**
     * List historical AI runs with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AiRun::query();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $runs = $query->latest('created_at')->paginate(25);
        return ApiResponse::paginated($runs);
    }

    /**
     * Show single AI run details.
     */
    public function show(string $id): JsonResponse
    {
        $run = AiRun::find($id);
        if (!$run) {
            return ApiResponse::rfcProblem('AI Run Not Found', 404);
        }

        return ApiResponse::success($run);
    }

    /**
     * Record human decision on AI generation (Human-in-the-loop feedback).
     */
    public function recordDecision(Request $request, string $id): JsonResponse
    {
        $run = AiRun::find($id);
        if (!$run) {
            return ApiResponse::rfcProblem('AI Run Not Found', 404);
        }

        $request->validate([
            'human_decision' => 'required|string|in:ACCEPTED,REJECTED,EDITED',
            'actual_mandays' => 'nullable|numeric',
            'decided_by' => 'nullable|string',
        ]);

        $run->update([
            'human_decision' => $request->human_decision,
            'actual_mandays' => $request->actual_mandays,
            'decided_by' => $request->decided_by ?? $request->user()?->name ?? 'User',
        ]);

        return ApiResponse::success($run, 'Keputusan manusia berhasil dicatat.');
    }
}

