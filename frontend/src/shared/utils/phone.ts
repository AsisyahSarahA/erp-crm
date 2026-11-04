/**
 * Standardize and validate Indonesian phone numbers (+62 format)
 */

export function toE164(phone: string): string {
  if (!phone) return ''
  // Strip all non-digit characters except leading +
  let cleaned = phone.trim().replace(/[^\d+]/g, '')

  // Remove leading +
  if (cleaned.startsWith('+')) {
    cleaned = cleaned.slice(1)
  }

  // Handle leading 0 -> replace with 62
  if (cleaned.startsWith('0')) {
    cleaned = '62' + cleaned.slice(1)
  } else if (!cleaned.startsWith('62')) {
    // If user typed 8123456789 directly
    cleaned = '62' + cleaned
  }

  return `+${cleaned}`
}

export function formatPhoneDisplay(phone: string): string {
  if (!phone) return ''
  const e164 = toE164(phone)
  // Format +6281234567890 -> +62 812-3456-7890
  const digits = e164.replace('+62', '')
  if (digits.length <= 4) return `+62 ${digits}`
  if (digits.length <= 8) return `+62 ${digits.slice(0, 3)}-${digits.slice(3)}`
  return `+62 ${digits.slice(0, 3)}-${digits.slice(3, 7)}-${digits.slice(7)}`
}

export function isValidIndonesianPhone(phone: string): boolean {
  if (!phone) return false
  const e164 = toE164(phone)
  // E.164 for ID must start with +628 and have 10-15 total digits (+ and 62 + 8 + 8..12 digits)
  const regex = /^\+628[1-9][0-9]{7,11}$/
  return regex.test(e164)
}

