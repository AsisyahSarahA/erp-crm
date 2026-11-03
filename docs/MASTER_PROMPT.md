# MASTER PROMPT — ASTACODE ERP ORCHESTRATOR
**Dokumen:** `docs/MASTER_PROMPT.md` · **Sumber kebenaran:** `docs/MASTER PRD.md` (PRD-ASTA-ERP-001 v1.1) · **Prototipe UI:** folder prototipe CRM di `docs/` (Vanilla JS)
**Stack:** Backend **Laravel 11/12 (PHP 8.3+)** · Database **MySQL 8** (sudah tersedia di server SSH, **tanpa instalasi server, tanpa Docker**) · Frontend **React 18 + TypeScript + Vite** (sudah ada di `frontend/`)
**Arsitektur pengerjaan:** **Microservice** (satu aplikasi Laravel per domain) + 1 Gateway + 1 Orchestrator + shared kernel.

---

## DAFTAR ISI
- **BAGIAN 0 — Cara Pakai & Protokol "Lanjutkan"**
- **BAGIAN A — Konteks Global & Aturan Emas** (ditempel sekali di awal sesi / otomatis dibaca agen dari file ini)
- **BAGIAN B — Task Fondasi** (T00 – T09)
- **BAGIAN C — Modul 1 CRM** (CRM-BE-xx, CRM-FE-xx)
- **BAGIAN D — Modul 3 HRD** (HRD-BE/FE)
- **BAGIAN E — Modul 4 Project Management** (PM-BE/FE)
- **BAGIAN F — Modul 5 Finance** (FIN-BE/FE)
- **BAGIAN G — Modul 2 Marketing & AI Content Engine** (MKT-BE/FE)
- **BAGIAN H — Lintas Modul** (Portal Klien, Admin, Laporan Terpadu, Integrasi, Realtime)
- **BAGIAN I — Fitur Tambahan Penambal Gap (skala industri software house)**
- **BAGIAN J — Hardening, Testing, Deployment tanpa Docker**
- **BAGIAN K — Prompt Utilitas** (Lanjutkan, Audit, Perbaiki, Sinkron PRD, Ringkas Konteks)
- **LAMPIRAN — Matriks Keterlacakan PRD → Task**

> **Urutan eksekusi yang disarankan:** B → C(BE lalu FE per sub-fitur) → F → E → D → G → H → I → J.
> Alasan: CRM adalah hulu rantai nilai (Lead→Deal), Finance & PM adalah hilir yang dipicu saga Deal-Won, HRD memasok kapasitas/biaya, Marketing memasok lead.

---

# BAGIAN 0 — CARA PAKAI & PROTOKOL "LANJUTKAN"

## 0.1 Cara memakai file ini
1. Simpan file ini sebagai `docs/MASTER_PROMPT.md` (sejajar dengan `docs/MASTER PRD.md` dan folder prototipe).
2. Buka agen coding (Claude Code / sejenisnya) di root `erp-crm/`.
3. **Sesi pertama:** tempel prompt **T00** (Analisis & Audit). Tunggu hingga `docs/ANALYSIS.md` dan `docs/PROGRESS.md` terbentuk.
4. **Task berikutnya:** salin **satu blok prompt** (di dalam kotak kode) → tempel → jalankan. Satu task = satu sesi kerja. Jangan menggabungkan banyak task.
5. Setiap blok prompt sudah memuat: **[KONTEKS]** (BE atau FE, service, sumber PRD), **[TASK]** (perintah rinci), **[OUTPUT TARGET]** (file/endpoint/halaman yang harus ada), dan **[DoD]** (kriteria selesai). Anda tidak perlu menambahkan konteks lagi.

## 0.2 Protokol "Lanjutkan" (anti-hilang-konteks)
Agen **wajib** memelihara `docs/PROGRESS.md` dengan format:

```
## <TASK-ID> — <judul>
Status: NOT_STARTED | IN_PROGRESS | DONE | BLOCKED
Checklist OUTPUT TARGET:
- [x] item selesai (path file / endpoint)
- [ ] item belum selesai
Catatan keputusan: ...
Utang teknis / TODO: ...
Terakhir diperbarui: <tanggal-waktu>
```

Jika produksi **berhenti sebelum target tercapai** (token habis, error, dll.), cukup ketik:

```
lanjutkan task <TASK-ID>
```

Agen harus: (1) membaca `docs/MASTER_PROMPT.md` bagian task tersebut, (2) membaca `docs/PROGRESS.md`, (3) memeriksa kode yang sudah ada (`git status`, `git diff`, file di OUTPUT TARGET), (4) **melanjutkan hanya item yang belum selesai**, tidak menulis ulang yang sudah benar, (5) memperbarui `PROGRESS.md`. Prompt lengkap tersedia di **BAGIAN K (U1)**.

## 0.3 Catatan penting tentang input yang Anda kirim
- Pada tangkapan layar Explorer, folder `docs/` hanya menampilkan `MASTER PRD.md`. **Folder prototipe CRM harus berada di dalam `docs/`** (mis. `docs/prototype folder crm/`). Task **T00** akan memindai `docs/**` untuk menemukannya; jika tidak ditemukan, agen **wajib berhenti dan meminta** Anda menaruhnya — agen tidak boleh mengarang tampilan.
- Pada PRD, arsitektur yang ditulis adalah *modular monolith*. Anda memilih **microservice**. Master prompt ini **mempertahankan seluruh logika bisnis PRD** tetapi **mengganti topologi deploy** (lihat A.3). Redis/Reverb/Meilisearch/MinIO pada PRD diganti padanan **tanpa server tambahan** (lihat A.4) agar sesuai syarat "tanpa instalasi server, tanpa Docker". Semua penggantian bersifat adapter sehingga dapat dinaikkan ke komponen PRD asli kelak.

---

# BAGIAN A — KONTEKS GLOBAL & ATURAN EMAS

> Agen harus membaca bagian A ini di awal **setiap** task (cukup `Read docs/MASTER_PROMPT.md` bagian A). Setiap blok task juga mengulang ringkasan konteks agar tidak hilang.

## A.1 Produk
ASTACODE ERP Orchestrator: satu platform untuk software house — **Marketing → CRM → Project Management → Finance**, ditopang **HRD**. Satu sumber kebenaran, otomasi lintas modul tanpa input ganda. Detail lengkap: `docs/MASTER PRD.md`.

## A.2 Lima modul + lapisan lintas
| Modul | Service | Port dev | Database |
|---|---|---|---|
| Gateway/BFF (satu pintu untuk React) | `gateway` | 8000 | — (stateless; cache file/db ringan) |
| Core (Auth, RBAC, Audit, Files, Notif, Numbering, Settings, Approval, Webhook) | `core-service` | 8001 | `astacode_core` |
| CRM | `crm-service` | 8002 | `astacode_crm` |
| Marketing & AI Content | `marketing-service` | 8003 | `astacode_marketing` |
| HRD & Workload | `hrd-service` | 8004 | `astacode_hrd` |
| Project Management | `pm-service` | 8005 | `astacode_pm` |
| Finance & Cashflow | `finance-service` | 8006 | `astacode_finance` |
| AI Gateway (provider abstraction, prompt registry, guardrail) | `ai-service` | 8007 | `astacode_ai` |
| Orchestrator (event bus, saga, rule engine) | `orchestrator-service` | 8008 | `astacode_orchestrator` |
| Shared kernel (paket Composer lokal) | `shared/astacode-kernel` | — | — |

Aturan keras microservice:
1. **Database per service.** Tidak ada query/JOIN/foreign key lintas database. Referensi lintas service hanya berupa ID (`customer_id`, `project_id`, `deal_id`, …) + *snapshot* field yang perlu ditampilkan.
2. **Komunikasi:** (a) **sinkron** REST internal bertanda tangan (HMAC + header `X-Service-Token`, `X-Correlation-Id`, `Idempotency-Key`) untuk query/command; (b) **asinkron** event melalui **Outbox → Orchestrator → konsumen** (lihat A.5).
3. **Frontend hanya memanggil `gateway`** (`/api/v1/...`). Gateway memverifikasi sesi, menyuntikkan JWT internal (berisi user, role, permission), melakukan routing ke service, dan menerapkan rate-limit + field masking tambahan.
4. **Margin/HPP/gaji/NPWP/rekening disaring di server (service pemilik data)**, bukan hanya di UI.
5. Setiap service: modular (Actions/Services/Policies/Requests/Resources/Events/Listeners/Jobs), test sendiri, OpenAPI sendiri, health endpoint `/internal/health`.

## A.3 Struktur folder target
```
erp-crm/
├─ docs/
│   ├─ MASTER PRD.md
│   ├─ MASTER_PROMPT.md        ← file ini
│   ├─ PROGRESS.md             ← ledger status task (dibuat T00)
│   ├─ ANALYSIS.md             ← hasil audit (dibuat T00)
│   ├─ PROTOTYPE-MAP.md        ← peta halaman prototipe → rute/komponen React (dibuat T00)
│   ├─ API-CONTRACTS/          ← OpenAPI per service
│   ├─ EVENT-CATALOG.md
│   └─ prototype folder crm/   ← prototipe Vanilla JS (sumber visual)
├─ backend/
│   ├─ gateway/                (Laravel)
│   ├─ services/
│   │   ├─ core/  crm/  marketing/  hrd/  pm/  finance/  ai/  orchestrator/   (masing-masing app Laravel)
│   ├─ shared/astacode-kernel/ (paket Composer path-repository)
│   ├─ scripts/                (dev-up.sh, dev-down.sh, migrate-all.sh, seed-all.sh, test-all.sh, tunnel.sh)
│   └─ .env.shared.example
└─ frontend/                   (Vite + React + TS, sudah ada)
    └─ src/{app,shared,features/{crm,marketing,hrd,pm,finance,admin},portal-client}
```
> Jika `backend/` sudah berisi sesuatu, **audit dulu (T00) dan adaptasi**, jangan menimpa.

## A.4 Penyesuaian "tanpa instalasi server, tanpa Docker"
| PRD | Implementasi di proyek ini | Catatan |
|---|---|---|
| Redis (cache/queue/session) | `CACHE_STORE=database`, `QUEUE_CONNECTION=database`, `SESSION_DRIVER=database` | Abstraksi tetap Laravel; ganti ke Redis cukup ubah `.env` |
| Laravel Horizon | `php artisan queue:work --tries=5 --backoff=…` + halaman **Queue Monitor** sendiri (tabel `jobs`/`failed_jobs`) | Dijalankan via script/supervisor/systemd di host SSH |
| Reverb WebSocket | **Server-Sent Events (SSE)** via endpoint gateway + fallback polling; antarmuka `RealtimeChannel` sehingga bisa diganti Reverb | Tanpa daemon WebSocket tambahan |
| Meilisearch (Scout) | Scout driver `database` / query MySQL FULLTEXT + `LIKE` terindeks | Adapter `SearchService` |
| S3/MinIO | Filesystem disk lokal terenkripsi (`storage/app/private`) + tautan bertanda tangan berkedaluwarsa | Adapter disk `files` |
| Docker Compose | **Tidak dipakai.** Jalankan service dengan `php artisan serve --port=…` (dev) dan Nginx + Supervisor/systemd (produksi) | Skrip `backend/scripts/*` |
| ClamAV | Interface `VirusScanner`; implementasi `NullScanner` (dev) & `ClamScanner` (bila tersedia) | Wajib ada hook |
| Browsershot (Chromium) | Jika Chromium tersedia di host → Browsershot; fallback **DomPDF/mPDF**. Interface `PdfRenderer` | Output A4 vektor |
| Vector store | MySQL tabel embedding + cosine di aplikasi (kecil) atau adapter eksternal | Adapter `VectorStore` |
| MySQL | **Sudah ada di server SSH.** Agen hanya membuat **database + user via skrip SQL idempoten** `backend/scripts/sql/create-databases.sql` dan menjalankan `artisan migrate` | **Jangan** menulis langkah instal MySQL |

## A.5 Pola komunikasi & event (wajib seragam)
- **Outbox pattern:** tiap service punya tabel `outbox_events` (id ULID, event_name, aggregate, payload JSON, idempotency_key, status, attempts, available_at). Event ditulis **dalam transaksi DB yang sama** dengan perubahan data.
- **Relay:** job terjadwal `outbox:relay` per service mengirim event ke `orchestrator-service` (`POST /internal/events`, tanda tangan HMAC). Orchestrator menyimpan di `event_log`, mencocokkan **subscription**, lalu mengirim ke endpoint konsumen `POST /internal/events/{name}` (retry eksponensial maks 5×, dead-letter).
- **Idempotensi:** kunci unik `event_id + consumer`; konsumen menyimpan `processed_events`.
- **Saga Deal-Won** (PRD §5.3) berada di orchestrator: Customer upsert → PM project+WBS → Finance receivable schedule → HRD capacity demand → status handover COMPLETED/PARTIAL + tombol Retry.
- Katalog event lengkap (PRD §5.2): `lead.captured, lead.created, lead.stage_changed, quotation.sent, quotation.approved, deal.won, deal.lost, project.created, milestone.approved, invoice.issued, invoice.paid, timelog.recorded, payroll.approved, expense.claimed, ads.spent, ticket.created, wa.message_received` + event tambahan yang Anda definisikan di `docs/EVENT-CATALOG.md`.

## A.6 ATURAN EMAS (berlaku untuk SEMUA task)
1. **PRD adalah hukum.** Jika ada konflik: PRD menang, *kecuali* topologi (microservice) & penyesuaian A.4. Bila menemukan konflik/ambiguitas PRD (mis. Open Questions Lampiran G), pakai **default yang diusulkan PRD**, catat di `docs/PROGRESS.md` bagian "Keputusan", jangan berhenti.
2. **Prototype-first (FE):** sebelum menulis komponen React, buka file prototipe yang relevan di `docs/prototype*/`, tiru **tata letak, warna, tipografi, ikon, teks/label, urutan tab, perilaku interaksi**. Peta ada di `docs/PROTOTYPE-MAP.md`. Jika PRD lebih lengkap daripada prototipe, **tampilan mengikuti prototipe, kelengkapan fitur mengikuti PRD**; elemen baru harus memakai bahasa visual yang sama.
3. **Tidak ada fitur yang dilewati / disederhanakan.** Setiap FR-/BR-/AC- pada cakupan task harus ada implementasinya + test. Dilarang menaruh `// TODO` tanpa dicatat di PROGRESS "Utang teknis". Dilarang data palsu hard-code di FE kecuali di seeder/factory (Faker) atau Storybook.
4. **Aturan bisnis ditegakkan di server** (BR-xx, guard transisi, validasi 100% TOP, debit=kredit, dll.). UI hanya mencerminkan + UX.
5. **Uang** `DECIMAL(18,2)` (PHP: `brick/money` atau `bcmath`; JS: kirim string/integer sen, jangan float). **Persen** `DECIMAL(7,4)`. **Waktu** simpan UTC, tampil `Asia/Jakarta`. **Enum** nilai `snake_case` huruf kecil.
6. **Keamanan:** Form Request validasi, Policy per model, permission granular `modul.entitas.aksi` (Spatie), audit log immutable (siapa/kapan/lama→baru), field masking di Resource, token/NPWP/rekening `encrypted` cast, upload tervalidasi (+VirusScanner), rate-limit endpoint AI & webhook, HMAC pada webhook & internal.
7. **AI = draf.** Semua keluaran AI berstatus draf + `ai_runs` log + keputusan manusia (diterima/diedit/ditolak). Output wajib JSON Schema tervalidasi (retry bila invalid). Provider melalui `ai-service`.
8. **Kualitas:** PSR-12 + Larastan level 6+, Pest (feature+unit) ≥ 80% domain inti, ESLint/Prettier, Vitest + RTL untuk komponen kunci, Playwright untuk alur E2E kritis. Conventional Commits. Tiap task diakhiri `git commit`.
9. **Setiap FE page wajib punya 4 state:** loading (skeleton), empty (CTA), error (retry + correlation id), 403. Permission guard di route + komponen. Optimistic update pada drag-drop. i18n `id-ID` default (+ `en`).
10. **Setiap BE endpoint wajib:** Form Request, Policy/permission, API Resource (masking), pagination/filter/sort/include standar, respons error RFC 7807, OpenAPI annotation, Feature test (happy + gagal + izin).
11. **Dilarang:** Docker/Compose, menginstal server MySQL/Redis, menjalankan perintah destruktif (`migrate:fresh` pada DB non-dev, `rm -rf` luar proyek), membuat file di luar `erp-crm/`.
12. **Batas sesi:** bila konteks/token hampir habis, **berhenti di titik aman**, `git commit` WIP, perbarui `docs/PROGRESS.md` (checklist akurat), lalu tulis ringkasan "Yang tersisa". Pengguna akan mengetik `lanjutkan task <ID>`.
13. **Tidak mengarang.** Jika file/informasi yang diperlukan tidak ada (mis. prototipe), tulis di PROGRESS sebagai BLOCKED dan tanyakan secara singkat.

