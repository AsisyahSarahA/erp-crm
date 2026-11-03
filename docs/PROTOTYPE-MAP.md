# ASTACODE ERP — PETA PROTOTIPE KE REACT & PRD (PROTOTYPE MAP)

Dokumen ini memetakan **100% berkas, tampilan, menu, modal, dan interaksi** dari folder prototipe Vanilla JS (`docs/prototype crm/`) ke target rute React 18, arsitektur komponen di `frontend/src/`, kebutuhan API endpoint backend, serta keterlacakan fungsional terhadap PRD (`docs/MASTER PRD.md`).

---

## 1. Peta Rute & Tampilan Aplikasi (100% Cakupan Prototipe)

| # | Halaman / Fitur di Prototipe | Berkas Prototipe Sumber | Target Rute React | Komponen React Target | Endpoint Backend yang Dibutuhkan | Fitur PRD Terkait |
|:---:|---|---|---|---|---|---|
| **01** | **Global Shell & Topbar** | `Header.js` | `/*` (Layout) | `frontend/src/shared/layout/AppHeader.tsx` | `GET /api/v1/core/me`<br>`GET /api/v1/core/notifications`<br>`POST /api/v1/core/auth/logout` | §3 RBAC, §15 Layout, §5.6 Notifikasi |
| **02** | **Unified Sidebar Nav** | `Sidebar.js`, `main.js` | `/*` (Layout) | `frontend/src/shared/layout/AppSidebar.tsx` | `GET /api/v1/core/me/permissions`<br>`GET /api/v1/crm/pipeline/counts` | §15.2 Layout Global, §3.2 Matriks Peran |
| **03** | **Command Palette (Cmd+K)** | `common/CommandPalette.js` | Global modal | `frontend/src/shared/components/CommandPalette.tsx` | `GET /api/v1/gateway/search/quick?q=` | §15.3, A.7 Pencarian Global |
| **04** | **Pipeline Kanban Board** | `crm/PipelineKanban.js` | `/crm/pipeline` (`?view=kanban`) | `frontend/src/features/crm/pages/PipelineKanbanPage.tsx`<br>`frontend/src/features/crm/components/PipelineBoard.tsx` | `GET /api/v1/crm/leads?view=kanban`<br>`PATCH /api/v1/crm/leads/{id}/stage`<br>`POST /api/v1/crm/leads/{id}/reject` | FR-CRM-02 (Pipeline Kanban), FR-CRM-10 (Reject/Lost), BR-CRM-04..08 |
| **05** | **Pipeline Dense Table** | `crm/PipelineTable.js` | `/crm/pipeline` (`?view=table`) | `frontend/src/features/crm/pages/PipelineTablePage.tsx`<br>`frontend/src/features/crm/components/PipelineTable.tsx` | `GET /api/v1/crm/leads?page=1&per_page=10`<br>`GET /api/v1/crm/leads/export-csv` | FR-CRM-02 (Pipeline Table), FR-CRM-24 (Audit Trail) |
| **06** | **Pipeline Workflow Tracker** | `crm/PipelineWorkflowTracker.js` | `/crm/pipeline` (`?view=workflow`) | `frontend/src/features/crm/pages/PipelineWorkflowPage.tsx`<br>`frontend/src/features/crm/components/WorkflowGates.tsx` | `GET /api/v1/crm/leads/workflow-status`<br>`GET /api/v1/crm/activities/timeline`<br>`GET /api/v1/crm/stage-files` | FR-CRM-20 (Files & Stage Docs), FR-CRM-24 (Pipeline Guard & Audit) |
| **07** | **Modal Tambah Lead Baru** | `crm/NewLeadModal.js` | Global / Trigger Button | `frontend/src/features/crm/components/NewLeadModal.tsx` | `POST /api/v1/crm/leads`<br>`GET /api/v1/crm/customers/lookup`<br>`GET /api/v1/core/users?role=sales` | FR-CRM-01 (Input Lead & Auto-Master), BR-CRM-01, BR-CRM-02 |
| **08** | **Modal Alasan Reject Lead** | `crm/PipelineKanban.js` (inline modal) | Inline modal | `frontend/src/features/crm/components/RejectLeadModal.tsx` | `POST /api/v1/crm/leads/{id}/reject` | FR-CRM-10 (Reject/Lost), BR-CRM-08, AC-CRM-13 |
| **09** | **Detail Workspace Lead** | `crm/LeadDetailPage.js` | `/crm/leads/:id` | `frontend/src/features/crm/pages/LeadDetailPage.tsx` | `GET /api/v1/crm/leads/{id}`<br>`PUT /api/v1/crm/leads/{id}` | FR-CRM-03 (Detail Lead Slide-over / Full Page) |
| **10** | ↳ *Sub-tab 1: Overview* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=overview` | `frontend/src/features/crm/components/lead-detail/TabOverview.tsx` | `GET /api/v1/crm/leads/{id}/summary` | FR-CRM-03, FR-CRM-12 (Lead Scoring) |
| **11** | ↳ *Sub-tab 2: Activity & Jadwal* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=activity` | `frontend/src/features/crm/components/lead-detail/TabActivity.tsx` | `GET /api/v1/crm/leads/{id}/activities`<br>`POST /api/v1/crm/leads/{id}/activities` | FR-CRM-04 (Activity & Penjadwalan, Google Meet/Calendar) |
| **12** | ↳ *Sub-tab 3: Files & Stage Docs* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=files` | `frontend/src/features/crm/components/lead-detail/TabFilesDocs.tsx` | `GET /api/v1/crm/leads/{id}/files`<br>`POST /api/v1/core/files/upload` | FR-CRM-20 (Repositori Dokumen per Tahapan) |
| **13** | ↳ *Sub-tab 4: Voice Note Presales* | `crm/VoiceNoteAndDocsManager.js` | `/crm/leads/:id?tab=voice-notes` | `frontend/src/features/crm/components/lead-detail/TabVoiceNotes.tsx` | `POST /api/v1/crm/leads/{id}/voice-notes`<br>`POST /api/v1/ai/transcribe`<br>`POST /api/v1/ai/summarize-voice-note` | FR-CRM-19 (Voice Note Recorder & AI Transcriber Presales) |
| **14** | ↳ *Sub-tab 5: Requirement & AI* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=requirement` | `frontend/src/features/crm/components/lead-detail/TabRequirement.tsx` | `GET /api/v1/crm/leads/{id}/requirements`<br>`POST /api/v1/crm/leads/{id}/requirements/generate-rab` | FR-CRM-05 (Requirement + AI Generate RAB Mandays) |
| **15** | ↳ *Sub-tab 6: RAB Spreadsheet* | `crm/LeadDetailPage.js`, `utils/rabCalculator.js` | `/crm/leads/:id?tab=rab` | `frontend/src/features/crm/components/lead-detail/TabRabSpreadsheet.tsx` | `GET /api/v1/crm/leads/{id}/rab`<br>`PUT /api/v1/crm/leads/{id}/rab`<br>`POST /api/v1/crm/leads/{id}/rab/versions` | FR-CRM-06 (RAB Editable, Mandays, Dual-View Internal vs Client), BR-CRM-05 |
| **16** | ↳ *Sub-tab 7: Setting TOP (Termin)*| `crm/LeadDetailPage.js`, `utils/topCalculator.js` | `/crm/leads/:id?tab=top` | `frontend/src/features/crm/components/lead-detail/TabTopSetting.tsx` | `GET /api/v1/crm/leads/{id}/top`<br>`PUT /api/v1/crm/leads/{id}/top` | FR-CRM-07 (Setting TOP, Validasi 100%), BR-CRM-06, AC-CRM-12 |
| **17** | ↳ *Sub-tab 8: Quotation + MoU* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=quotation` | `frontend/src/features/crm/components/lead-detail/TabQuotationMou.tsx` | `GET /api/v1/crm/leads/{id}/quotations`<br>`POST /api/v1/crm/leads/{id}/quotations/generate-pdf`<br>`POST /api/v1/core/approvals/discount` | FR-CRM-08 (Generate Quotation + MoU 1 PDF), FR-CRM-17, BR-CRM-07 |
| **18** | ↳ *Sub-tab 9: Contacts 360* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=contacts` | `frontend/src/features/crm/components/lead-detail/TabContacts.tsx` | `GET /api/v1/crm/customers/{customerId}/contacts`<br>`POST /api/v1/crm/customers/{customerId}/contacts` | FR-CRM-13 (Master Data Customer 360°) |
| **19** | ↳ *Sub-tab 10: Catatan & Histori* | `crm/LeadDetailPage.js` | `/crm/leads/:id?tab=history` | `frontend/src/features/crm/components/lead-detail/TabHistory.tsx` | `GET /api/v1/crm/leads/{id}/audit-trail` | FR-CRM-24 (Pipeline Guard & Audit Trail) |
| **20** | **Master Customer 360°** | `customers/CustomersList.js` | `/crm/customers` | `frontend/src/features/crm/pages/CustomersListPage.tsx`<br>`frontend/src/features/crm/components/CustomerDetailDrawer.tsx` | `GET /api/v1/crm/customers`<br>`POST /api/v1/crm/customers`<br>`GET /api/v1/crm/customers/{id}/360` | FR-CRM-13 (Master Customer 360, Profil, Dokumen, Riwayat Transaksi) |
| **21** | **Setting TOP & Invoicing (Finance)**| `finance/SettingTopInvoicePage.js` | `/finance/top-invoices` | `frontend/src/features/finance/pages/FinanceTopInvoicesPage.tsx` | `GET /api/v1/finance/receivables/projects`<br>`POST /api/v1/finance/invoices/issue`<br>`POST /api/v1/finance/invoices/{id}/mark-paid` | FR-FIN-01 (Invoicing & Termin AR), FR-CRM-22 (Kalender Penagihan AR) |
| **22** | **Legal Studio (22 Pasal MoU & Addendum)** | `legal/ContractsLegalPage.js` | `/crm/contracts` | `frontend/src/features/crm/pages/ContractsLegalPage.tsx`<br>`frontend/src/features/crm/components/ClauseEditorModal.tsx` | `GET /api/v1/crm/contracts/clauses`<br>`PUT /api/v1/crm/contracts/clauses/{id}`<br>`POST /api/v1/crm/contracts/addendum`<br>`GET /api/v1/crm/contracts/esign-logs` | FR-CRM-21 (Contracts & Legal Studio, 22 Pasal MoU, Addendum, Log e-Sign), FR-CRM-14 (Contracts & Renewal) |
| **23** | **WhatsApp Client Care Inbox** | `clientcare/WhatsAppInbox.js` | `/crm/client-care` | `frontend/src/features/crm/pages/WhatsAppInboxPage.tsx`<br>`frontend/src/features/crm/components/ChatThreadView.tsx` | `GET /api/v1/crm/wa/conversations`<br>`POST /api/v1/crm/wa/messages/send`<br>`POST /api/v1/ai/wa-draft-reply` | FR-CRM-11 (WhatsApp Client Care Inbox AI), BR-WA-01..04 |
| **24** | **Delivery Command Center & Projects** | `projects/ProjectsList.js` | `/pm/projects` | `frontend/src/features/pm/pages/ProjectsListPage.tsx`<br>`frontend/src/features/pm/components/ProjectIntakeModal.tsx` | `GET /api/v1/pm/projects`<br>`POST /api/v1/pm/projects/intake`<br>`GET /api/v1/pm/projects/{id}/handover-status` | FR-PM-01 (Intake Deal Won 3 Langkah), FR-CRM-09, FR-CRM-22, FR-CRM-23 (Readiness & Kickoff Gate) |
| **25** | **Executive Analytics & Reports** | `reports/ReportsDashboard.js` | `/crm/reports` | `frontend/src/features/crm/pages/CrmReportsPage.tsx`<br>`frontend/src/features/crm/components/FunnelChart.tsx` | `GET /api/v1/crm/reports/funnel`<br>`GET /api/v1/crm/reports/attribution`<br>`GET /api/v1/crm/reports/forecast` | FR-CRM-15 (Forecast, Target & Analytics, Attribution & Win/Loss) |

