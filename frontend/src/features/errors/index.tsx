import React from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { ShieldAlert, ArrowLeft, Home } from 'lucide-react'

export const ForbiddenPage: React.FC = () => {
  const navigate = useNavigate()

  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 text-center dark:bg-slate-950">
      <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400">
        <ShieldAlert className="h-8 w-8" />
      </div>
      <h1 className="mt-6 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        403 — Akses Ditolak
      </h1>
      <p className="mt-2 max-w-md text-sm text-slate-600 dark:text-slate-400">
        Anda tidak memiliki izin (permission) yang sesuai untuk mengakses modul atau tindakan ini. Hubungi Administrator jika ini merupakan kekeliruan.
      </p>
      <div className="mt-6 flex items-center gap-3">
        <button
          onClick={() => navigate(-1)}
          className="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        >
          <ArrowLeft className="h-4 w-4" /> Kembali
        </button>
        <Link
          to="/"
          className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-xs hover:bg-indigo-700"
        >
          <Home className="h-4 w-4" /> Ke Beranda
        </Link>
      </div>
    </div>
  )
}

export const NotFoundPage: React.FC = () => {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 text-center dark:bg-slate-950">
      <div className="text-6xl font-extrabold text-indigo-600 dark:text-indigo-400">404</div>
      <h1 className="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        Halaman Tidak Ditemukan
      </h1>
      <p className="mt-2 max-w-md text-sm text-slate-600 dark:text-slate-400">
        Tautan yang Anda tuju mungkin salah ketik atau telah dipindahkan ke alamat lain.
      </p>
      <div className="mt-6">
        <Link
          to="/"
          className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-xs hover:bg-indigo-700"
        >
          <Home className="h-4 w-4" /> Kembali ke Dashboard
        </Link>
      </div>
    </div>
  )
}

export const ServerErrorPage: React.FC<{ error?: any; resetErrorBoundary?: () => void }> = ({
  error,
  resetErrorBoundary,
}) => {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 text-center dark:bg-slate-950">
      <div className="text-6xl font-extrabold text-rose-600">500</div>
      <h1 className="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        Terjadi Kesalahan Sistem
      </h1>
      <p className="mt-2 max-w-md text-sm text-slate-600 dark:text-slate-400">
        Layanan kami mengalami kendala teknis tak terduga. Silakan coba muat ulang atau laporkan ID korelasi ke tim DevOps.
      </p>
      {error?.message && (
        <pre className="mt-4 max-w-lg overflow-x-auto rounded-lg bg-slate-100 p-3 text-left font-mono text-xs text-rose-700 dark:bg-slate-900 dark:text-rose-400">
          {error.message}
        </pre>
      )}
      <div className="mt-6 flex items-center gap-3">
        {resetErrorBoundary ? (
          <button
            onClick={resetErrorBoundary}
            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          >
            Coba Lagi
          </button>
        ) : (
          <button
            onClick={() => window.location.reload()}
            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          >
            Muat Ulang Halaman
          </button>
        )}
      </div>
    </div>
  )
}

