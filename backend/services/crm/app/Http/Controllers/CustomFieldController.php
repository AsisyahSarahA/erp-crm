<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomFieldDefinition;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomFieldController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CustomFieldDefinition::query()->orderBy('sort_order')->orderBy('id');

        if ($request->filled('entity_type')) {
            $query->where('entity_type', strtolower((string) $request->query('entity_type')));
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return ApiResponse::success($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entity_type' => ['required', 'string', 'in:customer,contact,lead'],
            'field_key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
            'label' => ['required', 'string', 'max:128'],
            'field_type' => ['required', 'string', 'in:text,number,date,boolean,select,multi_select,json'],
            'options' => ['nullable', 'array'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Definisi custom field tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $definition = CustomFieldDefinition::updateOrCreate(
            [
                'entity_type' => $data['entity_type'],
                'field_key' => $data['field_key'],
                'company_id' => 1,
            ],
            $data
        );

        return ApiResponse::success($definition, ['message' => 'Custom field berhasil disimpan.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $definition = CustomFieldDefinition::find($id);
        if (!$definition) {
            return ApiResponse::rfcProblem('Not Found', 404, 'Custom field tidak ditemukan.');
        }

        $definition->update($request->only(['label', 'field_type', 'options', 'is_required', 'is_active', 'sort_order']));

        return ApiResponse::success($definition);
    }

    public function destroy(int $id): JsonResponse
    {
        $definition = CustomFieldDefinition::find($id);
        if (!$definition) {
            return ApiResponse::rfcProblem('Not Found', 404, 'Custom field tidak ditemukan.');
        }

        $definition->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }
}

