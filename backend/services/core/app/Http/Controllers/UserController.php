<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and role filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('roles');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }

        $perPage = min((int) ($request->get('per_page', 15)), 100);
        $users = $query->latest()->paginate($perPage);

        return ApiResponse::paginated($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|max:50',
            'password' => 'required|string|min:8',
            'company_id' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'company_id' => $validated['company_id'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assign Role
        $roleModel = Role::where('name', $validated['role'])->first();
        if ($roleModel) {
            $user->assignRole($roleModel);
        }

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'user.created',
            'entity_type' => 'user',
            'entity_id' => (string) $user->id,
            'new_values' => ['email' => $user->email, 'role' => $user->role, 'name' => $user->name],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "User {$user->email} created.",
        ]);

        return ApiResponse::success($user, 'User berhasil dibuat.', 201);
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with(['roles', 'permissions'])->find($id);
        if (!$user) {
            return ApiResponse::rfcProblem('User Not Found', 404, "User dengan ID {$id} tidak ditemukan.");
        }

        return ApiResponse::success($user);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::rfcProblem('User Not Found', 404, "User dengan ID {$id} tidak ditemukan.");
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'sometimes|string|max:50',
            'password' => 'nullable|string|min:8',
            'company_id' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldValues = $user->only(['name', 'email', 'phone', 'role', 'is_active']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['role'])) {
            $roleModel = Role::where('name', $validated['role'])->first();
            if ($roleModel) {
                $user->syncRoles([$roleModel]);
            }
        }

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'user.updated',
            'entity_type' => 'user',
            'entity_id' => (string) $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->only(['name', 'email', 'phone', 'role', 'is_active']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "User {$user->email} diperbarui.",
        ]);

        return ApiResponse::success($user, 'User berhasil diperbarui.');
    }

    /**
     * Deactivate / delete user.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::rfcProblem('User Not Found', 404, "User dengan ID {$id} tidak ditemukan.");
        }

        // Soft deactivation
        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'user.deactivated',
            'entity_type' => 'user',
            'entity_id' => (string) $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "User {$user->email} dinonaktifkan.",
        ]);

        return ApiResponse::success(null, 'User berhasil dinonaktifkan.');
    }

    /**
     * Impersonate a user with strict audit logging (Super Admin / Director only).
     */
    public function impersonate(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        if (!$admin || !in_array($admin->role, ['super_admin', 'director'], true)) {
            return ApiResponse::rfcProblem('Unauthorized', 403, 'Hanya Super Admin atau Direktur yang dapat melakukan impersonasi.');
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return ApiResponse::rfcProblem('User Not Found', 404, "Target impersonasi tidak ditemukan.");
        }

        // Cannot impersonate yourself
        if ($admin->id === $targetUser->id) {
            return ApiResponse::rfcProblem('Invalid Action', 422, 'Tidak dapat mengimpersonasi akun sendiri.');
        }

        // Generate temporary impersonation token
        $token = $targetUser->createToken("Impersonated by {$admin->name} ({$admin->email})")->plainTextToken;

        // Log sensitive audit trail
        AuditLog::create([
            'user_id' => (string) $admin->id,
            'service' => 'core-service',
            'action' => 'security.impersonate',
            'entity_type' => 'user',
            'entity_id' => (string) $targetUser->id,
            'new_values' => [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'target_id' => $targetUser->id,
                'target_email' => $targetUser->email,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "Admin {$admin->name} melakukan impersonasi terhadap {$targetUser->email}.",
        ]);

        return ApiResponse::success([
            'impersonated_user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role,
            ],
            'token' => $token,
            'message' => "Impersonasi aktif sebagai {$targetUser->name}.",
        ]);
    }
}

