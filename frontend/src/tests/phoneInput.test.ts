import { describe, it, expect } from 'vitest'
import { toE164, isValidIndonesianPhone, formatPhoneDisplay } from '../shared/utils/phone'

describe('Indonesian Phone Formatter & Validator', () => {
  it('converts local 08xxx to E.164 +628xxx format', () => {
    expect(toE164('081234567890')).toBe('+6281234567890')
    expect(toE164('81234567890')).toBe('+6281234567890')
    expect(toE164('+6281234567890')).toBe('+6281234567890')
    expect(toE164('0812-3456-7890')).toBe('+6281234567890')
  })

  it('validates legitimate Indonesian mobile numbers', () => {
    expect(isValidIndonesianPhone('081234567890')).toBe(true)
    expect(isValidIndonesianPhone('+6281234567890')).toBe(true)
    expect(isValidIndonesianPhone('085712345678')).toBe(true)
    // Invalid prefixes or too short
    expect(isValidIndonesianPhone('021123456')).toBe(false)
    expect(isValidIndonesianPhone('12345')).toBe(false)
  })

  it('formats for friendly display', () => {
    expect(formatPhoneDisplay('081234567890')).toBe('+62 812-3456-7890')
  })
})

