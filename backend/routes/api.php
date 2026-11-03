<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'service' => 'CRM API',
        'message' => 'CRM Backend is running',
    ]);
});
