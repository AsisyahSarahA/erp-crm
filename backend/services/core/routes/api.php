<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NumberingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SensitiveDataController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-core',
        'port' => 8001,
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Authentication (Public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/2fa/verify', [AuthController::class, 'verify2fa']);
});

// Webhook Inbound (Public / Protected by Provider Signatures)
Route::post('/webhooks/incoming/{provider}', [WebhookController::class, 'handleIncoming']);

// Protected Routes (Sanctum auth or internal token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Identity
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/2fa/setup', [AuthController::class, 'setup2fa']);
        Route::get('/sessions', [AuthController::class, 'sessions']);
        Route::delete('/sessions/{id}', [AuthController::class, 'revokeSession']);
    });

    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/me/permissions', [AuthController::class, 'permissions']);

    // User Management
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/impersonate', [UserController::class, 'impersonate']);

    // RBAC: Roles & Permissions
    Route::get('roles', [RolePermissionController::class, 'roles']);
    Route::post('roles/{id}/permissions', [RolePermissionController::class, 'assignPermissions']);
    Route::get('permissions', [RolePermissionController::class, 'permissions']);

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index']);
    Route::get('audit-logs/sensitive-access', [AuditLogController::class, 'sensitiveAccess']);
    Route::post('audit-logs/record', [AuditLogController::class, 'record']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/preferences', [NotificationController::class, 'getPreferences']);
    Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences']);
    Route::get('notifications/stream', [NotificationController::class, 'stream']);

    // File Management
    Route::get('files', [FileController::class, 'index']);
    Route::post('files', [FileController::class, 'upload']);
    Route::get('files/{id}', [FileController::class, 'show']);
    Route::get('files/{id}/signed-url', [FileController::class, 'signedUrl']);

    // Approvals
    Route::get('approvals', [ApprovalController::class, 'index']);
    Route::post('approvals', [ApprovalController::class, 'submit']);
    Route::get('approvals/{id}', [ApprovalController::class, 'show']);
    Route::post('approvals/{id}/decide', [ApprovalController::class, 'decide']);

    // Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::get('settings/{key}', [SettingController::class, 'show']);
    Route::put('settings/{key}', [SettingController::class, 'update']);

    // Numbering Patterns
    Route::get('numbering', [NumberingController::class, 'index']);
    Route::put('numbering/{codeType}', [NumberingController::class, 'update']);

    // Webhooks
    Route::get('webhooks', [WebhookController::class, 'index']);
    Route::post('webhooks', [WebhookController::class, 'store']);
    Route::get('webhooks/{id}/deliveries', [WebhookController::class, 'deliveries']);
    Route::post('webhooks/{id}/replay/{deliveryId}', [WebhookController::class, 'replay']);

    // Integrations
    Route::get('integrations', [IntegrationController::class, 'index']);
    Route::post('integrations', [IntegrationController::class, 'store']);
    Route::post('integrations/{provider}/test', [IntegrationController::class, 'testConnection']);

    // Security E2E-08 Verification Endpoints
    Route::get('sensitive/salaries', [SensitiveDataController::class, 'salaries']);
    Route::get('sensitive/deal-margins/{id}', [SensitiveDataController::class, 'dealMargin']);
});

// Signed File Download (temporary signed URL)
Route::get('files/{id}/download', [FileController::class, 'download'])->name('files.download');
