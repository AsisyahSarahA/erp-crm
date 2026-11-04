import React, { useState, useEffect } from 'react'
import { CheckCircle, AlertCircle, Info, X } from 'lucide-react'
import { cn } from '../utils/cn'
import { toastListeners, type ToastMessage } from './toast'

export const ToastContainer: React.FC = () => {
  const [toasts, setToasts] = useState<ToastMessage[]>([])

  useEffect(() => {
    const handleToast = (newToast: ToastMessage) => {
      setToasts((prev) => [...prev, newToast])

      if (newToast.duration && newToast.duration > 0) {
        setTimeout(() => {
          setToasts((prev) => prev.filter((t) => t.id !== newToast.id))
        }, newToast.duration)
      }
    }

    toastListeners.push(handleToast)
    return () => {
      const idx = toastListeners.indexOf(handleToast)
      if (idx !== -1) toastListeners.splice(idx, 1)
    }
  }, [])

  const removeToast = (id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id))
  }

  if (toasts.length === 0) return null

  return (
    <div className="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
      {toasts.map((t) => {
        return (
          <div
            key={t.id}
            className={cn(
              'pointer-events-auto flex items-start gap-3 rounded-xl p-4 shadow-lg border transition-all duration-200',
              'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800'
            )}
          >
            <div className="shrink-0 mt-0.5">
              {t.type === 'success' && <CheckCircle className="h-5 w-5 text-emerald-500" />}
              {t.type === 'error' && <AlertCircle className="h-5 w-5 text-rose-500" />}
              {t.type === 'info' && <Info className="h-5 w-5 text-indigo-500" />}
              {t.type === 'warning' && <AlertCircle className="h-5 w-5 text-amber-500" />}
            </div>

            <div className="flex-1">
              <h4 className="text-xs font-bold text-slate-900 dark:text-slate-100">{t.title}</h4>
              {t.message && <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-400">{t.message}</p>}
            </div>

            <button
              onClick={() => removeToast(t.id)}
              className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
            >
              <X className="h-4 w-4" />
            </button>
          </div>
        )
      })}
    </div>
  )
}
