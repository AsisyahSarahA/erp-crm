<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class UserContext
{
    public static function resolve(?Request $request = null): array
    {
        $req = $request ?? request();

        $internalUser = $req->attributes->get('internal_user');
        if (is_array($internalUser) && !empty($internalUser)) {
            $role = strtolower((string) ($internalUser['role'] ?? 'user'));
            $roles = array_map('strtolower', (array) ($internalUser['roles'] ?? [$role]));

            return [
                'id' => isset($internalUser['id']) ? (int) $internalUser['id'] : null,
                'name' => $internalUser['name'] ?? 'Internal User',
                'email' => $internalUser['email'] ?? null,
                'role' => $role,
                'roles' => $roles,
                'permissions' => (array) ($internalUser['permissions'] ?? []),
                'team_id' => isset($internalUser['team_id']) ? (int) $internalUser['team_id'] : null,
                'team_member_ids' => array_map('intval', (array) ($internalUser['team_member_ids'] ?? [])),
                'company_id' => isset($internalUser['company_id']) ? (int) $internalUser['company_id'] : 1,
                'is_authenticated' => true,
            ];
        }

        $authUser = $req->user();
        if ($authUser !== null) {
            $role = strtolower((string) ($authUser->role ?? 'user'));
            $roles = isset($authUser->roles) && is_iterable($authUser->roles)
                ? collect($authUser->roles)->map(fn ($r) => strtolower(is_string($r) ? $r : ($r->name ?? '')))->all()
                : [$role];

            $permissions = method_exists($authUser, 'getAllPermissions')
                ? $authUser->getAllPermissions()->pluck('name')->toArray()
                : (array) ($authUser->permissions ?? []);

            return [
                'id' => isset($authUser->id) ? (int) $authUser->id : null,
                'name' => $authUser->name ?? 'Authenticated User',
                'email' => $authUser->email ?? null,
                'role' => $role,
                'roles' => $roles,
                'permissions' => $permissions,
                'team_id' => isset($authUser->team_id) ? (int) $authUser->team_id : null,
                'team_member_ids' => array_map('intval', (array) ($authUser->team_member_ids ?? [])),
                'company_id' => isset($authUser->company_id) ? (int) $authUser->company_id : 1,
                'is_authenticated' => true,
            ];
        }

        if ($req->hasHeader('X-User-Role') || $req->hasHeader('X-User-Id')) {
            $role = strtolower((string) $req->header('X-User-Role', 'user'));
            $permsRaw = (string) $req->header('X-User-Permissions', '');
            $perms = $permsRaw !== ''
                ? array_values(array_filter(array_map('trim', explode(',', $permsRaw))))
                : [];

            return [
                'id' => $req->hasHeader('X-User-Id') ? (int) $req->header('X-User-Id') : 1,
                'name' => (string) $req->header('X-User-Name', 'Header User'),
                'email' => (string) $req->header('X-User-Email', 'user@astacode.id'),
                'role' => $role,
                'roles' => [$role],
                'permissions' => $perms,
                'team_id' => $req->hasHeader('X-User-Team-Id') ? (int) $req->header('X-User-Team-Id') : null,
                'team_member_ids' => [],
                'company_id' => 1,
                'is_authenticated' => true,
            ];
        }

        return [
            'id' => 1,
            'name' => 'System Default',
            'email' => 'system@astacode.id',
            'role' => 'super_admin',
            'roles' => ['super_admin'],
            'permissions' => ['*'],
            'team_id' => null,
            'team_member_ids' => [],
            'company_id' => 1,
            'is_authenticated' => false,
        ];
    }

    /**
     * Helper to build Base64 X-Internal-User header for tests or internal calls.
     */
    public static function encodeHeader(array $userData): string
    {
        return base64_encode((string) json_encode($userData));
    }

    /**
     * Determines if the current user can view internal HPP, cost_rate, and Gross Margin (BR-CRM-07, AC-CRM-07).
     */
    public static function canViewMargin(?Request $request = null): bool
    {
        $req = $request ?? request();

        $viewMode = strtolower((string) $req->query('view_mode', $req->query('view', '')));
        if ($viewMode === 'client') {
            return false;
        }

        $user = self::resolve($req);
        $permissions = array_map('strtolower', (array) ($user['permissions'] ?? []));
        $role = strtolower((string) ($user['role'] ?? 'user'));

        if (in_array('crm.margin.view', $permissions, true) || in_array('*', $permissions, true)) {
            return true;
        }

        // If permissions were explicitly provided and didn't include crm.margin.view, deny
        if (!empty($permissions)) {
            return false;
        }

        // Default role-based fallback when permissions array is empty
        return in_array($role, ['super_admin', 'director', 'presales', 'sales_manager', 'finance_manager'], true);
    }
}

