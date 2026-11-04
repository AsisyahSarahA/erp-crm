import React, { useState } from 'react'
import { formatIDR, parseIDR, terbilang } from '../utils/formatters'
import { cn } from '../utils/cn'

interface CurrencyInputProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange' | 'value'> {
  value: number | string | null | undefined
  onChange: (value: number) => void
  label?: string
  error?: string
  showTerbilang?: boolean
  helperText?: string
}

export const CurrencyInput: React.FC<CurrencyInputProps> = ({
  value,
  onChange,
  label,
  error,
  showTerbilang = true,
  helperText,
  className,
  disabled,
  ...props
}) => {
  const numericValue = typeof value === 'string' ? parseIDR(value) : (value || 0)
  const [internalText, setInternalText] = useState<string | null>(null)

  const displayValue = internalText !== null ? internalText : formatIDR(numericValue)

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const rawDigits = e.target.value.replace(/[^\d]/g, '')
    const num = rawDigits ? parseInt(rawDigits, 10) : 0
    setInternalText(formatIDR(num))
    onChange(num)
  }

  const handleBlur = () => {
    setInternalText(null)
  }

  const terbilangText = numericValue > 0 ? terbilang(numericValue) : ''

  return (
    <div className="w-full">
      {label && (
        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          {label}
        </label>
      )}
      <div className="relative">
        <input
          type="text"
          inputMode="numeric"
          disabled={disabled}
          value={displayValue}
          onChange={handleChange}
          onBlur={handleBlur}
          className={cn(
            'w-full font-mono rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900',
            'placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500',
            'dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800',
            error && 'border-rose-500 focus:border-rose-500 focus:ring-rose-500',
            className
          )}
          {...props}
        />
      </div>

      {showTerbilang && terbilangText && (
        <p className="mt-1 text-xs italic text-indigo-600 dark:text-indigo-400 font-medium">
          {terbilangText}
        </p>
      )}

      {error ? (
        <p className="mt-1 text-xs text-rose-600">{error}</p>
      ) : helperText && !terbilangText ? (
        <p className="mt-1 text-xs text-slate-500">{helperText}</p>
      ) : null}
    </div>
  )
}
