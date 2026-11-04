# KONVENSI API REST ASTACODE ERP ORCHESTRATOR
Versi: 1.0.0
Standar: OpenAPI 3.1.0 · RFC 7807 (Problem Details) · JSON:API Pragmatis

Dokumen ini adalah spesifikasi resmi arsitektur kontrak REST API yang diekspos melalui Gateway (port dev 8000) menuju frontend React dan integrasi eksternal.

---

## 1. Topologi URL & Routing Gateway

Frontend **hanya** berkomunikasi dengan gateway:
```
http://<host>:8000/api/v1/{service}/{resource}
```

Contoh rute gateway:
| URL Panggilan FE | Service Tujuan | Port Internal | Rute Internal Service |
|---|---|---|---|
| `/api/v1/core/auth/login` | `core-service` | 8001 | `/api/auth/login` |
| `/api/v1/crm/leads` | `crm-service` | 8002 | `/api/crm/leads` |
| `/api/v1/marketing/campaigns` | `marketing-service` | 8003 | `/api/marketing/campaigns` |
| `/api/v1/hrd/employees` | `hrd-service` | 8004 | `/api/hrd/employees` |
| `/api/v1/pm/projects` | `pm-service` | 8005 | `/api/pm/projects` |
| `/api/v1/finance/invoices` | `finance-service` | 8006 | `/api/finance/invoices` |
| `/api/v1/ai/run` | `ai-service` | 8007 | `/api/internal/ai/run` |
| `/api/v1/orchestrator/events` | `orchestrator-service` | 8008 | `/api/internal/events` |

---

## 2. Struktur Envelope Respons Standar

### 2.1 Respons Sukses (200 OK / 201 Created)
```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 68
  }
}
```

### 2.2 Respons Error (RFC 7807 Problem Details)
Header: `Content-Type: application/problem+json`
```json
{
  "type": "https://astacode.com/errors/validation-failed",
  "title": "Unprocessable Entity",
  "status": 422,
  "detail": "Data formulir tidak memenuhi kriteria validasi bisnis.",
  "instance": "/api/v1/crm/leads",
  "correlation_id": "a9b8c7d6-e5f4-4321-abcd-0123456789ab",
  "errors": {
    "title": ["Judul proyek wajib diisi minimal 2 karakter."],
    "est_value": ["Nilai estimasi proyek harus lebih besar dari 0."]
  }
}
```

---

## 3. Konvensi Query String

### 3.1 Paginasi (Pagination)
Sistem mendukung **Page-based Pagination** (default untuk tabel admin) dan **Cursor-based Pagination** (untuk timeline, chat WhatsApp, dan audit log volume tinggi).

- **Page-based:**
  - `page=1` (dimulai dari 1)
  - `per_page=15` (default 15, maksimum 100)
  - Respons menyertakan `meta.current_page`, `meta.last_page`, `meta.per_page`, `meta.total`.
- **Cursor-based:**
  - `cursor=eyJpZCI6MTB9`
  - `limit=20`
  - Respons menyertakan `meta.next_cursor`, `meta.has_more`.

### 3.2 Penyaringan (Filtering)
Format: `filter[<field>]=<value>` atau operator eksplisit:
- **Kesamaan langsung:**
  - `/api/v1/crm/leads?filter[stage]=NEW_LEAD`
  - `/api/v1/crm/leads?filter[priority]=HOT`
- **Rentang nilai (Range):**
  - `/api/v1/finance/invoices?filter[issue_date][gte]=2026-01-01&filter[issue_date][lte]=2026-03-31`
- **Pencarian teks bebas (Search):**
  - Parameter `search=<query>` (mis. `search=Megah+Nusantara`) mencari di kolom nama, kode, kontak.

### 3.3 Pengurutan (Sorting)
Format: `sort=<field>` (ascending) atau `sort=-<field>` (descending):
- Tunggal: `sort=-created_at` (terbaru dulu)
- Ganda (bertingkat): `sort=-priority,target_closing` (prioritas tertinggi dulu, lalu target closing terdekat)

### 3.4 Inklusi Relasi (Eager Loading / Include)
Format: `include=relation1,relation2`
Menghindari N+1 query pada frontend:
- `/api/v1/crm/leads?include=customer,contacts,quotations`
- `/api/v1/pm/projects?include=milestones,tasks`
- `/api/v1/finance/invoices?include=lines,journal_entry`

### 3.5 Pemilihan Kolom (Sparse Fieldsets)
Format: `fields[<entity>]=col1,col2`
Mengurangi beban payload data mobile/low-bandwidth:
- `/api/v1/crm/customers?fields[customer]=id,legal_name,phone,status`

---

## 4. Header Wajib & Keamanan

| Header | Wajib | Keterangan |
|---|---|---|
| `Accept: application/json` | Ya | Memastikan respons berupa JSON |
| `Authorization: Bearer <token>` | Ya (Kecuali Login/Public) | Token Sanctum API / JWT sesi |
| `X-Correlation-Id` | Otomatis | UUID pelacak request end-to-end lintas log |
| `Idempotency-Key` | Ya (pada POST mutasi kritis) | Mencegah duplikasi order, win deal, invoice termin |

---

## 5. Kontrak CI & Garansi Perubahan (DoD T09)
Perubahan endpoint pada controller backend **wajib** selaras dengan `docs/API-CONTRACTS/gateway.yaml`. Skrip automated contract testing dijalankan di CI (`npm run test:contract` atau `./backend/scripts/contract-test.sh`). Jika rute backend tidak terdokumentasi di OpenAPI atau skema bertentangan, pipeline CI otomatis **GAGAL**.

