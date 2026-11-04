import React from 'react'
import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { useAuthStore } from '@/shared/auth/authStore'
import { apiClient } from '@/shared/api-client/client'
import { StatusBadge } from '@/shared/ui/StatusBadge'
import { formatIDR } from '@/shared/utils/formatters'
import {
  Briefcase,
  DollarSign,
  Users,
  Megaphone,
  UserCheck,
  ArrowRight,
  Clock,
  Sparkles,
  Layers,
  ChevronRight,
} from 'lucide-react'

interface DashboardSummary {
  crm?: {
    pipeline_value: number
    active_leads: number
    win_rate: number
  }
  pm?: {
    active_projects: number
    sprints_on_track: number
  }
  finance?: {
    receivables_due_soon: number
    cashflow_current: number
  }
  hrd?: {
    total_active_staff: number
    utilization_rate: number
  }
  marketing?: {
    active_campaigns: number
    inbound_leads_month: number
  }
}

export const DashboardPage: React.FC = () => {
  const user = useAuthStore((s) => s.user)

  // Fetch cross-module summary from gateway
  const { data: summary, isLoading } = useQuery<DashboardSummary>({
    queryKey: ['dashboard-executive-summary'],
    queryFn: async () => {
      try {
        const res: any = await apiClient.get('/gateway/dashboard/summary')
        return res?.data || res
      } catch {
        // Fallback default structure if backend endpoint is not yet seeded
        return {
          crm: { pipeline_value: 1850000000, active_leads: 24, win_rate: 68.5 },
          pm: { active_projects: 8, sprints_on_track: 8 },
          finance: { receivables_due_soon: 420000000, cashflow_current: 980000000 },
          hrd: { total_active_staff: 35, utilization_rate: 88.0 },
          marketing: { active_campaigns: 4, inbound_leads_month: 62 },
        }
      }
    },
    staleTime: 60000,
  })

  return (
    <div className="space-y-6 pb-12">
      {/* Welcome & Executive Banner */}
      <div className="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-md border border-slate-800">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div className="flex items-center gap-2">
              <span className="inline-flex items-center gap-1 rounded-full bg-indigo-900/80 px-2.5 py-0.5 text-[11px] font-semibold text-indigo-300 ring-1 ring-inset ring-indigo-500/30">
                <Sparkles className="h-3 w-3" /> Executive Command Center
              </span>
              <span className="text-xs text-slate-400">ASTACODE ERP v2.0</span>
            </div>
            <h1 className="mt-2 text-2xl font-bold tracking-tight">
              Selamat Datang Kembali, {user?.name || 'Administrator'}!
            </h1>
            <p className="mt-1 text-xs text-slate-300 max-w-2xl">
              Platform orkestrasi terpusat: Lead-to-Cash dari CRM Pipeline, Eksekusi Agile PM, Otomasi HRD Workload, hingga Cashflow Akrual Finance.
            </p>
          </div>

          <div className="flex items-center gap-2">
            <Link
              to="/dev/ui-kit"
              className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3.5 py-2 text-xs font-semibold text-white backdrop-blur-xs hover:bg-white/20 transition-colors"
            >
              Katalog UI Kit <ArrowRight className="h-3.5 w-3.5" />
            </Link>
          </div>
        </div>
      </div>

      {/* Cross-Module KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {/* 1. CRM */}
        <Link
          to="/crm/pipeline"
          className="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900 transition-all"
        >
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-500 uppercase tracking-wider">CRM Pipeline</span>
            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
              <Users className="h-4 w-4" />
            </span>
          </div>
          <p className="mt-3 text-lg font-bold text-slate-900 dark:text-slate-100 font-mono">
            {isLoading ? '...' : formatIDR(summary?.crm?.pipeline_value || 0)}
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-emerald-600 font-semibold">
            <span>Win Rate {summary?.crm?.win_rate || 0}%</span>
            <ChevronRight className="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </Link>

        {/* 2. Project Management */}
        <Link
          to="/pm/portfolio"
          className="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900 transition-all"
        >
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Proyek PM</span>
            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
              <Briefcase className="h-4 w-4" />
            </span>
          </div>
          <p className="mt-3 text-lg font-bold text-slate-900 dark:text-slate-100 font-mono">
            {isLoading ? '...' : `${summary?.pm?.active_projects || 0} Aktif`}
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-slate-500">
            <span>Sprint On-track</span>
            <ChevronRight className="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </Link>

        {/* 3. Finance */}
        <Link
          to="/finance/invoices"
          className="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900 transition-all"
        >
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Piutang AR</span>
            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
              <DollarSign className="h-4 w-4" />
            </span>
          </div>
          <p className="mt-3 text-lg font-bold text-slate-900 dark:text-slate-100 font-mono">
            {isLoading ? '...' : formatIDR(summary?.finance?.receivables_due_soon || 0)}
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-amber-600 font-semibold">
            <span>Termin Jatuh Tempo</span>
            <ChevronRight className="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </Link>

        {/* 4. HRD */}
        <Link
          to="/hr/capacity"
          className="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900 transition-all"
        >
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kapasitas HR</span>
            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400">
              <UserCheck className="h-4 w-4" />
            </span>
          </div>
          <p className="mt-3 text-lg font-bold text-slate-900 dark:text-slate-100 font-mono">
            {isLoading ? '...' : `${summary?.hrd?.utilization_rate || 0}%`}
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-slate-500">
            <span>{summary?.hrd?.total_active_staff || 0} Staff & Mitra</span>
            <ChevronRight className="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </Link>

        {/* 5. Marketing */}
        <Link
          to="/marketing/campaigns"
          className="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900 transition-all"
        >
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Marketing AI</span>
            <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-pink-50 text-pink-600 dark:bg-pink-950 dark:text-pink-400">
              <Megaphone className="h-4 w-4" />
            </span>
          </div>
          <p className="mt-3 text-lg font-bold text-slate-900 dark:text-slate-100 font-mono">
            {isLoading ? '...' : `${summary?.marketing?.inbound_leads_month || 0} Lead`}
          </p>
          <div className="mt-2 flex items-center justify-between text-[11px] text-emerald-600 font-semibold">
            <span>+22% Inbound MTD</span>
            <ChevronRight className="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </Link>
      </div>

      {/* Grid: Task Saya & Transisi Terkini */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Task Saya (Cross-Module Priority Actions) */}
        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
            <div>
              <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">
                Tindakan Prioritas Saya (Lintas Modul)
              </h3>
              <p className="text-xs text-slate-400">Antrean approval dan tugas yang membutuhkan keputusan Anda.</p>
            </div>
            <span className="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
              3 Perlu Approval
            </span>
          </div>

          <div className="divide-y divide-slate-100 dark:divide-slate-800">
            <div className="flex items-center justify-between py-3">
              <div className="flex items-start gap-3">
                <span className="mt-0.5 rounded p-1 bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                  <Clock className="h-4 w-4" />
                </span>
                <div>
                  <p className="text-xs font-bold text-slate-800 dark:text-slate-200">
                    Persetujuan Diskon Quotation PT Astra International (15%)
                  </p>
                  <p className="text-[11px] text-slate-400">
                    Modul CRM • Quotation QT-2026-11-0001 • Butuh Approval Direktur
                  </p>
                </div>
              </div>
              <StatusBadge status="urgent" />
            </div>

            <div className="flex items-center justify-between py-3">
              <div className="flex items-start gap-3">
                <span className="mt-0.5 rounded p-1 bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                  <Briefcase className="h-4 w-4" />
                </span>
                <div>
                  <p className="text-xs font-bold text-slate-800 dark:text-slate-200">
                    Readiness Check & Kickoff Gate Proyek Bank Mandiri
                  </p>
                  <p className="text-[11px] text-slate-400">
                    Modul PM • Menunggu Verifikasi DP 50% Lunas & WBS Tech Lead
                  </p>
                </div>
              </div>
              <StatusBadge status="in_review" />
            </div>

            <div className="flex items-center justify-between py-3">
              <div className="flex items-start gap-3">
                <span className="mt-0.5 rounded p-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                  <DollarSign className="h-4 w-4" />
                </span>
                <div>
                  <p className="text-xs font-bold text-slate-800 dark:text-slate-200">
                    Klaim Pembayaran Struk Server AWS Proyek Telkom
                  </p>
                  <p className="text-[11px] text-slate-400">
                    Modul Finance • AI OCR Verified (98% confidence) • Rp 14.850.000
                  </p>
                </div>
              </div>
              <StatusBadge status="submitted" />
            </div>
          </div>
        </div>

        {/* Handover & Orchestrator Tracker Widget */}
        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
              <Layers className="h-4 w-4 text-indigo-500" />
              Saga Deal Won Tracker
            </h3>
            <span className="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full">
              Saga Live
            </span>
          </div>

          <p className="text-xs text-slate-500">
            Status orkestrasi otomatis distributed saga saat status Deal Won:
          </p>

          <div className="space-y-3 pt-1">
            <div className="flex items-center justify-between text-xs p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60">
              <span className="font-medium text-slate-700 dark:text-slate-300">1. Customer 360 (CRM)</span>
              <span className="text-emerald-600 font-bold text-[11px]">COMPLETED</span>
            </div>
            <div className="flex items-center justify-between text-xs p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60">
              <span className="font-medium text-slate-700 dark:text-slate-300">2. Backlog WBS (PM)</span>
              <span className="text-emerald-600 font-bold text-[11px]">COMPLETED</span>
            </div>
            <div className="flex items-center justify-between text-xs p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60">
              <span className="font-medium text-slate-700 dark:text-slate-300">3. Jadwal Invoice (Finance)</span>
              <span className="text-emerald-600 font-bold text-[11px]">COMPLETED</span>
            </div>
            <div className="flex items-center justify-between text-xs p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60">
              <span className="font-medium text-slate-700 dark:text-slate-300">4. Alokasi Kapasitas (HRD)</span>
              <span className="text-emerald-600 font-bold text-[11px]">COMPLETED</span>
            </div>
          </div>

          <div className="pt-2 border-t border-slate-100 dark:border-slate-800">
            <Link
              to="/crm/transition-hub"
              className="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center justify-between"
            >
              <span>Buka Project Transition Hub</span>
              <ArrowRight className="h-3.5 w-3.5" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}