## A.7 Konvensi teknis ringkas
- **API:** `/api/v1/{modul}/...` di gateway → diteruskan ke service. Respons `{data, meta, links}`; error RFC 7807. Header `Idempotency-Key` pada POST kritis (win deal, issue invoice, payment). Optimistic locking `version` (409) pada RAB/Quotation/Task.
- **Penomoran (Numbering Service di core):** `LD-YYYY-NNNN`, `QT/YYYY/MM/NNNN-Rn`, `MOU/…`, `ADD/…`, `CUS-NNNN`, `PRJ-NNN`, `INV/…`, `SPK/…`, `TICKET-NNNN`, `JRN-{AR|AP|HR|OPEX|TRF}/…`, `PAY/YYYY/MM` — atomic via row lock, reset bulanan/tahunan konfigurabel.
- **Multi-company-ready:** kolom `company_id` pada tabel utama.
- **Soft delete** entitas bisnis; **FK RESTRICT** untuk data finansial (di dalam DB service sendiri).
- **Folder service:** `app/Domain/{Entity}/{Models,Actions,Services,Policies,Events,Listeners,Jobs}`, `app/Http/{Controllers,Requests,Resources}`, `database/{migrations,seeders,factories}`, `routes/api.php`, `routes/internal.php`, `tests/{Feature,Unit}`.
- **Frontend:** `features/{modul}/{pages,components,hooks,api,schemas,store}`; TanStack Query (server state), Zustand (UI), React Router v6 (guard permission), Tailwind + shadcn/ui, React Hook Form + Zod, TanStack Table (+virtual), dnd-kit, Recharts/ECharts, TipTap, SSE client.

---

# BAGIAN B — TASK FONDASI

## T00 — Analisis & Audit Mendalam (WAJIB PERTAMA)
````text
[KONTEKS] BE + FE (lintas). Proyek: erp-crm (backend/, frontend/, docs/). Sumber kebenaran: docs/MASTER PRD.md. Arsitektur: microservice (lihat docs/MASTER_PROMPT.md BAGIAN A). Stack: Laravel 11/12 + MySQL 8 (server SSH, tanpa Docker, tanpa instal server) + React 18/TS/Vite.

[TASK]
Lakukan analisis mendalam dan JANGAN menulis kode fitur di task ini.
1. Baca seluruh docs/MASTER PRD.md. Buat docs/ANALYSIS.md berisi:
   a. Ringkasan sistem per modul (CRM, Marketing, HRD, PM, Finance) + alur Lead-to-Cash.
   b. Daftar LENGKAP seluruh ID: FR-CRM-01..24, FR-MKT-01..12, FR-HRD-01..12, FR-PM-01..17, FR-FIN-01..15, BR-*, AC-*, E2E-* beserta service pemilik dan task yang akan memenuhinya (gunakan LAMPIRAN Matriks Keterlacakan di docs/MASTER_PROMPT.md sebagai awal, koreksi bila ada yang kurang).
   c. Daftar event lintas modul + produsen/konsumen (PRD §5.2) → tulis juga docs/EVENT-CATALOG.md.
   d. Daftar inkonsistensi/ambiguitas PRD + keputusan default (PRD Lampiran G & F). Contoh: ambang diskon, jatuh tempo invoice, jalur WhatsApp, Readiness Check, Kickoff Gate, pasal MoU yang perlu review Legal.
   e. Analisis dampak perubahan topologi modular-monolith → microservice (batas data, event, saga, konsistensi, joins lintas modul yang harus diganti snapshot/API, laporan konsolidasi/read-model).
