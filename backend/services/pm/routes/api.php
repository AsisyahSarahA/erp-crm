<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-pm',
        'port' => 8005,
        'timestamp' => now()->toIso8601String(),
    ]);
});
