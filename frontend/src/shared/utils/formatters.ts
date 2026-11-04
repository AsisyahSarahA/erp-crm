import { format, parseISO } from 'date-fns'

/**
 * Format number to Indonesian Rupiah currency string: Rp 15.000.000
 */
export function formatIDR(amount: number | string | null | undefined): string {
  if (amount === null || amount === undefined || amount === '') return 'Rp 0'
  const numeric = typeof amount === 'string' ? parseFloat(amount.replace(/[^\d.-]/g, '')) || 0 : amount
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
    minimumFractionDigits: 0,
  }).format(numeric)
}

/**
 * Parse IDR formatted string back to raw number
 */
export function parseIDR(formatted: string): number {
  if (!formatted) return 0
  const clean = formatted.replace(/[^\d]/g, '')
  return clean ? parseInt(clean, 10) : 0
}

/**
 * Convert number to Indonesian Words (Terbilang)
 * e.g., 2500000 -> "Dua juta lima ratus ribu rupiah"
 */
const SATUAN = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas']

function terbilangHelper(n: number): string {
  n = Math.floor(Math.abs(n))
  if (n < 12) return SATUAN[n]
  if (n < 20) return `${terbilangHelper(n - 10)} belas`
  if (n < 100) return `${terbilangHelper(Math.floor(n / 10))} puluh ${terbilangHelper(n % 10)}`.trim()
  if (n < 200) return `seratus ${terbilangHelper(n - 100)}`.trim()
  if (n < 1000) return `${terbilangHelper(Math.floor(n / 100))} ratus ${terbilangHelper(n % 100)}`.trim()
  if (n < 2000) return `seribu ${terbilangHelper(n - 1000)}`.trim()
  if (n < 1000000) return `${terbilangHelper(Math.floor(n / 1000))} ribu ${terbilangHelper(n % 1000)}`.trim()
  if (n < 1000000000) return `${terbilangHelper(Math.floor(n / 1000000))} juta ${terbilangHelper(n % 1000000)}`.trim()
  if (n < 1000000000000) return `${terbilangHelper(Math.floor(n / 1000000000))} milyar ${terbilangHelper(n % 1000000000)}`.trim()
  if (n < 1000000000000000) return `${terbilangHelper(Math.floor(n / 1000000000000))} triliun ${terbilangHelper(n % 1000000000000)}`.trim()
  return `${n}`
}

export function terbilang(n: number): string {
  if (n === 0) return 'Nol rupiah'
  const text = terbilangHelper(n)
  const capitalized = text.charAt(0).toUpperCase() + text.slice(1)
  return `${capitalized} rupiah`
}

/**
 * Format ISO date string or Date object to readable Indonesian format
 */
export function formatDate(dateInput: string | Date | null | undefined, pattern = 'dd MMM yyyy'): string {
  if (!dateInput) return '-'
  try {
    const d = typeof dateInput === 'string' ? parseISO(dateInput) : dateInput
    return format(d, pattern)
  } catch {
    return String(dateInput)
  }
}

export function formatDateTime(dateInput: string | Date | null | undefined): string {
  return formatDate(dateInput, 'dd MMM yyyy, HH:mm')
}

