<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Prompt;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PromptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Prompt::query();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prompts = $query->latest('updated_at')->paginate(25);
        return ApiResponse::paginated($prompts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:100',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'module' => 'required|string|max:50',
            'system_prompt' => 'required|string',
            'user_template' => 'required|string',
            'schema_name' => 'nullable|string',
            'model_preference' => 'nullable|string',
            'temperature' => 'nullable|numeric',
        ]);

        $latest = Prompt::where('slug', $validated['slug'])->orderBy('version', 'desc')->first();
        $newVersion = $latest ? ($latest->version + 1) : 1;

        $prompt = Prompt::create(array_merge($validated, [
            'version' => $newVersion,
            'status' => 'active',
            'is_active' => true,
        ]));

        return ApiResponse::success($prompt, 'Prompt berhasil disimpan.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $prompt = Prompt::find($id);
        if (!$prompt) {
            return ApiResponse::rfcProblem('Prompt Not Found', 404);
        }

        return ApiResponse::success($prompt);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $prompt = Prompt::find($id);
        if (!$prompt) {
            return ApiResponse::rfcProblem('Prompt Not Found', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'system_prompt' => 'sometimes|string',
            'user_template' => 'sometimes|string',
            'schema_name' => 'nullable|string',
            'status' => 'sometimes|string|in:draft,active,deprecated',
        ]);

        $prompt->update($validated);
        return ApiResponse::success($prompt, 'Prompt berhasil diperbarui.');
    }

    public function destroy(int $id): JsonResponse
    {
        $prompt = Prompt::find($id);
        if (!$prompt) {
            return ApiResponse::rfcProblem('Prompt Not Found', 404);
        }

        $prompt->delete();
        return ApiResponse::success(null, 'Prompt berhasil dihapus.');
    }
}

