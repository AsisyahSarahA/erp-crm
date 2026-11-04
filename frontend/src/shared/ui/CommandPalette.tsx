import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Search,
  FolderPlus,
  FileText,
  Users,
  DollarSign,
  Briefcase,
  Settings,
  Megaphone,
  UserCheck,
  Sparkles,
  Layers,
  X,
} from 'lucide-react'
import { cn } from '../utils/cn'

interface CommandItem {
  id: string
  title: string
  subtitle?: string
  icon: React.ReactNode
  action: () => void
  category: 'Navigasi' | 'Aksi Cepat' | 'Pengaturan'
}

export const CommandPalette: React.FC<{ isOpen: boolean; onClose: () => void }> = ({
  isOpen,
  onClose,
}) => {
  const navigate = useNavigate()
  const [query, setQuery] = useState('')
  const [selectedIndex, setSelectedIndex] = useState(0)

  const items: CommandItem[] = [
    {
      id: 'nav-dashboard',
      title: 'Executive Dashboard',
      subtitle: 'Ringkasan KPI lintas modul',
      icon: <Layers className="h-4 w-4 text-indigo-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/dashboard')
        onClose()
      },
    },
    {
      id: 'nav-crm-pipeline',
      title: 'CRM — Sales Pipeline Kanban',
      subtitle: 'Buka Kanban 7 tahapan pipeline',
      icon: <Users className="h-4 w-4 text-blue-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/crm/pipeline')
        onClose()
      },
    },
    {
      id: 'nav-crm-leads',
      title: 'CRM — Leads',
      subtitle: 'Daftar prospek dan kontak klien baru',
      icon: <Users className="h-4 w-4 text-blue-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/crm/leads')
        onClose()
      },
    },
    {
      id: 'nav-marketing',
      title: 'Marketing — AI Content Studio',
      subtitle: 'Generator konten media sosial (Carousel, Video, Blog)',
      icon: <Megaphone className="h-4 w-4 text-pink-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/marketing/studio')
        onClose()
      },
    },
    {
      id: 'nav-pm-projects',
      title: 'PM — Portofolio Proyek',
      subtitle: 'Daftar proyek aktif dan backlog sprint',
      icon: <Briefcase className="h-4 w-4 text-emerald-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/pm/portfolio')
        onClose()
      },
    },
    {
      id: 'nav-hr-employees',
      title: 'HRD — Data Karyawan & Mitra',
      subtitle: 'Tetap, Freelance, dan Magang PKL',
      icon: <UserCheck className="h-4 w-4 text-purple-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/hr/employees')
        onClose()
      },
    },
    {
      id: 'nav-finance-invoices',
      title: 'Finance — Invoicing & Piutang AR',
      subtitle: 'Jadwal termin & penerbitan faktur resmi',
      icon: <DollarSign className="h-4 w-4 text-amber-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/finance/invoices')
        onClose()
      },
    },
    {
      id: 'nav-portal',
      title: 'Portal Klien Eksternal',
      subtitle: 'Area login dan pantauan klien',
      icon: <Layers className="h-4 w-4 text-cyan-500" />,
      category: 'Navigasi',
      action: () => {
        navigate('/portal/dashboard')
        onClose()
      },
    },
    {
      id: 'nav-ai-studio',
      title: 'AI Studio — Prompt Registry',
      subtitle: 'Kelola template prompt sistem LLM',
      icon: <Sparkles className="h-4 w-4 text-violet-400" />,
      category: 'Navigasi',
      action: () => {
        navigate('/ai/prompts')
        onClose()
      },
    },
    {
      id: 'action-new-lead',
      title: 'Aksi: Tambah Lead Baru',
      subtitle: 'Input prospek klien baru ke pipeline CRM',
      icon: <FolderPlus className="h-4 w-4 text-indigo-500" />,
      category: 'Aksi Cepat',
      action: () => {
        navigate('/crm/leads')
        onClose()
      },
    },
    {
      id: 'action-new-rab',
      title: 'Aksi: Buat Quotation & RAB',
      subtitle: 'Buka draf estimasi biaya dan mandays',
      icon: <FileText className="h-4 w-4 text-emerald-500" />,
      category: 'Aksi Cepat',
      action: () => {
        navigate('/crm/quotations')
        onClose()
      },
    },
    {
      id: 'nav-admin-users',
      title: 'Pengaturan: Kelola Pengguna & RBAC',
      subtitle: 'Kelola 18 peran baku PRD dan izin akses',
      icon: <Settings className="h-4 w-4 text-slate-400" />,
      category: 'Pengaturan',
      action: () => {
        navigate('/admin/users')
        onClose()
      },
    },
  ]

  const filteredItems = items.filter(
    (item) =>
      item.title.toLowerCase().includes(query.toLowerCase()) ||
      item.subtitle?.toLowerCase().includes(query.toLowerCase()) ||
      item.category.toLowerCase().includes(query.toLowerCase())
  )

  const handleQueryChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setQuery(e.target.value)
    setSelectedIndex(0)
  }

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (!isOpen) return

      if (e.key === 'ArrowDown') {
        e.preventDefault()
        setSelectedIndex((prev) => (prev < filteredItems.length - 1 ? prev + 1 : 0))
      } else if (e.key === 'ArrowUp') {
        e.preventDefault()
        setSelectedIndex((prev) => (prev > 0 ? prev - 1 : filteredItems.length - 1))
      } else if (e.key === 'Enter') {
        e.preventDefault()
        if (filteredItems[selectedIndex]) {
          filteredItems[selectedIndex].action()
        }
      } else if (e.key === 'Escape') {
        onClose()
      }
    }

    window.addEventListener('keydown', handleKeyDown)
    return () => window.removeEventListener('keydown', handleKeyDown)
  }, [isOpen, filteredItems, selectedIndex, onClose])

  if (!isOpen) return null

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20">
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        onClick={onClose}
      />

      {/* Palette Container */}
      <div className="relative w-full max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">
        <div className="flex items-center border-b border-slate-200 px-4 py-3 dark:border-slate-800">
          <Search className="h-5 w-5 text-slate-400" />
          <input
            autoFocus
            type="text"
            placeholder="Ketik perintah atau cari modul... (↑↓ untuk pilih, Enter untuk buka)"
            value={query}
            onChange={handleQueryChange}
            className="w-full border-0 bg-transparent px-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none dark:text-slate-100"
          />
          <button
            onClick={onClose}
            className="rounded p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
          >
            <X className="h-4 w-4" />
          </button>
        </div>

        <div className="max-h-80 overflow-y-auto p-2">
          {filteredItems.length === 0 ? (
            <p className="p-4 text-center text-xs text-slate-400">Tidak ada aksi atau navigasi yang cocok.</p>
          ) : (
            filteredItems.map((item, index) => {
              const isSelected = index === selectedIndex
              return (
                <div
                  key={item.id}
                  onClick={item.action}
                  onMouseEnter={() => setSelectedIndex(index)}
                  className={cn(
                    'flex cursor-pointer items-center justify-between rounded-xl px-3 py-2.5 transition-colors',
                    isSelected
                      ? 'bg-indigo-50 text-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-200'
                      : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800/50'
                  )}
                >
                  <div className="flex items-center gap-3">
                    <span className={cn('p-1.5 rounded-lg', isSelected ? 'bg-indigo-100 dark:bg-indigo-900' : 'bg-slate-100 dark:bg-slate-800')}>
                      {item.icon}
                    </span>
                    <div>
                      <p className="text-xs font-bold">{item.title}</p>
                      {item.subtitle && <p className="text-[11px] text-slate-400">{item.subtitle}</p>}
                    </div>
                  </div>
                  <span className="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    {item.category}
                  </span>
                </div>
              )
            })
          )}
        </div>
      </div>
    </div>
  )
}
