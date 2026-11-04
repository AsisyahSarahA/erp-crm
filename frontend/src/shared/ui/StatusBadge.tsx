import React from 'react'
import { cn } from '../utils/cn'

export type StatusType =
  // CRM Pipeline
  | 'new_lead'
  | 'follow_up'
  | 'requirement'
  | 'quotation'
  | 'negotiation'
  | 'deal_won'
  | 'lost'
  // General / Documents
  | 'draft'
  | 'submitted'
  | 'in_review'
  | 'approved'
  | 'rejected'
  | 'active'
  | 'completed'
  | 'cancelled'
  | 'paid'
  | 'partially_paid'
  | 'unpaid'
  | 'overdue'
  | 'high'
  | 'medium'
  | 'low'
  | 'urgent'
  | string

interface StatusBadgeProps {
  status: StatusType
  label?: string
  className?: string
  dot?: boolean
  size?: 'sm' | 'md'
}

const STATUS_CONFIGS: Record<string, { label: string; bg: string; text: string; dot: string }> = {
  // CRM Pipeline
  new_lead: {
    label: 'New Lead (10%)',
    bg: 'bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900',
    text: 'text-blue-700 dark:text-blue-300',
    dot: 'bg-blue-500',
  },
  follow_up: {
    label: 'Follow Up (20%)',
    bg: 'bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-900',
    text: 'text-purple-700 dark:text-purple-300',
    dot: 'bg-purple-500',
  },
  requirement: {
    label: 'Requirement (35%)',
    bg: 'bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-900',
    text: 'text-cyan-700 dark:text-cyan-300',
    dot: 'bg-cyan-500',
  },
  quotation: {
    label: 'Quotation (55%)',
    bg: 'bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900',
    text: 'text-amber-700 dark:text-amber-300',
    dot: 'bg-amber-500',
  },
  negotiation: {
    label: 'Negosiasi (75%)',
    bg: 'bg-pink-50 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-900',
    text: 'text-pink-700 dark:text-pink-300',
    dot: 'bg-pink-500',
  },
  deal_won: {
    label: 'Deal Won (100%)',
    bg: 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900',
    text: 'text-emerald-700 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  lost: {
    label: 'Lost / Reject (0%)',
    bg: 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900',
    text: 'text-rose-700 dark:text-rose-300',
    dot: 'bg-rose-500',
  },

  // Document & Workflow Statuses
  draft: {
    label: 'Draf',
    bg: 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700',
    text: 'text-slate-700 dark:text-slate-300',
    dot: 'bg-slate-400',
  },
  submitted: {
    label: 'Diajukan',
    bg: 'bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900',
    text: 'text-indigo-700 dark:text-indigo-300',
    dot: 'bg-indigo-500',
  },
  in_review: {
    label: 'Menunggu Persetujuan',
    bg: 'bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900',
    text: 'text-amber-700 dark:text-amber-300',
    dot: 'bg-amber-500',
  },
  approved: {
    label: 'Disetujui',
    bg: 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900',
    text: 'text-emerald-700 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  rejected: {
    label: 'Ditolak',
    bg: 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900',
    text: 'text-rose-700 dark:text-rose-300',
    dot: 'bg-rose-500',
  },
  paid: {
    label: 'Lunas',
    bg: 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900',
    text: 'text-emerald-700 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  partially_paid: {
    label: 'Dibayar Sebagian',
    bg: 'bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-900',
    text: 'text-cyan-700 dark:text-cyan-300',
    dot: 'bg-cyan-500',
  },
  unpaid: {
    label: 'Belum Bayar',
    bg: 'bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700',
    text: 'text-slate-800 dark:text-slate-200',
    dot: 'bg-slate-500',
  },
  overdue: {
    label: 'Jatuh Tempo',
    bg: 'bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800',
    text: 'text-rose-700 dark:text-rose-300',
    dot: 'bg-rose-600',
  },
  active: {
    label: 'Aktif',
    bg: 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900',
    text: 'text-emerald-700 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  completed: {
    label: 'Selesai',
    bg: 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900',
    text: 'text-emerald-700 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  urgent: {
    label: 'Urgent',
    bg: 'bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800',
    text: 'text-rose-700 dark:text-rose-300',
    dot: 'bg-rose-600',
  },
}

export const StatusBadge: React.FC<StatusBadgeProps> = ({
  status,
  label,
  className,
  dot = true,
  size = 'md',
}) => {
  const normalizedKey = status.toLowerCase().replace(/[\s-]/g, '_')
  const config = STATUS_CONFIGS[normalizedKey] || {
    label: label || status,
    bg: 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700',
    text: 'text-slate-700 dark:text-slate-300',
    dot: 'bg-slate-400',
  }

  const displayText = label || config.label

  return (
    <span
      className={cn(
        'inline-flex items-center gap-1.5 font-semibold rounded-full tracking-wide',
        size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs',
        config.bg,
        config.text,
        className
      )}
    >
      {dot && <span className={cn('h-1.5 w-1.5 rounded-full shrink-0', config.dot)} />}
      <span>{displayText}</span>
    </span>
  )
}

