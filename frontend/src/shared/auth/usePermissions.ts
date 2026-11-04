import { useAuthStore } from './authStore'

export function usePermissions() {
  const user = useAuthStore((s) => s.user)
  const hasPermission = useAuthStore((s) => s.hasPermission)
  const hasAnyPermission = useAuthStore((s) => s.hasAnyPermission)
  const hasRole = useAuthStore((s) => s.hasRole)

  return {
    user,
    roles: user?.roles || [],
    permissions: user?.permissions || [],
    hasPermission,
    hasAnyPermission,
    hasRole,
    isSuperAdmin: Boolean(user?.roles.includes('super_admin') || user?.roles.includes('owner')),
    isClient: Boolean(user?.roles.includes('klien') || user?.customer_id),
  }
}

