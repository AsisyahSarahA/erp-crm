<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-hrd',
        'port' => 8004,
        'timestamp' => now()->toIso8601String(),
    ]);
});
