# LEDGER PROGRES PENGEMBANGAN — ASTACODE ERP ORCHESTRATOR

Dokumen ini adalah ledger resmi pelacak status seluruh task pengerjaan proyek ASTACODE ERP.
Sesuai **Protokol Lanjutkan (Master Prompt Bagian 0.2)**, setiap sesi kerja memperbarui status dan checklist dokumen ini.

Terakhir diperbarui: 2026-11-04 20:20 WIB

---

## T00 — Analisis & Audit Mendalam (WAJIB PERTAMA)
Status: DONE
Checklist OUTPUT TARGET:
- [x] docs/ANALYSIS.md, docs/EVENT-CATALOG.md, docs/PROTOTYPE-MAP.md, docs/DESIGN-TOKENS.md, docs/PROGRESS.md (semua terisi lengkap, bukan kerangka).
- [x] Ringkasan akhir ≤ 15 baris di chat: temuan kritis + pertanyaan yang benar-benar memblokir.
Catatan keputusan: Seluruh audit PRD, event catalog, prototype map, design tokens, dan struktur microservice telah dirumuskan secara lengkap.
Utang teknis / TODO: Tidak ada (selesai 100%).
Terakhir diperbarui: 2026-11-04 20:20 WIB

## T01 — [BE] Scaffold Monorepo Microservice (tanpa Docker)
Status: DONE
Checklist OUTPUT TARGET:
- [x] backend/gateway, backend/services/* (9 app Laravel), backend/shared/astacode-kernel, backend/scripts/* berjalan; `./scripts/dev-up.sh && ./scripts/status.sh` menampilkan semua service UP.
- [x] Test kernel: Money, Terbilang, HMAC signer, FieldMasker, Idempotency (Pest) lulus (16 tests, 34 assertions passed).
- [x] backend/README.md + .env.shared.example.
Catatan keputusan: Monorepo microservice terstruktur menjadi 1 Gateway + 8 Service + shared kernel astacode/astacode-kernel. Seluruh service berjalan tanpa Docker dengan database terisolasi dan skrip manajemen dev terpadu.
Utang teknis / TODO: Tidak ada (selesai 100%).
Terakhir diperbarui: 2026-11-03 14:48 WIB

## T02 — [BE] Core Service: Auth, RBAC, Audit, Notifikasi, Files, Numbering, Settings
Status: DONE
Checklist OUTPUT TARGET:
- [x] Endpoint: /auth/*, /me, /users, /roles, /permissions, /audit-logs, /notifications, /files, /approvals, /settings, /numbering, /webhooks, /integrations (+OpenAPI di backend/services/core/openapi.yaml).
- [x] Pest/PHPUnit: login+2FA, lockout, policy kepemilikan, masking, audit immutable, numbering concurrency (paralel tidak duplikat), approval serial/paralel (22 tests, 91 assertions passed).
- [x] AC: E2E-08 (user Sales mengakses data gaji/HPP via API → 403 Forbidden / field sensitif HPP & margin dimasking '******' + tercatat di audit_logs immutable).
Catatan keputusan: Mengimplementasikan Sanctum SPA/API Auth dengan 2FA RFC 6238 TOTP, brute force lockout (5 percobaan), Spatie-compatible RBAC (18 peran PRD dan permission granular per modul), AssertDifferentUser Segregation of Duties (BR-FIN-08), Append-only immutable AuditLog (mencegah update/delete pada model level), NumberingService atomik dengan row lock SELECT FOR UPDATE sesuai Lampiran C PRD, Generic ApprovalEngine dengan transisi multi-tahap dan event outbox approval.decided, Webhooks dengan signature HMAC & replay protection, serta Files dengan VirusScanner & signed temporary URLs.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 15:10 WIB

## T03 — [BE] Orchestrator Service: Event Bus, Saga Deal-Won, Automation Rule Engine
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi: event_log, subscriptions, sagas, saga_steps, automation_rules, automation_runs, processed_events, read_models di backend/services/orchestrator/database/migrations/.
- [x] Test: saga sukses, saga partial + retry, idempotensi (event ganda tidak membuat proyek ganda — BR-CRM-13), rule engine kondisi/aksi, dead-letter (15 tests, 64 assertions passed).
- [x] DoD: Simulasi deal.won dengan konsumen stub menghasilkan status handover COMPLETED dan dapat di-retry per langkah; Kickoff Gate & Readiness Check (FR-CRM-23) teruji; kontrak di docs/API-CONTRACTS/orchestrator.yaml.
Catatan keputusan: Event Broker terpusat memproses event asinkron via transactional outbox dengan verifikasi HMAC internal. Distributed Saga Deal-Won mengkoordinasikan 4 langkah handover (CRM Customer 360 -> PM Project/Tasks WBS -> Finance Receivable Schedule -> HRD Capacity Demand) dengan kunci idempoten 'deal_id+pm' (BR-CRM-13) dan mendukung kompensasi manual. Rule engine no-code mengevaluasi kondisi JSON-logic aman (tanpa eval) dengan integrasi adapter WhatsApp, Email, dan Telegram.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 15:26 WIB

## T04 — [BE] AI Service (AI Gateway)
Status: DONE
Checklist OUTPUT TARGET:
- [x] Endpoint: POST /internal/ai/run, GET /internal/ai/runs/{id}, /prompts CRUD, /providers, /metrics, /budgets (terdaftar di backend/services/ai/routes/).
- [x] Test: schema validation+retry, fallback provider, guardrail injection, budget limit (8 tests, 93 assertions passed).
- [x] DoD: Panggilan generate RAB dengan mock provider mengembalikan JSON valid sesuai skema; ai_runs tercatat (terverifikasi di RabGeneratorTest).
- [x] Kontrak API: docs/API-CONTRACTS/ai.yaml dibuat lengkap (OpenAPI 3.1).
Catatan keputusan: AI Gateway mengisolasi akses LLM untuk semua microservice dengan abstraksi provider (Gemini, Claude, OpenAI, Mock Deterministic), auto-fallback berantai, Prompt Registry berversi dengan rendering placeholder {{var}}, penegakan skema JSON terstruktur (RAB, voicenote, wa_reply, dll.) dengan auto-repair, guardrail deteksi prompt injection dan masking PII, anggaran biaya bulanan per modul, RAG MemoryVectorStore terisolasi per customer, serta observabilitas ai_runs dan metrik akurasi MAPE.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 15:38 WIB

## T05 — [FE] Fondasi Frontend: App Shell, Design System, Guard, API Client
Status: DONE
Checklist OUTPUT TARGET:
- [x] Aplikasi berjalan `npm run dev` & `npm run build`; login → shell → navigasi modul (menu kosong per modul diisi task berikutnya); /dev/ui-kit lengkap; tampilan konsisten dengan prototipe.
- [x] Design Tokens terimplementasi (`docs/DESIGN-TOKENS.md`) pada Tailwind v4 & CSS variables (`:root` dan `.dark`, WCAG 2.1 AA, palet warna pipeline CRM & status dokumen).
- [x] API Client (Fetch wrapper dengan penanganan RFC 7807 problem details, Sanctum CSRF, header `X-Correlation-Id`, `Idempotency-Key`, auto-retry GET).
- [x] Auth Store (Zustand) dengan login, logout, sesi 2FA TOTP challenge, `usePermissions()` hook, dan `<Can>` permission guard.
- [x] Route Guards: `AuthGuard`, `GuestGuard`, `PermissionGuard`, serta halaman error `403 Forbidden`, `404 Not Found`, dan `500 Server Error`.
- [x] Global Layout & Shell: `AppHeader` (global search Cmd+K, SSE notifications, quick-create, dark/light mode toggle, user profile), collapsible `AppSidebar` dengan menu berbasis permission, `Breadcrumbs`, dan `CommandPalette`.
- [x] Komponen Inti UI: `CurrencyInput` (IDR + Terbilang real-time), `PhoneInput` (+62 format E.164), `StatusBadge` (semua tahapan pipeline & dokumen), `DataTable` (pencarian, sorting, paginasi, bulk actions), `SpreadsheetGrid` (RAB kalkulasi mandays, margin %, client rate, dual view Internal vs Klien), `ConfirmDialog`, `SlideOver`, `Tabs`, `Stepper`, `Toast`, `EmptyState`, `ErrorState`, `TableSkeleton`, `ActivityFeed`, `ApprovalTimeline`, dan `FileDropzone`.
- [x] Dev UI Kit Showcase di `/dev/ui-kit` menampilkan seluruh komponen secara interaktif.
- [x] Vitest & Unit Tests: 4 test suites (11 tests) lulus (`currencyInput.test.ts`, `phoneInput.test.ts`, `permissionGuard.test.tsx`, `rabSpreadsheet.test.ts`).
- [x] Linting & Typecheck: `npm run lint` dan `npm run build` (`tsc -b && vite build`) lulus 100% tanpa error.
Catatan keputusan: Fondasi Frontend React 18 + TS + Tailwind v4 mengacu presisi pada design tokens prototipe CRM (`docs/DESIGN-TOKENS.md`). Seluruh panggilan backend diarahkan melalui API Gateway (`/api/v1`), mendukung multi-step 2FA TOTP, RBAC granular, dan kalkulator estimasi RAB interaktif dengan navigasi keyboard.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 09:22 WIB

## T06 — [BE] Kernel Terbilang, Pajak, Money & Util Dokumen (jika belum tuntas di T01)
Status: DONE
Checklist OUTPUT TARGET:
- [x] Terbilang (`Astacode\Kernel\Support\Terbilang`) menangani konversi angka integer, desimal/sen, nilai negatif, hingga triliun ke teks bahasa Indonesia, termasuk contoh kasus PRD "Rp92.966.940" -> "Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah" (35 kasus uji dalam data provider).
- [x] Kalkulator RAB (`Astacode\Kernel\Finance\RabCalculator` & `RabCalculationResult`): subtotal item, kontingensi risiko default 10% (wajib alasan jika < 5% sesuai PRD §6.4), diskon (% / nominal), DPP, PPN 11% konfigurabel, total investasi, terbilang otomatis, simulasi HPP & margin gross, serta pembagian termin TOP dengan penyerapan selisih pembulatan pada termin terakhir (PRD FR-CRM-07).
- [x] Kalkulator Pajak Termin (`Astacode\Kernel\Finance\TaxCalculator` & `TaxCalculationResult`): menghitung DPP, PPN 11%, pemotongan PPh 23 2% (jika pemotong pajak), total tagihan invoice, dan kas bersih diterima (15 kasus uji dalam data provider).
- [x] Perhitungan Margin (`Astacode\Kernel\Finance\MarginCalculator`): gross margin %, indikator warna visual (`GREEN` ≥ 40%, `YELLOW` 25–40%, `RED` < 25%), serta trigger persetujuan direksi bila margin < 25%.
- [x] Hash Dokumen SHA-256 (`Astacode\Kernel\Document\DocumentHasher`): hash string/file, verifikasi tamper-proof, dan format fingerprint e-Meterai.
- [x] QR Verifikasi Generator (`Astacode\Kernel\Document\DocumentVerificationQr`): generate verification URL, structured audit payload, dan standalone vector SVG QR (resolusi 300 DPI ready, tanpa binary eksternal).
- [x] Normalisasi Telepon E.164 (`Astacode\Kernel\Support\PhoneNormalizer`): standardisasi nomor Indonesia (`+628...`), validasi cellular, dan format tampilan.
- [x] Fuzzy Matching Perusahaan (`Astacode\Kernel\Support\CompanyMatcher`): pembersihan noise token legalitas Indonesia (PT, CV, UD, Tbk, Persero, dll.), similarity score bertingkat, dan deduplikasi threshold ≥ 85%.
- [x] Kalender Kerja & Libur Indonesia (`Astacode\Kernel\Support\WorkdayCalendar`): deteksi hari kerja vs akhir pekan, daftar hari libur nasional Indonesia, kalkulasi deadline SLA (melewati libur/akhir pekan), dan penambahan libur kustom.
- [x] Test Suite: 96 unit tests dengan 225 assertions lulus 100% pada kernel, mencakup ≥ 50 kasus uji Terbilang & kalkulasi pajak.
Catatan keputusan: Seluruh perhitungan moneter dan pajak menggunakan presisi desimal tanpa floating point error (`Money` + `bcmath`), siap dipakai ulang oleh `crm-service`, `finance-service`, dan `pm-service`.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 09:32 WIB

## T07 — [FE] Shell Navigasi Seluruh Modul + Dashboard Landing
Status: DONE
Checklist OUTPUT TARGET:
- [x] Semua rute terdaftar (95 rute PRD §15.4 lengkap mencakup CRM 21 menu §6.2, Marketing 13 menu §7.2, HRD 14 menu §8.2, PM 15 menu §9.2, Finance 17 menu §10.2, Portal 5 menu, Admin 6 menu, AI Studio 4 menu) dengan lazy loading (`React.lazy` + `Suspense`).
- [x] Navigasi lengkap: `AppSidebar` dengan module switcher dinamis, responsive collapsible, deteksi module berbasis URL aktif, dan breadcrumb otomatis dinamis (`Breadcrumbs.tsx`).
- [x] Guard permission bekerja: filter menu sidebar & rute terproteksi via `PermissionGuard` dan `usePermissions()`.
- [x] PlaceholderPage informatif untuk rute bertahap berlabel jelas "Belum diimplementasikan — task <ID>" memuat referensi PRD, peran akses, dan endpoint API yang direncanakan (tanpa data palsu).
- [x] FeatureErrorBoundary pada setiap modul/fitur untuk graceful degradation.
- [x] Dashboard eksekutif lintas modul (`/dashboard`): KPI cards (CRM Pipeline, PM Active Projects, Finance AR/Cashflow, HRD Capacity, Marketing Reach), widget "Tindakan Prioritas Saya", widget "Saga Deal Won Tracker", dan empty states.
- [x] Vitest route registry test `navigationRoutes.test.ts` (8 tests) lulus 100%, total frontend tests 19 tests lulus; linting 0 error.
Catatan keputusan: Seluruh 95 rute PRD dipetakan dalam `routeMetadata.ts` dengan metadata task ID, modul, PRD ref, dan permission, mempermudah implementasi bertahap pada task FE berikutnya.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 15:15 WIB

## T08 — [BE] Data Master Bersama & Seeder Demo Lintas Modul
Status: DONE
Checklist OUTPUT TARGET:
- [x] `backend/scripts/seed-all.sh` (+ `--demo`): skrip seeder lintas service yang mendukung mode master only maupun master + full demo data dengan eksekusi aman dan idempoten.
- [x] Seeder master idempoten per service (`crm`, `finance`, `hrd`, `pm`, `marketing`, `core`, `ai`, `orchestrator`) menggunakan `updateOrInsert`/`updateOrCreate` tanpa duplikasi saat dijalankan berulang.
- [x] Chart of Accounts (COA) 23 akun persis sesuai PRD Lampiran A (18 akun asli + 4 akun tambahan: 2040, 2050, 5040, 6050) beserta kategori dan normal balance (D/K).
- [x] Posting Rules default (§10.5) untuk 11 event otomatisasi akuntansi akrual (penerbitan invoice termin/DP, pengakuan pendapatan DP, kas masuk & bukti potong PPh 23, reimbursement, SPK freelance, tagihan AWS, payroll, dan transfer internal).
- [x] Template Quotation & MoU + 22 Pasal baku standar software house (Lampiran H) terdaftar lengkap dalam database `crm_contract_clauses` dan `crm_document_templates` dengan merge variables.
- [x] Lead Sources mencakup seluruh 13 kanal FR-CRM-01 dan Rejection Reasons mencakup seluruh 8 kategori FR-CRM-10.
- [x] Rate Cards lengkap role × level (Junior, Mid, Senior, Lead untuk PM, SA, BE, FE, Mobile, QA, UI/UX, DevOps) dengan rate HPP internal dan sell rate klien.
- [x] Module Catalog mencakup 43 modul software house (≥ 40 modul baku) dengan estimasi mandays per role (JSON).
- [x] Canned responses WhatsApp (Follow up penawaran, Pengingat UAT, Notifikasi invoice termin, Onboarding, Discovery call).
- [x] Master HRD & Core: jenis cuti (Leave Types), shift kerja (Normal, Flexi, Night > 22:00), kuota PKL per divisi, kalender libur nasional 2026, dan kebijakan SLA terkonfigurasi.
- [x] Seeder Demo Lintas Modul (`--demo` / `SEED_DEMO=1`):
  - 10 Customer 360 lengkap dengan data legal, NPWP 16 digit, alamat, dan PIC.
  - 30 Lead terdistribusi realistis pada seluruh 7 tahapan pipeline (New Lead, Follow Up, Requirement, Quotation, Negotiation, Deal Won, Lost) beserta draf quotation.
  - 5 Proyek aktif/selesai di PM (menyesuaikan deal won) dengan milestone termin dan WBS backlog tasks.
  - 16 Karyawan realistis di HRD (Tetap, Freelance, PKL) dengan data presensi dan daily log berkait task.
  - Invoices dan Jurnal Umum seimbang (Debit = Kredit) untuk transaksi AR, payroll, pengeluaran kantor, dan transfer kas.
- [x] Test Suites: Test feature seeder lulus 100% pada semua service (`crm`, `finance`, `hrd`, `pm`, `marketing`, `core`, `ai`, `orchestrator`), total 158 tests dan 1.021 assertions lulus tanpa warning.
Catatan keputusan: Seluruh data master dan demo mengacu penuh pada PRD §6.8, §10, Lampiran A, dan Lampiran H. Mekanisme seeder idempoten terverifikasi ganda tanpa duplikasi key.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 15:43 WIB

## T09 — [BE+FE] Kontrak API Terpadu & Generasi Tipe
Status: DONE
Checklist OUTPUT TARGET:
- [x] gateway.yaml: Spesifikasi terpadu OpenAPI 3.1.0 (`docs/API-CONTRACTS/gateway.yaml`) menggabungkan seluruh endpoint Gateway (`/api/health`) dan 8 microservice (`/api/v1/{service}/*`) dengan skema data lengkap (RFC 7807 problem details, UserResponse, Lead, Customer, Project, Invoice, Employee, dll.).
- [x] Dokumen Konvensi (`docs/API-CONTRACTS/CONVENTIONS.md`): Konvensi reverse proxy URL, amplop response standar, penanganan error RFC 7807, filtering (`filter[field]`), sorting multi-kolom (`sort`), eager loading (`include`), paginasi page & cursor, serta header wajib (`X-Correlation-Id`, `Idempotency-Key`, `Authorization`).
- [x] Koleksi Postman (`docs/API-CONTRACTS/ASTACODE_ERP.postman_collection.json`): Koleksi Postman v2.1 terstruktur per 8 folder service dengan preset variabel environment (`{{baseUrl}}`, `{{token}}`), request payloads, dan test scripts verifikasi status code.
- [x] Generator Tipe TS & Hook (`frontend/scripts/generate-api.js` + `npm run gen:api`): Skrip native Node.js tanpa dependensi eksternal yang mem-parsing `gateway.yaml` dan menghasilkan `api-types.ts` dan hook TanStack Query v5 `hooks.ts` (`useGatewayHealthQuery`, `useCurrentUserQuery`, `useLeadsQuery`, `useProjectsQuery`, `useInvoicesQuery`, dll.).
- [x] CI Contract Test (`backend/scripts/contract-test.sh` + `backend/gateway/tests/Feature/GatewayOpenApiContractTest.php`): Otomatisasi pengujian kontrak di CI memvalidasi spesifikasi OpenAPI vs route registri gateway `services_map.php`, validasi RFC 7807, dan validasi response `api/health`.
- [x] DoD Terpenuhi: Perubahan endpoint tanpa memperbarui OpenAPI membuat CI gagal (exit code 1 di `contract-test.sh`).
Catatan keputusan: Seluruh komunikasi frontend ke backend diarahkan melalui API Gateway terpadu dengan standar OpenAPI 3.1 dan RFC 7807 problem details. Generator tipe dan hook TanStack Query terotomatisasi via script `npm run gen:api`.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-01 — Skema, Model & Master Customer/Contact/Lead Source
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi Customer 360, Contact, Lead Source, Attribution, Custom Fields, Merge History, dan Contracts di `backend/services/crm/database/migrations/`.
- [x] Model Eloquent dengan cast NPWP terenkripsi (`EncryptedNpwpCast`), normalisasi telepon E.164 (`PhoneNormalizer`), kalkulasi Health Score 0–100 (`calculateHealthScore`), dan scope `accessibleBy`.
- [x] Resource API dengan server-side field masking NPWP (`CustomerResource`, `Customer360Resource`, `ContactResource`) sesuai permission `crm.npwp.view_unmasked` dan `finance.tax.view`.
- [x] Policy `CustomerPolicy`: Sales hanya dapat mengakses customer milik sendiri dan tim; Finance AR dapat memverifikasi NPWP (`verify-npwp`).
- [x] Layanan deteksi duplikat multi-faktor (`CustomerDuplicateDetector`): WA E.164, email, NPWP hash, dan nama perusahaan fuzzy matching ≥ 85% (`CompanyMatcher`).
- [x] Penggabungan customer dengan histori terjaga (`CustomerMerger` - BR-CRM-09) dan anonimisasi data pribadi UU PDP (`CustomerPdpAnonymizer`).
- [x] Endpoint Customer 360, export/import CSV, CRUD Kontak, Lead Sources, dan Custom Fields terdaftar dan teruji.
- [x] Test Suite `CrmCustomer360AndDuplicateTest` (7 tests, 461 assertions) lulus 100%; DoD AC-CRM-02 terpenuhi di level API.
Catatan keputusan: Seluruh skema master customer, kontak stakeholder, deteksi duplikat, dan keamanan data (enkripsi NPWP & masking) telah aktif di crm-service.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:05 WIB

## CRM-BE-02 — Lead, Input Manual/Web/Marketing, Scoring, Auto-assign
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi tabel `crm_leads` (kolom lengkap PRD §6.8: code LD-YYYY-NNNN, customer_id, contact_id, source_id, utm JSON, stage_id enum, win_probability, est_value, budget_range, target_closing, target_golive, owner_id, score, score_breakdown, score_label, days_in_stage, stale_since, is_stale, has_next_step, active_quotation_id, reject_reason_code, won_at, lost_at, reopened_at, industry, services, tags, consent), `crm_lead_stage_histories`, `crm_lead_watchers`, `crm_activities`, dan `crm_saved_views`.
- [x] Endpoint `POST /leads` (FR-CRM-01): validasi judul, PIC min 2 karakter, WA wajib (form manual) dengan normalisasi E.164, est_value wajib, sumber wajib; kanal Marketing diizinkan email saja hingga tahap Follow Up. Mode Existing vs New Customer (NPWP 16 digit terenkripsi), upsert customer + contact + attribution dalam 1 transaksi DB.
- [x] Pembuatan otomatis Activity "Follow up pertama" (SLA ≤ 30 menit jam kerja), auto-assign Sales PIC (rule-based untuk enterprise ≥ Rp 300 jt ke Sales Manager, round-robin pool aktif untuk standar, dan manual override).
- [x] Emit Outbox Event `lead.created` pada pembuatan lead & `lead.reopened` pada pembukaan kembali lead yang ditolak.
- [x] Endpoint `POST /public/lead-capture` (dan `/api/public/lead-capture`): penangkapan lead dari web/landing page dengan ekstraksi otomatis UTM, proteksi honeypot anti-bot, dan emisi event `lead.captured`.
- [x] Layanan Lead Scoring terkonfigurasi (`LeadScoringService` - FR-CRM-12): menghitung 4 dimensi Fit (30%), Engagement (30%), Intent (25%), dan Sumber (15%), menghasilkan skor 0–100 dan label HOT / WARM / COLD dengan rincian breakdown JSON + hook ML Fase 2.
- [x] Logika Stale Command (`CheckStaleLeadsCommand` - `crm:leads:stale-check`): mendeteksi lead tanpa aktivitas terjadwal ke depan (`has_next_step`), menghitung `days_in_stage`, menandai `is_stale = true` dan menerbitkan event `lead.stale_warning` (≥ 5 hari) serta eskalasi `lead.stale_escalation` ke Sales Manager (≥ 10 hari) sesuai BR-CRM-08 dan FR-CRM-24.
- [x] Daftar & Filter crm/leads: stage, source, owner, rentang nilai, tanggal, tag, industri, stale-only, filter milik saya/tim; sorting multi-kolom; CRUD Saved Views (`SavedViewController`); aksi massal (bulk-assign, bulk-tags, bulk-delete); dan ekspor CSV (`/leads/export`).
- [x] Reopen Lead Lost/Reject (FR-CRM-10): diizinkan khusus peran Sales Manager atau Super Admin dengan alasan wajib, memindahkan stage kembali ke `follow_up`.
- [x] Test Suite `CrmLeadManagementAndScoringTest` (8 feature tests, 86 assertions) + `CrmCustomer360AndDuplicateTest` (7 tests, 461 assertions) lulus 100%; DoD terpenuhi: Lead baru otomatis muncul di stage `new_lead` dan aktivitas follow-up pertama terbuat.
Catatan keputusan: Logika input lead terpadu menyatukan penangkapan web, deteksi duplikat instan, pembagian beban sales terotomasi, kalkulasi skor lead real-time, dan pemantauan SLA follow-up 30 menit.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-03 — Pipeline Guard, Transisi Stage, Audit & Override
Status: DONE
Checklist OUTPUT TARGET:
- [x] Kelas terpusat `PipelineTransitionGuard` (`App\Services\PipelineTransitionGuard`) sebagai satu-satunya jalur sah mutasi `leads.stage` / `leads.stage_id` (dijaga di level model `Lead::booted()` dengan verifikasi stack caller `authorizeStageMutationFromGuard()` yang melempar `DomainException` bila dimutasi langsung di luar guard).
- [x] Tabel transisi PRD §6.3 & §6.6 lengkap: `NEW_LEAD -> FOLLOW_UP/REJECT`, `FOLLOW_UP -> REQUIREMENT/REJECT`, `REQUIREMENT -> QUOTATION/REJECT`, `QUOTATION -> NEGOTIATION/REJECT`, `NEGOTIATION -> DEAL/QUOTATION/REJECT`, `REJECT -> FOLLOW_UP` (via Reopen Sales Manager/Super Admin), serta transisi mundur (`BR-CRM-12`) wajib alasan dari stage aktif dan dilarang dari `DEAL`/`REJECT`.
- [x] Syarat masuk per stage:
  - `FOLLOW_UP`: PIC ditetapkan & kontak awal tercatat/terjadwal (SLA ≤ 30 menit jam kerja).
  - `REQUIREMENT`: Klien merespons & Discovery Call terjadwal/tercatat.
  - `QUOTATION` (`BR-CRM-01`, `AC-CRM-11`): RAB valid (≥ 1 item modul, `total > 0`, WBS selesai) dengan pesan persis `"Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul)."` dan `deep_link_tab = "rab"`.
  - `NEGOTIATION`: Quotation & draf MoU terkirim (`status` SENT/VIEWED/NEGOTIATION/ACCEPTED).
  - `DEAL` (`BR-CRM-02`, `AC-CRM-05`, `AC-CRM-12`, `FR-CRM-20`, `FR-CRM-23`): Quotation ACCEPTED + TOP balance 100% + RAB valid + Delivery Readiness Check PM lolos + dokumen wajib stage Deal; pesan gagal TOP persis memuat `"Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: X%."` beserta nominal Rupiah dan `deep_link_tab = "top"`.
  - `REJECT` (`BR-CRM-03`, `BR-CRM-14`, `AC-CRM-10`, `AC-CRM-13`): Kategori alasan wajib + catatan evaluasi minimal 10 karakter.
- [x] Endpoint `PATCH /api/v1/crm/leads/{id}/stage`: mengembalikan HTTP 422 (RFC 7807 Problem Details) dengan field `guard_failure` dan `deep_link_tab` untuk mengarahkan FE langsung ke tab penyebab blokir (`activity`, `rab`, `top`, `quotation`, `handover`, `files`, `overview`).
- [x] Probabilitas default per stage (`10 / 20 / 35 / 55 / 75 / 100 / 0`) dapat dikonfigurasi Admin (`GET|PUT /api/v1/crm/pipeline-stages/{code}`) dan otomatis menghitung `weighted_value` pada setiap lead.
- [x] Super Admin Override (`BR-CRM-15`): wajib permission `crm.stage.override`, wajib alasan (`reason`), dan mencatat `is_override = true` ke dalam immutable audit log.
- [x] Audit Log Immutable (`crm_immutable_audit_logs` + `CrmImmutableAuditLog`) untuk setiap perubahan `stage`, `est_value`, `discount` (RAB/Quotation), dan `top_scheme` (memblokir `updating` dan `deleting` dengan `LogicException`), dilengkapi endpoint `GET /api/v1/crm/leads/{id}/audit-logs`.
- [x] Checklist aktivitas wajib per stage (`crm_stage_checklists` + `crm_lead_checklist_progress` + `StageChecklistSeeder`) tampil di Overview Lead serta endpoint `GET /leads/{id}/checklists` dan `POST /leads/{id}/checklists/{checklistId}/toggle`.
- [x] Endpoint Next Best Action (`GET|POST /api/v1/crm/leads/{id}/next-actions`) menggabungkan rule engine pipeline guard, SLA stale, checklist wajib, dan integrasi AI Gateway (`ai-service`) dengan fallback deterministik.
- [x] Emit Outbox event `lead.stage_changed` pada setiap transisi stage.
- [x] Test Suite `CrmPipelineGuardAndStageTransitionTest` (10 tests mencakup BDD `AC-CRM-05`, `AC-CRM-10`, `AC-CRM-11`, `AC-CRM-12`, `AC-CRM-13`, `BR-CRM-12`, `BR-CRM-15`, audit immutable, dan DoD static codebase scan) lulus 100% (total 34 tests, 697 assertions di `crm-service`).
Catatan keputusan: Penegakan `PipelineTransitionGuard` dikunci ganda melalui model hook `Lead::saving` dengan inspeksi call stack dan tes pemindaian statis codebase di CI, memastikan tidak ada jalur bypass perubahan stage.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:38 WIB

## CRM-BE-04 — Activities, Penjadwalan, Google Calendar/Meet
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi tabel `crm_activities` diperkaya (location, link, next_step, attachments, recording_url, transcript_text, ai_summary, reminder_h1_sent_at, reminder_1h_sent_at) dan tabel `crm_google_accounts` untuk OAuth tokens per user (terenkripsi) di `backend/services/crm/database/migrations/2026_11_04_000006_enhance_crm_activities_and_google_tokens_tables.php`.
- [x] Model `Activity` (`App\Models\Activity`) & `GoogleAccount` (`App\Models\GoogleAccount`) dengan standardisasi type (MEETING, CALL, FOLLOW_UP, EMAIL, WHATSAPP, DEMO, SITE_VISIT, TASK, NOTE), status (SCHEDULED, COMPLETED, CANCELLED, OVERDUE, RESCHEDULED), pemetaan outcome Indonesia (berhasil -> SUCCESS, reschedule -> RESCHEDULE, tidak_merespons -> NO_RESPONSE), serta sinkronisasi otomatis status `has_next_step` pada model Lead saat aktivitas disimpan/dihapus.
- [x] Integrasi Google Calendar & Meet (`App\Services\GoogleCalendarService`): adapter Google Calendar API v3 (scope minimum `https://www.googleapis.com/auth/calendar.events`) dengan mode mock untuk dev/test (`GOOGLE_CALENDAR_MOCK=true`), pembuatan event dengan `create_meet: true` menghasilkan `google_event_id` dan `meet_link` (`https://meet.google.com/...`), serta webhook push notification handler (`/webhooks/google-calendar`).
- [x] Pengingat H-1 dan 1 Jam sebelum jadwal (`App\Console\Commands\SendActivityRemindersCommand` — `crm:activities:reminders`): mendeteksi aktivitas berstatus SCHEDULED pada jendela 23–25 jam (H-1) dan 50–70 menit (1 Jam), mencatat timestamp pengiriman dan menerbitkan outbox event `activity.reminder_triggered` multi-kanal (in-app, email, WA) secara idempoten.
- [x] Penyelesaian aktivitas wajib isi outcome (`POST /activities/{id}/complete`): memvalidasi outcome (HTTP 422 bila kosong), mencatat next step, serta opsi membuat aktivitas berikutnya secara otomatis (`next_activity`).
- [x] Penegakan aturan "No Next Step" & Stale (BR-CRM-08): ketika aktivitas selesai dan tidak ada aktivitas terjadwal di masa depan, sistem otomatis memperbarui `lead->has_next_step = false`.
- [x] Rekaman pertemuan & AI Meeting Notes (`POST /activities/{id}/recording`, `POST /activities/{id}/ai-notes`, `POST /activities/{id}/push-to-requirement`): menerima URL rekaman/transkrip, menghasilkan AI Meeting Notes terstruktur (executive_summary, core_needs, technical_scope, decisions, action_items) via AI Service, dan dapat dikirimkan ke draf Requirement Lead (mengisi `lead->client_responded_at`).
- [x] Kalender aktivitas (`GET /api/v1/crm/activities/calendar`): mendukung rentang tanggal (`start_date`, `end_date`), filter tipe/status, dan scope akses (`mine`, `team`, `all`) dengan palet warna terstandarisasi.
- [x] Endpoint Google OAuth: `GET /integrations/google/status`, `GET /integrations/google/auth-url`, `POST /integrations/google/callback`, dan `POST /integrations/google/disconnect`.
- [x] Test Suite `CrmActivitiesAndGoogleMeetTest` (8 tests, 75 assertions) lulus 100% (total 42 tests, 778 assertions di `crm-service`); DoD terpenuhi: Membuat meeting dengan opsi Meet menghasilkan meet_link & undangan (mock) dan tersinkron.
Catatan keputusan: Seluruh komunikasi Google Calendar berjalan melalui adapter yang otomatis aktif dalam mode mock di lingkungan test/dev dan beralih ke REST API resmi saat kredensial OAuth riil dikonfigurasi.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:51 WIB

## CRM-BE-05 — Requirement, Upload, AI Generate RAB (Async)
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi tabel `crm_requirements` (`RequirementVersion`), `crm_requirement_files` (`RequirementFile` dengan `kind` `doc|audio|image`, `transcript_text`, `antivirus_status`), dan `crm_ai_runs` (`CrmAiRun`), serta kolom `origin` dan `ai_confidence` pada `crm_rab_items` di `backend/services/crm/database/migrations/2026_11_04_000007_create_crm_requirements_and_ai_runs_tables.php`.
- [x] Layanan ingesti & validasi (`App\Services\RequirementIngestionService`): mendukung input prompt dan/atau file (minimal salah satu), batas ukuran maks. 25 MB/file, antivirus hook (deteksi tanda tangan EICAR & ekstensi berbahaya `.exe`, `.bat`, `.sh`), ekstraksi teks/transkripsi, redaksi PII otomatis (`[EMAIL_REDACTED]`, `[PHONE_REDACTED]`, `[ID_REDACTED]`), token chunking (`token_chunks_count`), few-shot context dari `crm_module_catalog`, dan opsi `zero_retention`.
- [x] Transisi stage otomatis ke `requirement` saat requirement diinput oleh user, dijalankan sepenuhnya melalui `PipelineTransitionGuard` setelah melengkapi prasyarat respons klien dan aktivitas discovery.
- [x] Layanan AI RAB Generator (`App\Services\AiRabGeneratorService`) & Job asinkron (`App\Jobs\GenerateRabJob`): memanggil `ai-service` (`/internal/ai/run` task `rab_decomposition`), menormalisasi & mendekomposisi kebutuhan ke 6 kategori fungsional (`Master Data -> Transaksi -> Laporan -> Dashboard & KPI -> Non-Fungsional -> Integrasi`) dan 6 layer WBS (`Frontend UI/UX · Backend Core & DB · Integrasi API/Payment Gateway · Mobile · DevOps & Cloud Security · QA & UAT`), mengestimasi mandays per role dengan harga dari `crm_rate_cards`, serta menyusun asumsi, risiko, pertanyaan klarifikasi, out-of-scope, dan rekomendasi Tech Stack.
- [x] Menyimpan `rab_draft` (`RabVersion` dengan `source = 'AI'`, `status = 'DRAFT'`, item berlabel `origin = 'AI'` & `ai_confidence`) dan `crm_ai_runs`, serta memancarkan notifikasi/event SSE `RabGenerated` dalam waktu ≤ 60 detik (`AC-CRM-03`).
- [x] Versioning & Human-in-the-Loop (`BR-CRM-10`): setiap generasi membuat versi baru tanpa pernah menimpa RAB hasil editan manusia dan **tidak pernah** otomatis membuat/mengubah `Quotation`; dilengkapi aksi `"Gunakan sebagai RAB"` (`POST /leads/{id}/requirements/{requirementId}/use-as-rab`), `"Gabungkan (merge) ke RAB saat ini"` (`POST /leads/{id}/requirements/{requirementId}/merge-to-rab`), `"Regenerate dengan feedback"` (`generation_mode = REGENERATE_FEEDBACK`), dan `"Generate per bagian"` (`generation_mode = PARTIAL_SECTION`).
- [x] Kontrak OpenAPI (`docs/API-CONTRACTS/gateway.yaml`) dan tipe TypeScript/TanStack Query hooks (`frontend/src/shared/api-client/generated/*`) diperbarui.
- [x] Test Suite `CrmRequirementAndAiRabGenerationTest` (5 tests, 109 assertions) lulus 100% (total 47 tests, 887 assertions di `crm-service`).
Catatan keputusan: Pemisahan ketat antara `rab_draft` hasil AI (`source = AI`) dan RAB aktif/Quotation memastikan prinsip Human-in-the-Loop (`BR-CRM-10`) terjaga di level database maupun service.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 21:24 WIB

## CRM-BE-06 — RAB Engine (Editable, Versi, Kalkulasi, Margin Dual-View)
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi `2026_11_04_000008_enhance_crm_rab_engine_and_approvals_tables.php`: melengkapi `crm_rab_versions` (`is_pkp`, `direct_costs`, `direct_costs_total`, `gross_margin_nominal`, `team_size_by_role`, `duration_derivation`, `warnings`, `requires_approval`, `approval_tier`, `approval_reasons`, `approval_status`, `core_approval_id`), `crm_rab_items` (`category`, `layer`, `is_optional`, `is_recurring`, `is_bonus`, `recurring_period`, `origin` `AI|MANUAL|TEMPLATE`, `ai_confidence`, `notes`), serta tabel `crm_settings` ([`CrmSetting`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Models/CrmSetting.php)) dan `crm_approvals` ([`CrmApproval`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Models/CrmApproval.php)).
- [x] Mesin Kalkulasi Otoritatif Server ([`RabEngineService`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Services/RabEngineService.php)) yang terintegrasi dengan Kernel `RabCalculator`, `MarginCalculator`, dan `Terbilang`: `Subtotal (non-optional & non-bonus) -> Kontingensi (default 10%, wajib alasan jika <5%) -> Diskon (% atau nominal atas Subtotal+Kontingensi) -> DPP -> PPN (default 11% dari settings, hanya jika PKP) -> TOTAL INVESTASI -> Terbilang` (termasuk kasus DoD PRD: `DPP Rp100.000.000 -> PPN Rp11.000.000 -> Total Rp111.000.000`).
- [x] Margin Internal View: `HPP Internal = Σ(qty × cost_rate per role dari Rate Card) + biaya langsung lain (cloud, lisensi, freelance)`, `GM% = (DPP - HPP)/DPP`, indikator warna `GREEN` (≥40%), `YELLOW` (25–40%), `RED` (<25%) yang dapat dikonfigurasi via `crm_settings`.
- [x] Dual-View Serializer ([`RabResource`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Http/Resources/RabResource.php) + [`UserContext::canViewMargin`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Support/UserContext.php)): menghapus seluruh key `hpp_internal`, `margin_pct`, `gross_margin_nominal`, `margin_status`, `direct_costs`, `cost_rate`, `daily_rate_cost`, `subtotal_cost`, dan `hpp` dari JSON respons maupun ekspor CSV untuk peran tanpa izin `crm.margin.view` (`BR-CRM-07`, `AC-CRM-07`).
- [x] Optimistic Locking (`version`) pada `PUT /api/v1/crm/leads/{id}/rab` ([`OptimisticLockException`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Services/OptimisticLockException.php)): mengembalikan **HTTP 409 Conflict** (RFC 7807 Problem Details) apabila versi yang dikirim klien sudah usang.
- [x] Perubahan Badge `AI -> MANUAL` Saat Diedit (`AC-CRM-04`): item dengan `origin = 'AI'` otomatis berubah menjadi `'MANUAL'` ketika user mengubah `qty`, `unit`, `unit_price`, `name`, atau `role`.
- [x] Approval Engine Diskon/Margin/Nilai Tinggi (`FR-CRM-17`): `<=10%` Sales, `>10%..15%` Sales Manager, `>15%` atau `margin <25%` atau `quotation >Rp500 juta` Direktur — terintegrasi dengan Core Approval Engine dan menegakkan Segregation of Duties (SoD) pada `POST /api/v1/crm/leads/{id}/rab/approve`.
- [x] Fitur Spreadsheet Lengkap di [`RabController`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/app/Http/Controllers/RabController.php): Impor Template Katalog (`POST /leads/{id}/rab/import-catalog`), Impor & Ekspor Excel/CSV (`POST /leads/{id}/rab/import-excel`, `GET /leads/{id}/rab/export-excel`), Duplikasi baris (`POST /leads/{id}/rab/items/{itemId}/duplicate`), Urut ulang (`POST /leads/{id}/rab/reorder`), Grup kategori (`grouped_categories`), Riwayat versi & Diff qty/harga (`GET /leads/{id}/rab/versions`, `GET /leads/{id}/rab/diff`), Auto-save draft (`PATCH /leads/{id}/rab/autosave`), Derivasi durasi + kapasitas HRD (`duration_derivation`), dan peringatan role tanpa tarif Rate Card (`warnings`).
- [x] Test Suite [`CrmRabEngineTest.php`](file:///run/media/asisyahs/New%20Volume/PROJECT/LARE/erp-crm/backend/services/crm/tests/Feature/CrmRabEngineTest.php) (10 tests dengan DataProvider tabel kasus kalkulasi, DoD DPP 100jt -> PPN 11jt, AC-CRM-04, AC-CRM-07, Optimistic Lock 409, dan Approval FR-CRM-17) lulus 100% (total 57 tests, 1001 assertions di `crm-service`).
Catatan keputusan: Setiap penyimpanan penuh (`PUT /leads/{id}/rab`) menghasilkan snapshot versi baru untuk menjaga histori & perbandingan diff, sementara `PATCH /leads/{id}/rab/autosave` memperbarui draf secara in-place.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 21:42 WIB

## CRM-BE-07 — TOP / Termin Pembayaran
Status: DONE
Checklist OUTPUT TARGET:
- [x] Migrasi `2026_11_04_000009_enhance_crm_top_schemes_and_milestones_tables.php`: melengkapi `crm_top_schemes` (`rab_version_id`, `reference_dpp`, `reference_total`, `vat_rate`, `pph23_rate`, `is_pkp`, `is_pph23_withholder`, `total_dpp`, `total_ppn`, `total_pph23`, `total_net_cash`, `total_terbilang`, `remaining_percent`, `remaining_amount_idr`, `balance_warning`, `locked_by_finance_at`, `locked_by_finance_by`, `finance_lock_notes`, `unlock_approval_status`, `pending_change_payload`) dan `crm_top_milestones` (`term_number`, `name`, `percentage`, `dpp_amount`, `ppn_amount`, `pph23_amount`, `amount_idr`, `net_cash_idr`, `terbilang_text`, `trigger_type` [`signing`, `date`, `milestone`, `uat_passed`, `go_live`, `bast`, `retention_end`], `description`, `due_days_offset` default ≤ 14 hari, `due_date`, `project_milestone_ref`, `is_retention`).
- [x] Layanan TOP (`App\Services\TopService`) dengan dukungan penuh preset (`30_40_30`, `50_50`, `20_30_30_20`, `40_30_20_10_RETENTION`, `MONTHLY_RECURRING`, `CUSTOM`), sinkronisasi dua arah persen ↔ nominal (`percentage <-> amount_idr`), serta penyerapan selisih pembulatan pada termin terakhir (`FR-CRM-07`).
- [x] Validasi Balance `100,00%` (`is_balanced`, `BR-CRM-02`, `AC-CRM-05`, `AC-CRM-12`): bila Σ ≠ 100%, simpan final (`finalize = true`) & ekspor dokumen dikunci (`can_finalize = false`, `can_export_document = false`) dengan peringatan persis `"Sisa alokasi belum diatur: X% (Rp…)"`, serta pesan blokir transisi Deal persis `"Total termin harus 100%"` (`AC-CRM-05`) dan `"Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: X% (Rp…)."` (`AC-CRM-12`).
- [x] Perhitungan Pajak per Termin: `dpp_amount`, `ppn_amount`, estimasi `pph23_amount` (informatif 2% jika pemotong), `net_cash_idr`, dan `terbilang_text` otomatis per termin & total.
- [x] Review & Kunci oleh Finance AR (`POST /api/v1/crm/leads/{id}/top/lock` & `POST /api/v1/crm/top/lock`), pengecekan verifikasi NPWP 16 digit sebelum Invoice DP terbit, serta mekanisme persetujuan Finance untuk setiap perubahan setelah dikunci (`BR-CRM-18`, `POST /api/v1/crm/leads/{id}/top/approve-change`).
- [x] Generator Receivable Schedule (`GET /api/v1/crm/leads/{id}/top/receivable-schedule`) sebagai sumber jadwal penagihan piutang Finance saat Deal Won.
- [x] Test Suite `CrmTopTermsOfPaymentTest.php` (4 tests, 64 assertions) lulus 100% (total 61 tests, 1.065 assertions di `crm-service`).
Catatan keputusan: Ketika skema TOP yang telah dikunci oleh Finance diajukan perubahan oleh Sales, payload perubahan disimpan di `pending_change_payload` dengan status `PENDING` sampai Finance menyetujuinya via `/top/approve-change`, menjaga integritas jadwal piutang (`BR-CRM-18`).
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 21:53 WIB

## CRM-BE-08 — Quotation + MoU Satu PDF, Template & Clause Library, Versioning, Tracking
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/quotations, /quotations/{id}/generate-pdf|send|status, /templates, /clauses; PDF contoh terlampir di tests/fixtures; Pest test (halaman berurutan, nomor kontinu, snapshot, kelas guard klausul).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-09 — Deal Won, Handover, Readiness Check, Kickoff Gate, Transition Hub
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/win|lose, /deals/{id}/handover, /readiness, /transition-hub, /ar-calendar; test BDD AC-CRM-08/14, idempotensi (tidak ada proyek ganda).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-10 — Voice Note Recorder & AI Transcriber (Backend)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/voice-notes, /voice-notes (lintas lead), test dengan ai mock.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-11 — Files & Stage Docs, Dokumen Lead
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/documents, /documents/checklist; test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-12 — Legal Studio: 22 Pasal, Addendum, Log e-Sign/e-Meterai
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /clauses, /clause-versions, /addendums, /esign-logs, /verify/{hash}; test AC-CRM-16, AC-CRM-17.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-13 — Kontrak, SLA, Renewal
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /contracts, /contracts/{id}/renew; test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-14 — WhatsApp Client Care Inbox + AI Reply Modes (Backend)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /wa/conversations, /wa/messages, /wa/canned, /wa/broadcasts, /webhooks/whatsapp; test AC-CRM-09 (status proyek→draf; diskon→"Perlu Sales"), BR-WA-01..04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-15 — Forecast, Target, Analytics, Win/Loss, Upsell, Campaign
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /reports/{type}, /forecast, /targets, /campaigns, /account-plans; test rumus.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-BE-16 — Rate Card, Katalog Modul, Template Library, Pengaturan CRM
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /rate-cards, /module-catalog, /templates, /settings/crm; test permission.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-01 — Dashboard CRM, Leads (Tabel & Form Input), Customers 360
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + komponen + test (Vitest form, Playwright AC-CRM-01/02).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-02 — Sales Pipeline: Kanban, Tabel, Visual Workflow Tracker
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] /crm/pipeline (+ state), komponen Kanban/Table/WorkflowTracker; test Playwright untuk AC-CRM-11/12/13.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-03 — Detail Lead: Layout, Overview, Activity, Requirement
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Komponen + rute; test Playwright AC-CRM-03 (mock BE AI).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-04 — Tab RAB (Spreadsheet Grid, Panel Ringkasan, Dual-View)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman tab RAB + test kalkulasi klien == server (fixture bersama).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-05 — Tab TOP, Quotation Editor (Split A4), Persetujuan
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test Playwright AC-CRM-05/06.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-06 — Tab Komunikasi, Kontak, Dokumen, Tugas, Kompetitor, Riwayat, Handover, Voice Notes
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua tab 1–16 selesai (tidak ada placeholder tersisa).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-07 — WhatsApp Client Care Inbox
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-08 — Legal Studio, Contracts & Renewals, Transition Hub, Kalender AR
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test AC-CRM-16/17.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## CRM-FE-09 — Forecast, Analytics, Rate Card, Template, Campaign, Upsell, Pengaturan, Voice Notes lintas lead
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Seluruh 21 menu CRM (PRD §6.2) tersambung ke data nyata.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-BE-01 — Master Pegawai, Kontrak, Struktur Upah, Dokumen & Surat
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint CRUD + test masking & log akses.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-BE-02 — Presensi, Daily Log, Night Workload
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-HRD-01/02.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-BE-03 — Cuti, Kapasitas & Alokasi, Capacity Demand
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leave, /capacity, /allocations, /capacity-demands, /internal/events/deal.won.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-BE-04 — Rekrutmen (ATS), PKL/Magang
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + PDF + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-BE-05 — Multi-Model Payroll, Penilaian Kinerja, Laporan
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + PDF slip + test AC-HRD-04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## HRD-FE-01 — Seluruh Menu HRD
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §8.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## PM-BE-01 — Proyek dari Deal Won, Epics/Tasks dari WBS, Intake 3 Langkah
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test idempotensi & AC-PM-01/02.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## PM-BE-02 — Board Kanban, Backlog & Sprint, Roadmap, Daftar Pekerjaan, Task Detail
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test JQL parser & transisi.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## PM-BE-03 — QA, Docs & Wiki, Time Tracking, Budget & Margin, Milestone/UAT
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-PM-04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## PM-BE-04 — Service Desk, SLA, Telegram, GitHub, AI Fix Bug, Change Request
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + adapter Telegram/GitHub (mock) + test AC-PM-03.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## PM-FE-01 — Portofolio, Deals Won Intake, Workspace Proyek (10 sub-tab)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §9.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## FIN-BE-01 — COA, Periode, Posting Rules, Jurnal Umum Otomatis
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-FIN-04, E2E-07.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## FIN-BE-02 — Invoicing & Termin (AR), Pembayaran, Pajak, Reminder
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-FIN-01 (kas bersih 109 jt).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## FIN-BE-03 — AP: Klaim Struk OCR, Vendor, SPK Freelancer, Payroll Posting, OPEX
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## FIN-BE-04 — Rekening, Mutasi Internal, Laporan (P&L, Per Proyek, Portofolio, Arus Kas, Dashboard)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /finance/reports/*; test AC-FIN-02 (mutasi tidak ubah laba/EBITDA).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## FIN-FE-01 — Seluruh Menu Finance
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §10.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## MKT-BE-01 — Riset Tren, Sprint Planner, AI Content Studio, Brand Kit, Approval
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-MKT-01.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## MKT-BE-02 — Scheduler, Distribusi, Omnichannel Inbox, Listening, Analytics, Ads, Atribusi
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + adapter mock sosmed + test AC-MKT-02/03.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## MKT-FE-01 — Seluruh Menu Marketing
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §7.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## X-01 — Portal Klien (FE + BE)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Portal berfungsi.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## X-02 — Admin: Pengguna, Role, Integrasi, Audit, Automation Rules, Approval Config, Queue Monitor
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman admin lengkap.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## X-03 — Laporan Konsolidasi & Executive Dashboard Lintas Modul
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + dashboard.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## X-04 — Realtime (SSE), Pencarian Global, Notifikasi Lintas Modul
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Berfungsi di FE.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## X-05 — Test E2E Lintas Modul (E2E-01..08)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Suite e2e + laporan.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## J-01 — Keamanan & Kepatuhan (OWASP, UU PDP)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Perbaikan + docs/SECURITY.md + test keamanan.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## J-02 — Performa, Observabilitas, Backup
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Laporan benchmark + skrip.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## J-03 — Deployment di Server SSH tanpa Docker
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] docs/DEPLOY.md, deploy/*.service, nginx.conf, deploy.sh, rollback.sh.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

## J-04 — UAT, Pelatihan, Migrasi Data
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Skrip ETL + dokumen SOP/UAT.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-04 20:20 WIB

