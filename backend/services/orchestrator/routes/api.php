<?php

use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReadModelController;
use App\Http\Controllers\SagaController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-orchestrator',
        'port' => 8008,
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Event Broker Management
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/dead-letters', [EventController::class, 'deadLetters']);
    Route::get('/{id}', [EventController::class, 'show']);
    Route::post('/{id}/replay', [EventController::class, 'replay']);
});

// Subscriptions
Route::apiResource('subscriptions', SubscriptionController::class)->only(['index', 'store', 'destroy']);

// Sagas (Distributed Workflows & Handover Tracker)
Route::prefix('sagas')->group(function () {
    Route::get('/', [SagaController::class, 'index']);
    Route::get('/{id}', [SagaController::class, 'show']);
    Route::post('/{id}/retry-step/{stepOrder}', [SagaController::class, 'retryStep']);
    Route::post('/{id}/readiness', [SagaController::class, 'updateReadiness']);
    Route::post('/{id}/waive-kickoff', [SagaController::class, 'waiveKickoff']);
    Route::post('/{id}/compensate', [SagaController::class, 'compensate']);
});

// Automation Rules & Executions
Route::apiResource('automation-rules', AutomationRuleController::class);
Route::get('automation-rules/{id}/runs', [AutomationRuleController::class, 'runs']);
Route::post('automation-rules/{id}/trigger', [AutomationRuleController::class, 'triggerManual']);

// Read Models / Consolidated Aggregations
Route::get('read-models', [ReadModelController::class, 'index']);
Route::get('read-models/{type}/{key}', [ReadModelController::class, 'show']);
