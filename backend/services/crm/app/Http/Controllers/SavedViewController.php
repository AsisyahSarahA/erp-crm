<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SavedView;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavedViewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = $userContext['id'] ?? 1;
        $module = (string) $request->query('module', 'crm_leads');

        $views = SavedView::where('user_id', $userId)
            ->where('module', $module)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($views);
    }

    public function store(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = $userContext['id'] ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:128'],
            'module' => ['nullable', 'string', 'max:64'],
            'filters' => ['nullable', 'array'],
            'sorts' => ['nullable', 'array'],
            'columns' => ['nullable', 'array'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data saved view tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $module = $data['module'] ?? 'crm_leads';

        if (!empty($data['is_default'])) {
            SavedView::where('user_id', $userId)->where('module', $module)->update(['is_default' => false]);
        }

        $view = SavedView::create([
            'user_id' => $userId,
            'module' => $module,
            'name' => $data['name'],
            'filters' => $data['filters'] ?? null,
            'sorts' => $data['sorts'] ?? null,
            'columns' => $data['columns'] ?? null,
            'is_default' => (bool) ($data['is_default'] ?? false),
        ]);

        return ApiResponse::success($view, ['message' => 'Saved view berhasil dibuat.'], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $userId = $userContext['id'] ?? 1;

        $view = SavedView::where('id', $id)->where('user_id', $userId)->first();
        if (!$view) {
            return ApiResponse::rfcProblem('Not Found', 404, 'Saved view tidak ditemukan.');
        }

        $view->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }
}

