<?php

use App\Http\Controllers\EventController;
use Astacode\Kernel\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'health']);

// Internal Event Ingestion from Outbox Relays
Route::post('/events', [EventController::class, 'receive']);
