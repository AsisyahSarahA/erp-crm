import React, { Component, ErrorInfo, ReactNode } from 'react'
import { AlertCircle, RefreshCw, Home } from 'lucide-react'
import { Link } from 'react-router-dom'

interface Props {
  children: ReactNode
  featureName?: string
}

interface State {
  hasError: boolean
  error: Error | null
}

export class FeatureErrorBoundary extends Component<Props, State> {
  public state: State = {
    hasError: false,
    error: null,
  }

  public static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error }
  }

  public componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    console.error('FeatureErrorBoundary caught an error:', error, errorInfo)
  }

  public handleReset = () => {
    this.setState({ hasError: false, error: null })
  }

  public render() {
    if (this.state.hasError) {
      return (
        <div className="flex min-h-[400px] flex-col items-center justify-center rounded-2xl border border-rose-200 bg-rose-50/50 p-8 text-center dark:border-rose-900/50 dark:bg-rose-950/20">
          <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400">
            <AlertCircle className="h-6 w-6" />
          </div>
          <h2 className="mt-4 text-base font-bold text-slate-900 dark:text-slate-100">
            Kendala Teknis pada Fitur {this.props.featureName || 'Ini'}
          </h2>
          <p className="mt-1 max-w-md text-xs text-slate-600 dark:text-slate-400">
            Komponen ini mengalami error saat proses rendering. Anda dapat mencoba memuat ulang fitur atau kembali ke Dashboard.
          </p>

          {this.state.error?.message && (
            <pre className="mt-3 max-w-lg overflow-x-auto rounded-lg bg-white/80 p-2.5 font-mono text-[11px] text-rose-700 dark:bg-slate-900/80 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
              {this.state.error.message}
            </pre>
          )}

          <div className="mt-5 flex items-center gap-3">
            <button
              onClick={this.handleReset}
              className="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-indigo-700"
            >
              <RefreshCw className="h-3.5 w-3.5" /> Coba Lagi
            </button>
            <Link
              to="/dashboard"
              className="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
            >
              <Home className="h-3.5 w-3.5" /> Dashboard
            </Link>
          </div>
        </div>
      )
    }

    return this.props.children
  }
}