2. Audit frontend yang ada di frontend/: package.json, vite.config.ts, tsconfig*, src/* — catat library yang sudah ada, struktur, konvensi, apa yang sudah dibuat, dan apa yang kurang dibanding stack PRD (§4.2). Jangan menghapus apa pun; hanya rekomendasikan penambahan dependency di docs/ANALYSIS.md.
3. Audit backend/: apa yang sudah ada? Bila kosong, catat. 
4. CARI folder prototipe CRM di docs/** (nama mengandung "prototype"/"prototipe"). Jika TIDAK ditemukan: berhenti dan minta pengguna menaruhnya di docs/. Jika ditemukan: baca SEMUA file (HTML/CSS/JS), lalu buat docs/PROTOTYPE-MAP.md berisi tabel: halaman/menu prototipe → rute React target (/crm/...) → komponen → data/endpoint yang dibutuhkan → fitur PRD terkait; juga ekstrak DESIGN TOKENS (warna, font, radius, spacing, shadow, ikon, status badge, gaya Kanban/tabel/modal/tab) ke docs/DESIGN-TOKENS.md.
5. Buat "GAP LIST": fitur di PRD yang tidak ada di prototipe, fitur di prototipe yang tidak ada di PRD, dan fitur industri software house yang belum ada di keduanya (rujuk BAGIAN I master prompt untuk usulan).
6. Buat docs/PROGRESS.md berisi semua TASK-ID dari docs/MASTER_PROMPT.md (BAGIAN B–J) dengan Status NOT_STARTED dan checklist OUTPUT TARGET disalin dari master prompt.

[OUTPUT TARGET]
- docs/ANALYSIS.md, docs/EVENT-CATALOG.md, docs/PROTOTYPE-MAP.md, docs/DESIGN-TOKENS.md, docs/PROGRESS.md (semua terisi lengkap, bukan kerangka).
- Ringkasan akhir ≤ 15 baris di chat: temuan kritis + pertanyaan yang benar-benar memblokir.

[DoD] Semua ID FR/BR/AC PRD terpetakan ke task; peta prototipe mencakup 100% halaman prototipe; tidak ada perubahan kode produksi.
````

## T01 — [BE] Scaffold Monorepo Microservice (tanpa Docker)
````text
[KONTEKS] BE. Folder backend/. Laravel 11/12, PHP 8.3+, MySQL 8 di server SSH (sudah ada). Tanpa Docker, tanpa instal server. Baca docs/MASTER_PROMPT.md BAGIAN A (A.2–A.5) dan docs/ANALYSIS.md.

[TASK]
1. Buat struktur backend/ sesuai A.3: gateway, services/{core,crm,marketing,hrd,pm,finance,ai,orchestrator}, shared/astacode-kernel, scripts/.
2. Tiap service = aplikasi Laravel terpisah (composer create-project laravel/laravel) dengan namespace berbeda, port dev sesuai A.2, `.env.example` memakai CACHE_STORE/QUEUE_CONNECTION/SESSION_DRIVER=database, DB_DATABASE=astacode_<service>, DB_HOST/PORT dapat diarahkan ke 127.0.0.1 (server SSH) atau ke tunnel (opsional via backend/scripts/tunnel.sh yang membuat `ssh -N -L 3307:127.0.0.1:3306 user@host`).
3. Paket shared `astacode/astacode-kernel` (path repository Composer) berisi: trait/kelas `HasOutbox`, `OutboxEvent` model+migration stub, `EventPublisher`, `InternalRequestSigner` (HMAC), middleware `VerifyServiceToken`, `CorrelationId`, `ResolveInternalUser` (membaca JWT internal dari gateway → user/role/permission), `ApiResponse` (envelope + RFC 7807 exception handler), `Money` (bcmath/brick-money), `Terbilang` (Rupiah → teks Indonesia, uji "Rp92.966.940"), `NumberingClient`, `AuditClient`, `FieldMasker`, `IdempotencyMiddleware` (tabel idempotency_keys), `HealthController`, base `FormRequest`, enum bersama (Role, Stage, dsb.).
4. Skrip backend/scripts: `dev-up.sh` (jalankan semua service + queue worker + scheduler per service di background, log ke storage/logs/dev/*.log, simpan PID), `dev-down.sh`, `migrate-all.sh`, `seed-all.sh`, `test-all.sh`, `tunnel.sh`, `sql/create-databases.sql` (CREATE DATABASE IF NOT EXISTS utf8mb4_unicode_ci + user per service dengan hak minimum; idempoten), `status.sh` (cek /internal/health semua service).
5. Gateway Laravel: konfigurasi daftar service (config/services_map.php), proxy generik `ProxyController` yang meneruskan `/api/v1/{service}/*` ke service terkait dengan header internal bertanda tangan + JWT internal (RS256/HS256, TTL pendek), timeout/retry/circuit-breaker sederhana, agregasi error, `X-Correlation-Id`.
6. Contoh endpoint `/internal/health` di semua service dan uji `status.sh` hijau semua.
7. Dokumentasi backend/README.md: cara menjalankan di server SSH (tanpa Docker), variabel env bersama, cara menambah service baru, aturan "database per service".
8. Pasang tooling: Pest, Larastan (level 6), Pint, Scribe/L5-Swagger per service. CI skeleton `.github/workflows/backend.yml` (matriks per service).

[OUTPUT TARGET]
- backend/gateway, backend/services/* (9 app Laravel), backend/shared/astacode-kernel, backend/scripts/* berjalan; `./scripts/dev-up.sh && ./scripts/status.sh` menampilkan semua service UP.
- Test kernel: Money, Terbilang, HMAC signer, FieldMasker, Idempotency (Pest) lulus.
- backend/README.md + .env.shared.example.

[DoD] Semua service boot, migrate dasar (jobs, failed_jobs, outbox_events, idempotency_keys) sukses ke MySQL, tidak ada file Docker.
````

## T02 — [BE] Core Service: Auth, RBAC, Audit, Notifikasi, Files, Numbering, Settings
````text
[KONTEKS] BE. Service: backend/services/core (port 8001, DB astacode_core) + gateway. Sumber PRD: §3 (RBAC), §4, §5.5–5.7, §17. Aturan: docs/MASTER_PROMPT.md BAGIAN A.

[TASK]
1. AUTH (di core, diekspos via gateway): Sanctum SPA cookie + CSRF (web) dan token personal (integrasi); 2FA TOTP (wajib untuk peran Finance/HR/Admin/Direktur), lockout, daftar sesi/perangkat + revoke, reset password, SSO Google (opsional, scope minimum). Guard terpisah `client` untuk portal klien (data dibatasi customer_id). Gateway menukar sesi menjadi JWT internal berisi {user_id, roles, permissions, company_id, customer_id?}.
2. RBAC: spatie/laravel-permission. Seed SEMUA peran PRD §3.1 (Owner/Direktur, Sales Manager, Sales, Presales, Marketing Lead/Content Creator, HR Manager/Staff, Project Manager, Developer/QA, Tech Lead, Finance, Finance AR Specialist, Legal Counsel, Klien, Super Admin) dan permission granular `modul.entitas.aksi` untuk matriks §3.2 + §3.4 (termasuk `crm.margin.view`, `crm.discount.approve`, `crm.top.lock`, `crm.clause.edit`, `crm.stage.override`, `finance.journal.view`, `hrd.payroll.approve`, dst.). Policy dasar record-level (mis. Sales hanya milik sendiri + tim). Endpoint `/me/permissions` untuk FE.
3. Segregation of duties (BR-FIN-08, §3.3): helper `AssertDifferentUser`.
4. AUDIT LOG immutable (append-only; spatie/laravel-activitylog atau custom): who/when/before/after/IP/UA/correlation_id; endpoint pencarian audit + akses-data-sensitif log (siapa melihat slip gaji/margin). Larang UPDATE/DELETE via trigger MySQL/permission DB.
5. NOTIFIKASI TERPADU: in-app, email, WhatsApp, Telegram (adapter); preferensi per pengguna, digest harian, quiet hours, template Blade/Markdown + variabel; endpoint daftar/mark-read; stream SSE (A.4).
6. FILES: upload ke disk terenkripsi, metadata (hash SHA-256, mime, ukuran, owner, polymorphic attachable), `VirusScanner` interface, tautan bertanda tangan berkedaluwarsa (+OTP opsional), versioning, batas 25 MB konfigurabel, izin per file.
7. NUMBERING SERVICE: format konfigurabel atomic (SELECT … FOR UPDATE), reset per bulan/tahun, semua format Lampiran C; endpoint internal `POST /internal/numbering/next`.
8. SETTINGS: key-value berversi (pajak PPN/PPh, jatuh tempo default, ambang diskon/margin, SLA, kop surat, kebijakan AI, jam kerja, hari libur) + cache.
9. APPROVAL ENGINE generik (PRD §5.5): definisi alur (serial/paralel), delegasi, SLA approval, eskalasi, riwayat; jenis: diskon, quotation>nilai, payroll, lembur malam, pengeluaran>batas, jurnal manual, publikasi konten, klausul menyimpang, addendum, TOP perubahan, waive readiness. Endpoint `POST /internal/approvals` (dipanggil service lain) + callback event `approval.decided`.
10. WEBHOOK masuk/keluar (HMAC, replay protection, dead-letter, log delivery) + UI data (endpoint).
11. Admin: manajemen user, role, permission, impersonate (dengan audit), integrasi (token terenkripsi), status kesehatan service.
12. Seeder: super admin, semua role, user demo per peran, setting default, numbering default, hari libur Indonesia.

[OUTPUT TARGET]
- Endpoint: /auth/*, /me, /users, /roles, /permissions, /audit-logs, /notifications, /files, /approvals, /settings, /numbering, /webhooks, /integrations (+OpenAPI).
- Pest: login+2FA, lockout, policy kepemilikan, masking, audit immutable, numbering concurrency (paralel tidak duplikat), approval serial/paralel.
- AC: E2E-08 (user Sales mengakses data gaji/HPP via API → 403/field hilang + tercatat audit).

[DoD] Semua permission PRD terseed; gateway meneruskan JWT internal yang diverifikasi service lain.
````

## T03 — [BE] Orchestrator Service: Event Bus, Saga Deal-Won, Automation Rule Engine
````text
[KONTEKS] BE. Service: backend/services/orchestrator (8008, DB astacode_orchestrator). PRD: §5 (5.1–5.4). Pola: Outbox → relay → orchestrator → konsumen (docs/MASTER_PROMPT.md A.5).

[TASK]
1. Endpoint `POST /internal/events` (HMAC) → simpan `event_log`, cocokkan `subscriptions` (konfigurasi event→konsumen+endpoint), dispatch job pengiriman dengan retry eksponensial 5×, dead-letter, replay manual, idempotensi (event_id+consumer).
2. Implementasi seluruh Event Catalog PRD §5.2 sebagai konfigurasi + skema payload tervalidasi (JSON Schema) di `config/events/*.json`.
3. SAGA "Deal Won" (PRD §5.3) sebagai state machine tersimpan (`sagas`, `saga_steps`): langkah (1) upsert Customer 360 di CRM, (2) buat Project+Epics+Tasks backlog dari WBS RAB di PM (idempotent key `deal_id+pm`), (3) Receivable Schedule di Finance, (4) Capacity Demand di HRD; hasil COMPLETED / PARTIAL; retry exp. backoff; endpoint **Retry Handover** per langkah; kompensasi manual (batalkan proyek bila deal dibatalkan sebelum kickoff); callback status ke CRM (Handover Tracker + Project Transition Hub FR-CRM-22; target ≤ 2 menit). Dukung Kickoff Gate (FR-CRM-23) & Readiness Check sebagai prasyarat.
4. AUTOMATION RULE ENGINE no-code (Trigger → Condition → Action): trigger event/idle/overdue/cron; condition ekspresi aman (JSON-logic, bukan eval); aksi: assign (user/round-robin), kirim WA/email/Telegram, buat task/aktivitas, ubah field, buat tiket, webhook, minta approval, jalankan AI. Log `automation_runs`.
5. Seed aturan bawaan PRD §5.4: round-robin lead baru; reminder follow-up H+1; eskalasi stale ≥5 hari ke Sales Manager (≥10 hari manager); reminder invoice H-3/H+1/H+7/H+14; alert utilisasi >100%; alert margin <30%; reminder kontrak SLA H-60 (+H-90/-30).
6. Integration Bridge: antarmuka adapter untuk WhatsApp, Google, GitHub, Telegram, Sosmed, Email (implementasi konkret di task lintas modul).
7. Read-model/summary builder: konsumen event untuk tabel ringkasan laporan konsolidasi (dipakai BAGIAN H).
8. Endpoint admin: lihat event log, saga, rule, run; replay; health.
9. Dokumentasikan semua kontrak di docs/EVENT-CATALOG.md & docs/API-CONTRACTS/orchestrator.yaml.

[OUTPUT TARGET]
- Migrasi: event_log, subscriptions, sagas, saga_steps, automation_rules, automation_runs, processed_events.
- Test: saga sukses, saga partial + retry, idempotensi (event ganda tidak membuat proyek ganda — BR-CRM-13), rule engine kondisi/aksi, dead-letter.

[DoD] Simulasi `deal.won` dengan konsumen stub menghasilkan status handover benar dan dapat di-retry.
````

## T04 — [BE] AI Service (AI Gateway)
````text
[KONTEKS] BE. Service: backend/services/ai (8007, DB astacode_ai). PRD: §13. Semua modul memanggil AI hanya lewat service ini.

[TASK]
1. Provider abstraction (Gemini/Claude/OpenAI/model lokal) + fallback otomatis + pemilihan model per use case (cepat-murah vs akurat); kunci API dari integrasi terenkripsi/env.
2. Prompt Registry berversi (DB): variabel, A/B, rollback, status (draft/active), pemilik modul.
3. Structured output wajib JSON Schema; validasi + retry perbaikan bila invalid. Sediakan skema: RAB (summary{total_mandays,duration_weeks,assumptions[],risks[],clarifying_questions[],tech_stack{}}, items[{category,layer,name,description,role,mandays,complexity,confidence,notes}], out_of_scope[]), voice-note (executive_summary, core_needs[3-5], technical_scope[], action_items[]), WA intent/reply, lead summary, quotation intro, MoU clause draft, content (carousel/video/blog), OCR struk, RCA bug, resume parser, daily log summary, sprint summary, forecast narrative, trend scoring.
4. Pipeline media: transkripsi audio (STT Bahasa Indonesia + istilah Inggris, diarization opsional), ekstraksi teks dokumen (PDF/DOCX/TXT) + OCR gambar, chunking token.
5. RAG: `VectorStore` adapter; indeks Module Catalog, proyek lampau, FAQ/SOP (isolasi per customer).
6. Guardrail: PII redaction opsional, deteksi prompt-injection pada dokumen/pesan klien, allowlist tool/action agen, AI dilarang menulis ke Finance/GL & dilarang mengirim ke klien tanpa kebijakan, budget biaya per modul/bulan + rate-limit.
7. Observabilitas: `ai_runs` (module, purpose, provider, model, prompt_ref+versi, input_hash, output JSON, tokens in/out, cost, latency, status, human_decision, decided_by) + endpoint metrik (akurasi MAPE estimasi vs realisasi, tingkat penerimaan).
8. Evaluasi: golden set (RAB & balasan WA) + command `ai:eval` — rilis prompt baru hanya jika lolos ambang.
9. API sinkron untuk respons cepat (mis. WA suggest) dan asinkron (job + callback event `ai.run.completed` + SSE) untuk generate berat. Mode mock deterministik untuk test/dev tanpa API key.
10. Feedback loop Module Catalog (PRD §13.3): konsumsi realisasi mandays dari PM → statistik rata-rata/deviasi → konteks few-shot.

[OUTPUT TARGET]
- Endpoint: POST /internal/ai/run, GET /internal/ai/runs/{id}, /prompts CRUD, /providers, /metrics, /budgets.
- Test: schema validation+retry, fallback provider, guardrail injection, budget limit.

[DoD] Panggilan generate RAB dengan mock provider mengembalikan JSON valid sesuai skema; ai_runs tercatat.
````

## T05 — [FE] Fondasi Frontend: App Shell, Design System, Guard, API Client
````text
[KONTEKS] FE. Folder frontend/ (Vite + React 18 + TS sudah ada). PRD: §15 (UI/UX), §4.5. Sumber visual: prototipe CRM di docs/ (lihat docs/PROTOTYPE-MAP.md & docs/DESIGN-TOKENS.md). Semua API lewat gateway (http://<host>:8000/api/v1).

[TASK]
1. Audit & lengkapi dependency sesuai PRD §4.2: tailwindcss, shadcn/ui(radix), @tanstack/react-query, zustand, react-router-dom v6, react-hook-form, zod, @tanstack/react-table (+virtual), @dnd-kit/*, recharts/echarts, @tiptap/*, lucide-react, date-fns, vitest, @testing-library/react, playwright, msw (mock hanya untuk test). Jangan hapus paket yang sudah ada.
2. Terapkan DESIGN TOKENS dari prototipe (warna, font, radius, spacing, status badge) ke tailwind.config + CSS variables; dark/light mode; WCAG 2.1 AA; grid 8-pt; Inter + JetBrains Mono; ikon Lucide.
3. Struktur `src/` sesuai A.7/PRD §4.5: app/(router, providers, guards), shared/(ui, hooks, api-client, utils, i18n, permissions), features/*, portal-client/.
4. API client (axios/fetch wrapper): base URL dari env, CSRF Sanctum, interceptor 401/403/409/422/429, RFC 7807 → error terpetakan ke field form, header Idempotency-Key & X-Correlation-Id, retry untuk GET, tipe TS dari OpenAPI (openapi-typescript).
5. Auth: halaman login (+2FA), logout, sesi, lupa password; `usePermissions()`, `<Can permission="...">`, route guard berbasis permission, halaman 403/404/500, session expiry modal.
6. Layout global: Sidebar per modul (collapse), Topbar (pencarian global, notifikasi, quick-create, switch modul, profil), Breadcrumb, Command Palette (Ctrl/Cmd+K), Notification Center (SSE), widget "Task Saya" lintas modul — tiru gaya prototipe.
7. Komponen dasar (PRD §15.3): DataTable (virtualized, saved views, kolom dapat diatur, bulk action), Kanban (dnd-kit), SpreadsheetGrid (inti untuk RAB: keyboard nav, paste Excel, undo/redo), A4Preview, GanttChart wrapper, ChatThread, FileDropzone (+progres), ApprovalTimeline, ActivityFeed, CurrencyInput (IDR, Terbilang), PhoneInput (+62 → E.164), DateRange, StatusBadge, EmptyState, ErrorState, Skeletons, ConfirmDialog, SlideOver, Tabs, Stepper, Toast.
8. Offline: banner + antrean draf lokal (IndexedDB) untuk form panjang (lead/quotation/daily log/presensi); PWA manifest + service worker dasar.
9. i18n id-ID default + en; format Rp `1.234.567,00`, tanggal `dd MMM yyyy`, zona Asia/Jakarta.
10. Storybook ringan atau halaman `/dev/ui-kit` yang memuat semua komponen dasar.
11. Test: Vitest untuk CurrencyInput, PhoneInput, permission guard, SpreadsheetGrid kalkulasi; Playwright smoke login→dashboard.

[OUTPUT TARGET]
- Aplikasi berjalan `npm run dev`; login → shell → navigasi modul (menu kosong per modul diisi task berikutnya); /dev/ui-kit lengkap; tampilan konsisten dengan prototipe.

[DoD] Tidak ada hard-code data bisnis; semua komponen punya state loading/empty/error; lint & typecheck lulus.
````

## T06 — [BE] Kernel Terbilang, Pajak, Money & Util Dokumen (jika belum tuntas di T01)
````text
[KONTEKS] BE. shared/astacode-kernel. PRD §6.4 FR-CRM-06/07, §10.4.

[TASK]
Lengkapi & uji: Terbilang (Rupiah → teks Indonesia, termasuk "Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah"), kalkulator RAB (subtotal, kontingensi default 10% [wajib alasan jika <5%], diskon %/nominal, DPP, PPN 11% konfigurabel, total, pembulatan sisa ke termin terakhir), kalkulator pajak termin (PPN, PPh 23 2% jika pemotong), perhitungan margin (HPP internal, gross margin %, ambang warna hijau ≥40/kuning 25–40/merah <25), hash dokumen SHA-256, QR verifikasi generator, normalisasi telepon E.164 (+628…), fuzzy matching nama perusahaan (≥85%), kalender kerja/hari libur (untuk SLA).

[OUTPUT TARGET] Kelas + Pest test dengan tabel kasus (≥ 30 kasus uji Terbilang & kalkulasi pajak).
[DoD] Semua test hijau; dipakai ulang oleh crm, finance, pm.
````

## T07 — [FE] Shell Navigasi Seluruh Modul + Dashboard Landing
````text
[KONTEKS] FE. frontend/src/app + features/*. PRD §15.2, §15.4 (peta rute). Prototipe: lihat PROTOTYPE-MAP.md.

[TASK]
Daftarkan SELURUH rute PRD §15.4 (crm, marketing, hr, pm, finance, portal, admin) dengan lazy-loading per modul, error boundary per fitur, breadcrumb otomatis, menu sidebar per modul sesuai peta menu PRD (CRM 21 menu §6.2; Marketing §7.2; HRD §8.2; PM §9.2; Finance §10.2), item menu disembunyikan berdasarkan permission. Buat halaman placeholder BERLABEL "Belum diimplementasikan — task <ID>" untuk rute yang belum dikerjakan (bukan data palsu). Buat `/dashboard` eksekutif lintas modul (kartu KPI + grafik) yang membaca endpoint ringkasan (diisi bertahap; saat belum ada data tampilkan empty state).

[OUTPUT TARGET] Semua rute terdaftar; navigasi lengkap; guard permission bekerja; daftar task→rute tercatat di docs/PROGRESS.md.
[DoD] Tidak ada rute PRD yang terlewat; tidak ada 404 untuk menu yang tampil.
````

## T08 — [BE] Data Master Bersama & Seeder Demo Lintas Modul
````text
[KONTEKS] BE (semua service). PRD §6.8, §10, Lampiran A.

[TASK]
Buat seeder idempoten per service: lead_sources (semua sumber FR-CRM-01), alasan reject (FR-CRM-10), jenis proyek, industri, rate_cards (role×level), module_catalog (Presensi GPS, Payroll, Payment Gateway, dst. — min. 40 modul dengan mandays baku), template quotation/MoU + 22 pasal (Lampiran H), canned responses WA (follow up penawaran, pengingat UAT, notifikasi invoice termin), COA 23 akun (Lampiran A) + posting rules default (§10.5), pajak, pipeline & probabilitas, SLA, hari libur, leave types, shift, PKL quota. Seeder demo (opsional via flag) membuat data realistis end-to-end: 30 lead dengan berbagai stage, 10 customer, 5 proyek, invoice, karyawan, jurnal — untuk demo & uji FE.

[OUTPUT TARGET] `backend/scripts/seed-all.sh` (+ `--demo`).
[DoD] Seeder dapat dijalankan berulang tanpa duplikasi; COA sesuai Lampiran A.
````

## T09 — [BE+FE] Kontrak API Terpadu & Generasi Tipe
````text
[KONTEKS] BE + FE. docs/API-CONTRACTS/, frontend/src/shared/api-client.

[TASK]
Gabungkan OpenAPI semua service menjadi satu spesifikasi gateway (docs/API-CONTRACTS/gateway.yaml), buat skrip `npm run gen:api` (openapi-typescript) menghasilkan tipe & hook TanStack Query dasar; Postman collection; contract test di CI (backend vs OpenAPI). Dokumentasikan konvensi filter/sort/include/pagination/cursor.

[OUTPUT TARGET] gateway.yaml, tipe TS ter-generate, koleksi Postman, CI contract test.
[DoD] Perubahan endpoint tanpa memperbarui OpenAPI membuat CI gagal.
````

---

# BAGIAN C — MODUL 1: CRM
> Service: `backend/services/crm` (8002, DB `astacode_crm`) · FE: `frontend/src/features/crm`. PRD: §6 (FR-CRM-01..24, BR-CRM-01..21, AC-CRM-01..17). Prototipe: lihat `docs/PROTOTYPE-MAP.md` (halaman CRM).

## CRM-BE-01 — Skema, Model & Master Customer/Contact/Lead Source
````text
[KONTEKS] BE. Service crm (8002). PRD §6.8, FR-CRM-01, FR-CRM-13, BR-CRM-05/09. Baca docs/MASTER_PROMPT.md BAGIAN A.

[TASK]
1. Migrasi + model + factory + policy untuk: customers (code CUS-NNNN, legal_name, brand_name, type[perusahaan,perorangan,instansi,startup,umkm], industry, size, npwp(encrypted, 16 digit), nib, is_pkp, is_pph23_withholder, status[prospect,active,inactive,churned,blacklist], first_source_id, latest_source_id, owner_id, health_score, billing_address JSON, company_id), contacts (role[decision_maker,technical_lead,finance_lead,influencer], title, email, phone_wa E.164, is_primary, consent_comm, consent_at, kanal preferensi), lead_sources (+channel_group, is_active), customer_attributions (first/latest touch, utm), custom_fields (definisi+nilai), merge_history.
2. Endpoint: CRUD customers/contacts, /customers/{id}/360 (profil, legal & pajak, atribusi, matriks stakeholder, riwayat lead/quotation/deal/proyek/kontrak/tiket/invoice [snapshot dari event/API service lain], keuangan [total kontrak, piutang, DSO, kolektibilitas — dari finance via API internal/cache read-model], health score 0–100 [ketepatan bayar bobot utama + repeat project + aktivitas + tiket terbuka + NPS/CSAT], komunikasi, account plan).
3. Deteksi duplikat: nomor WA (E.164), email, nama perusahaan fuzzy ≥85% → endpoint /customers/duplicates-check; merge customer dengan histori terjaga (BR-CRM-09); impor/ekspor CSV/Excel.
4. Verifikasi NPWP oleh Finance AR (flag npwp_verified_by/at) — permission crm→finance.
5. Anonimisasi PDP atas permintaan (kolom diacak, tercatat audit).

[OUTPUT TARGET] Migrasi, model, resource (masking npwp), policy (Sales hanya milik sendiri+tim), Pest test (duplikat, merge, masking, health score).
[DoD] AC-CRM-02 terpenuhi di level API.
````

## CRM-BE-02 — Lead, Input Manual/Web/Marketing, Scoring, Auto-assign
````text
[KONTEKS] BE. crm. FR-CRM-01, FR-CRM-12, BR-CRM-05/08, event lead.captured/lead.created.

[TASK]
1. Tabel leads (kolom lengkap PRD §6.8: code LD-YYYY-NNNN via numbering core, customer_id, contact_id, title, project_type[web_mobile_app, erp_custom, fintech, iot_telematics, ecommerce, lainnya], priority[hot,high,medium,low], source_id, sub_source, utm JSON, stage_id enum, probability, est_value, budget_range, target_closing, target_golive, owner_id, score, days_in_stage, stale_since, active_quotation_id, reject_reason_id, reject_note, won_at, industry, services multi-enum, tags, consent) + lead_stage_history + watchers + tags.
2. POST /leads: validasi lengkap FR-CRM-01 (judul, PIC min 2 char, WA wajib untuk input manual & E.164, est_value wajib, source wajib; lead dari Marketing boleh Email saja sampai Follow Up), mode Existing/New Customer (NPWP 16 digit untuk new), upsert customer+contact+lead_sources dalam satu transaksi, atribusi first/latest touch, auto-assign Sales PIC (round-robin/berbasis aturan/manual), buat Activity "Follow up pertama" (SLA ≤30 menit jam kerja), hitung Lead Score awal, emit `lead.created` (outbox).
3. Endpoint webhook/form web: POST /public/lead-capture (token+HMAC, UTM otomatis, honeypot/captcha hook) → `lead.captured`; konsumen event `lead.captured` dari Marketing/WA.
4. Lead Scoring (FR-CRM-12): aturan konfigurabel (Fit, Engagement, Intent, Sumber) skor 0–100, label Hot/Warm/Cold, recalculation job; fase-2 hook model prediktif.
5. Stale logic (BR-CRM-08/FR-CRM-24): command terjadwal hitung days_in_stage, flag "No Next Step", stale ≥5 hari → notifikasi Sales PIC, ≥10 hari → eskalasi Sales Manager.
6. Daftar/filter: stage, source, owner, rentang nilai, tanggal, tag, industri, stale-only, milik saya/tim; sort multi-kolom; saved views (tabel saved_views); bulk action (assign ulang, ubah tag, ekspor CSV, soft delete); pencarian global (SearchService).
7. Reopen lead Reject oleh Sales Manager dengan alasan (FR-CRM-10).

[OUTPUT TARGET] Endpoint /leads (CRUD+bulk+export), /leads/{id}, /public/lead-capture, command `crm:leads:stale-check`, Pest test AC-CRM-01, AC-CRM-02, validasi form, scoring, auto-assign.
[DoD] Lead baru muncul di stage new_lead + aktivitas follow-up pertama terbuat.
````

## CRM-BE-03 — Pipeline Guard, Transisi Stage, Audit & Override
````text
[KONTEKS] BE. crm. FR-CRM-24, §6.3 tabel transisi, BR-CRM-01/02/03/12/14/15, state machine §6.6.

[TASK]
1. Kelas terpusat `PipelineTransitionGuard` (satu-satunya jalur ubah stage; dipakai drag-drop, tombol aksi, API, automation rule) dengan tabel transisi PRD (NEW→FOLLOW_UP/REJECT; FOLLOW_UP→REQUIREMENT/REJECT; REQUIREMENT→QUOTATION/REJECT; QUOTATION→NEGOTIATION/REJECT; NEGOTIATION→DEAL/QUOTATION/REJECT; REJECT→FOLLOW_UP via Reopen) + syarat masuk: Follow Up (PIC ditetapkan & kontak awal ≤30 mnt jam kerja), Requirement (klien merespons & discovery call terjadwal), Quotation (RAB ≥1 item total>0, WBS selesai; pesan persis "Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (minimal 1 item modul)."), Negotiation (quotation & draf MoU terkirim/ada versi SENT), Deal (quotation ACCEPTED + TOP balance 100% + RAB valid + Readiness Check PM lolos + dokumen wajib stage Deal; pesan "Termin pembayaran pada Quotation belum balance (100%). Sisa alokasi: X%." + nominal), Reject (kategori alasan + catatan ≥10 karakter).
2. PATCH /leads/{id}/stage (response 422 dengan `guard_failure` + `deep_link_tab` agar FE bisa menautkan ke tab penyebab), stage mundur dengan catatan alasan (BR-CRM-12, kecuali dari Deal/Reject).
3. Probabilitas default per stage (10/20/35/55/75/100/0) konfigurabel Admin; weighted value.
4. Override Super Admin (BR-CRM-15): permission `crm.stage.override`, alasan wajib, audit log.
5. Audit immutable perubahan stage/nilai/diskon/TOP (user, ISO-8601, lama→baru).
6. Checklist aktivitas wajib per stage (tabel stage_checklists + progress) ditampilkan di Overview.
7. Next Best Action (aturan + AI via ai-service): endpoint /leads/{id}/next-actions.
8. Emit `lead.stage_changed`.

[OUTPUT TARGET] Kelas guard + test untuk SETIAP transisi sah/tidak sah, AC-CRM-05, AC-CRM-10, AC-CRM-11, AC-CRM-12, AC-CRM-13 (BDD → Pest).
[DoD] Tidak ada jalur lain yang bisa mengubah leads.stage selain guard (uji reflection/grep di CI).
````

## CRM-BE-04 — Activities, Penjadwalan, Google Calendar/Meet
````text
[KONTEKS] BE. crm. FR-CRM-04, integrasi Google (PRD §14).

[TASK]
1. Tabel activities (type MEETING,CALL,FOLLOW_UP,EMAIL,WHATSAPP,DEMO,SITE_VISIT,TASK,NOTE; start/end, status, outcome[berhasil,reschedule,tidak_merespons], next_step, attendees JSON internal+kontak klien, location/link, google_event_id, meet_link, notes, lampiran).
2. Integrasi Google Calendar API (OAuth per user, scope minimum): "Buat Google Meet" membuat event + conference link + undangan; sinkron dua arah (webhook/poll push notification channel) perubahan jadwal.
3. Pengingat H-1 dan 1 jam sebelum (in-app, email, WA ke sales) via scheduler.
4. Wajib isi outcome setelah selesai; opsi buat aktivitas berikutnya otomatis.
5. Rekaman meeting (upload audio/video/tautan transkrip) → dapat dikirim ke Requirement; AI Meeting Notes (ringkasan, keputusan, action item) via ai-service.
6. Aturan "No Next Step" & Stale (BR-CRM-08).
7. Kalender: endpoint /activities/calendar (rentang tanggal, filter milik saya/tim).
8. Adapter Google dengan mode mock untuk dev/test.

[OUTPUT TARGET] Endpoint /leads/{id}/activities, /activities/calendar, OAuth connect/disconnect, Pest test dengan Google mock.
[DoD] Membuat meeting dengan opsi Meet menghasilkan meet_link & undangan (mock) dan tersinkron.
````

## CRM-BE-05 — Requirement, Upload, AI Generate RAB (Async)
````text
[KONTEKS] BE. crm + ai-service. FR-CRM-05, BR-CRM-10, AC-CRM-03.

[TASK]
1. Tabel requirements (versi), requirement_files (kind doc/audio/image, transcript_text), requirement params JSON (platform, tech preference, jumlah user, integrasi wajib, deadline, tingkat detail Ringkas/Standar/Rinci, asumsi produktivitas, template katalog).
2. Endpoint POST /leads/{id}/requirements (prompt+file; minimal salah satu), POST …/requirements/generate → simpan requirement_version status PROCESSING, dispatch `GenerateRabJob` → ai-service (transkripsi, ekstraksi dokumen/OCR, normalisasi, dekomposisi Master Data→Transaksi→Laporan→Dashboard/KPI→Non-fungsional→Integrasi, estimasi mandays per role, kompleksitas, asumsi, risiko, pertanyaan klarifikasi, out-of-scope, rekomendasi Tech Stack, layer WBS: Frontend UI/UX · Backend Core & DB · Integrasi API/Payment Gateway · Mobile · DevOps & Cloud Security · QA & UAT).
3. Simpan rab_draft (origin AI + ai_confidence) + ai_run; notifikasi SSE `RabGenerated`; ≤60 detik (AC-CRM-03).
4. Versioning: setiap generate = versi baru, TIDAK menimpa RAB yang diedit; aksi "Gunakan sebagai RAB" atau "Gabungkan (merge) ke RAB saat ini"; "Regenerate dengan feedback"; "Generate per bagian".
5. Upload: antivirus hook, batas 25 MB, chunking token, opsi redaksi PII & provider zero-retention (kebijakan dari settings).
6. Template Katalog dipakai sebagai konteks few-shot.
7. Transisi ke stage Requirement otomatis dicatat saat requirement diinput (sesuai syarat).

[OUTPUT TARGET] Endpoint + job + test dengan ai-service mock; tabel requirement_versions.
[DoD] AC-CRM-03 lulus dengan mock; hasil AI tidak pernah otomatis masuk quotation (BR-CRM-10).
````

## CRM-BE-06 — RAB Engine (Editable, Versi, Kalkulasi, Margin Dual-View)
````text
[KONTEKS] BE. crm. FR-CRM-06, BR-CRM-07, AC-CRM-04, AC-CRM-07. Kernel Money/Pajak/Terbilang.

[TASK]
1. rab_versions & rab_items sesuai DDL PRD §11.3 + kolom tambahan (category, layer, is_optional, is_recurring, is_bonus, origin AI/MANUAL/TEMPLATE, ai_confidence, notes). GET/PUT /leads/{id}/rab (bulk upsert, optimistic locking `version`), POST /rab/recalculate.
2. Kalkulasi server (otoritatif; klien menghitung ulang <100 ms hanya untuk UX): Subtotal (non-optional) → kontingensi default 10% (wajib alasan jika <5%) → diskon (nominal/%) atas (Subtotal+Kontingensi) → DPP → PPN (default 11%, dari settings, hanya jika PKP) → TOTAL → terbilang.
3. Margin Internal View: HPP = Σ(qty×cost_rate per role dari rate card) + biaya langsung lain (cloud, lisensi, freelance); GM% = (DPP−HPP)/DPP; indikator hijau ≥40/kuning 25–40/merah <25 (konfigurabel); peringatan margin < batas → Approval.
4. Dual-View: field HPP/margin/cost_rate DIHILANGKAN di serializer untuk peran tanpa `crm.margin.view` (AC-CRM-07; uji bahwa key tidak ada pada JSON).
5. Fitur: impor Excel & Template Katalog, ekspor Excel, duplikasi/urut ulang/grup kategori, baris Optional/Gratis/Recurring, riwayat versi + diff (qty/harga), auto-save draft, derivasi durasi (mandays per role ÷ ukuran tim + kapasitas dari HRD [informatif via API]), validasi (qty>0, harga≥0, ≥1 item, peringatan role tanpa tarif).
6. Approval diskon (FR-CRM-17): ≤10% Sales; >10–15% Sales Manager; >15% atau margin <25% Direktur; quotation >Rp500 jt Direktur — via approval engine core; ambang dari settings.
7. Item AI berubah menjadi badge Manual saat diedit (AC-CRM-04).

[OUTPUT TARGET] Endpoint + test kalkulasi (tabel kasus), masking, optimistic lock 409, approval.
[DoD] Contoh PRD: dpp 100jt → ppn 11jt terhitung benar; AC-CRM-04 & 07 lulus.
````

## CRM-BE-07 — TOP / Termin Pembayaran
````text
[KONTEKS] BE. crm + finance (read). FR-CRM-07, BR-CRM-02/18, AC-CRM-05, AC-CRM-12.

[TASK]
1. top_schemes/top_milestones (term_number, nama [DP, Termin n, UAT, Retensi, Pelunasan], percentage, amount_idr, terbilang_text, trigger_type [signing,date,milestone,uat_passed,go_live,bast,retention_end], deskripsi syarat, due_days_offset (Net N, default ≤14 hari), project_milestone_ref).
2. Preset: 30/40/30; 50/50; 20/30/30/20; 40/30/20/10 dengan retensi; bulanan/recurring; Custom bebas. Sinkron persen↔nominal; selisih pembulatan diserap termin terakhir.
3. Validasi total = 100,00% (is_balanced); bila ≠100% kunci simpan final & ekspor dokumen; tampilkan "Sisa alokasi belum diatur: X% (Rp…)".
4. Perhitungan per termin: DPP, PPN, estimasi PPh 23 (informatif).
5. Review & KUNCI oleh Finance AR (locked_by_finance_at); perubahan setelah kunci butuh persetujuan Finance (BR-CRM-18); Finance memverifikasi NPWP sebelum invoice DP terbit.
6. Menjadi sumber Receivable Schedule saat Deal Won.

[OUTPUT TARGET] Endpoint PUT /leads/{id}/top, POST /top/lock, test balance & preset, approval perubahan setelah kunci.
[DoD] AC-CRM-05/AC-CRM-12 lulus (pesan error persis).
````

## CRM-BE-08 — Quotation + MoU Satu PDF, Template & Clause Library, Versioning, Tracking
````text
[KONTEKS] BE. crm. FR-CRM-08, FR-CRM-21A, Lampiran D/E/H, BR-CRM-04/06/11/16, AC-CRM-06, AC-CRM-17.

[TASK]
1. Tabel quotations (number QT/YYYY/MM/NNNN-Rn, version, status DRAFT→PENDING_APPROVAL→APPROVED_INTERNAL→SENT→VIEWED→NEGOTIATION→ACCEPTED/REJECTED/EXPIRED/SUPERSEDED, valid_until default 30 hari, rab_version_id, snapshot JSON RAB+TOP+konten, pdf_file_id, signed_file_id, sent/viewed/accepted_at, hash) + quotation_sections (INTRO, RAB_SCOPE, MOU, SIGN) + quotation_clauses.
2. Template engine: merge fields Lampiran D (`{{customer.name}}`, `{{rab.table}}`, `{{top.table}}`, `{{total.terbilang}}`, …), template TipTap/Blade, Clause Library 22 pasal (Lampiran H) dengan varian, wajib/opsional, urutan dapat diubah; variabel klausul (PPN, jatuh tempo, garansi, denda, SLA P1/P2/P3) ditarik dari parameter sistem.
3. Generate "Quotation & MoU" → SATU PDF A4: Hal.1 Cover & Pendahuluan (kop, nomor, masa berlaku, profil PT Astacode Solusindo Teknologi, Yth., perihal, pengantar [AI draft], ringkasan investasi, kontak Sales) → Hal.2..k Item RAB & Scope (tabel kategori→modul→qty→satuan→harga→subtotal, kontingensi, diskon, DPP, PPN, Total, Terbilang, uraian scope, asumsi, batasan, exclusion, timeline, TOP+trigger) → Hal.k+1..m 22 Pasal MoU terisi otomatis → Hal.terakhir Tanda Tangan (Pihak Pertama/Kedua, alokasi e-Meterai, saksi, QR + hash). Footer: No. Dokumen · Versi · "Hal. X dari Y" · Kerahasiaan. Watermark DRAFT pada versi belum disetujui. Async job ≤15 s; interface `PdfRenderer` (Browsershot/DomPDF).
4. Pendahuluan via AI (ai-service) dari data lead/requirement (draf, dapat diedit).
5. Versioning R1,R2…; snapshot immutable saat SENT/ACCEPTED (BR-CRM-04); perbandingan antar versi; Expired otomatis di valid_until (BR-CRM-11).
6. Persetujuan internal (BR-CRM-06) via approval engine sebelum SENT.
7. Kirim: unduh PDF, email/WA (tautan aman berkedaluwarsa + OTP opsional); tracking buka/unduh/durasi baca (endpoint publik bertoken).
8. Upload Signed Copy (PDF scan) + hash SHA-256; e-Sign opsional fase 2 (adapter PSrE).
9. Ubah status dari tab Quotation memetakan ke stage pipeline (tombol cepat: Tandai Terkirim, Klien Setuju→Deal, Klien Menolak→Reject) lewat PipelineTransitionGuard.
10. Hak akses klausul: hanya Legal/Super Admin mengubah teks klausul; setiap perubahan = versi klausul baru (effective_from, editor, alasan); dokumen lama memakai snapshot; permintaan penyimpangan klausul oleh Sales → approval Legal; klausul termodifikasi ditandai.

[OUTPUT TARGET] Endpoint /leads/{id}/quotations, /quotations/{id}/generate-pdf|send|status, /templates, /clauses; PDF contoh terlampir di tests/fixtures; Pest test (halaman berurutan, nomor kontinu, snapshot, kelas guard klausul).
[DoD] AC-CRM-06 & AC-CRM-17 lulus; PDF vektor A4 dengan nomor halaman kontinu.
````

## CRM-BE-09 — Deal Won, Handover, Readiness Check, Kickoff Gate, Transition Hub
````text
[KONTEKS] BE. crm + orchestrator. FR-CRM-09/22/23, BR-CRM-13/17/21, §5.3, AC-CRM-08, AC-CRM-14.

[TASK]
1. POST /leads/{id}/win (Idempotency-Key) → validasi guard (termasuk quotation ACCEPTED, signed copy [konfigurasi wajib/opsional], dokumen wajib stage Deal), snapshot quotation read-only, Customer PROSPECT→ACTIVE (NPWP & alamat penagihan wajib), emit `deal.won` (payload: deal_id, customer_id, quotation_id, wbs_modules, mandays_budget, top_scheme, deadline, tech_stack) via outbox, buat Kickoff Checklist otomatis, notifikasi PM/Finance/Tech Lead/Sales. Pesan sukses UI: "Deal berhasil disahkan! Proyek baru dan jadwal invoice telah diterbitkan otomatis."
2. delivery_readiness_checks: PM meninjau WBS/RAB (kejelasan scope, kelayakan mandays vs kapasitas HRD, tech stack, timeline) → Siap / Siap dengan catatan / Belum siap (blokir sampai selesai atau waive Direktur); SLA 1 hari kerja; mode wajib (default) atau advisory (settings).
3. Kickoff Gate: proyek status Provisioned; sprint & time logging aktif setelah invoice DP Paid (mode ikuti DP / langsung aktif; Direktur dapat membuka lebih awal dengan alasan) — terima event `invoice.paid` dari Finance.
4. Handover Tracker + Project Transition Hub: stepper (Project dibuat → WBS diimpor → PM ditetapkan → Receivable Schedule dibuat → Invoice DP terbit → DP lunas → Kickoff terjadwal → Proyek aktif), umur transisi, PM penanggung jawab, tombol Retry Handover (memanggil orchestrator), tautan proyek. Konsumen event status dari orchestrator.
5. Reject (FR-CRM-10): POST /leads/{id}/lose — kategori (Kalah Harga, Klien Batal, Kompetitor, Scope Tidak Cocok, Timeline Tidak Cocok, Tidak Ada Respons, Budget Tidak Tersedia, Lainnya), catatan ≥10 karakter, kompetitor+harga opsional, opsi Nurture (reaktivasi 90 hari) / Do Not Contact; emit `deal.lost`.
6. Kalender Penagihan Termin (AR) read-model dari event Finance (Draft/Sent/Paid/Overdue, umur piutang; Sales milik sendiri, Finance semua) + aksi kirim pengingat WA (template).

[OUTPUT TARGET] Endpoint /leads/{id}/win|lose, /deals/{id}/handover, /readiness, /transition-hub, /ar-calendar; test BDD AC-CRM-08/14, idempotensi (tidak ada proyek ganda).
[DoD] Simulasi end-to-end dengan orchestrator + stub PM/Finance/HRD menghasilkan tracker ✔.
````

## CRM-BE-10 — Voice Note Recorder & AI Transcriber (Backend)
````text
[KONTEKS] BE. crm + ai. FR-CRM-19, BR-CRM-20, AC-CRM-15.

[TASK]
Tabel voice_notes (lead_id, file_id, duration_sec ≤120 mnt konfigurabel, consent_at, transcript_text, executive_summary, core_needs JSON, technical_scope JSON, status) + voice_note_action_items (task, pic_user_id, due_date, synced_activity_id). Endpoint upload (.mp3 .wav .m4a .mp4 audio; consent wajib), job transkripsi async + ekstraksi terstruktur JSON Schema (Executive Summary, 3–5 Client Core Needs, Technical Scope [modul WBS + mandays awal], Action Items), progres via SSE. Aksi 1-klik: sinkronkan Action Item → activities; kirim Technical Scope → Requirement; simpan ringkasan ke timeline. Transkrip/ringkasan editable sebelum sinkron (status draf). Audio terenkripsi, retensi default 12 bulan lalu hanya transkrip (command purge terjadwal).

[OUTPUT TARGET] Endpoint /leads/{id}/voice-notes, /voice-notes (lintas lead), test dengan ai mock.
[DoD] AC-CRM-15 lulus.
````

## CRM-BE-11 — Files & Stage Docs, Dokumen Lead
````text
[KONTEKS] BE. crm + core(files). FR-CRM-20.

[TASK]
lead_documents (stage_id, doc_type, file_id, version, is_required, uploaded_by, tags, izin per dokumen mis. dokumen margin hanya Presales/Direktur). Daftar tipe dokumen per stage (tabel PRD FR-CRM-20) + status kelengkapan (Opsional/Disarankan/Wajib/Otomatis), pencarian, tag, versioning, hash integritas, unggah massal, tautan aman berkedaluwarsa untuk klien. Dokumen otomatis dari sistem (RAB export, TOP, quotation PDF, MoU draf). Konfigurasi blokir/peringatan dokumen wajib stage Deal. Integrasi PipelineTransitionGuard.

[OUTPUT TARGET] Endpoint /leads/{id}/documents, /documents/checklist; test.
[DoD] Deal diblokir/diperingatkan sesuai konfigurasi bila dokumen wajib belum ada.
````

## CRM-BE-12 — Legal Studio: 22 Pasal, Addendum, Log e-Sign/e-Meterai
````text
[KONTEKS] BE. crm. FR-CRM-21, BR-CRM-16/19, AC-CRM-16/17, Lampiran H & Open Question #16.

[TASK]
1. contract_clauses/clause_versions (clause_no 1–22, title, body, variables JSON, version, effective_from, edited_by Legal, alasan); seed 22 pasal Lampiran H dengan penanda "perlu review Legal"; permission `crm.clause.edit` hanya Legal/Super Admin.
2. Permintaan penyimpangan klausul (Sales/Presales) → approval Legal; flag is_modified pada quotation_clauses + audit.
3. Addendum/Change Request: addendums (number ADD/YYYY/MM/NNNN, parent_quotation_id, deal_id, reason, delta_rab_version_id, delta_value, new_top_scheme_id, dampak jadwal, status DRAFT→REVIEW_LEGAL→APPROVAL→SENT→SIGNED, signed_file_id). Dipicu dari PM Change Request (event `change_request.approved_by_client` → draf), revisi scope di Negotiation, atau manual. Setelah SIGNED: emit `addendum.signed` → nilai kontrak & scope PM terupdate, RAB tambahan masuk backlog, invoice tambahan dijadwalkan Finance; addendum signed tidak dapat diedit (BR-CRM-19). PDF Addendum + halaman tanda tangan.
4. esign_logs (document_type/id, signer, role, signed_at ISO-8601, ip, device, doc_hash SHA-256, cert_serial, emeterai_serial, emeterai_status, QR verifikasi) + view "Audit E-Sign Trail"; adapter PSrE (Privy/VIDA/Peruri) mock + tanda tangan gambar+OTP; berkas read-only.
5. Halaman verifikasi publik via QR (hash cocok / tidak).

[OUTPUT TARGET] Endpoint /clauses, /clause-versions, /addendums, /esign-logs, /verify/{hash}; test AC-CRM-16, AC-CRM-17.
[DoD] Sales tidak bisa menyimpan klausul (403); addendum signed mengubah kontrak & mengirim event.
````

## CRM-BE-13 — Kontrak, SLA, Renewal
````text
[KONTEKS] BE. crm. FR-CRM-14.

[TASK]
contracts (type PROJECT/SLA, start, end, value, scope, jam respons, biaya bulanan, renewal_alert_days, status), kontrak aktif otomatis dari Deal; kontrak maintenance/SLA terpisah; pengingat perpanjangan H-90/60/30 (scheduler + rule); "Quotation renewal" satu klik dari kontrak sebelumnya (salin RAB/TOP/klausul → versi baru); integrasi PM Service Desk (SLA terukur) & Finance (invoice recurring via event).

[OUTPUT TARGET] Endpoint /contracts, /contracts/{id}/renew; test.
[DoD] Renewal membuat quotation DRAFT dengan referensi kontrak lama.
````

## CRM-BE-14 — WhatsApp Client Care Inbox + AI Reply Modes (Backend)
````text
[KONTEKS] BE. crm + ai. FR-CRM-11, BR-WA-01..04, AC-CRM-09, risiko jalur WA (Open Q #3/#17).

[TASK]
1. Adapter WhatsApp: `WaGateway` interface; implementasi (a) Meta Cloud API resmi (webhook verifikasi signature, template message, jendela 24 jam BR-WA-01), (b) gateway pihak ketiga (WAHA/Twilio/360dialog) via config, (c) MockGateway. Webhook POST /api/v1/webhooks/whatsapp (HMAC, replay protection, dead-letter) → emit `wa.message_received`.
2. Tabel wa_conversations (phone, customer_id/lead_id, owner_id, ai_mode[off,suggest,auto_limited,after_hours], status, labels, last_message_at, sla_due_at), wa_messages (direction, type teks/gambar/dokumen/voice/lokasi/kontak, body, media_id, wa_msg_id, sent_by USER/AI/CLIENT, ai_confidence, delivered/read_at), wa_pins, canned_responses (template Meta + `/shortcut` + variabel; bawaan: follow-up penawaran, pengingat UAT, notifikasi invoice termin), wa_consents, wa_labels, broadcast_campaigns (segmen opt-in, rate limit, laporan delivery).
3. Pemetaan nomor→Lead/Customer/Kontak; nomor tak dikenal → "Buat Lead" (auto-capture); penugasan/transfer/catatan internal.
4. AI: konteks (profil customer, histori, status lead/proyek/invoice/tiket read-only, KB FAQ/layanan/SOP, tone brand); mode Suggest (default), Auto-reply terbatas (intent aman: FAQ, status proyek, jadwal, terima kasih; ambang keyakinan), After-hours auto + eskalasi besok; deteksi intent & sentimen (inquiry baru, minta harga, minta jadwal, bug/komplain→tiket PM, pembayaran, follow-up proposal, lainnya).
5. GUARDRAIL: AI tidak boleh diskon/harga final di luar RAB approved, janji tanggal/scope baru, bocorkan data internal/margin/data klien lain, setujui kontrak; hal sensitif (komplain, marah, hukum, sengketa bayar) → eskalasi + label "Perlu Sales"; BR-WA-02 consent; BR-WA-03 angka/harga/tanggal selalu Suggest kecuali sumber tervalidasi (status invoice); BR-WA-04 tombol "Ambil alih" menghentikan AI seketika; penanda pesan AI "dikirim oleh AI" + opsi disclosure; log keputusan AI (prompt, sumber konteks, skor).
6. Aksi cepat dari chat: buat lead, jadwalkan meeting, buat tiket service desk (event ke PM), kirim invoice/quotation, buat task; Auto-Log pesan ke tab Komunikasi + pin.
7. SLA respons (mis. 15 menit jam kerja), timer, eskalasi; opt-in/out (pesan "STOP" → Do Not Contact); broadcast terkontrol.
8. Realtime: SSE `WaMessageReceived` per percakapan.

[OUTPUT TARGET] Endpoint /wa/conversations, /wa/messages, /wa/canned, /wa/broadcasts, /webhooks/whatsapp; test AC-CRM-09 (status proyek→draf; diskon→"Perlu Sales"), BR-WA-01..04.
[DoD] Mock gateway mengirim/menerima pesan dua arah; guardrail lulus uji adversarial (prompt injection di pesan klien).
````

## CRM-BE-15 — Forecast, Target, Analytics, Win/Loss, Upsell, Campaign
````text
[KONTEKS] BE. crm. FR-CRM-15, menu 12–15, §6.2 menu 13–15, 17.

[TASK]
targets (per sales/tim/periode); Weighted Forecast = Σ(nilai×probabilitas); Win Rate = Won/(Won+Reject)×100%; Average Sales Cycle (hari New→Won); funnel per stage, velocity, average deal size, source ROI (CAC & ROAS bila data iklan dari Marketing tersedia via event ads.spent), loss reasons, kompetitor, aktivitas per sales, stale report, pipeline aging, forecast accuracy; Revenue Forecast AI (Weighted vs Unweighted per bulan/kuartal + narasi AI); Lead Source Attribution. Campaign & Segmentasi (segmen customer, broadcast WA/email opt-in). Upsell/Cross-sell & Account Plan (peluang lanjutan, rencana kunjungan, pemilik akun). Win/Loss Analytics. Laporan standar & custom (builder sederhana) + ekspor PDF/Excel/CSV + jadwal kirim email. Pakai read-model + job terjadwal.

[OUTPUT TARGET] Endpoint /reports/{type}, /forecast, /targets, /campaigns, /account-plans; test rumus.
[DoD] Rumus baku PRD terverifikasi dengan fixture.
````

## CRM-BE-16 — Rate Card, Katalog Modul, Template Library, Pengaturan CRM
````text
[KONTEKS] BE. crm. FR-CRM-16/18.

[TASK]
rate_cards (role, level Junior/Mid/Senior, cost_rate, sell_rate, valid_from/to, versi historis), module_catalog (name, category, default_mandays_by_role JSON, deskripsi; feedback realisasi dari PM), document_templates (Quotation, MoU, email, WA), pengaturan CRM: pipeline & probabilitas, sumber lead master, alasan lost, SLA follow-up, aturan assign (round-robin, berbasis sumber/industri/nilai), scoring rules, field kustom (definisi form builder), integrasi (WA, Google, email), kebijakan AI, tarif pajak, format penomoran, kop surat. Hak akses: Sales hanya baca tanpa margin; Presales/Super Admin ubah rate card.

[OUTPUT TARGET] Endpoint /rate-cards, /module-catalog, /templates, /settings/crm; test permission.
[DoD] RAB memakai rate card periode berlaku; perubahan tercatat versi.
````

## CRM-FE-01 — Dashboard CRM, Leads (Tabel & Form Input), Customers 360
````text
[KONTEKS] FE. frontend/src/features/crm. Prototipe: halaman Dashboard/Leads/Customers di docs/prototype* (lihat PROTOTYPE-MAP.md). PRD FR-CRM-01/13, AC-CRM-01/02.

[TASK]
1. /crm/dashboard: KPI (pipeline nilai/terbobot, win rate, lead baru, stale, forecast), funnel, aktivitas hari ini, grafik — tiru layout prototipe.
2. /crm/leads: DataTable (filter, saved views, bulk action, ekspor CSV, impor) dan form input lead (slide-over/modal sesuai prototipe) dengan toggle Existing/New Customer, semua field FR-CRM-01, validasi Zod (WA +62, NPWP 16 digit), CurrencyInput, modal duplikat "Tautkan / Buat baru / Gabung", draft offline.
3. /crm/customers & /crm/customers/:id (360°): tab Profil, Legal & Pajak, Atribusi, Kontak (matriks stakeholder), Riwayat, Keuangan (DSO, piutang), Kesehatan (health score), Komunikasi, Account Plan; merge duplikat; /crm/contacts.
4. Pencarian global & quick-add `+`.
5. Hook TanStack Query + tipe dari OpenAPI; 4 state UI; permission guard.

[OUTPUT TARGET] Halaman + komponen + test (Vitest form, Playwright AC-CRM-01/02).
[DoD] Tampilan setara prototipe; AC-CRM-01/02 lulus E2E.
````

## CRM-FE-02 — Sales Pipeline: Kanban, Tabel, Visual Workflow Tracker
````text
[KONTEKS] FE. features/crm/pipeline. FR-CRM-02, FR-CRM-24, AC-CRM-11/12/13. Prototipe: halaman Sales Pipeline.

[TASK]
Toggle Kanban (default) ⇄ Tabel ⇄ Visual Workflow Tracker (progres linear + days_in_stage), preferensi per user. Kartu: kode lead, nama proyek/perusahaan, nilai, avatar PIC, umur, ikon stale, probabilitas, label sumber, aktivitas berikutnya, ikon WA belum dibalas. Header kolom: jumlah, total, terbobot. dnd-kit + optimistic update + rollback + toast alasan + tombol langsung ke tab penyebab (mis. "Lengkapi TOP") menggunakan guard_failure.deep_link_tab. Drop ke Reject → modal alasan wajib (tombol disabled sampai kategori + catatan ≥10 karakter); drop ke Deal → modal konfirmasi + ringkasan handover. Filter/saved views, swimlane (Sales/Sumber/Industri), virtualized 1.000 kartu, render awal <1,2 s, scroll horizontal di mobile, stale badge merah, SSE update.

[OUTPUT TARGET] /crm/pipeline (+ state), komponen Kanban/Table/WorkflowTracker; test Playwright untuk AC-CRM-11/12/13.
[DoD] Pesan toast/modal persis sesuai PRD.
````

## CRM-FE-03 — Detail Lead: Layout, Overview, Activity, Requirement
````text
[KONTEKS] FE. /crm/pipeline/:id/{overview|activity|requirement|…}. FR-CRM-03/04/05. Prototipe: detail lead.

[TASK]
Layout slide-over/full page 70/30 (konten tab kiri, sidebar sticky kanan: PIC, stage, nilai, next action, watchers). Header (kode, dropdown ubah stage, PIC, nilai, probabilitas, lead score, tombol cepat WA/Email/Jadwalkan/Buat Quotation, indikator SLA). 16 tab sesuai PRD (rute lengkap). Implementasikan di task ini: Overview (profil, stepper riwayat stage, nilai RAB, margin jika izin, next action, AI Ringkas Lead, AI Presales Assistant, Next Best Action, checklist aktivitas wajib per stage, KPI summary), Activity (timeline + kalender mini + form meeting + opsi "Buat Google Meet" + outcome + AI Meeting Notes), Requirement (prompt + upload multi-file [drag-drop, progres, 25 MB], parameter opsional, tombol Generate, status "AI sedang menyusun…" via SSE, riwayat versi, "Gunakan sebagai RAB / Gabungkan", regenerate dengan feedback, generate per bagian). Sisa tab diisi task FE berikutnya (placeholder berlabel).

[OUTPUT TARGET] Komponen + rute; test Playwright AC-CRM-03 (mock BE AI).
[DoD] Tab berpindah tanpa reload; deep link per tab bekerja.
````

## CRM-FE-04 — Tab RAB (Spreadsheet Grid, Panel Ringkasan, Dual-View)
````text
[KONTEKS] FE. features/crm/rab. FR-CRM-06, AC-CRM-04/07, wireframe PRD §15.5.

[TASK]
SpreadsheetGrid editable (keyboard nav, paste dari Excel, drag reorder, grup kategori collapsible, undo/redo, auto-save draft + indikator, optimistic locking 409 handler); kolom: No, Kategori, Item/Modul (+deskripsi), Role, Qty (0,5), Satuan, Harga Satuan (default rate card, override), Sub Total, HPP Internal (hanya Internal View), Catatan, Sumber (badge AI/Manual/Template yang berubah ke Manual saat diedit). Baris Optional/Gratis/Recurring; tambah/hapus/duplikasi; impor Template Katalog & Excel; ekspor Excel; riwayat versi + diff. Panel kanan sticky real-time: Subtotal, Kontingensi (alasan wajib bila <5%), Diskon %/Rp (>batas → ajukan approval), DPP, PPN, TOTAL INVESTASI, Terbilang. Simulasi margin + indikator warna; toggle Client/Internal View (disembunyikan bila tanpa `crm.margin.view`). Derivasi durasi; validasi simpan; toolbar [AI Generate ▼][+Item][Impor][Template][Undo][Redo] + versi.

[OUTPUT TARGET] Halaman tab RAB + test kalkulasi klien == server (fixture bersama).
[DoD] Kalkulasi ulang <100 ms; tidak ada field margin di DOM/network untuk user tanpa izin.
````

## CRM-FE-05 — Tab TOP, Quotation Editor (Split A4), Persetujuan
````text
[KONTEKS] FE. features/crm/{top,quotation,approvals}. FR-CRM-07/08/17, AC-CRM-05/06.

[TASK]
Tab TOP: pilih preset (30/40/30, 50/50, 20/30/30/20, custom), tabel termin editable (nama, % ↔ nominal, trigger, syarat, jatuh tempo, milestone PM), terbilang per termin & total, progress 100% + banner "Sisa alokasi belum diatur", simpan final terkunci saat tidak balance, panel pajak (PPN/PPh23), status kunci Finance + permintaan perubahan. Tab Quotation: tombol "Generate Quotation & MoU", split-screen editor (TipTap) ↔ preview A4 live (Pendahuluan, RAB & Scope, MoU, TTD dapat disusun, indikator jumlah halaman, watermark DRAFT), AI draft pendahuluan, pemilih klausul (Clause Library, varian, ubah urutan, ajukan penyimpangan), daftar versi R1..Rn + bandingkan, status dropdown + tombol cepat (Tandai Terkirim, Klien Setuju→Deal, Klien Menolak→Reject), kirim email/WA (+OTP opsional), lacak buka/unduh/durasi, upload Signed Copy. Tab Persetujuan: ApprovalTimeline (diskon/quotation/klausul). Antrian /crm/approvals.

[OUTPUT TARGET] Halaman + test Playwright AC-CRM-05/06.
[DoD] PDF hasil = urutan baku dan nomor halaman kontinu.
````

## CRM-FE-06 — Tab Komunikasi, Kontak, Dokumen, Tugas, Kompetitor, Riwayat, Handover, Voice Notes
````text
[KONTEKS] FE. features/crm/lead-detail tabs 7–16. FR-CRM-03/19/20/22/23.

[TASK]
Implementasikan tab 7 Komunikasi (thread WA dua arah, email, catatan panggilan, AI suggest reply), 8 Kontak & Stakeholder (peran + peta relasi), 9 Dokumen/15 Files & Stage Docs (dokumen per stage, status kelengkapan, versioning, tag, preview, tautan aman), 10 Tugas & Catatan (mention @user), 11 Kompetitor & Risiko, 12 Persetujuan, 13 Riwayat & Audit (diff field), 14 Handover (tracker PM/Finance/HRD, tautan proyek, tombol Retry Handover, readiness check, kickoff gate), 16 Voice Notes & Dokumen AI: perekam browser (MediaRecorder: timer, jeda/lanjut, izin mikrofon + checkbox consent wajib), unggah, status transkripsi SSE, tampilan Executive Summary / Core Needs / Technical Scope / Action Items (editable), tombol Sinkronkan → Activities & Kirim ke Requirement.

[OUTPUT TARGET] Semua tab 1–16 selesai (tidak ada placeholder tersisa).
[DoD] AC-CRM-15 E2E lulus (rekam/unggah 30 menit mock).
````

## CRM-FE-07 — WhatsApp Client Care Inbox
````text
[KONTEKS] FE. /crm/wa. FR-CRM-11, AC-CRM-09. Prototipe: halaman WhatsApp.

[TASK]
Inbox terpadu mode tiga-panel (desktop) / satu-panel (mobile): daftar percakapan + filter (belum dibaca, milik saya, tanpa PIC, menunggu klien, AI aktif), pencarian, label; ChatThread (media, voice note + transkrip, lokasi, kontak); panel kanan konteks lead/customer/proyek/invoice; penugasan/transfer/catatan internal; template & `/shortcut`; AI Reply Mode switch (Off/Suggest/Auto-limited/After-hours) + draf AI dengan skor keyakinan + tombol "Ambil alih"; penanda "dikirim oleh AI"; label "Perlu Sales"; aksi cepat (buat lead, meeting, tiket, kirim invoice/quotation, task); pin ke timeline; SLA timer; consent/opt-out; broadcast terkontrol (pilih segmen, preview template, laporan delivery); SSE realtime.

[OUTPUT TARGET] Halaman + test.
[DoD] Pesan baru muncul realtime; guardrail AI tampak di UI.
````

## CRM-FE-08 — Legal Studio, Contracts & Renewals, Transition Hub, Kalender AR
````text
[KONTEKS] FE. /crm/contracts, /crm/legal, /crm/transition, /crm/ar-calendar. FR-CRM-14/21/22.

[TASK]
Contracts & Renewals (daftar kontrak aktif/SLA, tombol renewal 1-klik, pengingat), Legal Studio (editor 22 klausul: read-only untuk Sales/Presales, edit + versi + diff untuk Legal; permintaan penyimpangan; Generator Addendum: draf → review Legal → approval → kirim → tanda tangan; Audit E-Sign Trail; verifikasi QR), Project Transition Hub (stepper provisioning, umur transisi, PM, Retry Handover), Kalender Penagihan Termin (kalender+tabel, status Draft/Sent/Paid/Overdue, umur piutang, aksi kirim pengingat WA, buka invoice).

[OUTPUT TARGET] Halaman + test AC-CRM-16/17.
[DoD] Hak akses UI selaras RBAC §3.4.
````

## CRM-FE-09 — Forecast, Analytics, Rate Card, Template, Campaign, Upsell, Pengaturan, Voice Notes lintas lead
````text
[KONTEKS] FE. /crm/{forecast,reports,rate-cards,templates,campaigns,account-plans,win-loss,settings,voice-notes}. FR-CRM-15/16/18/19.

[TASK]
Forecast & Target (target per sales/tim, Weighted vs Unweighted, narasi AI), Win/Loss Analytics, Lead Source Attribution, Laporan standar/custom + ekspor + jadwal email, Rate Card & Katalog Modul (versi historis), Template Library, Campaign & Segmentasi, Upsell/Cross-sell & Account Plan, Pengaturan CRM (pipeline, sumber, alasan lost, SLA, assign rules, scoring, field kustom [drag form builder], integrasi, kebijakan AI, pajak, penomoran, kop surat), Voice Notes lintas lead, Approvals queue.

[OUTPUT TARGET] Seluruh 21 menu CRM (PRD §6.2) tersambung ke data nyata.
[DoD] Tidak ada menu CRM yang masih placeholder.
````

---

# BAGIAN D — MODUL 3: HRD & WORKLOAD
> Service: `backend/services/hrd` (8004, DB `astacode_hrd`) · FE `features/hrd`. PRD §8 (FR-HRD-01..12, BR-HRD-01..07, AC-HRD-01..04).

## HRD-BE-01 — Master Pegawai, Kontrak, Struktur Upah, Dokumen & Surat
````text
[KONTEKS] BE. hrd. FR-HRD-01/11, BR-HRD-05.
[TASK] employees (status Tetap/Kontrak/Freelance/PKL, jabatan, divisi, role teknis PM/BE/FE/Fullstack/QA/UIUX/DevOps, skill, atasan, rekening(encrypted), kontak darurat), employment_contracts, salary_structures (gaji pokok, tunjangan, hourly rate freelance; field masking BR-HRD-05), hr_documents + template kontrak kerja/surat peringatan/surat keterangan dengan penomoran otomatis, akses data gaji hanya HR/Finance/Direktur/pemilik + log akses. Hubungkan ke user core (user_id).
[OUTPUT TARGET] Endpoint CRUD + test masking & log akses. [DoD] Sales tidak dapat melihat gaji (403).
````

## HRD-BE-02 — Presensi, Daily Log, Night Workload
````text
[KONTEKS] BE. hrd. FR-HRD-02/03/04, BR-HRD-01/02/04, AC-HRD-01/02.
[TASK]
1. Presensi: check-in <10 dtk; Onsite validasi BSSID WiFi/IP statis; WFH GPS + geofence ≤50 m (atau lokasi terdaftar) dengan consent eksplisit; koordinat TIDAK disimpan permanen setelah verifikasi (BR-HRD-01) — simpan hanya hasil verifikasi; check-out; koreksi dengan approval; shift & toleransi telat; WFH tanpa consent ditolak (AC-HRD-01).
2. Daily Log Progress-Driven: ringkasan progres, ≥1 referensi task aktif (dari PM via API) atau URL commit/PR valid (validasi GitHub), jam kerja riil → emit `timelog.recorded` ke PM/Finance; ditolak tanpa referensi (AC-HRD-02); AI ringkas daily log.
3. Night Workload Engine: kerja >22:00 → toggle klaim; Manajer menyetujui Auto-Carryover (masuk siang H+1 tanpa potongan telat) atau Insentif Lembur (nominal ke payroll) via approval engine.
4. Freelancer hanya mencatat jam pada task proyek yang ditugaskan (BR-HRD-07).
[OUTPUT TARGET] Endpoint + test AC-HRD-01/02. [DoD] Event timelog.recorded terkirim via outbox.
````

## HRD-BE-03 — Cuti, Kapasitas & Alokasi, Capacity Demand
````text
[KONTEKS] BE. hrd. FR-HRD-05/06, AC-HRD-03, event deal.won/project.created.
[TASK] leave_types/balances/requests (kuota, approval berjenjang, kalender tim); kapasitas 40 jam/minggu dikurangi cuti/libur; allocations (jam teralokasi vs kapasitas), indikator Normal ≤80% / Hampir Penuh 81–100% / Overload >100%; terima Capacity Demand dari saga (mandays per role) dan rekomendasikan engineer (skill, beban, biaya); peringatan merah + saran alternatif saat alokasi >100% (AC-HRD-03); API kapasitas untuk CRM (informatif) & PM; event alert utilisasi >100% ke rule engine.
[OUTPUT TARGET] Endpoint /leave, /capacity, /allocations, /capacity-demands, /internal/events/deal.won. [DoD] AC-HRD-03 lulus.
````

## HRD-BE-04 — Rekrutmen (ATS), PKL/Magang
````text
[KONTEKS] BE. hrd. FR-HRD-07/08, BR-HRD-03.
[TASK] ATS: job_openings, portal karir publik, applicants + AI Resume Parser (skill, pengalaman, skor kecocokan via ai-service), stages Applied→Screening→Test→Interview→Offered→Hired, jadwal interview (Google Calendar), offering letter PDF. PKL: portal publik pengajuan sekolah/kampus, kalender kuota per divisi (tolak otomatis jika penuh dengan surat penolakan sopan otomatis), surat penerimaan bernomor, log harian pembimbing, evaluasi akhir, e-Sertifikat PDF ber-QR (opsi tampil/sembunyi transkrip). Endpoint publik dengan rate limit + captcha hook.
[OUTPUT TARGET] Endpoint + PDF + test. [DoD] Kuota penuh → penolakan otomatis.
````

## HRD-BE-05 — Multi-Model Payroll, Penilaian Kinerja, Laporan
````text
[KONTEKS] BE. hrd + finance. FR-HRD-09/10/12, BR-HRD-05/06, AC-HRD-04.
[TASK] payroll_runs/items/payslips; Tetap: gaji pokok + tunjangan + insentif malam + lembur − BPJS − PPh 21 − potongan; Freelance: Σ(jam riil × hourly rate) atau per milestone; PKL: uang saku. Alur Draft HR → Verifikasi Finance → Approval Direktur → Final Release; segregation of duties; tabel tarif BPJS & PPh 21 (TER/progresif) di Pengaturan (bukan hard-code); slip PDF terenkripsi (password/e-mail); payroll Final terkunci, koreksi via run penyesuaian (BR-HRD-06); emit `payroll.approved`. Penilaian kinerja: KPI/OKR per role, 360° ringan, skor dari data riil. Laporan: rekap kehadiran, keterlambatan, lembur, utilisasi, turnover, biaya SDM per proyek, headcount.
[OUTPUT TARGET] Endpoint + PDF slip + test AC-HRD-04. [DoD] Final release → event payroll.approved dengan totals benar.
````

## HRD-FE-01 — Seluruh Menu HRD
````text
[KONTEKS] FE. features/hrd. §8.2 peta menu. Tidak ada prototipe HRD khusus → gunakan design tokens & komponen CRM; konsisten dengan prototipe.
[TASK] Dashboard HR; Data Karyawan/Mitra (profil, upah bermasking); Presensi (tombol check-in 1-klik, deteksi WiFi/GPS + dialog consent, koreksi); Daily Log (form dengan referensi task/commit, jam); Workload Malam (klaim, approval); Cuti & Izin (kalender tim); Kapasitas & Alokasi (matriks utilisasi berwarna, drag alokasi, peringatan overload); Rekrutmen (Kanban ATS, parser CV, jadwal interview); PKL/Magang (kalender kuota, surat, sertifikat); Payroll Engine (stepper Draft→Verifikasi→Approval→Final, slip); Penilaian Kinerja; Dokumen & Surat; Laporan; Pengaturan (tarif BPJS/PPh21, shift, libur). Portal publik karir & PKL.
[OUTPUT TARGET] Semua menu §8.2. [DoD] AC-HRD-01..04 lulus E2E.
````

---

# BAGIAN E — MODUL 4: PROJECT MANAGEMENT
> Service: `backend/services/pm` (8005, DB `astacode_pm`) · FE `features/pm`. PRD §9 (FR-PM-01..17, BR-PM-01..07, AC-PM-01..04).

## PM-BE-01 — Proyek dari Deal Won, Epics/Tasks dari WBS, Intake 3 Langkah
````text
[KONTEKS] BE. pm. FR-PM-01, BR-PM-01, AC-PM-01/02, saga langkah 2.
[TASK] projects (code PRJ-NNN, customer_id, deal_id, contract_value, budget_mandays, top_scheme snapshot, status Provisioned/Active/…, health), project_members, epics, tasks (key PRJ-123, subtask, link), consumer `deal.won` idempotent (deal_id+pm): buat Project + Epics + Tasks backlog dari WBS RAB + tech stack + TOP scheme. Intake 3 langkah API: (1) Penerimaan Aplikasi & Spesifikasi, (2) Pembagian modul ke developer (matriks kapasitas 5+ peran, jam & role) + "Terbitkan ke Backlog Kanban", (3) Setting TOP & permintaan invoice ke Finance (PM = trigger/permintaan; penerbitan resmi di Finance). Proyek manual hanya Direktur/PM dengan alasan. Emit `project.created`. Kickoff Gate: aktivasi sprint/time logging menunggu `invoice.paid` DP (mode konfigurasi).
[OUTPUT TARGET] Endpoint + test idempotensi & AC-PM-01/02. [DoD] Event ganda tidak membuat proyek ganda.
````

## PM-BE-02 — Board Kanban, Backlog & Sprint, Roadmap, Daftar Pekerjaan, Task Detail
````text
[KONTEKS] BE. pm. FR-PM-03..07.
[TASK] Board 7 kolom (Backlog, To Do, In Progress, Code Review, In QA, Done, Released) + WIP limit + swimlane + filter; transisi tervalidasi (Done butuh PR merged & QA pass bila diaktifkan); epics, story points, sprint (Start/Complete, carry-over, burndown/burnup mandays, velocity); roadmap/Gantt (dependensi FS/SS, milestone, critical path sederhana); Daftar Pekerjaan dengan query ala JQL (parser aman: `status = "In QA" AND assignee = me`), saved filter, ekspor CSV; Task detail (deskripsi, Acceptance Criteria, RCA AI, subtask checklist, aktivitas, komentar, estimasi, prioritas Low/Med/High/Urgent, severity, SLA, SP, sprint, label). Automation rules PM (auto-assign, auto-label, eskalasi SLA, notifikasi — FR-PM-16).
[OUTPUT TARGET] Endpoint + test JQL parser & transisi. [DoD] Query JQL berbahaya ditolak (tidak ada injection).
````

## PM-BE-03 — QA, Docs & Wiki, Time Tracking, Budget & Margin, Milestone/UAT
````text
[KONTEKS] BE. pm. FR-PM-08..11, BR-PM-02/07, AC-PM-04.
[TASK] test_cases/test_runs/bug blocking; UAT sign-off → `milestone.approved` (termin_no, milestone) ke Finance ≤1 menit; wiki_pages berversi; time_entries (manual + dari `timelog.recorded` HRD), alert over-budget mandays 80% & 100%, ≥100% perlu persetujuan PM/Direktur (BR-PM-07); budget mandays vs realisasi, HPP langsung dari Finance, margin real-time & early warning <30%; feedback realisasi mandays ke Module Catalog (event ke crm/ai); Management Team (matriks kapasitas, utilisasi, skill sinkron HRD, FR-PM-15).
[OUTPUT TARGET] Endpoint + test AC-PM-04. [DoD] Approve UAT → event milestone.approved terkirim.
````

## PM-BE-04 — Service Desk, SLA, Telegram, GitHub, AI Fix Bug, Change Request
````text
[KONTEKS] BE. pm + ai. FR-PM-12..14, FR-PM-17, BR-PM-03..06, AC-PM-03.
[TASK]
1. Service Desk: tiket TICKET-NNNN dari bot Telegram 4 kanal (Umum, PM, Backend, Frontend), form portal, WhatsApp (event dari CRM); SLA countdown respons & resolusi per kontrak garansi (jam kontrak, kalender kerja & libur — BR-PM-05); Client Restricted View (tanpa catatan internal); UAT approval klien; `ticket.created` → AI diagnostik.
2. GitHub: GitHub App (disarankan)/OAuth/PAT, webhook dua arah, konvensi branch (`main` protected, `staging`, `develop`, `epic/`, `feat/`, `fix/`), syarat ≥1 approval + CI lulus, tautkan commit/PR ke task via key PRJ-123.
3. AI Fix Bug: AI Diagnostic Engine baca log/stack trace/screenshot → RCA → severity → usulan patch → buat branch `fix/<key>-<desc>` + draft PR; GUARDRAIL: dilarang merge `main`/deploy production, wajib approval Senior Dev/Tech Lead, sandbox/branch, secret disaring dari prompt, audit lengkap (BR-PM-03/04); status "Menunggu Review" (AC-PM-03).
4. Change Request (FR-PM-17, BR-PM-06): permintaan di luar scope → estimasi → persetujuan klien → draf Addendum di CRM + invoice tambahan Finance; scope baru tidak boleh langsung masuk sprint.
[OUTPUT TARGET] Endpoint + adapter Telegram/GitHub (mock) + test AC-PM-03. [DoD] Tidak ada kode yang melakukan merge otomatis (uji statis).
````

## PM-FE-01 — Portofolio, Deals Won Intake, Workspace Proyek (10 sub-tab)
````text
[KONTEKS] FE. features/pm. §9.2/9.3. Gunakan design tokens prototipe; konsisten dengan Kanban CRM.
[TASK] Portofolio Proyek (Card Grid / Tabel), Deals Won Intake 3 langkah (navigasi back 3→2→1→Katalog, matriks kapasitas 5+ peran, tombol "🚀 Terbitkan ke Backlog Kanban", setting TOP & kirim permintaan invoice), Workspace Proyek 10 sub-tab: Ringkasan, Board Kanban (WIP, swimlane, drag validasi), Backlog & Sprint (burndown/burnup), Roadmap/Gantt, Daftar Pekerjaan (editor query JQL), QA & Testing (UAT sign-off), Docs & Wiki (TipTap), Time Tracking, Finance & Budget (margin bar), Tiket. Task Detail Modal 70/30. Otomasi Fix Bug AI (halaman review draft PR + RCA), GitHub & Cabang, Service Desk/Portal, Management Team (kapasitas), Pengaturan.
[OUTPUT TARGET] Semua menu §9.2. [DoD] AC-PM-01..04 E2E.
````

---

# BAGIAN F — MODUL 5: FINANCE & CASHFLOW
> Service: `backend/services/finance` (8006, DB `astacode_finance`) · FE `features/finance`. PRD §10 (FR-FIN-01..15, BR-FIN-01..09, AC-FIN-01..05), Lampiran A.

## FIN-BE-01 — COA, Periode, Posting Rules, Jurnal Umum Otomatis
````text
[KONTEKS] BE. finance. FR-FIN-09/10, BR-FIN-01/02/04/06/08/09, §10.5, DDL §11.3.
[TASK] fin_coa (23 akun Lampiran A, hierarki, normal balance, nonaktifkan bukan hapus bila ada transaksi), fin_periods (lock periode), fin_journal_entries/items (DECIMAL(18,2), CHECK debit=kredit, dimensi project_id/customer_id, status POSTED/REVERSED, source AR/AP/HR/OPEX/TRF/MANUAL), `JournalService::post()` dalam DB transaction (Σdebit=Σkredit atau rollback), jurnal pembalik (tidak edit/hapus posted), jurnal manual terbatas dengan approval + SoD, fin_posting_rules (konfigurasi pola jurnal §10.5 tanpa coding, basis akrual), Trial Balance.
[OUTPUT TARGET] Endpoint + test AC-FIN-04, E2E-07. [DoD] Posting tidak seimbang ditolak; periode tutup menolak posting.
````

## FIN-BE-02 — Invoicing & Termin (AR), Pembayaran, Pajak, Reminder
````text
[KONTEKS] BE. finance. FR-FIN-01, BR-FIN-05, §10.4/10.5, AC-FIN-01/05.
[TASK] fin_receivable_schedules dari `deal.won`; invoice (status DRAFT→READY→ISSUED→PARTIAL→PAID/OVERDUE/VOID), aktivasi dari `milestone.approved`/tanggal/signing atau override Finance Manager + alasan; kalkulator pajak (DPP, PPN konfigurabel hanya PKP, PPh 23 2% jika klien pemotong); verifikasi NPWP sebelum invoice DP; PDF invoice + kirim email/WA; "Tandai Lunas" (tanggal, rekening, bukti transfer), pembayaran sebagian, credit note, bukti potong PPh 23; jurnal AKRUAL dua tahap (terbit: D1030/K4010+K2020; DP: K2040 lalu pengakuan; bayar: D1010+D1040/K1030); reminder H-3/H+1/H+7/H+14 via rule engine; emit `invoice.issued` & `invoice.paid` (memicu Kickoff Gate DP); Idempotency-Key.
[OUTPUT TARGET] Endpoint + test AC-FIN-01 (kas bersih 109 jt). [DoD] Contoh angka PRD §10.5 persis.
````

## FIN-BE-03 — AP: Klaim Struk OCR, Vendor, SPK Freelancer, Payroll Posting, OPEX
````text
[KONTEKS] BE. finance + ai. FR-FIN-02..06, BR-FIN-03/07, AC-FIN-03.
[TASK] Klaim struk AI OCR (ai-service; vendor, tanggal, nominal, confidence; verifikasi Finance; deteksi duplikat hash gambar + vendor+tanggal+nominal; kas kecil/transfer); tagihan vendor Cloud & SaaS (5030 vs 6030, widget jatuh tempo); SPK Freelancer (nomor, proyek, role, total fee, milestone, verifikasi deliverable PM → invoice freelancer → bayar → D5020; total bayar ≤ SPK + addendum; PPh 21/23 parameter); payroll posting dari `payroll.approved` (6010, 5020, 2030, 2050); biaya operasional HQ recurring + approval; `ads.spent` → 6050; proyek wajib project_id untuk HPP (BR-FIN-03); direct labor dari `timelog.recorded` → 5040.
[OUTPUT TARGET] Endpoint + test. [DoD] AC-FIN-03: bayar SPK menurunkan margin proyek real-time.
````

## FIN-BE-04 — Rekening, Mutasi Internal, Laporan (P&L, Per Proyek, Portofolio, Arus Kas, Dashboard)
````text
[KONTEKS] BE. finance. FR-FIN-07/08/11..15, BR-FIN-04, AC-FIN-02.
[TASK] fin_bank_accounts (saldo dari jurnal), mutasi internal non-P&L (D1020/K1010, tag TRF), Consolidated P&L (pendapatan, HPP, laba kotor, OPEX, EBITDA; periode & perbandingan), P&L per Proyek (nilai kontrak, termin terbayar, serapan HPP freelance+cloud+direct labor, margin Rp/%, early warning), Customer Portfolio (kolektibilitas, DSO, Top Paying Client, Upcoming Inflow 14 hari, aging 0–30/31–60/61–90/>90), Arus Kas aktual + proyeksi, Executive Dashboard (arus kas 3/6/12 bln, donut biaya, saldo, piutang/utang jatuh tempo, runway, margin, tren). Read-model + cache; ekspor PDF/Excel.
[OUTPUT TARGET] Endpoint /finance/reports/*; test AC-FIN-02 (mutasi tidak ubah laba/EBITDA). [DoD] Trial balance seimbang.
````

## FIN-FE-01 — Seluruh Menu Finance
````text
[KONTEKS] FE. features/finance. §10.2.
[TASK] Executive Dashboard; Invoicing & Termin (daftar, modal Edit Termin, kalkulator pajak, terbitkan, Tandai Lunas, partial, credit note); Klaim Struk (drag-drop OCR + verifikasi); Vendor Cloud & SaaS; SPK Freelancer; Payroll (verifikasi/posting); Biaya Operasional; Rekening Bank & Kas; Mutasi Internal; COA; Jurnal Umum (filter sumber, pembalik, jurnal manual+approval); Laporan: Consolidated P&L, P&L per Project (Visual Margin Bar, tfoot total), Customer Portfolio, Arus Kas, Trial Balance; Pengaturan (pajak, penomoran, periode lock). Peran Finance AR Specialist: verifikasi NPWP, kunci TOP.
[OUTPUT TARGET] Semua menu §10.2. [DoD] AC-FIN-01..05 E2E.
````

---

# BAGIAN G — MODUL 2: MARKETING & AI CONTENT ENGINE
> Service: `backend/services/marketing` (8003, DB `astacode_marketing`) · FE `features/marketing`. PRD §7 (FR-MKT-01..12, BR-MKT-01..06, AC-MKT-01..03).

## MKT-BE-01 — Riset Tren, Sprint Planner, AI Content Studio, Brand Kit, Approval
````text
[KONTEKS] BE. marketing + ai. FR-MKT-01..05, BR-MKT-01/03/05.
[TASK] Penarik tren terjadwal (GitHub Trending, HN, Dev.to, Reddit via API resmi/RSS) + pengelompokan topik AI + skor relevansi; pilar konten (40/30/15/10/5 konfigurabel) & matriks 7 hari; AI Content Studio: Carousel IG 5–10 slide (1080×1350, watermark logo/palet brand kit), Video 45–60 dtk (hook 3 dtk, teleprompter, arahan visual), Blog 800–1.500 kata + snippet + meta SEO (skor SEO ≥80 sebelum publish); prompt registry berversi; cek plagiarisme/faktual ringan + disclaimer; Preview data; Approval Queue (Approve, Edit&Approve, Regenerate with Feedback, Reject; berjenjang Creator→Marketing Lead).
[OUTPUT TARGET] Endpoint + test AC-MKT-01. [DoD] Konten tanpa Approved tidak bisa dijadwalkan (BR-MKT-02).
````

## MKT-BE-02 — Scheduler, Distribusi, Omnichannel Inbox, Listening, Analytics, Ads, Atribusi
````text
[KONTEKS] BE. marketing. FR-MKT-06..12, BR-MKT-02/04/06, AC-MKT-02/03.
[TASK] Editorial Calendar & Smart Scheduler (heatmap jam tayang, antrean, retry); Distribution Hub (Instagram Graph, TikTok Content Posting, LinkedIn, Blog webhook; token OAuth terenkripsi + monitor kedaluwarsa; API resmi saja); Omnichannel Inbox (DM IG, komentar, form web, email) + auto-reply FAQ + AI Intent Detection → "Transfer to CRM" (emit lead.captured dengan sumber/campaign/kontak; wajib Nama + WA/Email + consent); Social Listening & Kompetitor + ringkasan mingguan AI; Analytics (reach, saves, shares, engagement=(likes+comments+saves+shares)/reach, 48 jam) + Learning Loop (prompt versi baru, rollback); Campaign & Ads Budget (emit `ads.spent`); Atribusi lead→deal (CPL, CAC, ROAS).
[OUTPUT TARGET] Endpoint + adapter mock sosmed + test AC-MKT-02/03. [DoD] Komentar harga → inquiry → lead terbuat.
````

## MKT-FE-01 — Seluruh Menu Marketing
````text
[KONTEKS] FE. features/marketing. §7.2.
[TASK] Dashboard; Riset Tren; 7-Day Sprint Planner; AI Content Studio + Preview Studio (carousel swipe, teleprompter bertimer, pratinjau SERP, syntax highlight); Approval Queue split-screen + inline quick-edit; Editorial Calendar drag-drop; Distribution Hub (koneksi akun, status token); Omnichannel Inbox + Transfer to CRM; Social Listening; Performance Analytics; Brand Kit; Campaign & Ads Budget; Pengaturan.
[OUTPUT TARGET] Semua menu §7.2. [DoD] AC-MKT-01..03 E2E.
````

---

# BAGIAN H — LINTAS MODUL

## X-01 — Portal Klien (FE + BE)
````text
[KONTEKS] FE+BE. frontend/src/portal-client + guard `client`. PRD §3.3, FR-PM-13, Open Q #9.
[TASK] Login klien (guard client, data dibatasi customer_id), dashboard proyek (status, milestone, progres tanpa catatan internal), tiket (buat/lacak, SLA), persetujuan UAT, invoice milik sendiri (unduh, status, bukti bayar), dokumen (quotation/MoU/BAST/addendum + e-sign), persetujuan Change Request, notifikasi. Pengujian isolasi data antar klien.
[OUTPUT TARGET] Portal berfungsi. [DoD] Klien tidak dapat mengakses data klien lain/internal (test negatif).
````

## X-02 — Admin: Pengguna, Role, Integrasi, Audit, Automation Rules, Approval Config, Queue Monitor
````text
[KONTEKS] FE+BE. /admin. PRD §5.4/5.5, §17.
[TASK] UI manajemen user/role/permission, impersonate (audit), integrasi (WA, Google, GitHub, Telegram, sosmed, LLM, email, PSrE) status & token, Audit Log viewer (filter, akses data sensitif), Automation Rule Builder no-code (Trigger→Condition→Action, uji dry-run), Approval Flow Config, Webhook log, Queue Monitor (jobs/failed), event log & saga viewer, health semua service.
[OUTPUT TARGET] Halaman admin lengkap. [DoD] Hanya Super Admin.
````

## X-03 — Laporan Konsolidasi & Executive Dashboard Lintas Modul
````text
[KONTEKS] BE+FE. orchestrator read-model + /dashboard. PRD §18.
[TASK] Read-model (revenue, margin, pipeline, kas, utilisasi, backlog tiket, CAC/CPL/ROAS, Lead-to-Cash Cycle Time, SLA compliance, estimasi vs realisasi) diperbarui via event + job terjadwal; ekspor PDF/Excel/CSV; jadwal kirim email; dashboard Direktur & dashboard per peran.
[OUTPUT TARGET] Endpoint + dashboard. [DoD] Angka dashboard = angka laporan modul asal (test rekonsiliasi).
````

## X-04 — Realtime (SSE), Pencarian Global, Notifikasi Lintas Modul
````text
[KONTEKS] BE+FE. gateway/core. A.4.
[TASK] Endpoint SSE terautentikasi di gateway (channel privat: lead.{id}, board.{id}, wa.{conversationId}, user.{id}) + fallback polling + antarmuka siap-Reverb; SearchService lintas service (lead, customer, tiket, dokumen, karyawan) + hasil dikelompokkan per modul di command palette; notification center terpadu.
[OUTPUT TARGET] Berfungsi di FE. [DoD] Pesan WA & update Kanban realtime.
````

## X-05 — Test E2E Lintas Modul (E2E-01..08)
````text
[KONTEKS] BE+FE. Playwright + Pest. PRD §21.
[TASK] Implementasikan E2E-01 Lead→Deal, E2E-02 Milestone→Cash, E2E-03 Biaya Proyek, E2E-04 Payroll, E2E-05 Marketing→CRM, E2E-06 Bug→Fix, E2E-07 Integritas, E2E-08 Keamanan sebagai skenario otomatis terhadap seluruh service (seed demo).
[OUTPUT TARGET] Suite e2e + laporan. [DoD] Seluruh E2E hijau di staging.
````

---

# BAGIAN I — FITUR TAMBAHAN PENAMBAL GAP (skala industri software house)
> Tidak ada di PRD v1.1; ditambahkan agar sistem setara software house profesional. Tiap task bersifat opsional tetapi direkomendasikan; kerjakan setelah Bagian C–H stabil. Catat keputusan di PROGRESS.

| ID | Fitur | Alasan gap |
|---|---|---|
| GAP-01 | Resource Forecasting & Bench Management | Prediksi idle/overload 8–12 minggu ke depan dari pipeline berbobot |
| GAP-02 | Timesheet Approval & Billable/Non-billable | T&M, retainer, dan akurasi biaya |
| GAP-03 | Retainer & T&M Billing (selain fixed-price) | Model bisnis software house beragam |
| GAP-04 | Risk Register & Issue Log proyek | Manajemen risiko formal |
| GAP-05 | Release/Deployment Tracker & Environment Registry (dev/staging/prod, versi, changelog) | Governance rilis |
| GAP-06 | Client Health & NPS/CSAT Survey otomatis pasca-milestone | Retensi & upsell |
| GAP-07 | Knowledge Base internal + Estimasi Historis (post-mortem/lesson learned) | Kalibrasi lintas proyek |
| GAP-08 | Vendor/Subkon Scorecard & Onboarding Freelancer (NDA, e-sign) | Kualitas subkon |
| GAP-09 | Revenue Recognition Schedule (POC) & WIP Report | Akuntansi proyek akurat |
| GAP-10 | Budget vs Actual per Divisi (ringan) & Cash Forecast skenario | Kontrol keuangan (selaras tetap meniadakan budgeting terpisah: sifatnya laporan, bukan modul) |
| GAP-11 | SSO enterprise (SAML/OIDC), IP allowlist, session policy | Keamanan korporat |
| GAP-12 | Data Retention & Legal Hold, DSAR (UU PDP) tool | Kepatuhan |
| GAP-13 | API publik berkunci + Webhook marketplace untuk integrasi klien | Ekosistem |
| GAP-14 | Global Search + Saved Dashboards + Custom Report Builder | Analitik mandiri |
| GAP-15 | Mobile PWA offline (presensi, daily log, approval) | Lapangan |
| GAP-16 | Template Proyek (playbook kickoff, checklist security, DoD) | Standarisasi delivery |
| GAP-17 | Competency Matrix & Learning Plan | Pengembangan SDM |
| GAP-18 | Okr/Goals perusahaan terhubung KPI modul | Selaras strategi |

## GAP-BE/FE — Implementasi Gap (satu task per ID)
````text
[KONTEKS] BE+FE. Service sesuai fitur (GAP-01,02,04,05,16 → pm/hrd; GAP-03,09,10 → finance; GAP-06 → crm/pm; GAP-07,14,17,18 → lintas; GAP-08 → finance+hrd; GAP-11,12,13 → core+gateway; GAP-15 → frontend).
[TASK] Pilih SATU ID dari tabel BAGIAN I (sebut ID di awal), baca rancangan singkatnya, tulis spesifikasi 1 halaman (docs/gap/GAP-xx.md: tujuan, entitas, endpoint, event, UI, aturan bisnis, AC), setujui dengan pengguna bila ada perubahan skema besar, lalu implementasikan BE + FE + test mengikuti aturan emas. Gunakan komponen & design tokens yang sudah ada.
[OUTPUT TARGET] docs/gap/GAP-xx.md + kode + test + entri PROGRESS. [DoD] Tidak merusak AC modul yang sudah lulus (regresi hijau).
````

---

# BAGIAN J — HARDENING, TESTING, DEPLOYMENT (tanpa Docker)

## J-01 — Keamanan & Kepatuhan (OWASP, UU PDP)
````text
[KONTEKS] BE+FE. PRD §17.
[TASK] Audit OWASP Top 10 per service (CSRF, XSS, SQLi, SSRF pada fetch URL, upload, CSP, secure cookies, rate limit, dependency scan/Dependabot, SAST/DAST di CI), enkripsi kolom sensitif (NPWP, rekening, gaji, token integrasi), 2FA wajib peran sensitif, log akses data sensitif, prompt-injection defense & isolasi RAG per customer, UU PDP (consent, hak subjek data, retensi, notifikasi insiden ≤3×24 jam), data lokasi WFH tidak permanen, rotasi secret, checklist pentest. Dokumen docs/SECURITY.md.
[OUTPUT TARGET] Perbaikan + docs/SECURITY.md + test keamanan. [DoD] Tidak ada temuan High terbuka.
````

## J-02 — Performa, Observabilitas, Backup
````text
[KONTEKS] BE+FE. PRD §16.
[TASK] Target: P95 baca <300 ms/tulis <500 ms, TTI <3 s, Kanban 1.000 kartu, PDF ≤15 s, AI RAB ≤60 s, indeks komposit (stage+owner, project_id+status, dll.), N+1 audit, cache; logging terstruktur + correlation id, Sentry (opsional), metrik & alert (queue lag, error rate, kegagalan webhook); skrip backup `mysqldump` harian + binlog (bila akses), retensi 30 hari, uji restore bulanan, `backend/scripts/backup.sh` + `restore-test.sh`.
[OUTPUT TARGET] Laporan benchmark + skrip. [DoD] Target performa tercapai pada data seed besar.
````

## J-03 — Deployment di Server SSH tanpa Docker
````text
[KONTEKS] BE+FE. Server Linux dengan MySQL 8 sudah ada; akses SSH.
[TASK] Tulis docs/DEPLOY.md + skrip: (1) prasyarat PHP 8.3, Composer, Node, Nginx (cek versi; JANGAN instal MySQL), (2) buat DB & user via sql/create-databases.sql, (3) konfigurasi Nginx (satu vhost untuk frontend statis + reverse proxy `/api` ke gateway; service internal hanya bind 127.0.0.1), (4) systemd unit/Supervisor untuk setiap service + queue worker + scheduler (`schedule:work`/cron), (5) build frontend `npm run build` → rsync ke webroot, (6) deploy zero-downtime (symlink releases, migrate expand/contract), (7) `.env` produksi + secret, (8) HTTPS (certbot opsional), (9) rollback, (10) GitHub Actions CI/CD via SSH untuk staging & production.
[OUTPUT TARGET] docs/DEPLOY.md, deploy/*.service, nginx.conf, deploy.sh, rollback.sh. [DoD] Satu perintah deploy sukses di staging.
````

## J-04 — UAT, Pelatihan, Migrasi Data
````text
[KONTEKS] BE+FE. PRD §19.
[TASK] Skrip ETL & validasi migrasi (customer, kontrak aktif, COA, saldo awal) dengan dry-run & laporan selisih; checklist UAT per modul; SOP per peran (docs/SOP/*.md), panduan parallel-run Finance 1–2 bulan; feature flag per modul.
[OUTPUT TARGET] Skrip ETL + dokumen SOP/UAT. [DoD] Dry-run migrasi tanpa selisih kritis.
````

---

# BAGIAN K — PROMPT UTILITAS

## U1 — Lanjutkan Task
````text
lanjutkan task <TASK-ID>

Instruksi untuk agen: (1) Baca docs/MASTER_PROMPT.md (BAGIAN A + blok task <TASK-ID>), (2) baca docs/PROGRESS.md, (3) periksa kode yang sudah ada untuk setiap item OUTPUT TARGET (file, endpoint, rute, test) dan jalankan test terkait, (4) kerjakan HANYA yang belum selesai atau salah, jangan menulis ulang yang benar, (5) perbarui checklist PROGRESS (centang akurat; tulis alasan bila BLOCKED), (6) commit, (7) ringkas: selesai apa, sisa apa.
````

## U2 — Audit Kepatuhan PRD per Task
````text
audit task <TASK-ID>
Bandingkan implementasi dengan PRD (FR/BR/AC terkait di blok task) dan prototipe. Hasilkan tabel: ID → status (Sesuai/Parsial/Belum/Menyimpang) → bukti (path:baris) → perbaikan. Perbaiki semua yang Parsial/Belum/Menyimpang kecuali yang butuh keputusan pengguna.
````

## U3 — Sinkron Tampilan dengan Prototipe
````text
sinkronkan UI <HALAMAN/RUTE> dengan prototipe
Buka file prototipe terkait (lihat docs/PROTOTYPE-MAP.md), bandingkan layout, warna, tipografi, label, urutan elemen, interaksi; perbaiki komponen React hingga setara; sertakan screenshot sebelum/sesudah (Playwright) di docs/screens/.
````

## U4 — Perbaiki Error / Test Gagal
````text
perbaiki error <PESAN/LOG>
Reproduksi, temukan akar masalah (bukan menutup gejala), perbaiki, tambah test regresi, jalankan seluruh test service terkait, catat di PROGRESS.
````

## U5 — Ringkas Konteks (bila sesi terlalu panjang)
````text
ringkas konteks
Tulis ringkasan ≤ 40 baris ke docs/PROGRESS.md bagian "Handoff": keputusan arsitektur, konvensi yang dipakai, task selesai/berjalan, utang teknis, perintah penting. Lalu hentikan sesi agar saya memulai sesi baru dengan "lanjutkan task <ID>".
````

## U6 — Tambah Fitur Baru di Luar PRD (aman)
````text
tambah fitur: <deskripsi>
Analisis dampak ke modul/service/event/RBAC, tulis spesifikasi di docs/gap/<slug>.md (tujuan, entitas, endpoint, event, UI, aturan bisnis, AC), minta konfirmasi bila mengubah skema inti, implementasikan BE+FE+test sesuai aturan emas, perbarui EVENT-CATALOG & OpenAPI & PROGRESS.
````

---

# LAMPIRAN — MATRIKS KETERLACAKAN PRD → TASK

| ID PRD | Task |
|---|---|
| FR-CRM-01 | CRM-BE-01, CRM-BE-02, CRM-FE-01 |
| FR-CRM-02 | CRM-BE-03, CRM-FE-02 |
| FR-CRM-03 | CRM-FE-03, CRM-FE-06 |
| FR-CRM-04 | CRM-BE-04, CRM-FE-03 |
| FR-CRM-05 | CRM-BE-05, CRM-FE-03 |
| FR-CRM-06 | CRM-BE-06, CRM-FE-04 |
| FR-CRM-07 | CRM-BE-07, CRM-FE-05 |
| FR-CRM-08 | CRM-BE-08, CRM-FE-05 |
| FR-CRM-09 | CRM-BE-09, CRM-FE-06 |
| FR-CRM-10 | CRM-BE-09, CRM-FE-02 |
| FR-CRM-11 | CRM-BE-14, CRM-FE-07 |
| FR-CRM-12 | CRM-BE-02 |
| FR-CRM-13 | CRM-BE-01, CRM-FE-01 |
| FR-CRM-14 | CRM-BE-13, CRM-FE-08 |
| FR-CRM-15 | CRM-BE-15, CRM-FE-09 |
| FR-CRM-16 | CRM-BE-16, CRM-FE-09 |
| FR-CRM-17 | CRM-BE-06, CRM-FE-05 |
| FR-CRM-18 | CRM-BE-16, CRM-FE-09 |
| FR-CRM-19 | CRM-BE-10, CRM-FE-06, CRM-FE-09 |
| FR-CRM-20 | CRM-BE-11, CRM-FE-06 |
| FR-CRM-21 | CRM-BE-08, CRM-BE-12, CRM-FE-08 |
| FR-CRM-22 | CRM-BE-09, CRM-FE-08 |
| FR-CRM-23 | CRM-BE-09, CRM-FE-06 |
| FR-CRM-24 | CRM-BE-03, CRM-FE-02 |
| FR-MKT-01..05 | MKT-BE-01, MKT-FE-01 |
| FR-MKT-06..12 | MKT-BE-02, MKT-FE-01 |
| FR-HRD-01, 11 | HRD-BE-01, HRD-FE-01 |
| FR-HRD-02..04 | HRD-BE-02, HRD-FE-01 |
| FR-HRD-05, 06 | HRD-BE-03, HRD-FE-01 |
| FR-HRD-07, 08 | HRD-BE-04, HRD-FE-01 |
| FR-HRD-09, 10, 12 | HRD-BE-05, HRD-FE-01 |
| FR-PM-01 | PM-BE-01, PM-FE-01 |
| FR-PM-03..07, 16 | PM-BE-02, PM-FE-01 |
| FR-PM-08..11, 15 | PM-BE-03, PM-FE-01 |
| FR-PM-12..14, 17 | PM-BE-04, PM-FE-01 |
| FR-FIN-09, 10 | FIN-BE-01, FIN-FE-01 |
| FR-FIN-01 | FIN-BE-02, FIN-FE-01 |
| FR-FIN-02..06 | FIN-BE-03, FIN-FE-01 |
| FR-FIN-07, 08, 11..15 | FIN-BE-04, FIN-FE-01 |
| §3 RBAC, §5.5–5.7, §17 | T02, J-01 |
| §5 Orkestrasi | T03 |
| §13 AI Layer | T04 |
| §15 UI/UX | T05, T07 |
| §16 NFR | J-02 |
| §21 E2E | X-05 |
| Portal klien | X-01 |
| BR-* | Diuji pada task pemilik (lihat blok) |

---
*— Akhir MASTER PROMPT —*