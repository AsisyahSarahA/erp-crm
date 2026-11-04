import React, { useState } from 'react'
import { Link, NavLink, useLocation, useNavigate } from 'react-router-dom'
import {
  Users,
  Megaphone,
  Briefcase,
  UserCheck,
  DollarSign,
  Sparkles,
  Settings,
  ChevronLeft,
  ChevronRight,
  LayoutDashboard,
  Layers,
  Palette,
  FileSpreadsheet,
  FileText,
  PhoneCall,
  Calendar,
  Shield,
  Clock,
  TrendingUp,
  Award,
  BookOpen,
  PieChart,
  Sliders,
  CheckSquare,
  HelpCircle,
  Receipt,
  CreditCard,
  Building,
  ArrowRightLeft,
  BookMarked,
  FileCheck,
  Kanban,
  Target,
  Sparkle,
  GitBranch,
  LifeBuoy,
  FilePlus,
  Compass,
  FolderOpen,
  Terminal,
} from 'lucide-react'
import { cn } from '../utils/cn'
import { usePermissions } from '../auth/usePermissions'

export type ModuleKey = 'crm' | 'marketing' | 'pm' | 'hrd' | 'finance' | 'portal' | 'admin' | 'ai'

interface MenuItem {
  label: string
  to: string
  icon: React.ReactNode
  permission?: string
  role?: string
  badge?: string
}

interface ModuleDefinition {
  key: ModuleKey
  name: string
  shortName: string
  icon: React.ReactNode
  permission?: string
  role?: string
  items: MenuItem[]
}

