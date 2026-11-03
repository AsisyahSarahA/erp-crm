<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-finance',
        'port' => 8006,
        'timestamp' => now()->toIso8601String(),
    ]);
});
