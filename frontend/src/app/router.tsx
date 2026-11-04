/* eslint-disable react-refresh/only-export-components */
import React, { Suspense, lazy } from 'react'
import { createBrowserRouter, Navigate } from 'react-router-dom'
import { AppShell } from '@/shared/layout/AppShell'
import { AuthGuard, GuestGuard } from './guards/AuthGuard'
import { PermissionGuard } from './guards/PermissionGuard'
import { ForbiddenPage, NotFoundPage, ServerErrorPage } from '@/features/errors'
import { FeatureErrorBoundary } from '@/shared/ui/FeatureErrorBoundary'
import { TableSkeleton } from '@/shared/ui/FeedbackStates'
import { ROUTE_REGISTRY } from './routeMetadata'

// Lazy load primary application pages
const DashboardPage = lazy(() =>
  import('@/pages/DashboardPage').then((m) => ({ default: m.DashboardPage }))
)
const DevUiKitPage = lazy(() =>
  import('@/pages/DevUiKitPage').then((m) => ({ default: m.DevUiKitPage }))
)
const LoginPage = lazy(() =>
  import('@/features/auth/pages/LoginPage').then((m) => ({ default: m.LoginPage }))
)
const PlaceholderPage = lazy(() =>
  import('@/shared/ui/PlaceholderPage').then((m) => ({ default: m.PlaceholderPage }))
)

// Helper to wrap lazy components in Suspense + FeatureErrorBoundary
const withFeatureWrapper = (component: React.ReactNode, featureName?: string) => (
  <FeatureErrorBoundary featureName={featureName}>
    <Suspense fallback={<div className="p-4"><TableSkeleton rows={4} /></div>}>
      {component}
    </Suspense>
  </FeatureErrorBoundary>
)

export const router = createBrowserRouter([
  // Guest Routes (Login)
  {
    element: <GuestGuard />,
    children: [
      {
        path: '/login',
        element: withFeatureWrapper(<LoginPage />, 'Autentikasi'),
      },
    ],
  },

  // Authenticated Application Routes (Wrapped in AppShell)
  {
    element: <AuthGuard />,
    errorElement: <ServerErrorPage />,
    children: [
      {
        element: <AppShell />,
        children: [
          // 1. Executive Dashboard (Root and /dashboard)
          {
            path: '/',
            element: withFeatureWrapper(<DashboardPage />, 'Executive Dashboard'),
          },
          {
            path: '/dashboard',
            element: withFeatureWrapper(<DashboardPage />, 'Executive Dashboard'),
          },

          // 2. Developer Design System UI Kit
          {
            path: '/dev/ui-kit',
            element: withFeatureWrapper(<DevUiKitPage />, 'Design System UI Kit'),
          },

          // 3. Dynamic Registered PRD Routes with Permission Guards & Placeholders
          ...ROUTE_REGISTRY.map((route) => {
            const placeholderElement = (
              <PlaceholderPage
                title={route.title}
                module={route.module}
                taskId={route.taskId}
                prdRef={route.prdRef}
                description={route.description}
                endpoints={route.endpoints}
                permissions={route.permissions}
              />
            )

            // If route requires specific permissions, protect with PermissionGuard
            if (route.permissions && route.permissions.length > 0) {
              return {
                path: route.path,
                element: (
                  <PermissionGuard permissions={route.permissions}>
                    {withFeatureWrapper(placeholderElement, route.title)}
                  </PermissionGuard>
                ),
              }
            }

            return {
              path: route.path,
              element: withFeatureWrapper(placeholderElement, route.title),
            }
          }),

          // 4. Detail Parameterized Routes (PRD §15.4)
          {
            path: '/crm/leads/:id',
            element: withFeatureWrapper(
              <PlaceholderPage
                title="Workspace Detail Lead & Prospek Klien"
                module="CRM"
                taskId="CRM-FE-03"
                prdRef="FR-CRM-03"
                description="Detail lead dengan sub-tab: Overview, Activity, Files, Voice Notes, Requirement, RAB, TOP, Quotation+MoU, Contacts, dan History."
                endpoints={['GET /api/v1/crm/leads/{id}']}
                permissions={['crm.lead.view']}
              />,
              'Lead Detail Workspace'
            ),
          },
          {
            path: '/crm/pipeline/:id',
            element: withFeatureWrapper(
              <PlaceholderPage
                title="Workflow Detail Pipeline Stage"
                module="CRM"
                taskId="CRM-FE-03"
                prdRef="FR-CRM-02"
                description="Visual workflow tracker dan gate check transisi status lead di sepanjang 7 tahapan pipeline."
                endpoints={['GET /api/v1/crm/leads/{id}/stage']}
                permissions={['crm.lead.view']}
              />,
              'Pipeline Stage Detail'
            ),
          },
          {
            path: '/pm/projects/:id',
            element: withFeatureWrapper(
              <PlaceholderPage
                title="Workspace Proyek & 10 Sub-Tab Fungsional"
                module="PM"
                taskId="PM-FE-03"
                prdRef="FR-PM-02"
                description="Ringkasan, Board Kanban, Backlog & Sprint, Roadmap/Gantt, QA & Testing, Docs & Wiki, Time Tracking, Budget, dan Tiket."
                endpoints={['GET /api/v1/pm/projects/{id}']}
                permissions={['pm.project.view']}
              />,
              'Project Workspace Detail'
            ),
          },
          {
            path: '/finance/invoices/:id',
            element: withFeatureWrapper(
              <PlaceholderPage
                title="Faktur Pajak & Tagihan Termin"
                module="Finance"
                taskId="FIN-FE-02"
                prdRef="FR-FIN-01"
                description="Pratinjau faktur PDF resmi A4, perhitungan PPN 11% & PPh 23, serta bukti potong dan pembayaran."
                endpoints={['GET /api/v1/finance/invoices/{id}']}
                permissions={['finance.invoice.view']}
              />,
              'Invoice Detail'
            ),
          },
        ],
      },
    ],
  },

  // Error Pages
  {
    path: '/403',
    element: <ForbiddenPage />,
  },
  {
    path: '/500',
    element: <ServerErrorPage />,
  },
  {
    path: '/404',
    element: <NotFoundPage />,
  },
  {
    path: '*',
    element: <Navigate to="/404" replace />,
  },
])
