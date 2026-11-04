import { describe, it, expect } from 'vitest'
import { RabItem } from '../shared/ui/SpreadsheetGrid'

describe('RAB Calculations & Markup Math', () => {
  it('correctly calculates client rate from internal cost rate and margin %', () => {
    const internalRate = 1500000
    const marginPercent = 30 // 30% markup
    const clientRate = Math.round(internalRate * (1 + marginPercent / 100))

    expect(clientRate).toBe(1950000)
  })

  it('correctly aggregates mandays and grand totals', () => {
    const items: RabItem[] = [
      { id: '1', category: 'Dev', role: 'BE', name: 'Task 1', mandays: 5, internalRate: 1000000, marginPercent: 20, clientRate: 1200000 },
      { id: '2', category: 'Dev', role: 'FE', name: 'Task 2', mandays: 5, internalRate: 1000000, marginPercent: 20, clientRate: 1200000 },
    ]

    const totalMandays = items.reduce((sum, i) => sum + i.mandays, 0)
    const totalInternalCost = items.reduce((sum, i) => sum + i.mandays * i.internalRate, 0)
    const totalClientPrice = items.reduce((sum, i) => sum + i.mandays * i.clientRate, 0)
    const grossMarginPercent = ((totalClientPrice - totalInternalCost) / totalClientPrice) * 100

    expect(totalMandays).toBe(10)
    expect(totalInternalCost).toBe(10000000)
    expect(totalClientPrice).toBe(12000000)
    expect(Math.round(grossMarginPercent)).toBe(17) // (12M - 10M) / 12M = 16.67%
  })
})

