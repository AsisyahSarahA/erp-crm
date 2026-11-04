export type ToastType = 'success' | 'error' | 'info' | 'warning'

export interface ToastMessage {
  id: string
  type: ToastType
  title: string
  message?: string
  duration?: number
}

type ToastListener = (toast: ToastMessage) => void
export const toastListeners: ToastListener[] = []

export const toast = {
  show: (type: ToastType, title: string, message?: string, duration = 4000) => {
    const id = `toast-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`
    toastListeners.forEach((listener) => listener({ id, type, title, message, duration }))
  },
  success: (title: string, message?: string) => toast.show('success', title, message),
  error: (title: string, message?: string) => toast.show('error', title, message),
  info: (title: string, message?: string) => toast.show('info', title, message),
  warning: (title: string, message?: string) => toast.show('warning', title, message),
}

