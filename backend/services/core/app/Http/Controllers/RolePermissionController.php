<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RolePermissionController extends Controller
{
    /**
     * List all roles with associated permissions count.
     */
    public function roles(Request $request): JsonResponse
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions_count' => $role->permissions->count(),
                'permissions' => $role->permissions->pluck('name'),
            ];
        });

        return ApiResponse::success($roles);
    }

    /**
     * List all available permissions grouped by module.
     */
    public function permissions(Request $request): JsonResponse
    {
        $permissions = Permission::all();

        $grouped = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $module = $parts[0] ?? 'general';
            $grouped[$module][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'guard_name' => $perm->guard_name,
            ];
        }

        return ApiResponse::success([
            'total' => $permissions->count(),
            'modules' => $grouped,
            'list' => $permissions->pluck('name'),
        ]);
    }

    /**
     * Assign / sync permissions to a specific role.
     */
    public function assignPermissions(Request $request, int $roleId): JsonResponse
    {
        $role = Role::find($roleId);
        if (!$role) {
            return ApiResponse::rfcProblem('Role Not Found', 404, "Peran dengan ID {$roleId} tidak ditemukan.");
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissionModels = Permission::whereIn('name', $request->permissions)->get();
        $role->syncPermissions($permissionModels);

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'rbac.permissions_synced',
            'entity_type' => 'role',
            'entity_id' => (string) $role->id,
            'new_values' => ['role' => $role->name, 'permissions' => $request->permissions],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Hak akses peran {$role->name} diperbarui ({$permissionModels->count()} permissions).",
        ]);

        return ApiResponse::success([
            'role' => $role->name,
            'assigned_permissions' => $permissionModels->pluck('name'),
        ], 'Hak akses berhasil diperbarui.');
    }
}

