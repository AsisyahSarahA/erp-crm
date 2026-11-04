import React from 'react'
import { toE164, isValidIndonesianPhone } from '../utils/phone'
import { cn } from '../utils/cn'
import { CheckCircle2 } from 'lucide-react'

interface PhoneInputProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange' | 'value'> {
  value: string
  onChange: (value: string) => void
  label?: string
  error?: string
  helperText?: string
}

export const PhoneInput: React.FC<PhoneInputProps> = ({
  value,
  onChange,
  label,
  error,
  helperText,
  className,
  disabled,
  ...props
}) => {
  // Extract number part without leading +62 or 0
  const cleanInput = (val: string) => {
    let clean = val.replace(/[^\d]/g, '')
    if (clean.startsWith('62')) clean = clean.slice(2)
    else if (clean.startsWith('0')) clean = clean.slice(1)
    return clean
  }

  const rawNumber = cleanInput(value || '')
  const isValid = isValidIndonesianPhone(value)

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const digits = e.target.value.replace(/[^\d]/g, '')
    const fullE164 = toE164(digits)
    onChange(fullE164)
  }

  return (
    <div className="w-full">
      {label && (
        <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          {label}
        </label>
      )}
      <div className="relative flex rounded-lg shadow-2xs">
        <span className="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-3 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
          🇮🇩 +62
        </span>
        <input
          type="tel"
          inputMode="numeric"
          disabled={disabled}
          value={rawNumber}
          onChange={handleInputChange}
          placeholder="812-3456-7890"
          className={cn(
            'w-full rounded-r-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 font-mono',
            'placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500',
            'dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 disabled:opacity-50',
            error && 'border-rose-500 focus:border-rose-500 focus:ring-rose-500',
            className
          )}
          {...props}
        />
        {isValid && (
          <div className="absolute right-3 top-2.5 text-emerald-600 dark:text-emerald-400">
            <CheckCircle2 className="h-4 w-4" />
          </div>
        )}
      </div>

      {error ? (
        <p className="mt-1 text-xs text-rose-600">{error}</p>
      ) : helperText ? (
        <p className="mt-1 text-xs text-slate-500">{helperText}</p>
      ) : null}
    </div>
  )
}

