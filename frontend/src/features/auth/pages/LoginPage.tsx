import React, { useState } from 'react'
import { useNavigate, useLocation } from 'react-router-dom'
import { useAuthStore } from '@/shared/auth/authStore'
import { Shield, Lock, Mail, ArrowRight, KeyRound, AlertCircle } from 'lucide-react'

export const LoginPage: React.FC = () => {
  const navigate = useNavigate()
  const location = useLocation()
  const { login, verify2FA, is2FARequired, twoFactorChallenge } = useAuthStore()

  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [totpCode, setTotpCode] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [errorMessage, setErrorMessage] = useState<string | null>(null)

  const from = (location.state as any)?.from?.pathname || '/'

  const handleLoginSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setErrorMessage(null)
    setIsLoading(true)

    try {
      const result = await login({ email, password })
      if (!result.requires2FA) {
        navigate(from, { replace: true })
      }
    } catch (err: any) {
      setErrorMessage(err.message || 'Email atau kata sandi tidak valid.')
    } finally {
      setIsLoading(false)
    }
  }

  const handleTotpSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setErrorMessage(null)
    setIsLoading(true)

    try {
      await verify2FA(totpCode)
      navigate(from, { replace: true })
    } catch (err: any) {
      setErrorMessage(err.message || 'Kode 2FA tidak valid atau telah kedaluwarsa.')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="flex min-h-screen bg-slate-50 dark:bg-slate-950">
      {/* Left Banner */}
      <div className="hidden lg:flex lg:w-1/2 flex-col justify-between bg-slate-900 p-12 text-white">
        <div>
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 font-bold text-white shadow-lg">
              A
            </div>
            <span className="text-xl font-bold tracking-tight">ASTACODE ERP</span>
          </div>
          <div className="mt-20">
            <span className="inline-flex items-center rounded-full bg-indigo-950/60 px-3 py-1 text-xs font-medium text-indigo-300 ring-1 ring-inset ring-indigo-500/30">
              Enterprise Suite v2.0
            </span>
            <h2 className="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">
              Satu Platform Terintegrasi untuk Software House
            </h2>
            <p className="mt-4 text-base text-slate-400">
              Sinkronisasi data tanpa batas: CRM & Sales Pipeline → Estimasi RAB → Project Backlog PM → Invoice & Cashflow Finance.
            </p>
          </div>
        </div>

        <div className="text-xs text-slate-500">
          © {new Date().getFullYear()} ASTACODE. Dilindungi hak cipta & sistem keamanan multi-tier.
        </div>
      </div>

      {/* Right Login / 2FA Form */}
      <div className="flex flex-1 flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
        <div className="mx-auto w-full max-w-md">
          {!is2FARequired ? (
            <div>
              <div className="flex items-center gap-2 lg:hidden mb-8">
                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white">
                  A
                </div>
                <span className="text-lg font-bold text-slate-900 dark:text-white">ASTACODE ERP</span>
              </div>

              <h2 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                Masuk ke Akun Anda
              </h2>
              <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Gunakan kredensial internal yang diberikan oleh administrator.
              </p>

              {errorMessage && (
                <div className="mt-4 flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-400">
                  <AlertCircle className="h-4 w-4 shrink-0" />
                  <span>{errorMessage}</span>
                </div>
              )}

              <form onSubmit={handleLoginSubmit} className="mt-6 space-y-4">
                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Alamat Email
                  </label>
                  <div className="relative mt-1">
                    <Mail className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="nama@astacode.com"
                      className="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Kata Sandi
                  </label>
                  <div className="relative mt-1">
                    <Lock className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                    <input
                      type="password"
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="••••••••"
                      className="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    />
                  </div>
                </div>

                <div className="flex items-center justify-between text-xs">
                  <label className="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <input type="checkbox" className="rounded border-slate-300 text-indigo-600" />
                    Ingat perangkat ini
                  </label>
                  <a href="#forgot" className="font-semibold text-indigo-600 hover:text-indigo-500">
                    Lupa sandi?
                  </a>
                </div>

                <button
                  type="submit"
                  disabled={isLoading}
                  className="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 py-2.5 px-4 text-sm font-semibold text-white shadow-xs hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                  {isLoading ? 'Memverifikasi...' : 'Masuk ke Platform'}
                  <ArrowRight className="h-4 w-4" />
                </button>
              </form>
            </div>
          ) : (
            <div>
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                <Shield className="h-6 w-6" />
              </div>
              <h2 className="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                Verifikasi 2 Langkah (TOTP)
              </h2>
              <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Masukkan 6 digit kode dari aplikasi authenticator untuk akun {twoFactorChallenge?.email}.
              </p>

              {errorMessage && (
                <div className="mt-4 flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-400">
                  <AlertCircle className="h-4 w-4 shrink-0" />
                  <span>{errorMessage}</span>
                </div>
              )}

              <form onSubmit={handleTotpSubmit} className="mt-6 space-y-4">
                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Kode Keamanan 6 Digit
                  </label>
                  <div className="relative mt-1">
                    <KeyRound className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                    <input
                      type="text"
                      inputMode="numeric"
                      maxLength={6}
                      pattern="[0-9]{6}"
                      required
                      autoFocus
                      value={totpCode}
                      onChange={(e) => setTotpCode(e.target.value.replace(/\D/g, ''))}
                      placeholder="123456"
                      className="w-full tracking-widest text-center font-mono text-lg rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    />
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={isLoading || totpCode.length !== 6}
                  className="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 py-2.5 px-4 text-sm font-semibold text-white shadow-xs hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  {isLoading ? 'Memvalidasi...' : 'Verifikasi & Masuk'}
                </button>
              </form>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}

