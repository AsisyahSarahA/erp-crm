<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiRun;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class MetricController extends Controller
{
    /**
     * Observability metrics: accuracy MAPE, human acceptance rate, and spend.
     */
    public function metrics(): JsonResponse
    {
        $totalRuns = AiRun::count();
        $successfulRuns = AiRun::where('status', 'SUCCESS')->count();
        $blockedRuns = AiRun::where('status', 'BLOCKED_BY_GUARDRAIL')->count();

        // Acceptance Rate
        $accepted = AiRun::where('human_decision', 'ACCEPTED')->count();
        $rejected = AiRun::where('human_decision', 'REJECTED')->count();
        $decidedTotal = $accepted + $rejected;
        $acceptanceRate = $decidedTotal > 0 ? round(($accepted / $decidedTotal) * 100, 2) : 100.0;

        // MAPE (Mean Absolute Percentage Error) for Estimation vs Actual
        $feedbackRuns = AiRun::whereNotNull('actual_mandays')
            ->whereNotNull('estimated_mandays')
            ->where('actual_mandays', '>', 0)
            ->get();

        $mape = 0.0;
        if ($feedbackRuns->isNotEmpty()) {
            $sumError = 0.0;
            foreach ($feedbackRuns as $run) {
                $error = abs($run->actual_mandays - $run->estimated_mandays) / $run->actual_mandays;
                $sumError += $error;
            }
            $mape = round(($sumError / $feedbackRuns->count()) * 100, 2);
        }

        $totalTokensIn = (int) AiRun::sum('tokens_in');
        $totalTokensOut = (int) AiRun::sum('tokens_out');
        $totalCost = (float) AiRun::sum('cost');

        return ApiResponse::success([
            'total_runs' => $totalRuns,
            'success_rate_pct' => $totalRuns > 0 ? round(($successfulRuns / $totalRuns) * 100, 2) : 100.0,
            'blocked_by_guardrail' => $blockedRuns,
            'human_acceptance_rate_pct' => $acceptanceRate,
            'estimation_accuracy_mape_pct' => $mape,
            'total_tokens_consumed' => $totalTokensIn + $totalTokensOut,
            'total_spend_usd' => round($totalCost, 4),
        ]);
    }
}

