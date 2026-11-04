import { describe, it, expect } from 'vitest'
import { ROUTE_REGISTRY } from '../app/routeMetadata'

describe('Route Registry & PRD §15.4 Navigation Completeness', () => {
  it('registers all required PRD modules without duplicate paths', () => {
    const paths = ROUTE_REGISTRY.map((r) => r.path)
    const uniquePaths = new Set(paths)

    expect(paths.length).toBe(uniquePaths.size)
    expect(paths.length).toBeGreaterThanOrEqual(95)
  })

  it('contains all 21 CRM menus from PRD §6.2', () => {
    const crmRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'CRM')
    expect(crmRoutes.length).toBe(21)

    const expectedCrmPaths = [
      '/crm/dashboard',
      '/crm/leads',
      '/crm/pipeline',
      '/crm/customers',
      '/crm/contacts',
      '/crm/activities',
      '/crm/quotations',
      '/crm/contracts',
      '/crm/client-care',
      '/crm/rate-cards',
      '/crm/templates',
      '/crm/forecast',
      '/crm/segments',
      '/crm/account-plans',
      '/crm/win-loss',
      '/crm/approvals',
      '/crm/reports',
      '/crm/settings',
      '/crm/transition-hub',
      '/crm/ar-calendar',
      '/crm/voice-notes',
    ]

    expectedCrmPaths.forEach((path) => {
      const found = crmRoutes.some((r) => r.path === path)
      expect(found).toBe(true)
    })
  })

  it('contains all Marketing menus from PRD §7.2', () => {
    const mktRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'Marketing')
    expect(mktRoutes.length).toBe(13)
  })

  it('contains all HRD menus from PRD §8.2', () => {
    const hrRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'HRD')
    expect(hrRoutes.length).toBe(14)
  })

  it('contains all PM menus from PRD §9.2', () => {
    const pmRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'PM')
    expect(pmRoutes.length).toBe(15)
  })

  it('contains all Finance menus from PRD §10.2', () => {
    const finRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'Finance')
    expect(finRoutes.length).toBe(17)
  })

  it('contains Client Portal, Admin, and AI Studio routes', () => {
    const portalRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'Portal Klien')
    const adminRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'Admin Core')
    const aiRoutes = ROUTE_REGISTRY.filter((r) => r.module === 'AI Studio')

    expect(portalRoutes.length).toBe(5)
    expect(adminRoutes.length).toBe(6)
    expect(aiRoutes.length).toBe(4)
  })

  it('ensures every route has valid metadata and task ID', () => {
    ROUTE_REGISTRY.forEach((route) => {
      expect(route.path.startsWith('/')).toBe(true)
      expect(route.title.trim().length).toBeGreaterThan(0)
      expect(route.module.trim().length).toBeGreaterThan(0)
      expect(route.taskId.trim().length).toBeGreaterThan(0)
      expect(route.prdRef.trim().length).toBeGreaterThan(0)
      expect(route.description.trim().length).toBeGreaterThan(0)
    })
  })
})

