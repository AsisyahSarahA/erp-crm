<?php

use App\Http\Controllers\PublicLeadCaptureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Direct root public lead capture
Route::post('/public/lead-capture', [PublicLeadCaptureController::class, 'capture']);
