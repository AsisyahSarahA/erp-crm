<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-crm',
        'port' => 8002,
        'timestamp' => now()->toIso8601String(),
    ]);
});
