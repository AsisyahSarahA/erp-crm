import React, { useState, useEffect } from 'react'
import { useAuthStore } from '../auth/authStore'
import {
  Search,
  Bell,
  Plus,
  Moon,
  Sun,
  LogOut,
} from 'lucide-react'

interface AppHeaderProps {
  onOpenCommandPalette: () => void
  onToggleSidebar?: () => void
}

export const AppHeader: React.FC<AppHeaderProps> = ({ onOpenCommandPalette }) => {
  const { user, logout } = useAuthStore()
  const [isDark, setIsDark] = useState(() => {
    return (
      localStorage.getItem('theme') === 'dark' ||
      (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    )
  })
  const [showNotifications, setShowNotifications] = useState(false)
  const [showUserMenu, setShowUserMenu] = useState(false)
  const [unreadCount, setUnreadCount] = useState(3)

  useEffect(() => {
    if (isDark) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }, [isDark])

  const notifications = [
    {
      id: '1',
      title: 'Handover Deal Won Selesai',
      desc: 'Proyek PT Astra Graphia otomatis diteruskan ke PM & Finance.',
      time: '5m lalu',
      unread: true,
    },
    {
      id: '2',
      title: 'Approval Diskon Quotation',
      desc: 'Diskon 18% untuk Lead LD-2026-004 menunggu persetujuan Direktur.',
      time: '1j lalu',
      unread: true,
    },
    {
      id: '3',
      title: 'Jatuh Tempo Invoice #INV-0089',
      desc: 'Termin 1 (DP 50%) jatuh tempo dalam 3 hari ke depan.',
      time: '3j lalu',
      unread: false,
    },
  ]

  return (
    <header className="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white/80 px-6 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80">
      {/* Left: Global Search trigger */}
      <div className="flex items-center gap-4">
        <button
          onClick={onOpenCommandPalette}
          className="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-1.5 text-xs text-slate-500 shadow-2xs hover:border-slate-300 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400"
        >
          <Search className="h-4 w-4 text-slate-400" />
          <span>Cari lead, invoice, atau ketik perintah...</span>
          <kbd className="rounded border border-slate-300 bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900">
            ⌘K
          </kbd>
        </button>
      </div>

      {/* Right Controls */}
      <div className="flex items-center gap-3">
        {/* Quick Create Button */}
        <div className="relative">
          <button
            onClick={onOpenCommandPalette}
            className="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-indigo-700"
          >
            <Plus className="h-3.5 w-3.5" />
            <span>Aksi Cepat</span>
          </button>
        </div>

        {/* Theme Toggle */}
        <button
          onClick={() => setIsDark(!isDark)}
          className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
          title="Ganti Tema"
        >
          {isDark ? <Sun className="h-4 w-4 text-amber-400" /> : <Moon className="h-4 w-4" />}
        </button>

        {/* Notifications */}
        <div className="relative">
          <button
            onClick={() => setShowNotifications(!showNotifications)}
            className="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
          >
            <Bell className="h-4 w-4" />
            {unreadCount > 0 && (
              <span className="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75" />
                <span className="relative inline-flex h-2 w-2 rounded-full bg-rose-500" />
              </span>
            )}
          </button>

          {showNotifications && (
            <div className="absolute right-0 mt-2 w-80 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl dark:border-slate-800 dark:bg-slate-900">
              <div className="flex items-center justify-between border-b border-slate-100 pb-2 dark:border-slate-800">
                <h4 className="text-xs font-bold text-slate-900 dark:text-slate-100">Notifikasi</h4>
                <button
                  onClick={() => setUnreadCount(0)}
                  className="text-[10px] text-indigo-600 hover:underline dark:text-indigo-400"
                >
                  Tandai dibaca
                </button>
              </div>
              <div className="mt-2 space-y-2">
                {notifications.map((n) => (
                  <div
                    key={n.id}
                    className="cursor-pointer rounded-lg p-2 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                  >
                    <p className="text-xs font-semibold text-slate-800 dark:text-slate-200">{n.title}</p>
                    <p className="text-[11px] text-slate-500 dark:text-slate-400">{n.desc}</p>
                    <span className="mt-1 block text-[10px] text-slate-400">{n.time}</span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* User Profile */}
        <div className="relative">
          <button
            onClick={() => setShowUserMenu(!showUserMenu)}
            className="flex items-center gap-2 rounded-xl p-1.5 text-left hover:bg-slate-100 dark:hover:bg-slate-800"
          >
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-bold text-white shadow-2xs">
              {user?.name ? user.name.charAt(0).toUpperCase() : 'U'}
            </div>
            <div className="hidden md:block">
              <p className="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight">
                {user?.name || 'User Astacode'}
              </p>
              <p className="text-[10px] text-slate-400 capitalize">
                {user?.roles?.[0]?.replace(/_/g, ' ') || 'Staff'}
              </p>
            </div>
          </button>

          {showUserMenu && (
            <div className="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-800 dark:bg-slate-900">
              <div className="border-b border-slate-100 px-3 py-2 dark:border-slate-800">
                <p className="text-xs font-bold text-slate-900 dark:text-slate-100">{user?.name}</p>
                <p className="text-[11px] text-slate-400">{user?.email}</p>
              </div>

              <div className="mt-1">
                <button
                  onClick={logout}
                  className="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40"
                >
                  <LogOut className="h-4 w-4" /> Keluar Sesi
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </header>
  )
}