---

## 2. Struktur Komponen Shared & Utility Prototipe

| Utilitas Prototipe | Lokasi Prototipe | Rencana Implementasi di React (`frontend/src/shared/`) | Deskripsi Fungsi |
|---|---|---|---|
| `topCalculator.js` | `docs/prototype crm/js/utils/topCalculator.js` | `src/shared/utils/money.ts`<br>`src/shared/utils/terbilang.ts`<br>`src/features/crm/utils/topCalculator.ts` | Konversi Rupiah `formatRupiah()`, fungsi terbilang Indonesia `terbilang()`, rumus skema termin `calculateTopScheme()`, template termin preset (`30/40/30`, `50/50`, `20/30/30/20`, dsb.) |
| `rabCalculator.js` | `docs/prototype crm/js/utils/rabCalculator.js` | `src/features/crm/utils/rabCalculator.ts` | Kalkulasi otomatis mandays per role, biaya pokok (HPP internal), margin kotor, estimasi contingency, kalkulasi dual-view (internal vs client rate) |
| `pipelineRules.js` | `docs/prototype crm/js/utils/pipelineRules.js` | `src/features/crm/utils/pipelineRules.ts` | Definisi 7 stage pipeline, probabilitas default, aturan transisi stage (BR-CRM-04, BR-CRM-07, BR-CRM-08), kalkulasi metrik ringkasan pipeline |
| `pagination.js` | `docs/prototype crm/js/utils/pagination.js` | `src/shared/components/DataTablePagination.tsx` | Helper paginasi array client-side / server-side standard (`paginate(items, page, pageSize)`) |
| `seedData.js` | `docs/prototype crm/js/data/seedData.js` | `src/mocks/seedData.ts`<br>`backend/services/crm/database/seeders/` | Data demo: 22 pasal baku MoU ASTACODE, list leads awal, customers awal, percakapan WhatsApp contoh, skema invoice demo |

