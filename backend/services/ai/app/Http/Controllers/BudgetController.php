<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiBudget;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BudgetController extends Controller
{
    public function index(): JsonResponse
    {
        $budgets = AiBudget::all();
        return ApiResponse::success($budgets);
    }

    public function update(Request $request, string $module): JsonResponse
    {
        $budget = AiBudget::firstOrCreate(['module' => $module]);

        $validated = $request->validate([
            'monthly_budget_usd' => 'sometimes|numeric|min:0',
            'is_blocked' => 'sometimes|boolean',
        ]);

        $budget->update($validated);
        return ApiResponse::success($budget, "Anggaran AI untuk modul '{$module}' berhasil diperbarui.");
    }
}

