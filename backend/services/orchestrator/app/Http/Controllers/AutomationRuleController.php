<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Services\AutomationEngineService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AutomationRuleController extends Controller
{
    private AutomationEngineService $engine;

    public function __construct(AutomationEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index(Request $request): JsonResponse
    {
        $query = AutomationRule::query();

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $rules = $query->orderBy('priority', 'asc')->paginate(25);
        return ApiResponse::paginated($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:event,idle,overdue,cron',
            'trigger_event' => 'nullable|string|max:100',
            'trigger_config' => 'nullable|array',
            'conditions' => 'required|array',
            'actions' => 'required|array|min:1',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer',
        ]);

        $rule = AutomationRule::create($validated);
        return ApiResponse::success($rule, 'Aturan otomasi berhasil dibuat.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $rule = AutomationRule::with(['runs' => fn ($q) => $q->take(10)])->find($id);
        if (!$rule) {
            return ApiResponse::rfcProblem('Rule Not Found', 404);
        }

        return ApiResponse::success($rule);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rule = AutomationRule::find($id);
        if (!$rule) {
            return ApiResponse::rfcProblem('Rule Not Found', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'trigger_type' => 'sometimes|string|in:event,idle,overdue,cron',
            'trigger_event' => 'nullable|string|max:100',
            'trigger_config' => 'nullable|array',
            'conditions' => 'sometimes|array',
            'actions' => 'sometimes|array|min:1',
            'is_active' => 'sometimes|boolean',
            'priority' => 'nullable|integer',
        ]);

        $rule->update($validated);
        return ApiResponse::success($rule, 'Aturan otomasi berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $rule = AutomationRule::find($id);
        if (!$rule) {
            return ApiResponse::rfcProblem('Rule Not Found', 404);
        }

        $rule->delete();
        return ApiResponse::success(null, 'Aturan otomasi berhasil dihapus.');
    }

    public function runs(int $id): JsonResponse
    {
        $rule = AutomationRule::find($id);
        if (!$rule) {
            return ApiResponse::rfcProblem('Rule Not Found', 404);
        }

        $runs = $rule->runs()->paginate(25);
        return ApiResponse::paginated($runs);
    }

    public function triggerManual(Request $request, int $id): JsonResponse
    {
        $rule = AutomationRule::find($id);
        if (!$rule) {
            return ApiResponse::rfcProblem('Rule Not Found', 404);
        }

        $payload = $request->input('payload', []);
        $result = $this->engine->executeRule($rule, 'manual_test', $payload);

        return ApiResponse::success($result, 'Pengujian eksekusi aturan otomasi selesai.');
    }
}

