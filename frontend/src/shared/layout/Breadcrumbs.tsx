import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { ChevronRight, Home } from 'lucide-react'

export const Breadcrumbs: React.FC = () => {
  const location = useLocation()
  const pathnames = location.pathname.split('/').filter((x) => x)

  if (pathnames.length === 0 || (pathnames.length === 1 && pathnames[0] === 'dashboard')) {
    return null
  }

  const PATH_DICTIONARY: Record<string, string> = {
    // Top-Level Modules
    crm: 'CRM',
    marketing: 'Marketing',
    pm: 'Project Management',
    hr: 'HRD',
    hrd: 'HRD',
    finance: 'Finance',
    portal: 'Portal Klien',
    admin: 'Admin',
    ai: 'AI Studio',
    dev: 'Developer',

    // CRM Routes
    dashboard: 'Dashboard',
    leads: 'Leads',
    pipeline: 'Sales Pipeline',
    customers: 'Customer 360°',
    contacts: 'Contacts',
    activities: 'Activities & Calendar',
    quotations: 'Quotations & MoU',
    contracts: 'Contracts & Legal Studio',
    'client-care': 'WhatsApp Client Care',
    'rate-cards': 'Rate Card & Katalog',
    templates: 'Template Library',
    forecast: 'Forecast & Target',
    segments: 'Campaign & Segmentasi',
    'account-plans': 'Upsell & Account Plan',
    'win-loss': 'Win/Loss Analytics',
    approvals: 'Approvals',
    reports: 'Laporan',
    settings: 'Pengaturan',
    'transition-hub': 'Project Transition Hub',
    'ar-calendar': 'Kalender Penagihan AR',
    'voice-notes': 'Voice Notes & Dokumen AI',

    // Marketing Routes
    trends: 'Riset Tren',
    planner: '7-Day Sprint Planner',
    studio: 'AI Content Studio',
    calendar: 'Editorial Calendar',
    distribution: 'Distribution Hub',
    inbox: 'Omnichannel Inbox',
    listening: 'Social Listening',
    analytics: 'Performance Analytics',
    'brand-kit': 'Brand Kit',
    campaigns: 'Campaign & Ads Budget',

    // HRD Routes
    employees: 'Data Karyawan/Mitra',
    attendance: 'Presensi',
    'daily-log': 'Daily Log',
    'night-workload': 'Workload Malam & Lembur',
    leave: 'Cuti & Izin',
    capacity: 'Kapasitas & Alokasi',
    recruitment: 'Rekrutmen ATS',
    internship: 'PKL / Magang',
    payroll: 'Payroll Engine',
    performance: 'Penilaian Kinerja',
    documents: 'Dokumen & Surat',

    // PM Routes
    portfolio: 'Portofolio Proyek',
    'deals-intake': 'Deals Won Intake',
    projects: 'Workspace Proyek',
    board: 'Board Kanban',
    backlog: 'Backlog & Sprint',
    roadmap: 'Roadmap & Gantt',
    qa: 'QA & Testing',
    wiki: 'Docs & Wiki',
    'time-tracking': 'Time Tracking',
    budget: 'Finance & Budget',
    'ai-fix': 'Otomasi Fix Bug AI',
    github: 'GitHub & Cabang',
    'service-desk': 'Service Desk & Portal',
    'team-capacity': 'Team Capacity',

    // Finance Routes
    invoices: 'Invoicing & Termin AR',
    expenses: 'Klaim Struk AI OCR',
    vendors: 'Tagihan Cloud & SaaS',
    freelancers: 'SPK Freelancer',
    opex: 'Biaya Operasional HQ',
    accounts: 'Rekening Bank & Kas',
    transfers: 'Mutasi Internal',
    coa: 'Chart of Accounts',
    journal: 'Jurnal Umum Akrual',
    pnl: 'Consolidated P&L',
    'pnl-project': 'P&L per Project',
    'customer-portfolio': 'Customer Portfolio & DSO',
    cashflow: 'Arus Kas & Proyeksi',
    'trial-balance': 'Neraca Saldo',

    // Client Portal
    tickets: 'Tiket Layanan',

    // Core / Admin
    users: 'Kelola Pengguna',
    roles: 'Peran & Izin (RBAC)',
    'audit-logs': 'Audit Log Immutable',
    numbering: 'Penomoran Dokumen',
    webhooks: 'Webhooks & Integrasi',

    // AI Studio
    prompts: 'Prompt Registry',
    metrics: 'Metrik & Observabilitas',
    media: 'Media Tools',
    playground: 'Playground',

    // Dev
    'ui-kit': 'Design System UI Kit',
  }

  const getLabel = (segment: string): string => {
    return PATH_DICTIONARY[segment] || segment.replace(/[-_]/g, ' ').toUpperCase()
  }

  return (
    <nav className="flex items-center text-xs text-slate-500 dark:text-slate-400 py-1.5 flex-wrap">
      <Link
        to="/dashboard"
        className="hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1 transition-colors"
      >
        <Home className="h-3.5 w-3.5" />
        <span>Beranda</span>
      </Link>

      {pathnames.map((segment, index) => {
        const to = `/${pathnames.slice(0, index + 1).join('/')}`
        const isLast = index === pathnames.length - 1

        return (
          <React.Fragment key={to}>
            <ChevronRight className="h-3.5 w-3.5 mx-1 text-slate-400 shrink-0" />
            {isLast ? (
              <span className="font-semibold text-slate-800 dark:text-slate-200">
                {getLabel(segment)}
              </span>
            ) : (
              <Link to={to} className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                {getLabel(segment)}
              </Link>
            )}
          </React.Fragment>
        )
      })}
    </nav>
  )
}
