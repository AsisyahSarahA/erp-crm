import React, { useState } from 'react'
import { CurrencyInput } from '@/shared/ui/CurrencyInput'
import { PhoneInput } from '@/shared/ui/PhoneInput'
import { StatusBadge } from '@/shared/ui/StatusBadge'
import { DataTable, ColumnDef } from '@/shared/ui/DataTable'
import { SpreadsheetGrid, RabItem } from '@/shared/ui/SpreadsheetGrid'
import { ConfirmDialog, SlideOver } from '@/shared/ui/Modals'
import { Tabs, Stepper } from '@/shared/ui/Navigation'
import { toast } from '@/shared/ui/toast'
import { ActivityFeed, ApprovalTimeline, FileDropzone } from '@/shared/ui/TimelineAndFiles'
import { EmptyState, ErrorState, TableSkeleton } from '@/shared/ui/FeedbackStates'
import { formatIDR } from '@/shared/utils/formatters'
import { Palette } from 'lucide-react'

interface SampleLead {
  id: string
  code: string
  client: string
  project: string
  value: number
  stage: string
  pic: string
}

const SAMPLE_LEADS: SampleLead[] = [
  { id: '1', code: 'LD-2026-001', client: 'PT Astra International', project: 'Revamp ERP Logistics', value: 350000000, stage: 'negotiation', pic: 'Budi Santoso' },
  { id: '2', code: 'LD-2026-002', client: 'Bank Mandiri', project: 'Mobile Banking Microservice', value: 850000000, stage: 'deal_won', pic: 'Siti Aminah' },
  { id: '3', code: 'LD-2026-003', client: 'Telkom Indonesia', project: 'AI Analytics Gateway', value: 420000000, stage: 'quotation', pic: 'Andi Wijaya' },
  { id: '4', code: 'LD-2026-004', client: 'Kementerian ESDM', project: 'Portal Satu Data', value: 275000000, stage: 'requirement', pic: 'Rina Kurnia' },
  { id: '5', code: 'LD-2026-005', client: 'Fintech Maju Terus', project: 'Payment Gateway Integration', value: 150000000, stage: 'follow_up', pic: 'Budi Santoso' },
  { id: '6', code: 'LD-2026-006', client: 'Startup Logistik Nusantara', project: 'Fleet Tracker IoT', value: 180000000, stage: 'new_lead', pic: 'Siti Aminah' },
]

const INITIAL_RAB: RabItem[] = [
  { id: '1', category: 'Backend', role: 'Senior Go/Laravel Dev', name: 'API Microservices & Outbox Relay', mandays: 10, internalRate: 1500000, marginPercent: 35, clientRate: 2025000 },
  { id: '2', category: 'Frontend', role: 'React 18 / TS Dev', name: 'App Shell & Spreadsheet Grid', mandays: 8, internalRate: 1200000, marginPercent: 30, clientRate: 1560000 },
  { id: '3', category: 'AI & Data', role: 'AI Engineer', name: 'Prompt Registry & LLM Fallback', mandays: 5, internalRate: 1800000, marginPercent: 40, clientRate: 2520000 },
  { id: '4', category: 'QA', role: 'QA Automation', name: 'E2E Deal Won Saga & Regression', mandays: 4, internalRate: 1000000, marginPercent: 25, clientRate: 1250000 },
]

const SAMPLE_ACTIVITIES = [
  { id: '1', user: { name: 'Budi Santoso' }, action: 'mengunggah dokumen TOR KAK pada', target: 'Lead LD-2026-001', timestamp: '2026-11-04T08:00:00Z' },
  { id: '2', user: { name: 'Siti Aminah' }, action: 'mengajukan draf Quotation ke', target: 'Approval Direktur', timestamp: '2026-11-04T07:00:00Z' },
  { id: '3', user: { name: 'System Orchestrator' }, action: 'mengeksekusi saga deal won', target: 'Saga #SG-001', timestamp: '2026-11-04T05:00:00Z' },
]

