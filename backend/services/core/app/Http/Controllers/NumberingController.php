<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\NumberingSequence;
use App\Services\NumberingService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NumberingController extends Controller
{
    private NumberingService $numberingService;

    public function __construct(NumberingService $numberingService)
    {
        $this->numberingService = $numberingService;
    }

    /**
     * Atomically generate next code for a document type.
     * Internal endpoint called by other microservices.
     */
    public function next(Request $request): JsonResponse
    {
        $request->validate([
            'code_type' => 'required|string',
            'subtype' => 'nullable|string',
            'revision' => 'nullable|string',
        ]);

        $code = $this->numberingService->next(
            $request->code_type,
            [
                'subtype' => $request->subtype ?? 'GEN',
                'revision' => $request->revision ?? '0',
            ]
        );

        return ApiResponse::success([
            'code_type' => $request->code_type,
            'number' => $code,
        ]);
    }

    /**
     * List all numbering sequence patterns.
     */
    public function index(): JsonResponse
    {
        $sequences = NumberingSequence::all();
        return ApiResponse::success($sequences);
    }

    /**
     * Update pattern format for a sequence.
     */
    public function update(Request $request, string $codeType): JsonResponse
    {
        $sequence = NumberingSequence::where('code_type', $codeType)->first();
        if (!$sequence) {
            return ApiResponse::rfcProblem('Sequence Not Found', 404, "Pola penomoran '{$codeType}' tidak ditemukan.");
        }

        $request->validate([
            'format_pattern' => 'required|string',
            'reset_frequency' => 'required|string|in:monthly,yearly,never',
            'padding' => 'required|integer|min:3|max:8',
        ]);

        $old = $sequence->toArray();
        $sequence->update($request->only(['format_pattern', 'reset_frequency', 'padding']));

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'numbering.pattern_updated',
            'entity_type' => 'numbering_sequence',
            'entity_id' => (string) $sequence->id,
            'old_values' => $old,
            'new_values' => $sequence->toArray(),
            'notes' => "Pola penomoran {$codeType} diperbarui menjadi {$sequence->format_pattern}.",
        ]);

        return ApiResponse::success($sequence, 'Pola penomoran berhasil diperbarui.');
    }
}

