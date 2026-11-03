<?php

declare(strict_types=1);

namespace Astacode\Kernel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ResolveInternalUser
 * Resolves user identity, roles, and granular permissions forwarded by Gateway via X-Internal-User header.
 */
class ResolveInternalUser
{
    public const HEADER_NAME = 'X-Internal-User';

    public function handle(Request $request, Closure $next): Response
    {
        $rawUser = $request->header(self::HEADER_NAME);

        if ($rawUser) {
            $userData = json_decode(base64_decode($rawUser), true);

            if (is_array($userData)) {
                $request->attributes->set('internal_user', $userData);
                $request->attributes->set('current_user_id', $userData['id'] ?? null);
                $request->attributes->set('current_user_role', $userData['role'] ?? 'guest');
                $request->attributes->set('current_user_permissions', $userData['permissions'] ?? []);
            }
        }

        return $next($request);
    }
}

