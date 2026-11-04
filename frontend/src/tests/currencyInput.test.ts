import { describe, it, expect } from 'vitest'
import { formatIDR, parseIDR, terbilang } from '../shared/utils/formatters'

describe('Currency and Terbilang Formatter', () => {
  it('formats number to Indonesian Rupiah correctly', () => {
    expect(formatIDR(15000000)).toMatch(/Rp\s*15\.000\.000/)
    expect(formatIDR(0)).toMatch(/Rp\s*0/)
    expect(formatIDR(null)).toMatch(/Rp\s*0/)
  })

  it('parses formatted string back to raw integer', () => {
    expect(parseIDR('Rp 15.000.000')).toBe(15000000)
    expect(parseIDR('2.500.000')).toBe(2500000)
    expect(parseIDR('')).toBe(0)
  })

  it('converts numbers to Indonesian words (Terbilang)', () => {
    expect(terbilang(0)).toBe('Nol rupiah')
    expect(terbilang(1000)).toBe('Seribu rupiah')
    expect(terbilang(15000000)).toBe('Lima belas juta rupiah')
    expect(terbilang(2550000)).toBe('Dua juta lima ratus lima puluh ribu rupiah')
    expect(terbilang(1000000000)).toBe('Satu milyar rupiah')
  })
})

