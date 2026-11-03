<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Security\FieldMasker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SensitiveDataController extends Controller
{
    /**
     * E2E-08 Security Access Check:
     * Accessing confidential salary/payroll data.
     * If user lacks permission (e.g., Sales), returns 403 Forbidden RFC Problem Details
     * AND writes an audit log recording unauthorized attempt.
     */
    public function salaries(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $canViewSalaries = $user->hasPermissionTo('hrd.payroll.view')
            || in_array($user->role, ['super_admin', 'director', 'hrd_manager', 'finance_manager'], true);

        if (!$canViewSalaries) {
            // Write sensitive audit log of unauthorized attempt
            AuditLog::create([
                'user_id' => (string) $user->id,
                'service' => 'core-service',
                'action' => 'security.unauthorized_access',
                'entity_type' => 'salary_records',
                'entity_id' => 'all',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_sensitive_access' => true,
                'notes' => "User {$user->email} ({$user->role}) mencoba mengakses data gaji tanpa izin. Akses diblokir (403 Forbidden).",
                'created_at' => now(),
            ]);

            return ApiResponse::rfcProblem(
                title: 'Forbidden Access',
                status: 403,
                detail: 'Anda tidak memiliki hak akses (hrd.payroll.view) untuk melihat data gaji.',
                type: 'https://astacode.com/errors/forbidden'
            );
        }

        // Record authorized sensitive access audit
        AuditLog::create([
            'user_id' => (string) $user->id,
            'service' => 'core-service',
            'action' => 'audit.sensitive_data_view',
            'entity_type' => 'salary_records',
            'entity_id' => 'all',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_sensitive_access' => true,
            'notes' => "User {$user->email} ({$user->role}) melihat rekapitulasi data gaji.",
            'created_at' => now(),
        ]);

        return ApiResponse::success([
            [
                'employee_name' => 'Fajar Pratama',
                'role' => 'HRD Manager',
                'base_salary' => 18000000,
                'allowance' => 3500000,
            ],
            [
                'employee_name' => 'Citra Dewi',
                'role' => 'Sales Executive',
                'base_salary' => 12000000,
                'allowance' => 2000000,
            ],
        ]);
    }

    /**
     * Accessing deal margin & HPP:
     * If user lacks `crm.margin.view`, sensitive fields (HPP & margin) are masked.
     */
    public function dealMargin(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::rfcProblem('Unauthenticated', 401);
        }

        $canViewMargin = $user->hasPermissionTo('crm.margin.view')
            || in_array($user->role, ['super_admin', 'director', 'sales_manager', 'finance_manager'], true);

        // Record sensitive audit if view allowed
        AuditLog::create([
            'user_id' => (string) $user->id,
            'service' => 'core-service',
            'action' => 'audit.deal_margin_view',
            'entity_type' => 'deal',
            'entity_id' => $id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_sensitive_access' => true,
            'notes' => "User {$user->email} mengakses margin deal {$id}. Margin terlihat: " . ($canViewMargin ? 'Ya' : 'Dimask (******)'),
            'created_at' => now(),
        ]);

        $dealData = [
            'deal_id' => $id,
            'client_name' => 'PT Megah Perkasa',
            'deal_value' => 250000000,
            'hpp' => FieldMasker::sensitiveAmount(165000000, $canViewMargin),
            'gross_margin' => FieldMasker::sensitiveAmount(85000000, $canViewMargin),
            'margin_percentage' => $canViewMargin ? '34%' : '******',
        ];

        return ApiResponse::success($dealData);
    }
}