---

## 3. Strategi Transisi Kode (Vanilla JS ke React 18 + TS)

1. **Struktur DOM & Kelas CSS:** Semua kelas CSS semantik di prototipe (mis. `.kanban-board`, `.kanban-col`, `.btn-primary`, `.badge-stage`, `.tab-item`, `.app-header`) dipetakan ke Tailwind CSS utilities yang ekuivalen menggunakan token di `docs/DESIGN-TOKENS.md`.
2. **State Management:**
   - Server State (Leads, Customers, Quotations, Invoices): Menggunakan **TanStack Query (React Query v5)** dengan cache invalidation berbasis event SSE atau mutasi sukses.
   - Client UI State (Selected Lead ID, Active View Mode, Dark Mode, Sidebar Open): Menggunakan **Zustand** store (`useCrmStore`, `useUiStore`).
3. **Optimistic Updates:** Drag-and-drop pada Kanban board (`@dnd-kit`) langsung memperbarui tampilan lokal secara optimistik, lalu menembak `PATCH /api/v1/crm/leads/{id}/stage`. Jika gagal atau ditolak oleh validasi bisnis server (mis. BR-CRM-07: belum ada RAB saat mau ke Quotation), posisi kartu di-rollback otomatis dan toast error dimunculkan.
4. **Respon Error & Validasi:** Mengikuti format RFC 7807 dari backend Laravel. Field error langsung diikat ke form dengan `react-hook-form` + `zod`.

