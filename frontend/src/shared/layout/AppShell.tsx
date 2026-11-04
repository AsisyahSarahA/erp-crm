import React, { useState } from 'react'
import { Outlet } from 'react-router-dom'
import { AppSidebar } from './AppSidebar'
import { AppHeader } from './AppHeader'
import { Breadcrumbs } from './Breadcrumbs'
import { CommandPalette } from '../ui/CommandPalette'
import { ToastContainer } from '../ui/Toast'

export const AppShell: React.FC = () => {
  const [isCommandPaletteOpen, setIsCommandPaletteOpen] = useState(false)

  // Listen for Cmd+K / Ctrl+K globally
  React.useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault()
        setIsCommandPaletteOpen((prev) => !prev)
      }
    }
    window.addEventListener('keydown', handleKeyDown)
    return () => window.removeEventListener('keydown', handleKeyDown)
  }, [])

  return (
    <div className="flex h-screen w-screen overflow-hidden bg-slate-50 dark:bg-slate-950 font-sans">
      {/* Sidebar */}
      <AppSidebar />

      {/* Main Container */}
      <div className="flex flex-1 flex-col overflow-hidden">
        {/* Top Header */}
        <AppHeader onOpenCommandPalette={() => setIsCommandPaletteOpen(true)} />

        {/* Content Body */}
        <main className="flex-1 overflow-y-auto px-6 py-4">
          <div className="mx-auto max-w-7xl space-y-4">
            <Breadcrumbs />
            <Outlet />
          </div>
        </main>
      </div>

      {/* Global Modals & Notifications */}
      <CommandPalette
        isOpen={isCommandPaletteOpen}
        onClose={() => setIsCommandPaletteOpen(false)}
      />
      <ToastContainer />
    </div>
  )
}

