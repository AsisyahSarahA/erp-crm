<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadSourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LeadSource::query()->orderBy('sort_order')->orderBy('name');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->filled('channel_group')) {
            $query->where('channel_group', strtoupper((string) $request->query('channel_group')));
        }

        return ApiResponse::success($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['nullable', 'string', 'max:64', 'unique:crm_lead_sources,code'],
            'name' => ['required', 'string', 'min:2', 'max:128'],
            'channel_group' => ['required', 'string', 'max:64'],
            'category' => ['nullable', 'string', 'max:64'],
            'sub_sources' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data sumber lead tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $source = LeadSource::create($data);

        return ApiResponse::success($source, ['message' => 'Sumber lead berhasil dibuat.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return ApiResponse::rfcProblem('Not Found', 404, 'Sumber lead tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'min:2', 'max:128'],
            'channel_group' => ['sometimes', 'string', 'max:64'],
            'category' => ['nullable', 'string', 'max:64'],
            'sub_sources' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data sumber lead tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $source->update($validator->validated());

        return ApiResponse::success($source);
    }

    public function destroy(int $id): JsonResponse
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return ApiResponse::rfcProblem('Not Found', 404, 'Sumber lead tidak ditemukan.');
        }

        $source->update(['is_active' => false]);

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }
}

