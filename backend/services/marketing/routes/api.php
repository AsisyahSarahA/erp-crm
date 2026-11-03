<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-marketing',
        'port' => 8003,
        'timestamp' => now()->toIso8601String(),
    ]);
});
