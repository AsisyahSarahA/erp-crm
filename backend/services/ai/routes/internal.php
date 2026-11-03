<?php

use App\Http\Controllers\AiRunController;
use Astacode\Kernel\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'health']);

// Internal AI Generation Execution (Called by CRM, PM, Marketing, HRD, Finance)
Route::post('/ai/run', [AiRunController::class, 'run']);
Route::get('/ai/runs/{id}', [AiRunController::class, 'show']);
Route::post('/ai/runs/{id}/decision', [AiRunController::class, 'recordDecision']);
