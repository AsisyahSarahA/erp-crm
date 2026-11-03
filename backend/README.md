# ASTACODE ERP — MONOREPO BACKEND (MICROSERVICES)

Platform Backend ASTACODE ERP Orchestrator mengadopsi arsitektur **Monorepo Microservice** berbasis **Laravel 11/12 (PHP 8.3+)** dan **MySQL 8**. Seluruh sistem dirancang berjalan secara langsung pada server Linux/SSH **tanpa Docker dan tanpa instalasi server tambahan**.

---

## 1. Topologi Layanan & Alokasi Port

| Service | Direktori | Port Dev | Database Terisolasi | Tanggung Jawab Utama |
|---|---|:---:|---|---|
| **API Gateway** | `gateway/` | **8000** | *(Stateless / Memory)* | Satu pintu masuk bagi React, verifikasi sesi, inject JWT internal, routing proxy |
| **Core Service** | `services/core/` | **8001** | `astacode_core` | Auth, RBAC Spatie, Audit Log, File Storage, Numbering, Settings, Webhook |
| **CRM Service** | `services/crm/` | **8002** | `astacode_crm` | Leads, Pipeline, RAB Spreadsheet, TOP, Quotation+MoU PDF, Customer 360 |
| **Marketing Service** | `services/marketing/` | **8003** | `astacode_marketing` | Content Studio, Trend Hunter, Kalender Editorial, Ads, Omnichannel Inbox |
| **HRD Service** | `services/hrd/` | **8004** | `astacode_hrd` | Pegawai, Presensi Geofence, Daily Log, Night Workload, ATS, Multi-model Payroll |
| **PM Service** | `services/pm/` | **8005** | `astacode_pm` | Workspace Proyek, Kanban 7 Kolom, Backlog, Time Tracking, QA UAT, SLA Service Desk |
| **Finance Service** | `services/finance/` | **8006** | `astacode_finance` | Chart of Accounts (23 Akun), Jurnal Akrual Otomatis, Invoicing AR, AP, Laba Rugi |
| **AI Gateway Service**| `services/ai/` | **8007** | `astacode_ai` | Provider Abstraction (Gemini/OpenAI/Claude), Prompt Registry, Guardrails |
| **Orchestrator** | `services/orchestrator/`| **8008** | `astacode_orchestrator` | Outbox Event Bus, Saga Deal-Won, Rule Engine, Read-Model Aggregation |
| **Shared Kernel** | `shared/astacode-kernel` | — | *(Composer Path Repo)* | Money, Terbilang, HMAC Signer, Outbox Trait, Idempotency, Enums, RFC 7807 |

---

## 2. Aturan Keras Microservice (Architectural Guardrails)

1. **Database per Service:** Setiap service hanya boleh mengakses database miliknya sendiri. **Dilarang keras melakukan SQL JOIN lintas database**. Relasi lintas batas modul hanya berupa ID referensi (`customer_id`, `project_id`, `deal_id`) ditambah *snapshot* nama yang diperlukan.
2. **Komunikasi Sinkron:** Pemanggilan query/command REST internal wajib menandatangani header HMAC menggunakan `InternalRequestSigner` (`X-Service-Signature`, `X-Service-Timestamp`, `X-Service-Token`, `X-Correlation-Id`).
3. **Komunikasi Asinkron:** Perubahan data yang memicu modul lain wajib menggunakan **Transactional Outbox Pattern** (`HasOutbox` trait) yang direlay ke `orchestrator-service`.
4. **Keuangan & Presisi Angka:** Seluruh perhitungan uang menggunakan kelas `Money` (`bcmath`, 2 angka desimal). Teks penulisan nilai Rupiah resmi menggunakan `Terbilang::rupiah()`.
5. **Penyaringan Data Sensitif:** Field gaji, HPP, margin kotor, dan rekening bank wajib difilter di sisi server menggunakan `FieldMasker` sebelum dikirimkan ke Gateway / UI.

---

## 3. Panduan Menjalankan di Server SSH (Tanpa Docker)

