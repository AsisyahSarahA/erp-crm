<?php

declare(strict_types=1);

use App\Http\Controllers\CustomerController;
use Astacode\Kernel\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'health']);

// Internal service-to-service endpoints (Orchestrator Saga & Finance/PM snapshot updates)
Route::post('/customers/{id}/health-score', [CustomerController::class, 'recalculateHealthScore'])->whereNumber('id');
Route::match(['post', 'patch'], '/customers/{id}/verify-npwp', [CustomerController::class, 'verifyNpwp'])->whereNumber('id');

// Consumer for lead.captured event (from Marketing / WhatsApp / Orchestrator)
Route::post('/events/lead-captured', [\App\Http\Controllers\PublicLeadCaptureController::class, 'capture']);
Route::post('/events', function (\Illuminate\Http\Request $request) {
    $eventName = $request->input('event_name') ?? $request->input('event');
    if ($eventName === 'lead.captured') {
        $payload = $request->input('payload', $request->all());
        $subRequest = \Illuminate\Http\Request::create('/api/public/lead-capture', 'POST', $payload);
        return app(\App\Http\Controllers\PublicLeadCaptureController::class)->capture($subRequest);
    }
    return response()->json(['status' => 'ignored', 'event' => $eventName]);
});

