# LEDGER PROGRES PENGEMBANGAN — ASTACODE ERP ORCHESTRATOR

Dokumen ini adalah ledger resmi pelacak status seluruh task pengerjaan proyek ASTACODE ERP.
Sesuai **Protokol Lanjutkan (Master Prompt Bagian 0.2)**, setiap sesi kerja memperbarui status dan checklist dokumen ini.

Terakhir diperbarui: 2026-11-03 14:25 WIB

---

## T00 — Analisis & Audit Mendalam (WAJIB PERTAMA)
Status: DONE
Checklist OUTPUT TARGET:
- [x] docs/ANALYSIS.md, docs/EVENT-CATALOG.md, docs/PROTOTYPE-MAP.md, docs/DESIGN-TOKENS.md, docs/PROGRESS.md (semua terisi lengkap, bukan kerangka).
- [x] Ringkasan akhir ≤ 15 baris di chat: temuan kritis + pertanyaan yang benar-benar memblokir.
Catatan keputusan: Seluruh audit PRD, event catalog, prototype map, design tokens, dan struktur microservice telah dirumuskan secara lengkap.
Utang teknis / TODO: Tidak ada (selesai 100%).
Terakhir diperbarui: 2026-11-03 14:25 WIB

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
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Aplikasi berjalan `npm run dev`; login → shell → navigasi modul (menu kosong per modul diisi task berikutnya); /dev/ui-kit lengkap; tampilan konsisten dengan prototipe.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## T06 — [BE] Kernel Terbilang, Pajak, Money & Util Dokumen (jika belum tuntas di T01)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Kelas + Pest test dengan tabel kasus (≥ 30 kasus uji Terbilang & kalkulasi pajak).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## T07 — [FE] Shell Navigasi Seluruh Modul + Dashboard Landing
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua rute terdaftar; navigasi lengkap; guard permission bekerja; daftar task→rute tercatat di docs/PROGRESS.md.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## T08 — [BE] Data Master Bersama & Seeder Demo Lintas Modul
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] `backend/scripts/seed-all.sh` (+ `--demo`).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## T09 — [BE+FE] Kontrak API Terpadu & Generasi Tipe
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] gateway.yaml, tipe TS ter-generate, koleksi Postman, CI contract test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-01 — Skema, Model & Master Customer/Contact/Lead Source
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Migrasi, model, resource (masking npwp), policy (Sales hanya milik sendiri+tim), Pest test (duplikat, merge, masking, health score).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-02 — Lead, Input Manual/Web/Marketing, Scoring, Auto-assign
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads (CRUD+bulk+export), /leads/{id}, /public/lead-capture, command `crm:leads:stale-check`, Pest test AC-CRM-01, AC-CRM-02, validasi form, scoring, auto-assign.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-03 — Pipeline Guard, Transisi Stage, Audit & Override
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Kelas guard + test untuk SETIAP transisi sah/tidak sah, AC-CRM-05, AC-CRM-10, AC-CRM-11, AC-CRM-12, AC-CRM-13 (BDD → Pest).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-04 — Activities, Penjadwalan, Google Calendar/Meet
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/activities, /activities/calendar, OAuth connect/disconnect, Pest test dengan Google mock.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-05 — Requirement, Upload, AI Generate RAB (Async)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + job + test dengan ai-service mock; tabel requirement_versions.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-06 — RAB Engine (Editable, Versi, Kalkulasi, Margin Dual-View)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test kalkulasi (tabel kasus), masking, optimistic lock 409, approval.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-07 — TOP / Termin Pembayaran
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint PUT /leads/{id}/top, POST /top/lock, test balance & preset, approval perubahan setelah kunci.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-08 — Quotation + MoU Satu PDF, Template & Clause Library, Versioning, Tracking
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/quotations, /quotations/{id}/generate-pdf|send|status, /templates, /clauses; PDF contoh terlampir di tests/fixtures; Pest test (halaman berurutan, nomor kontinu, snapshot, kelas guard klausul).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-09 — Deal Won, Handover, Readiness Check, Kickoff Gate, Transition Hub
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/win|lose, /deals/{id}/handover, /readiness, /transition-hub, /ar-calendar; test BDD AC-CRM-08/14, idempotensi (tidak ada proyek ganda).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-10 — Voice Note Recorder & AI Transcriber (Backend)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/voice-notes, /voice-notes (lintas lead), test dengan ai mock.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-11 — Files & Stage Docs, Dokumen Lead
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leads/{id}/documents, /documents/checklist; test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-12 — Legal Studio: 22 Pasal, Addendum, Log e-Sign/e-Meterai
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /clauses, /clause-versions, /addendums, /esign-logs, /verify/{hash}; test AC-CRM-16, AC-CRM-17.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-13 — Kontrak, SLA, Renewal
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /contracts, /contracts/{id}/renew; test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-14 — WhatsApp Client Care Inbox + AI Reply Modes (Backend)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /wa/conversations, /wa/messages, /wa/canned, /wa/broadcasts, /webhooks/whatsapp; test AC-CRM-09 (status proyek→draf; diskon→"Perlu Sales"), BR-WA-01..04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-15 — Forecast, Target, Analytics, Win/Loss, Upsell, Campaign
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /reports/{type}, /forecast, /targets, /campaigns, /account-plans; test rumus.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-BE-16 — Rate Card, Katalog Modul, Template Library, Pengaturan CRM
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /rate-cards, /module-catalog, /templates, /settings/crm; test permission.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-01 — Dashboard CRM, Leads (Tabel & Form Input), Customers 360
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + komponen + test (Vitest form, Playwright AC-CRM-01/02).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-02 — Sales Pipeline: Kanban, Tabel, Visual Workflow Tracker
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] /crm/pipeline (+ state), komponen Kanban/Table/WorkflowTracker; test Playwright untuk AC-CRM-11/12/13.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-03 — Detail Lead: Layout, Overview, Activity, Requirement
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Komponen + rute; test Playwright AC-CRM-03 (mock BE AI).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-04 — Tab RAB (Spreadsheet Grid, Panel Ringkasan, Dual-View)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman tab RAB + test kalkulasi klien == server (fixture bersama).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-05 — Tab TOP, Quotation Editor (Split A4), Persetujuan
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test Playwright AC-CRM-05/06.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-06 — Tab Komunikasi, Kontak, Dokumen, Tugas, Kompetitor, Riwayat, Handover, Voice Notes
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua tab 1–16 selesai (tidak ada placeholder tersisa).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-07 — WhatsApp Client Care Inbox
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-08 — Legal Studio, Contracts & Renewals, Transition Hub, Kalender AR
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman + test AC-CRM-16/17.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## CRM-FE-09 — Forecast, Analytics, Rate Card, Template, Campaign, Upsell, Pengaturan, Voice Notes lintas lead
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Seluruh 21 menu CRM (PRD §6.2) tersambung ke data nyata.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-BE-01 — Master Pegawai, Kontrak, Struktur Upah, Dokumen & Surat
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint CRUD + test masking & log akses.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-BE-02 — Presensi, Daily Log, Night Workload
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-HRD-01/02.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-BE-03 — Cuti, Kapasitas & Alokasi, Capacity Demand
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /leave, /capacity, /allocations, /capacity-demands, /internal/events/deal.won.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-BE-04 — Rekrutmen (ATS), PKL/Magang
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + PDF + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-BE-05 — Multi-Model Payroll, Penilaian Kinerja, Laporan
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + PDF slip + test AC-HRD-04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## HRD-FE-01 — Seluruh Menu HRD
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §8.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## PM-BE-01 — Proyek dari Deal Won, Epics/Tasks dari WBS, Intake 3 Langkah
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test idempotensi & AC-PM-01/02.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## PM-BE-02 — Board Kanban, Backlog & Sprint, Roadmap, Daftar Pekerjaan, Task Detail
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test JQL parser & transisi.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## PM-BE-03 — QA, Docs & Wiki, Time Tracking, Budget & Margin, Milestone/UAT
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-PM-04.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## PM-BE-04 — Service Desk, SLA, Telegram, GitHub, AI Fix Bug, Change Request
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + adapter Telegram/GitHub (mock) + test AC-PM-03.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## PM-FE-01 — Portofolio, Deals Won Intake, Workspace Proyek (10 sub-tab)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §9.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## FIN-BE-01 — COA, Periode, Posting Rules, Jurnal Umum Otomatis
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-FIN-04, E2E-07.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## FIN-BE-02 — Invoicing & Termin (AR), Pembayaran, Pajak, Reminder
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-FIN-01 (kas bersih 109 jt).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## FIN-BE-03 — AP: Klaim Struk OCR, Vendor, SPK Freelancer, Payroll Posting, OPEX
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## FIN-BE-04 — Rekening, Mutasi Internal, Laporan (P&L, Per Proyek, Portofolio, Arus Kas, Dashboard)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint /finance/reports/*; test AC-FIN-02 (mutasi tidak ubah laba/EBITDA).
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## FIN-FE-01 — Seluruh Menu Finance
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §10.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## MKT-BE-01 — Riset Tren, Sprint Planner, AI Content Studio, Brand Kit, Approval
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + test AC-MKT-01.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## MKT-BE-02 — Scheduler, Distribusi, Omnichannel Inbox, Listening, Analytics, Ads, Atribusi
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + adapter mock sosmed + test AC-MKT-02/03.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## MKT-FE-01 — Seluruh Menu Marketing
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Semua menu §7.2.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## X-01 — Portal Klien (FE + BE)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Portal berfungsi.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## X-02 — Admin: Pengguna, Role, Integrasi, Audit, Automation Rules, Approval Config, Queue Monitor
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Halaman admin lengkap.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## X-03 — Laporan Konsolidasi & Executive Dashboard Lintas Modul
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Endpoint + dashboard.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## X-04 — Realtime (SSE), Pencarian Global, Notifikasi Lintas Modul
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Berfungsi di FE.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## X-05 — Test E2E Lintas Modul (E2E-01..08)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Suite e2e + laporan.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## J-01 — Keamanan & Kepatuhan (OWASP, UU PDP)
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Perbaikan + docs/SECURITY.md + test keamanan.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## J-02 — Performa, Observabilitas, Backup
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Laporan benchmark + skrip.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## J-03 — Deployment di Server SSH tanpa Docker
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] docs/DEPLOY.md, deploy/*.service, nginx.conf, deploy.sh, rollback.sh.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

## J-04 — UAT, Pelatihan, Migrasi Data
Status: NOT_STARTED
Checklist OUTPUT TARGET:
- [ ] Skrip ETL + dokumen SOP/UAT.
Catatan keputusan: Belum dimulai.
Utang teknis / TODO: -
Terakhir diperbarui: 2026-11-03 14:25 WIB

