import { create } from 'zustand'
import { apiClient } from '../api-client/client'

export interface User {
  id: number | string
  name: string
  email: string
  roles: string[]
  permissions: string[]
  avatar?: string
  company_id?: number | string
  customer_id?: number | string
}

interface TwoFactorChallenge {
  email: string
  session_token: string
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  isLoading: boolean
  is2FARequired: boolean
  twoFactorChallenge: TwoFactorChallenge | null

  // Actions
  login: (credentials: { email: string; password: string }) => Promise<{ requires2FA?: boolean }>
  verify2FA: (code: string) => Promise<void>
  logout: () => Promise<void>
  setUser: (user: User | null, token?: string | null) => void
  hasPermission: (permission: string) => boolean
  hasAnyPermission: (permissions: string[]) => boolean
  hasRole: (role: string) => boolean
  checkAuth: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  token: localStorage.getItem('astacode_token'),
  isAuthenticated: Boolean(localStorage.getItem('astacode_token')),
  isLoading: true,
  is2FARequired: false,
  twoFactorChallenge: null,

  login: async ({ email, password }) => {
    set({ isLoading: true })
    try {
      await apiClient.ensureCsrf()
      const res: any = await apiClient.post('/core/auth/login', { email, password }, { skipAuth: true })

      if (res.data?.requires_2fa || res.requires_2fa) {
        const challenge = {
          email,
          session_token: res.data?.session_token || res.session_token || '',
        }
        set({
          is2FARequired: true,
          twoFactorChallenge: challenge,
          isLoading: false,
        })
        return { requires2FA: true }
      }

      const token = res.data?.token || res.token
      const user = res.data?.user || res.user

      apiClient.setToken(token)
      set({
        user,
        token,
        isAuthenticated: true,
        is2FARequired: false,
        twoFactorChallenge: null,
        isLoading: false,
      })
      return { requires2FA: false }
    } catch (err) {
      set({ isLoading: false })
      throw err
    }
  },

  verify2FA: async (code: string) => {
    const { twoFactorChallenge } = get()
    if (!twoFactorChallenge) throw new Error('Sesi 2FA tidak ditemukan')

    set({ isLoading: true })
    try {
      const res: any = await apiClient.post(
        '/core/auth/2fa/verify',
        {
          session_token: twoFactorChallenge.session_token,
          code,
        },
        { skipAuth: true }
      )

      const token = res.data?.token || res.token
      const user = res.data?.user || res.user

      apiClient.setToken(token)
      set({
        user,
        token,
        isAuthenticated: true,
        is2FARequired: false,
        twoFactorChallenge: null,
        isLoading: false,
      })
    } catch (err) {
      set({ isLoading: false })
      throw err
    }
  },

  logout: async () => {
    try {
      await apiClient.post('/core/auth/logout')
    } catch {
      // Ignored
    } finally {
      apiClient.setToken(null)
      set({
        user: null,
        token: null,
        isAuthenticated: false,
        is2FARequired: false,
        twoFactorChallenge: null,
        isLoading: false,
      })
    }
  },

  setUser: (user, token = null) => {
    if (token) {
      apiClient.setToken(token)
    }
    set({
      user,
      token: token || get().token,
      isAuthenticated: Boolean(user),
      isLoading: false,
    })
  },

  hasPermission: (permission: string) => {
    const { user } = get()
    if (!user) return false
    if (user.roles.includes('super_admin') || user.roles.includes('owner')) return true
    return user.permissions.includes(permission) || user.permissions.includes('*')
  },

  hasAnyPermission: (permissions: string[]) => {
    const { user } = get()
    if (!user) return false
    if (user.roles.includes('super_admin') || user.roles.includes('owner')) return true
    return permissions.some((p) => user.permissions.includes(p) || user.permissions.includes('*'))
  },

  hasRole: (role: string) => {
    const { user } = get()
    if (!user) return false
    if (user.roles.includes('super_admin')) return true
    return user.roles.includes(role)
  },

  checkAuth: async () => {
    const token = apiClient.getToken()
    if (!token) {
      set({ user: null, isAuthenticated: false, isLoading: false })
      return
    }

    try {
      const res: any = await apiClient.get('/core/auth/me')
      const user = res.data?.user || res.data || res
      set({ user, isAuthenticated: true, isLoading: false })
    } catch {
      apiClient.setToken(null)
      set({ user: null, token: null, isAuthenticated: false, isLoading: false })
    }
  },
}))

// Listen for global unauthorized events
if (typeof window !== 'undefined') {
  window.addEventListener('astacode:unauthorized', () => {
    useAuthStore.getState().logout()
  })
}

