<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NumberingController;
use Astacode\Kernel\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'health']);

// Internal microservice inter-communications
Route::post('/numbering/next', [NumberingController::class, 'next']);
Route::post('/approvals', [ApprovalController::class, 'submit']);
Route::post('/audit-logs/record', [AuditLogController::class, 'record']);
Route::post('/notifications/send', [NotificationController::class, 'send']);
