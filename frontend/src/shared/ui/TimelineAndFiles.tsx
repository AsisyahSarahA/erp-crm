import React, { useRef, useState } from 'react'
import { formatDateTime } from '../utils/formatters'
import { cn } from '../utils/cn'
import { UploadCloud, XCircle, Clock, Check } from 'lucide-react'

// --- Activity Feed ---
export interface ActivityItem {
  id: string
  user: { name: string; avatar?: string }
  action: string
  target?: string
  timestamp: string | Date
  details?: string
}

export const ActivityFeed: React.FC<{ activities: ActivityItem[]; className?: string }> = ({
  activities,
  className,
}) => {
  return (
    <div className={cn('flow-root', className)}>
      <ul className="-mb-8">
        {activities.map((act, index) => {
          const isLast = index === activities.length - 1
          return (
            <li key={act.id}>
              <div className="relative pb-8">
                {!isLast && (
                  <span
                    className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-800"
                    aria-hidden="true"
                  />
                )}
                <div className="relative flex space-x-3">
                  <div>
                    <span className="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700 ring-4 ring-white dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-900">
                      {act.user.name.charAt(0).toUpperCase()}
                    </span>
                  </div>
                  <div className="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                    <div>
                      <p className="text-xs text-slate-700 dark:text-slate-300">
                        <span className="font-semibold text-slate-900 dark:text-slate-100">
                          {act.user.name}
                        </span>{' '}
                        {act.action}{' '}
                        {act.target && (
                          <span className="font-semibold text-indigo-600 dark:text-indigo-400">
                            {act.target}
                          </span>
                        )}
                      </p>
                      {act.details && (
                        <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{act.details}</p>
                      )}
                    </div>
                    <div className="whitespace-nowrap text-right text-[11px] text-slate-400">
                      {formatDateTime(act.timestamp)}
                    </div>
                  </div>
                </div>
              </div>
            </li>
          )
        })}
      </ul>
    </div>
  )
}

// --- Approval Timeline ---
export interface ApprovalStep {
  id: string
  approverRole: string
  approverName?: string
  status: 'PENDING' | 'APPROVED' | 'REJECTED'
  notes?: string
  updatedAt?: string | Date
}

export const ApprovalTimeline: React.FC<{ steps: ApprovalStep[]; className?: string }> = ({
  steps,
  className,
}) => {
  return (
    <div className={cn('space-y-4', className)}>
      {steps.map((step, idx) => {
        const isApproved = step.status === 'APPROVED'
        const isRejected = step.status === 'REJECTED'
        const isPending = step.status === 'PENDING'

        return (
          <div key={step.id || idx} className="flex items-start gap-3">
            <div
              className={cn(
                'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                isApproved && 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400',
                isRejected && 'bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
                isPending && 'bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400'
              )}
            >
              {isApproved && <Check className="h-4 w-4" />}
              {isRejected && <XCircle className="h-4 w-4" />}
              {isPending && <Clock className="h-4 w-4" />}
            </div>

            <div className="flex-1">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-slate-800 dark:text-slate-200">
                  {step.approverRole}
                </span>
                <span
                  className={cn(
                    'text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full',
                    isApproved && 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                    isRejected && 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
                    isPending && 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'
                  )}
                >
                  {step.status}
                </span>
              </div>
              <p className="text-xs text-slate-500">
                {step.approverName || 'Menunggu tindakan peninjau'}
                {step.updatedAt && ` • ${formatDateTime(step.updatedAt)}`}
              </p>
              {step.notes && (
                <div className="mt-1 rounded-md bg-slate-50 p-2 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                  Catatan: {step.notes}
                </div>
              )}
            </div>
          </div>
        )
      })}
    </div>
  )
}

// --- File Dropzone ---
interface FileDropzoneProps {
  onFilesSelected: (files: File[]) => void
  accept?: string
  maxSizeMB?: number
  multiple?: boolean
}

export const FileDropzone: React.FC<FileDropzoneProps> = ({
  onFilesSelected,
  accept = '.pdf,.doc,.docx,.xls,.xlsx,.png,.jpg',
  maxSizeMB = 10,
  multiple = false,
}) => {
  const [isDragOver, setIsDragOver] = useState(false)
  const fileInputRef = useRef<HTMLInputElement>(null)

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault()
    setIsDragOver(false)
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      onFilesSelected(Array.from(e.dataTransfer.files))
    }
  }

  return (
    <div
      onDragOver={(e) => {
        e.preventDefault()
        setIsDragOver(true)
      }}
      onDragLeave={() => setIsDragOver(false)}
      onDrop={handleDrop}
      onClick={() => fileInputRef.current?.click()}
      className={cn(
        'flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center cursor-pointer transition-colors',
        isDragOver
          ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20'
          : 'border-slate-300 hover:border-indigo-400 bg-white dark:border-slate-700 dark:bg-slate-900'
      )}
    >
      <input
        ref={fileInputRef}
        type="file"
        accept={accept}
        multiple={multiple}
        className="hidden"
        onChange={(e) => {
          if (e.target.files) onFilesSelected(Array.from(e.target.files))
        }}
      />
      <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
        <UploadCloud className="h-5 w-5" />
      </div>
      <p className="mt-2 text-xs font-semibold text-slate-800 dark:text-slate-200">
        Tarik & letakkan file ke sini, atau <span className="text-indigo-600">pilih dari perangkat</span>
      </p>
      <p className="mt-1 text-[11px] text-slate-400">
        Format {accept} (maksimal {maxSizeMB}MB)
      </p>
    </div>
  )
}
