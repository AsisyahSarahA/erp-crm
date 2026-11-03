<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuditLogController extends Controller
{
    /**
     * Search and filter immutable audit logs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::query()->with('user:id,name,email,role');

        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $perPage = min((int) ($request->get('per_page', 25)), 100);
        $logs = $query->orderBy('id', 'desc')->paginate($perPage);

        return ApiResponse::paginated($logs);
    }

    /**
     * Dedicated endpoint to trace sensitive data accesses (salaries, payroll, margins, contracts).
     */
    public function sensitiveAccess(Request $request): JsonResponse
    {
        $query = AuditLog::query()
            ->where('is_sensitive_access', true)
            ->with('user:id,name,email,role');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $perPage = min((int) ($request->get('per_page', 25)), 100);
        $logs = $query->orderBy('id', 'desc')->paginate($perPage);

        return ApiResponse::paginated($logs);
    }

    /**
     * Record a sensitive access or audit event (called internally by other microservices or core).
     */
    public function record(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'service' => 'required|string',
            'action' => 'required|string',
            'entity_type' => 'nullable|string',
            'entity_id' => 'nullable|string',
            'old_values' => 'nullable|array',
            'new_values' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_sensitive_access' => 'boolean',
        ]);

        $log = AuditLog::create([
            'user_id' => $validated['user_id'],
            'service' => $validated['service'],
            'action' => $validated['action'],
            'entity_type' => $validated['entity_type'] ?? null,
            'entity_id' => $validated['entity_id'] ?? null,
            'old_values' => $validated['old_values'] ?? null,
            'new_values' => $validated['new_values'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'correlation_id' => $request->header('X-Correlation-Id'),
            'notes' => $validated['notes'] ?? null,
            'is_sensitive_access' => $validated['is_sensitive_access'] ?? false,
            'created_at' => now(),
        ]);

        return ApiResponse::success($log, 'Audit log tercatat.', 201);
    }
}

