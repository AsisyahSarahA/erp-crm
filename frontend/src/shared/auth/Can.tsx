import React from 'react'
import { usePermissions } from './usePermissions'

interface CanProps {
  permission?: string
  permissions?: string[]
  matchAll?: boolean
  role?: string
  roles?: string[]
  fallback?: React.ReactNode
  children: React.ReactNode
}

export const Can: React.FC<CanProps> = ({
  permission,
  permissions,
  matchAll = false,
  role,
  roles,
  fallback = null,
  children,
}) => {
  const { hasPermission, hasAnyPermission, hasRole, isSuperAdmin } = usePermissions()

  if (isSuperAdmin) {
    return <>{children}</>
  }

  // Check roles
  if (role && !hasRole(role)) {
    return <>{fallback}</>
  }

  if (roles && roles.length > 0) {
    const roleMatches = roles.some((r) => hasRole(r))
    if (!roleMatches) return <>{fallback}</>
  }

  // Check single permission
  if (permission && !hasPermission(permission)) {
    return <>{fallback}</>
  }

  // Check multiple permissions
  if (permissions && permissions.length > 0) {
    if (matchAll) {
      const allMatch = permissions.every((p) => hasPermission(p))
      if (!allMatch) return <>{fallback}</>
    } else {
      if (!hasAnyPermission(permissions)) {
        return <>{fallback}</>
      }
    }
  }

  return <>{children}</>
}

