import React from 'react'
import { Link } from 'react-router-dom'
import { Clock, ExternalLink, ArrowLeft, Layers, Shield, Terminal } from 'lucide-react'

export interface PlaceholderConfig {
  title: string
  module: string
  taskId: string
  prdRef: string
  description: string
  endpoints?: string[]
  permissions?: string[]
}

export const PlaceholderPage: React.FC<PlaceholderConfig> = ({
  title,
  module,
  taskId,
  prdRef,
  description,
  endpoints = [],
  permissions = [],
}) => {
  return (
    <div className="space-y-6 pb-12">
      {/* Top Banner Notice */}
      <div className="rounded-2xl border border-amber-200 bg-amber-50/60 p-6 shadow-2xs dark:border-amber-900/50 dark:bg-amber-950/20">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex items-start gap-3">
            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
              <Clock className="h-5 w-5" />
            </div>
            <div>
              <div className="flex items-center gap-2 flex-wrap">
                <span className="rounded-md bg-amber-200/60 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:bg-amber-900/80 dark:text-amber-300">
                  {module}
                </span>
                <span className="rounded-md bg-slate-200 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-300">
                  {prdRef}
                </span>
              </div>
              <h1 className="mt-1 text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {title}
              </h1>
              <p className="mt-1 text-xs font-semibold text-amber-700 dark:text-amber-400">
                Belum diimplementasikan — task {taskId}
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <Link
              to="/dashboard"
              className="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
            >
              <ArrowLeft className="h-3.5 w-3.5" /> Dashboard
            </Link>
            <Link
              to="/dev/ui-kit"
              className="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-indigo-700"
            >
              <ExternalLink className="h-3.5 w-3.5" /> Lihat UI Kit
            </Link>
          </div>
        </div>
      </div>

      {/* Specification & Implementation Blueprint */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs dark:border-slate-800 dark:bg-slate-900 md:col-span-2 space-y-4">
          <div className="flex items-center gap-2">
            <Layers className="h-4 w-4 text-indigo-500" />
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">
              Spesifikasi PRD & Rencana Implementasi
            </h3>
          </div>
          <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            {description}
          </p>

          <div className="pt-3 border-t border-slate-100 dark:border-slate-800">
            <h4 className="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-1.5">
              <Terminal className="h-3.5 w-3.5 text-slate-400" />
              Endpoint Backend Gateway Terkait:
            </h4>
            {endpoints.length > 0 ? (
              <ul className="space-y-1 font-mono text-xs">
                {endpoints.map((ep, idx) => (
                  <li
                    key={idx}
                    className="rounded-md bg-slate-50 px-2.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {ep}
                  </li>
                ))}
              </ul>
            ) : (
              <p className="text-xs text-slate-400">Endpoint didefinisikan pada task {taskId}.</p>
            )}
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <div className="flex items-center gap-2">
            <Shield className="h-4 w-4 text-emerald-500" />
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">
              Otorisasi & Keamanan
            </h3>
          </div>

          <div className="space-y-3 text-xs">
            <div>
              <span className="text-slate-400 block mb-1">Izin Diperlukan:</span>
              {permissions.length > 0 ? (
                <div className="flex flex-wrap gap-1.5">
                  {permissions.map((p, idx) => (
                    <span
                      key={idx}
                      className="rounded bg-indigo-50 px-2 py-0.5 font-mono text-[11px] font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300"
                    >
                      {p}
                    </span>
                  ))}
                </div>
              ) : (
                <span className="text-slate-500 italic">Izin umum terotentikasi</span>
              )}
            </div>

            <div className="pt-2 border-t border-slate-100 dark:border-slate-800">
              <span className="text-slate-400 block mb-1">Status Pengerjaan:</span>
              <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                <span className="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse" />
                Dalam Antrian Task ({taskId})
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