const SAMPLE_APPROVAL_STEPS = [
  { id: '1', approverRole: 'Sales Manager', approverName: 'Budi Santoso', status: 'APPROVED' as const, notes: 'Margin 32% memenuhi syarat.', updatedAt: '2026-11-04T04:00:00Z' },
  { id: '2', approverRole: 'Finance Controller', approverName: 'Dewi Lestari', status: 'APPROVED' as const, notes: 'TOP Termin DP 50% / Pelunasan 50% disetujui.', updatedAt: '2026-11-04T07:00:00Z' },
  { id: '3', approverRole: 'Direktur Utama', status: 'PENDING' as const, notes: 'Menunggu tanda tangan digital MoU.' },
]

export const DevUiKitPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState('inputs')
  const [currencyVal, setCurrencyVal] = useState<number>(175000000)
  const [phoneVal, setPhoneVal] = useState<string>('+6281234567890')
  const [rabItems, setRabItems] = useState<RabItem[]>(INITIAL_RAB)
  const [stepperIdx, setStepperIdx] = useState(2)

  const [isConfirmOpen, setIsConfirmOpen] = useState(false)
  const [isSlideOverOpen, setIsSlideOverOpen] = useState(false)

  const leadColumns: ColumnDef<SampleLead>[] = [
    {
      id: 'code',
      header: 'Kode Lead',
      accessor: (r) => r.code,
      cell: (r) => <span className="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400">{r.code}</span>,
      sortable: true,
    },
    {
      id: 'client',
      header: 'Customer / Klien',
      accessor: (r) => r.client,
      cell: (r) => (
        <div>
          <p className="font-semibold text-slate-800 dark:text-slate-100">{r.client}</p>
          <p className="text-xs text-slate-400">{r.project}</p>
        </div>
      ),
      sortable: true,
    },
    {
      id: 'value',
      header: 'Estimasi Nilai',
      accessor: (r) => r.value,
      cell: (r) => <span className="font-mono font-semibold">{formatIDR(r.value)}</span>,
      sortable: true,
    },
    {
      id: 'stage',
      header: 'Tahapan Pipeline',
      accessor: (r) => r.stage,
      cell: (r) => <StatusBadge status={r.stage} />,
      sortable: true,
    },
    {
      id: 'pic',
      header: 'Sales PIC',
      accessor: (r) => r.pic,
      sortable: true,
    },
  ]

  return (
    <div className="space-y-8 pb-16">
      {/* Page Title */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
          <div className="flex items-center gap-2">
            <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
              <Palette className="h-4 w-4" />
            </span>
            <h1 className="text-xl font-extrabold text-slate-900 dark:text-slate-100">
              ASTACODE Design System & UI Kit
            </h1>
          </div>
          <p className="mt-1 text-xs text-slate-500">
            Katalog showcase komponen standar sesuai spesifikasi PRD §15 & DESIGN-TOKENS.md.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => setIsSlideOverOpen(true)}
            className="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
          >
            Buka Slide-Over
          </button>
          <button
            onClick={() => setIsConfirmOpen(true)}
            className="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-rose-700"
          >
            Buka Confirm Dialog
          </button>
        </div>
      </div>

      {/* Tabs */}
      <Tabs
        tabs={[
          { id: 'inputs', label: 'Inputs & Formatting' },
          { id: 'badges', label: 'Badges & Statuses' },
          { id: 'datatable', label: 'Data Table' },
          { id: 'spreadsheet', label: 'RAB Spreadsheet Grid' },
          { id: 'timeline', label: 'Timeline & Feed' },
          { id: 'feedback', label: 'States & Skeletons' },
        ]}
        activeTab={activeTab}
        onChange={setActiveTab}
      />

      {/* Tab: Inputs */}
      {activeTab === 'inputs' && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">CurrencyInput (IDR & Terbilang)</h3>
            <p className="text-xs text-slate-500">
              Input nominal otomatis memformat ribuan dan menampilkan konversi kalimat Bahasa Indonesia secara real-time.
            </p>
            <CurrencyInput
              label="Nilai Quotation / Estimasi Deal"
              value={currencyVal}
              onChange={setCurrencyVal}
              helperText="Masukkan angka tanpa simbol Rp"
            />
            <div className="rounded-lg bg-slate-50 p-3 text-xs font-mono dark:bg-slate-800">
              Raw Value (State): {currencyVal}
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">PhoneInput (Format +62 Indonesia)</h3>
            <p className="text-xs text-slate-500">
              Standardisasi input nomor telepon ke format E.164 (+628xxx) dengan verifikasi otomatis.
            </p>
            <PhoneInput
              label="Nomor WhatsApp PIC Klien"
              value={phoneVal}
              onChange={setPhoneVal}
              helperText="Format nomor Indonesia (812... atau 0812...)"
            />
            <div className="rounded-lg bg-slate-50 p-3 text-xs font-mono dark:bg-slate-800">
              E.164 Value (State): {phoneVal}
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4 md:col-span-2">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">Stepper Component</h3>
            <Stepper
              steps={[
                { id: 1, title: 'Lead Qualification', description: 'BANT validation' },
                { id: 2, title: 'RAB & WBS', description: 'Mandays estimation' },
                { id: 3, title: 'Quotation Review', description: 'Legal & margin gate' },
                { id: 4, title: 'Deal Won & Handover', description: 'Project initialization' },
              ]}
              currentStep={stepperIdx}
              onStepClick={setStepperIdx}
            />
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4 md:col-span-2">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">Toast Notifications</h3>
            <div className="flex flex-wrap gap-2">
              <button
                onClick={() => toast.success('Berhasil!', 'Deal Won otomatis membuat proyek PM & jadwal invoice Finance.')}
                className="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700"
              >
                Trigger Success Toast
              </button>
              <button
                onClick={() => toast.error('Gagal!', 'Readiness Check gagal: WBS belum memiliki approval Tech Lead.')}
                className="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-rose-700"
              >
                Trigger Error Toast
              </button>
              <button
                onClick={() => toast.warning('Peringatan Margin', 'Margin 18% di bawah ambang batas (threshold 20%). Memerlukan persetujuan Direktur.')}
                className="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-amber-700"
              >
                Trigger Warning Toast
              </button>
              <button
                onClick={() => toast.info('Info Event Bus', 'Event crm.deal.won telah didispatch ke Orchestrator.')}
                className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-indigo-700"
              >
                Trigger Info Toast
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Tab: Badges */}
      {activeTab === 'badges' && (
        <div className="space-y-6">
          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">CRM Pipeline Stages (Design Tokens §2.3)</h3>
            <div className="flex flex-wrap gap-3">
              <StatusBadge status="new_lead" />
              <StatusBadge status="follow_up" />
              <StatusBadge status="requirement" />
              <StatusBadge status="quotation" />
              <StatusBadge status="negotiation" />
              <StatusBadge status="deal_won" />
              <StatusBadge status="lost" />
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">Document & Workflow Statuses</h3>
            <div className="flex flex-wrap gap-3">
              <StatusBadge status="draft" />
              <StatusBadge status="submitted" />
              <StatusBadge status="in_review" />
              <StatusBadge status="approved" />
              <StatusBadge status="rejected" />
              <StatusBadge status="paid" />
              <StatusBadge status="partially_paid" />
              <StatusBadge status="unpaid" />
              <StatusBadge status="overdue" />
              <StatusBadge status="urgent" />
            </div>
          </div>
        </div>
      )}

      {/* Tab: DataTable */}
      {activeTab === 'datatable' && (
        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <div>
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">DataTable Component</h3>
            <p className="text-xs text-slate-500">Mendukung pencarian cepat, sorting kolom, seleksi baris (bulk actions), dan paginasi.</p>
          </div>
          <DataTable
            data={SAMPLE_LEADS}
            columns={leadColumns}
            keyExtractor={(r) => r.id}
            bulkActions={(selected) => (
              <button
                onClick={() => toast.info('Aksi Massal', `${selected.length} baris telah diekspor.`)}
                className="font-bold underline ml-2"
              >
                Ekspor Terpilih
              </button>
            )}
          />
        </div>
      )}

      {/* Tab: Spreadsheet */}
      {activeTab === 'spreadsheet' && (
        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <div>
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">SpreadsheetGrid — Estimasi Biaya & RAB</h3>
            <p className="text-xs text-slate-500">
              Kalkulator interaktif mandays, cost rate, margin markup %, dan tarif klien dengan tombol switch mode Internal vs Klien.
            </p>
          </div>
          <SpreadsheetGrid items={rabItems} onChange={setRabItems} />
        </div>
      )}

      {/* Tab: Timeline & Feed */}
      {activeTab === 'timeline' && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">ActivityFeed (Audit Trail)</h3>
            <ActivityFeed activities={SAMPLE_ACTIVITIES} />
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">ApprovalTimeline Multi-Level</h3>
            <ApprovalTimeline steps={SAMPLE_APPROVAL_STEPS} />

            <div className="pt-4 border-t border-slate-100 dark:border-slate-800">
              <h4 className="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">FileDropzone</h4>
              <FileDropzone onFilesSelected={(files) => toast.success('File Diterima', `${files.length} file siap diunggah.`)} />
            </div>
          </div>
        </div>
      )}

      {/* Tab: Feedback & States */}
      {activeTab === 'feedback' && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">EmptyState Component</h3>
            <EmptyState
              title="Belum Ada Quotation"
              description="Buat penawaran harga pertama Anda dari pipeline lead."
              action={
                <button
                  onClick={() => toast.info('Aksi', 'Navigasi ke form draf penawaran.')}
                  className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                >
                  + Buat Quotation Baru
                </button>
              }
            />
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">ErrorState Component</h3>
            <ErrorState
              title="Gagal Menghubungi Gateway"
              message="Layanan CRM (Port 8002) sedang sibuk atau mengalami timeout."
              onRetry={() => toast.info('Coba Lagi', 'Menghubungi ulang gateway...')}
            />
          </div>

          <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs dark:border-slate-800 dark:bg-slate-900 space-y-4 md:col-span-2">
            <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">Table Skeleton Loader</h3>
            <TableSkeleton rows={3} />
          </div>
        </div>
      )}

      {/* Modals */}
      <ConfirmDialog
        isOpen={isConfirmOpen}
        onClose={() => setIsConfirmOpen(false)}
        onConfirm={() => {
          setIsConfirmOpen(false)
          toast.success('Dikonfirmasi', 'Tindakan berhasil dieksekusi.')
        }}
        title="Batalkan Lead dan Deal?"
        description="Apakah Anda yakin ingin membatalkan status lead ini? Data pipeline akan diubah menjadi Lost (0%). Tindakan ini tidak dapat dibatalkan secara sepihak."
        variant="danger"
        confirmText="Ya, Batalkan Lead"
      />

      <SlideOver
        isOpen={isSlideOverOpen}
        onClose={() => setIsSlideOverOpen(false)}
        title="Detail Prospek Klien (Slide-Over)"
        subtitle="LD-2026-001 — PT Astra International"
        footer={
          <div className="flex justify-end gap-2">
            <button
              onClick={() => setIsSlideOverOpen(false)}
              className="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700"
            >
              Tutup
            </button>
            <button
              onClick={() => {
                setIsSlideOverOpen(false)
                toast.success('Disimpan', 'Perubahan lead berhasil disimpan.')
              }}
              className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white"
            >
              Simpan Perubahan
            </button>
          </div>
        }
      >
        <div className="space-y-4 text-xs">
          <div>
            <span className="font-semibold text-slate-500">Nilai Prospek:</span>
            <p className="text-base font-bold text-slate-900 dark:text-slate-100 font-mono">Rp 350.000.000</p>
          </div>
          <div>
            <span className="font-semibold text-slate-500">Status Saat Ini:</span>
            <div className="mt-1">
              <StatusBadge status="negotiation" />
            </div>
          </div>
          <div>
            <span className="font-semibold text-slate-500">Catatan Negosiasi:</span>
            <p className="mt-1 text-slate-600 dark:text-slate-400">
              Klien meminta klausul penyesuaian SLA 99.9% dan diskon volume 10%. Menunggu tinjauan tim Legal & Direksi.
            </p>
          </div>
        </div>
      </SlideOver>
    </div>
  )
}