### 3.1 Prasyarat Server
- PHP 8.3+ (ekstensi: `bcmath`, `curl`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`)
- Composer 2.x
- MySQL 8 (sudah berjalan di server SSH lokal atau remote)

### 3.2 Inisialisasi Database
Jalankan skrip SQL idempoten untuk membuat seluruh 8 database terisolasi:
```bash
mysql -u root -p < backend/scripts/sql/create-databases.sql
```

### 3.3 Mengaktifkan Port Forwarding Tunnel (Bila Database Remote)
```bash
SSH_USER=asisyahs SSH_HOST=127.0.0.1 ./backend/scripts/tunnel.sh
```

### 3.4 Menjalankan Seluruh Service (Development)
Skrip `dev-up.sh` akan menjalankan API Gateway dan ke-8 microservice di background:
```bash
./backend/scripts/dev-up.sh
```
Pemeriksaan status kesehatan seluruh layanan:
```bash
./backend/scripts/status.sh
```
Menghentikan seluruh layanan:
```bash
./backend/scripts/dev-down.sh
```

### 3.5 Migrasi & Seeding Seluruh Database
```bash
./backend/scripts/migrate-all.sh
./backend/scripts/seed-all.sh
```

### 3.6 Menjalankan Test Suite (Kernel & Services)
```bash
./backend/scripts/test-all.sh
```

---

## 4. Penggunaan Paket Bersama (`astacode/astacode-kernel`)

### 4.1 Perhitungan Moneter Aman (`Money`)
```php
use Astacode\Kernel\Support\Money;

$subtotal = Money::of('92966940');
$ppn = $subtotal->percentage(11); // 11% PPN
$grandTotal = $subtotal->add($ppn);

echo $grandTotal->formatRupiah(); // "Rp103.193.303"
echo $grandTotal->toTerbilang();  // "Seratus Tiga Juta Seratus Sembilan Puluh Tiga Ribu Tiga Ratus Tiga Rupiah"
```

### 4.2 Teks Terbilang Resmi Indonesia (`Terbilang`)
```php
use Astacode\Kernel\Support\Terbilang;

echo Terbilang::rupiah('Rp92.966.940');
// Output: "Sembilan Puluh Dua Juta Sembilan Ratus Enam Puluh Enam Ribu Sembilan Ratus Empat Puluh Rupiah"
```

### 4.3 Menandatangani & Memverifikasi Request Internal (`InternalRequestSigner`)
```php
use Astacode\Kernel\Security\InternalRequestSigner;

// Pengirim (mis. Gateway atau PM Service)
$headers = InternalRequestSigner::sign('gateway', $jsonPayload);
$response = Http::withHeaders($headers)->post('http://127.0.0.1:8002/api/leads', $payload);

// Penerima (Service internal dilindungi middleware VerifyServiceToken)
Route::middleware('service.token')->group(function () {
    Route::post('/internal/events', [EventController::class, 'handle']);
});
```

### 4.4 Merekam Event ke Transactional Outbox (`HasOutbox`)
```php
use Astacode\Kernel\Outbox\HasOutbox;

class DealService {
    use HasOutbox;

    public function markAsWon(Deal $deal) {
        DB::transaction(function () use ($deal) {
            $deal->update(['stage_id' => 'deal']);
            
            $this->recordEvent(
                eventName: 'deal.won',
                payload: ['deal_id' => $deal->id, 'contract_value' => $deal->value],
                aggregateType: 'deal',
                aggregateId: $deal->id
            );
        });
    }
}
```

---

## 5. Cara Menambah Service Baru

1. Buat folder di `backend/services/<nama-service>` mengacu pada template `backend/services/core`.
2. Buat database baru `astacode_<nama-service>` di MySQL.
3. Tetapkan port unik dev (mis. 8009) dan cantumkan di `.env`.
4. Daftarkan mapping URL di `backend/gateway/config/services_map.php`.
5. Tambahkan service ke daftar array di `backend/scripts/dev-up.sh` dan `backend/scripts/status.sh`.