export const AppSidebar: React.FC = () => {
  const [collapsed, setCollapsed] = useState(false)
  const location = useLocation()
  const navigate = useNavigate()
  const { hasPermission, hasRole, isSuperAdmin } = usePermissions()

  // Derive active module reactively from current location
  const path = location.pathname
  const activeModule: ModuleKey = path.startsWith('/crm')
    ? 'crm'
    : path.startsWith('/marketing')
    ? 'marketing'
    : path.startsWith('/pm')
    ? 'pm'
    : path.startsWith('/hr')
    ? 'hrd'
    : path.startsWith('/finance')
    ? 'finance'
    : path.startsWith('/portal')
    ? 'portal'
    : path.startsWith('/admin')
    ? 'admin'
    : path.startsWith('/ai')
    ? 'ai'
    : 'crm'

  const modules: ModuleDefinition[] = [
    // 1. CRM (PRD §6.2: 21 Menus)
    {
      key: 'crm',
      name: 'CRM & Penjualan',
      shortName: 'CRM',
      icon: <Users className="h-4 w-4" />,
      permission: 'crm.lead.view',
      items: [
        { label: '1. Dashboard CRM', to: '/crm/dashboard', icon: <LayoutDashboard className="h-4 w-4" /> },
        { label: '2. Leads', to: '/crm/leads', icon: <Users className="h-4 w-4" /> },
        { label: '3. Sales Pipeline (Kanban)', to: '/crm/pipeline', icon: <Kanban className="h-4 w-4" /> },
        { label: '4. Customers (Master 360°)', to: '/crm/customers', icon: <Layers className="h-4 w-4" /> },
        { label: '5. Contacts', to: '/crm/contacts', icon: <Users className="h-4 w-4" /> },
        { label: '6. Activities & Calendar', to: '/crm/activities', icon: <Calendar className="h-4 w-4" /> },
        { label: '7. Quotations & MoU', to: '/crm/quotations', icon: <FileSpreadsheet className="h-4 w-4" /> },
        { label: '8. Contracts & Legal Studio', to: '/crm/contracts', icon: <FileCheck className="h-4 w-4" /> },
        { label: '9. WhatsApp Client Care', to: '/crm/client-care', icon: <PhoneCall className="h-4 w-4" /> },
        { label: '10. Rate Card & Modul', to: '/crm/rate-cards', icon: <CreditCard className="h-4 w-4" /> },
        { label: '11. Template Library', to: '/crm/templates', icon: <BookOpen className="h-4 w-4" /> },
        { label: '12. Forecast & Target', to: '/crm/forecast', icon: <Target className="h-4 w-4" /> },
        { label: '13. Campaign & Segmen', to: '/crm/segments', icon: <Megaphone className="h-4 w-4" /> },
        { label: '14. Upsell & Account Plan', to: '/crm/account-plans', icon: <TrendingUp className="h-4 w-4" /> },
        { label: '15. Win/Loss Analytics', to: '/crm/win-loss', icon: <PieChart className="h-4 w-4" /> },
        { label: '16. Approvals Diskon', to: '/crm/approvals', icon: <CheckSquare className="h-4 w-4" /> },
        { label: '17. Laporan CRM', to: '/crm/reports', icon: <FileText className="h-4 w-4" /> },
        { label: '18. Pengaturan CRM', to: '/crm/settings', icon: <Sliders className="h-4 w-4" /> },
        { label: '19. Project Transition Hub', to: '/crm/transition-hub', icon: <Briefcase className="h-4 w-4" /> },
        { label: '20. Kalender Penagihan AR', to: '/crm/ar-calendar', icon: <Clock className="h-4 w-4" /> },
        { label: '21. Voice Notes & AI', to: '/crm/voice-notes', icon: <Sparkles className="h-4 w-4 text-violet-400" /> },
      ],
    },

    // 2. Marketing (PRD §7.2: 13 Menus)
    {
      key: 'marketing',
      name: 'Marketing & AI Content',
      shortName: 'MKT',
      icon: <Megaphone className="h-4 w-4" />,
      permission: 'marketing.content.view',
      items: [
        { label: 'Dashboard Marketing', to: '/marketing/dashboard', icon: <LayoutDashboard className="h-4 w-4" /> },
        { label: 'Riset Tren', to: '/marketing/trends', icon: <Compass className="h-4 w-4" /> },
        { label: '7-Day Sprint Planner', to: '/marketing/planner', icon: <Calendar className="h-4 w-4" /> },
        { label: 'AI Content Studio', to: '/marketing/studio', icon: <Sparkle className="h-4 w-4 text-pink-400" /> },
        { label: 'Approval Queue', to: '/marketing/approvals', icon: <CheckSquare className="h-4 w-4" /> },
        { label: 'Editorial Calendar', to: '/marketing/calendar', icon: <Calendar className="h-4 w-4" /> },
        { label: 'Distribution Hub', to: '/marketing/distribution', icon: <Megaphone className="h-4 w-4" /> },
        { label: 'Omnichannel Inbox', to: '/marketing/inbox', icon: <PhoneCall className="h-4 w-4" /> },
        { label: 'Social Listening', to: '/marketing/listening', icon: <TrendingUp className="h-4 w-4" /> },
        { label: 'Performance Analytics', to: '/marketing/analytics', icon: <PieChart className="h-4 w-4" /> },
        { label: 'Brand Kit', to: '/marketing/brand-kit', icon: <Palette className="h-4 w-4" /> },
        { label: 'Campaign & Ads Budget', to: '/marketing/campaigns', icon: <CreditCard className="h-4 w-4" /> },
        { label: 'Pengaturan Marketing', to: '/marketing/settings', icon: <Sliders className="h-4 w-4" /> },
      ],
    },

    // 3. Project Management (PRD §9.2: 15 Menus)
    {
      key: 'pm',
      name: 'Project Management',
      shortName: 'PM',
      icon: <Briefcase className="h-4 w-4" />,
      permission: 'pm.project.view',
      items: [
        { label: 'Portofolio Proyek', to: '/pm/portfolio', icon: <FolderOpen className="h-4 w-4" /> },
        { label: 'Deals Won (Intake 3 Langkah)', to: '/pm/deals-intake', icon: <FilePlus className="h-4 w-4" /> },
        { label: 'Workspace Proyek', to: '/pm/projects', icon: <Briefcase className="h-4 w-4" /> },
        { label: 'Board Kanban', to: '/pm/board', icon: <Kanban className="h-4 w-4" /> },
        { label: 'Backlog & Sprint', to: '/pm/backlog', icon: <Layers className="h-4 w-4" /> },
        { label: 'Roadmap / Gantt', to: '/pm/roadmap', icon: <Calendar className="h-4 w-4" /> },
        { label: 'QA & Testing', to: '/pm/qa', icon: <CheckSquare className="h-4 w-4" /> },
        { label: 'Docs & Wiki', to: '/pm/wiki', icon: <BookOpen className="h-4 w-4" /> },
        { label: 'Time Tracking', to: '/pm/time-tracking', icon: <Clock className="h-4 w-4" /> },
        { label: 'Finance & Budget Proyek', to: '/pm/budget', icon: <DollarSign className="h-4 w-4" /> },
        { label: 'Otomasi Fix Bug AI', to: '/pm/ai-fix', icon: <Sparkles className="h-4 w-4 text-violet-400" /> },
        { label: 'GitHub & Cabang', to: '/pm/github', icon: <GitBranch className="h-4 w-4" /> },
        { label: 'Service Desk / Tiket Klien', to: '/pm/service-desk', icon: <LifeBuoy className="h-4 w-4" /> },
        { label: 'Management Team (Kapasitas)', to: '/pm/team-capacity', icon: <UserCheck className="h-4 w-4" /> },
        { label: 'Pengaturan PM', to: '/pm/settings', icon: <Sliders className="h-4 w-4" /> },
      ],
    },

    // 4. HRD & Workload (PRD §8.2: 14 Menus)
    {
      key: 'hrd',
      name: 'HRD & Workload',
      shortName: 'HRD',
      icon: <UserCheck className="h-4 w-4" />,
      permission: 'hrd.employee.view',
      items: [
        { label: 'Dashboard HR', to: '/hr/dashboard', icon: <LayoutDashboard className="h-4 w-4" /> },
        { label: 'Data Karyawan/Mitra', to: '/hr/employees', icon: <Users className="h-4 w-4" /> },
        { label: 'Presensi (Geofence/WiFi)', to: '/hr/attendance', icon: <Clock className="h-4 w-4" /> },
        { label: 'Daily Log Progress', to: '/hr/daily-log', icon: <FileText className="h-4 w-4" /> },
        { label: 'Workload Malam & Lembur', to: '/hr/night-workload', icon: <Clock className="h-4 w-4" /> },
        { label: 'Cuti & Izin', to: '/hr/leave', icon: <Calendar className="h-4 w-4" /> },
        { label: 'Kapasitas & Alokasi', to: '/hr/capacity', icon: <PieChart className="h-4 w-4" /> },
        { label: 'Rekrutmen (ATS)', to: '/hr/recruitment', icon: <Users className="h-4 w-4" /> },
        { label: 'PKL / Magang', to: '/hr/internship', icon: <Award className="h-4 w-4" /> },
        { label: 'Payroll Engine', to: '/hr/payroll', icon: <DollarSign className="h-4 w-4" /> },
        { label: 'Penilaian Kinerja', to: '/hr/performance', icon: <Award className="h-4 w-4" /> },
        { label: 'Dokumen & Surat', to: '/hr/documents', icon: <FileText className="h-4 w-4" /> },
        { label: 'Laporan HRD', to: '/hr/reports', icon: <PieChart className="h-4 w-4" /> },
        { label: 'Pengaturan HRD', to: '/hr/settings', icon: <Sliders className="h-4 w-4" /> },
      ],
    },

    // 5. Finance & Cashflow (PRD §10.2: 17 Menus)
    {
      key: 'finance',
      name: 'Finance & Cashflow',
      shortName: 'FIN',
      icon: <DollarSign className="h-4 w-4" />,
      permission: 'finance.invoice.view',
      items: [
        { label: 'Executive Dashboard Finance', to: '/finance/dashboard', icon: <LayoutDashboard className="h-4 w-4" /> },
        { label: 'Invoicing & Termin (AR)', to: '/finance/invoices', icon: <Receipt className="h-4 w-4" /> },
        { label: 'Klaim Struk AI OCR (AP)', to: '/finance/expenses', icon: <Sparkles className="h-4 w-4" /> },
        { label: 'Tagihan Cloud & SaaS', to: '/finance/vendors', icon: <CreditCard className="h-4 w-4" /> },
        { label: 'SPK Freelancer / Subkon', to: '/finance/freelancers', icon: <FileText className="h-4 w-4" /> },
        { label: 'Payroll Finance', to: '/finance/payroll', icon: <DollarSign className="h-4 w-4" /> },
        { label: 'Biaya Operasional HQ (OPEX)', to: '/finance/opex', icon: <Building className="h-4 w-4" /> },
        { label: 'Rekening Bank & Kas', to: '/finance/accounts', icon: <Building className="h-4 w-4" /> },
        { label: 'Mutasi Internal', to: '/finance/transfers', icon: <ArrowRightLeft className="h-4 w-4" /> },
        { label: 'Chart of Accounts (COA)', to: '/finance/coa', icon: <BookMarked className="h-4 w-4" /> },
        { label: 'Jurnal Umum Akrual', to: '/finance/journal', icon: <FileText className="h-4 w-4" /> },
        { label: 'Consolidated P&L', to: '/finance/reports/pnl', icon: <PieChart className="h-4 w-4" /> },
        { label: 'P&L per Project', to: '/finance/reports/pnl-project', icon: <PieChart className="h-4 w-4" /> },
        { label: 'Customer Portfolio & DSO', to: '/finance/reports/customer-portfolio', icon: <Users className="h-4 w-4" /> },
        { label: 'Arus Kas & Proyeksi', to: '/finance/reports/cashflow', icon: <TrendingUp className="h-4 w-4" /> },
        { label: 'Neraca Saldo', to: '/finance/reports/trial-balance', icon: <FileCheck className="h-4 w-4" /> },
        { label: 'Pengaturan Finance', to: '/finance/settings', icon: <Sliders className="h-4 w-4" /> },
      ],
    },

    // 6. Client Portal (PRD §15.4 / portal)
    {
      key: 'portal',
      name: 'Portal Klien Eksternal',
      shortName: 'Portal',
      icon: <Layers className="h-4 w-4 text-cyan-400" />,
      items: [
        { label: 'Dashboard Portal', to: '/portal/dashboard', icon: <LayoutDashboard className="h-4 w-4" /> },
        { label: 'Proyek Saya', to: '/portal/projects', icon: <Briefcase className="h-4 w-4" /> },
        { label: 'Faktur & Tagihan', to: '/portal/invoices', icon: <Receipt className="h-4 w-4" /> },
        { label: 'Tiket Layanan & SLA', to: '/portal/tickets', icon: <HelpCircle className="h-4 w-4" /> },
        { label: 'Dokumen & Kontrak', to: '/portal/documents', icon: <FileCheck className="h-4 w-4" /> },
      ],
    },

    // 7. Core / Admin System (PRD §15.4 / admin)
    {
      key: 'admin',
      name: 'Admin & Core System',
      shortName: 'Admin',
      icon: <Settings className="h-4 w-4 text-slate-400" />,
      role: 'super_admin',
      items: [
        { label: 'Kelola Pengguna', to: '/admin/users', icon: <Users className="h-4 w-4" /> },
        { label: 'Peran & Izin (RBAC)', to: '/admin/roles', icon: <Shield className="h-4 w-4" /> },
        { label: 'Audit Log Immutable', to: '/admin/audit-logs', icon: <FileText className="h-4 w-4" /> },
        { label: 'Penomoran Dokumen', to: '/admin/numbering', icon: <FileSpreadsheet className="h-4 w-4" /> },
        { label: 'Webhooks & Integrasi', to: '/admin/webhooks', icon: <ArrowRightLeft className="h-4 w-4" /> },
        { label: 'Pengaturan Sistem', to: '/admin/settings', icon: <Settings className="h-4 w-4" /> },
      ],
    },

    // 8. AI Studio & Gateway
    {
      key: 'ai',
      name: 'AI Studio & Gateway',
      shortName: 'AI Studio',
      icon: <Sparkles className="h-4 w-4 text-violet-400" />,
      permission: 'ai.prompt.view',
      items: [
        { label: 'Prompt Registry', to: '/ai/prompts', icon: <BookOpen className="h-4 w-4" /> },
        { label: 'Metrik & Observabilitas', to: '/ai/metrics', icon: <PieChart className="h-4 w-4" /> },
        { label: 'Media Tools (STT/OCR)', to: '/ai/media', icon: <Sparkles className="h-4 w-4" /> },
        { label: 'AI Playground', to: '/ai/playground', icon: <Terminal className="h-4 w-4" /> },
      ],
    },
  ]

  // Filter accessible modules
  const accessibleModules = modules.filter((m) => {
    if (isSuperAdmin) return true
    if (m.role && !hasRole(m.role)) return false
    if (m.permission && !hasPermission(m.permission)) return false
    return true
  })

  const currentModuleDef = modules.find((m) => m.key === activeModule) || modules[0]

  // Filter items in active module based on permission
  const visibleItems = currentModuleDef.items.filter((item) => {
    if (isSuperAdmin) return true
    if (item.role && !hasRole(item.role)) return false
    if (item.permission && !hasPermission(item.permission)) return false
    return true
  })

  return (
    <aside
      className={cn(
        'relative flex flex-col border-r border-slate-800 bg-slate-900 text-slate-300 transition-all duration-300 select-none z-20',
        collapsed ? 'w-18' : 'w-72'
      )}
    >
      {/* Brand & Collapse Header */}
      <div className="flex h-16 items-center justify-between px-4 border-b border-slate-800 shrink-0">
        <Link to="/dashboard" className="flex items-center gap-3 overflow-hidden">
          <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 font-bold text-white shadow-md">
            A
          </div>
          {!collapsed && (
            <div className="truncate">
              <span className="block text-sm font-bold text-white tracking-tight leading-tight">
                ASTACODE ERP
              </span>
              <span className="text-[10px] text-indigo-400 font-medium">Orchestrator v2.0</span>
            </div>
          )}
        </Link>

        <button
          onClick={() => setCollapsed(!collapsed)}
          className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition-colors"
          title={collapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'}
        >
          {collapsed ? <ChevronRight className="h-4 w-4" /> : <ChevronLeft className="h-4 w-4" />}
        </button>
      </div>

      {/* Module Selector (Top Bar / Dropdown Pill) */}
      {!collapsed ? (
        <div className="px-3 pt-3 pb-2 border-b border-slate-800 shrink-0">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
            Pilih Modul Aktif:
          </label>
          <div className="relative">
            <select
              value={activeModule}
              onChange={(e) => {
                const targetMod = e.target.value as ModuleKey
                const def = modules.find((m) => m.key === targetMod)
                if (def && def.items.length > 0) {
                  navigate(def.items[0].to)
                }
              }}
              className="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
              {accessibleModules.map((m) => (
                <option key={m.key} value={m.key}>
                  {m.name}
                </option>
              ))}
            </select>
          </div>
        </div>
      ) : (
        <div className="py-2 flex flex-col items-center gap-1 border-b border-slate-800">
          <button
            onClick={() => setCollapsed(false)}
            className="p-2 rounded-lg bg-indigo-600/30 text-indigo-300 hover:bg-indigo-600/50"
            title={currentModuleDef.name}
          >
            {currentModuleDef.icon}
          </button>
        </div>
      )}

      {/* Navigation Menu List */}
      <div className="flex-1 overflow-y-auto px-3 py-3 space-y-1">
        {/* Global Dashboard Link */}
        <NavLink
          to="/dashboard"
          className={({ isActive }) =>
            cn(
              'flex items-center gap-3 rounded-xl px-3 py-2 text-xs font-medium transition-colors mb-2',
              isActive && location.pathname === '/dashboard'
                ? 'bg-indigo-600 text-white shadow-xs font-semibold'
                : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
            )
          }
          title={collapsed ? 'Executive Dashboard' : undefined}
        >
          <span className="shrink-0">
            <LayoutDashboard className="h-4 w-4" />
          </span>
          {!collapsed && <span className="truncate">Executive Dashboard</span>}
        </NavLink>

        {!collapsed && (
          <div className="px-2 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center justify-between">
            <span>Menu {currentModuleDef.shortName}</span>
            <span className="text-[9px] bg-slate-800 px-1.5 py-0.2 rounded text-slate-400">
              {visibleItems.length} menu
            </span>
          </div>
        )}

        {visibleItems.map((item) => (
          <NavLink
            key={item.to}
            to={item.to}
            className={({ isActive }) =>
              cn(
                'flex items-center gap-3 rounded-xl px-3 py-2 text-xs font-medium transition-colors',
                isActive
                  ? 'bg-indigo-600 text-white shadow-xs font-semibold'
                  : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
              )
            }
            title={collapsed ? item.label : undefined}
          >
            <span className="shrink-0">{item.icon}</span>
            {!collapsed && <span className="truncate">{item.label}</span>}
            {!collapsed && item.badge && (
              <span className="ml-auto rounded-full bg-indigo-500/30 px-1.5 py-0.2 text-[10px] font-bold text-indigo-300">
                {item.badge}
              </span>
            )}
          </NavLink>
        ))}

        {/* Developer UI Kit Shortcut */}
        <div className="pt-3 border-t border-slate-800/80">
          <NavLink
            to="/dev/ui-kit"
            className={({ isActive }) =>
              cn(
                'flex items-center gap-3 rounded-xl px-3 py-2 text-xs font-medium transition-colors',
                isActive
                  ? 'bg-emerald-600 text-white shadow-xs'
                  : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
              )
            }
            title={collapsed ? 'Design System UI Kit' : undefined}
          >
            <span className="shrink-0">
              <Palette className="h-4 w-4 text-emerald-400" />
            </span>
            {!collapsed && <span className="truncate">Design System UI Kit</span>}
          </NavLink>
        </div>
      </div>

      {/* Footer / Status */}
      {!collapsed && (
        <div className="border-t border-slate-800 p-3 shrink-0">
          <div className="rounded-xl bg-slate-800/60 p-2.5 text-xs">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse" />
                <span className="font-semibold text-slate-200">Gateway BFF</span>
              </div>
              <span className="font-mono text-[10px] text-slate-400">:8000</span>
            </div>
            <p className="mt-1 text-[10px] text-slate-400">8 Microservices Online</p>
          </div>
        </div>
      )}
    </aside>
  )
}
