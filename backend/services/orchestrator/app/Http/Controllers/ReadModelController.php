<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ReadModel;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReadModelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ReadModel::query();

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        $models = $query->latest('updated_at')->paginate(25);
        return ApiResponse::paginated($models);
    }

    public function show(string $type, string $key): JsonResponse
    {
        $model = ReadModel::where('model_type', $type)->where('model_key', $key)->first();
        if (!$model) {
            return ApiResponse::rfcProblem('Read Model Not Found', 404);
        }

        return ApiResponse::success($model);
    }
}

