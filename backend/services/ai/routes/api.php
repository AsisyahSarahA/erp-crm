<?php

use App\Http\Controllers\AiRunController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-ai',
        'port' => 8007,
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Prompts CRUD
Route::apiResource('prompts', PromptController::class);

// Providers List
Route::get('providers', [ProviderController::class, 'index']);

// Metrics & Observability
Route::get('metrics', [MetricController::class, 'metrics']);

// Budgets per module
Route::get('budgets', [BudgetController::class, 'index']);
Route::put('budgets/{module}', [BudgetController::class, 'update']);

// AI Runs History & Human Feedback
Route::get('runs', [AiRunController::class, 'index']);
Route::get('runs/{id}', [AiRunController::class, 'show']);
Route::post('runs/{id}/decision', [AiRunController::class, 'recordDecision']);
