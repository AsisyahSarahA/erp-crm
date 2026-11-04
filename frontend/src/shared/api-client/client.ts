/**
 * ASTACODE ERP API Client (Fetch-based with RFC 7807 problem details)
 */

export interface ApiResponse<T = any> {
  success: boolean
  data: T
  meta?: {
    current_page?: number
    last_page?: number
    per_page?: number
    total?: number
    [key: string]: any
  }
  error?: {
    code: string
    message: string
    details?: Record<string, string[]>
  }
}

export interface ApiProblemDetails {
  type: string
  title: string
  status: number
  detail: string
  instance?: string
  errors?: Record<string, string[]>
  correlation_id?: string
}

export class ApiError extends Error {
  public status: number
  public problem: ApiProblemDetails
  public fieldErrors: Record<string, string[]>

  constructor(status: number, problem: ApiProblemDetails) {
    super(problem.detail || problem.title || `API Error ${status}`)
    this.name = 'ApiError'
    this.status = status
    this.problem = problem
    this.fieldErrors = problem.errors || {}
  }
}

const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1'

function generateUUID(): string {
  if (typeof crypto !== 'undefined' && crypto.randomUUID) {
    return crypto.randomUUID()
  }
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
    const r = (Math.random() * 16) | 0
    const v = c === 'x' ? r : (r & 0x3) | 0x8
    return v.toString(16)
  })
}

export interface RequestOptions extends RequestInit {
  idempotencyKey?: string
  correlationId?: string
  skipAuth?: boolean
}

class ApiClient {
  private token: string | null = null

  constructor() {
    this.token = localStorage.getItem('astacode_token')
  }

  public setToken(token: string | null) {
    this.token = token
    if (token) {
      localStorage.setItem('astacode_token', token)
    } else {
      localStorage.removeItem('astacode_token')
    }
  }

  public getToken(): string | null {
    return this.token || localStorage.getItem('astacode_token')
  }

  /**
   * Initialize Sanctum CSRF cookie for SPA mode
   */
  public async ensureCsrf(): Promise<void> {
    try {
      await fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        credentials: 'include',
      })
    } catch {
      // Non-fatal if using Bearer token directly
    }
  }

  public async request<T = any>(endpoint: string, options: RequestOptions = {}): Promise<T> {
    const url = endpoint.startsWith('http') ? endpoint : `${BASE_URL}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`

    const headers = new Headers(options.headers || {})
    headers.set('Accept', 'application/json')
    if (!headers.has('Content-Type') && !(options.body instanceof FormData)) {
      headers.set('Content-Type', 'application/json')
    }

    // Set Correlation ID
    const correlationId = options.correlationId || generateUUID()
    headers.set('X-Correlation-Id', correlationId)

    // Set Idempotency Key for mutating requests
    const method = (options.method || 'GET').toUpperCase()
    if (['POST', 'PUT', 'PATCH'].includes(method)) {
      headers.set('Idempotency-Key', options.idempotencyKey || generateUUID())
    }

    // Set Auth Token
    const currentToken = this.getToken()
    if (currentToken && !options.skipAuth) {
      headers.set('Authorization', `Bearer ${currentToken}`)
    }

    let response: Response
    try {
      response = await fetch(url, {
        ...options,
        headers,
        credentials: 'include',
      })
    } catch (err: any) {
      // Retry once for GET if network glitch
      if (method === 'GET') {
        try {
          response = await fetch(url, { ...options, headers, credentials: 'include' })
        } catch (retryErr: any) {
          throw new ApiError(0, {
            type: 'https://astacode.com/errors/network-error',
            title: 'Koneksi Terputus',
            status: 0,
            detail: retryErr.message || 'Gagal menghubungi server.',
          })
        }
      } else {
        throw new ApiError(0, {
          type: 'https://astacode.com/errors/network-error',
          title: 'Koneksi Terputus',
          status: 0,
          detail: err.message || 'Gagal menghubungi server.',
        })
      }
    }

    if (!response.ok) {
      let problem: ApiProblemDetails
      try {
        const errorBody = await response.json()
        problem = {
          type: errorBody.type || errorBody.error?.code || 'about:blank',
          title: errorBody.title || errorBody.message || errorBody.error?.message || 'Terjadi kesalahan sistem',
          status: response.status,
          detail: errorBody.detail || errorBody.message || errorBody.error?.message || response.statusText,
          errors: errorBody.errors || errorBody.error?.details || {},
          correlation_id: correlationId,
        }
      } catch {
        problem = {
          type: 'about:blank',
          title: response.statusText || 'Terjadi kesalahan',
          status: response.status,
          detail: `Server mengembalikan status HTTP ${response.status}`,
          correlation_id: correlationId,
        }
      }

      if (response.status === 401) {
        this.setToken(null)
        window.dispatchEvent(new CustomEvent('astacode:unauthorized'))
      } else if (response.status === 403) {
        window.dispatchEvent(new CustomEvent('astacode:forbidden', { detail: problem }))
      }

      throw new ApiError(response.status, problem)
    }

    if (response.status === 204) {
      return {} as T
    }

    const json = await response.json()
    return json as T
  }

  public get<T = any>(endpoint: string, options?: RequestOptions) {
    return this.request<T>(endpoint, { ...options, method: 'GET' })
  }

  public post<T = any>(endpoint: string, body?: any, options?: RequestOptions) {
    return this.request<T>(endpoint, {
      ...options,
      method: 'POST',
      body: body instanceof FormData ? body : JSON.stringify(body),
    })
  }

  public put<T = any>(endpoint: string, body?: any, options?: RequestOptions) {
    return this.request<T>(endpoint, {
      ...options,
      method: 'PUT',
      body: body instanceof FormData ? body : JSON.stringify(body),
    })
  }

  public patch<T = any>(endpoint: string, body?: any, options?: RequestOptions) {
    return this.request<T>(endpoint, {
      ...options,
      method: 'PATCH',
      body: body instanceof FormData ? body : JSON.stringify(body),
    })
  }

  public delete<T = any>(endpoint: string, options?: RequestOptions) {
    return this.request<T>(endpoint, { ...options, method: 'DELETE' })
  }
}

export const apiClient = new ApiClient()

