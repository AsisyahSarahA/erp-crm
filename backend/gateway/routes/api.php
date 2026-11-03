<?php

use App\Http\Controllers\ProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-gateway',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Proxy routes for microservices: /api/v1/{service}/{any}
Route::any('/v1/{service}/{path?}', [ProxyController::class, 'proxy'])
    ->where('path', '.*')
    ->name('gateway.proxy');
