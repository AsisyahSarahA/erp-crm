import React from 'react'
import { Navigate, Outlet } from 'react-router-dom'
import { usePermissions } from '@/shared/auth/usePermissions'

interface PermissionGuardProps {
  permission?: string
  permissions?: string[]
  role?: string
  roles?: string[]
  children?: React.ReactNode
}

export const PermissionGuard: React.FC<PermissionGuardProps> = ({
  permission,
  permissions,
  role,
  roles,
  children,
}) => {
  const { hasPermission, hasAnyPermission, hasRole, isSuperAdmin } = usePermissions()

  if (isSuperAdmin) {
    return children ? <>{children}</> : <Outlet />
  }

  if (role && !hasRole(role)) {
    return <Navigate to="/403" replace />
  }

  if (roles && roles.length > 0 && !roles.some((r) => hasRole(r))) {
    return <Navigate to="/403" replace />
  }

  if (permission && !hasPermission(permission)) {
    return <Navigate to="/403" replace />
  }

  if (permissions && permissions.length > 0 && !hasAnyPermission(permissions)) {
    return <Navigate to="/403" replace />
  }

  return children ? <>{children}</> : <Outlet />
}
