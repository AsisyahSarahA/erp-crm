# ASTACODE ERP — DESIGN TOKENS & UI SPECIFICATION

Dokumen ini mendefinisikan seluruh design token yang diekstrak secara presisi dari prototipe CRM Vanilla JS (`docs/prototype crm/css/styles.css` dan komponen visual di `docs/prototype crm/js/components/`). Seluruh implementasi frontend React 18 + Tailwind CSS di `frontend/` **wajib mengacu pada spesifikasi ini** guna memastikan konsistensi visual 100%.

---

## 1. Tipografi (Typography)

| Token | Nilai CSS | Tailwind Utility / Class | Penggunaan |
|---|---|---|---|
| `--font-sans` | `'Plus Jakarta Sans', system-ui, -apple-system, sans-serif` | `font-sans` | Teks utama UI, tombol, label formulir, heading, badge |
| `--font-mono` | `'JetBrains Mono', 'Fira Code', monospace` | `font-mono` | Kode referensi dokumen (`LD-*`, `QT-*`, `INV-*`), angka nominal/keuangan, hash, koordinat |

### Skala Tipografi
- **Display / H1:** `24px` (`text-2xl`), `font-bold` (700), `line-height: 1.25`
- **Section / H2:** `18px` (`text-lg`), `font-bold` (700) / `font-semibold` (600), `line-height: 1.3`
- **Subheader / H3:** `15px` (`text-base`), `font-semibold` (600), `line-height: 1.4`
- **Body Regular:** `14px` (`text-sm`), `font-normal` (400), `line-height: 1.5` (Ukuran standar aplikasi)
- **Body Medium / Caption:** `13px` (`text-[13px]`), `font-medium` (500), `line-height: 1.4` (Tabel padat, sub-label)
- **Small / Badges / Pills:** `11px` - `12px` (`text-xs`), `font-semibold` (600), uppercase/normal tracking

---

## 2. Palet Warna (Color Palette)

### 2.1 Slate Grayscale (Neutrals)
| Token CSS | Nilai HEX | Tailwind | Peran di Mode Terang | Peran di Mode Gelap |
|---|---|---|---|---|
| `--slate-50` | `#f8fafc` | `slate-50` | Background utama layar (`--bg-primary`) | Hover item pada panel gelap |
| `--slate-100` | `#f1f5f9` | `slate-100` | Background sekunder / card zebra / input | Border halus |
| `--slate-200` | `#e2e8f0` | `slate-200` | Border standar (`--border`), divider | Border input non-aktif |
| `--slate-300` | `#cbd5e1` | `slate-300` | Border hover, placeholder teks sekunder | Text disabled |
| `--slate-400` | `#94a3b8` | `slate-400` | Icon non-aktif, teks muted (`--text-muted`) | Teks sekunder |
| `--slate-500` | `#64748b` | `slate-500` | Teks sekunder (`--text-secondary`), label | Teks muted |
| `--slate-600` | `#475569` | `slate-600` | Subtitle, button ghost hover | Border sekunder |
| `--slate-700` | `#334155` | `slate-700` | Card border gelap, tag outline | Border card mode gelap |
| `--slate-800` | `#1e293b` | `slate-800` | Teks primer mode terang (`--text-primary`) | Background card (`--bg-card`) |
| `--slate-900` | `#0f172a` | `slate-900` | Background sidebar default | Background sekunder gelap |
| `--slate-950` | `#020617` | `slate-950` | Background footer / ultra dark | Background kanvas gelap (`--bg-primary`) |

### 2.2 Brand & Aksen Utama (Brand Indigo & Violet)
| Token CSS | Nilai HEX | Tailwind | Penggunaan |
|---|---|---|---|
| `--indigo-400` | `#818cf8` | `indigo-400` | Highlight di mode gelap, link hover |
| `--indigo-500` | `#6366f1` | `indigo-500` | Aksen tombol aktif, active border, tab indicator |
| `--indigo-600` | `#4f46e5` | `indigo-600` | Tombol CTA utama (Primary Button), header badge brand |
| `--indigo-700` | `#4338ca` | `indigo-700` | Hover state Primary Button |
| `--violet-500` | `#8b5cf6` | `violet-500` | Aksen AI Engine, gradient branding, Stage Follow Up |
| `--violet-600` | `#7c3aed` | `violet-600` | Gradient stop, tombol aksi sekunder beraksen |

### 2.3 Status & Pipeline Colors
| Status / Domain | HEX Utama | HEX Light (Bg) | HEX Dark (Bg) | CSS Variables | Tahapan Pipeline CRM / Modul |
|---|---|---|---|---|---|
| **New Lead (Blue)** | `#3b82f6` | `#eff6ff` (`blue-50`) | `rgba(30,58,138,0.3)` | `--blue-500`, `--blue-400` | Stage 1 (New Lead 10%), Tiket Open, Informasi |
| **Follow Up (Purple)**| `#8b5cf6` | `#f5f3ff` (`purple-50`)| `rgba(88,28,135,0.3)` | `--purple-500` | Stage 2 (Follow Up 20%), Penjadwalan |
| **Requirement (Cyan)**| `#06b6d4` | `#ecfeff` (`cyan-50`) | `rgba(22,78,99,0.3)` | `--cyan-500` | Stage 3 (Requirement 35%), Tech Spec, KAK |
| **Quotation (Amber)** | `#f59e0b` | `#fffbeb` (`amber-50`)| `rgba(120,53,15,0.3)` | `--amber-500`, `--amber-400` | Stage 4 (Quotation 55%), Pending Approval, Peringatan |
| **Negotiation (Pink)**| `#ec4899` | `#fdf2f8` (`pink-50`) | `rgba(131,24,67,0.3)` | `--pink-500` | Stage 5 (Negosiasi 75%), Addendum, Diskon |
| **Deal Won (Emerald)**| `#10b981` | `#ecfdf5` (`emerald-50`)| `rgba(6,78,59,0.3)` | `--emerald-500`, `--emerald-600` | Stage 6 (Deal Won 100%), Lunas, Approved, Active |
| **Reject/Lost (Red)** | `#ef4444` | `#fff1f2` (`rose-50`) | `rgba(136,19,55,0.3)` | `--red-500`, `--rose-500` | Stage 7 (Reject 0%), Overdue, Error, Batalkan |
| **Orange (Ad Spend)** | `#f97316` | `#fff7ed` (`orange-50`)| `rgba(124,45,18,0.3)` | `--orange-500` | Campaign Ads, Tagihan Cloud, Urgensi Tinggi |
| **Teal (Client Care)** | `#14b8a6` | `#f0fdfa` (`teal-50`) | `rgba(19,78,74,0.3)` | `--teal-500` | WhatsApp Verified, SLA Running |

---

## 3. Variabel Semantik (Semantic Variables)

```css
/* Mode Terang (Default) */
:root {
  --bg-primary:   #f8fafc;
  --bg-secondary: #f1f5f9;
  --bg-card:      #ffffff;
  --bg-sidebar:   #0f172a;
  --border:       #e2e8f0;
  --border-light: #f1f5f9;
  --text-primary:   #1e293b;
  --text-secondary: #64748b;
  --text-muted:     #94a3b8;
  --text-inverse:   #f8fafc;
}

/* Mode Gelap (.dark) */
.dark {
  --bg-primary:   #020617;
  --bg-secondary: #0f172a;
  --bg-card:      #1e293b;
  --bg-sidebar:   #020617;
  --border:       #1e293b;
  --border-light: #334155;
  --text-primary:   #f1f5f9;
  --text-secondary: #94a3b8;
  --text-muted:     #64748b;
  --text-inverse:   #0f172a;
}
```

---

## 4. Spacing, Corner Radius & Elevation (Shadows)

### 4.1 Radius Sudut (Corner Radius)
- `--radius-sm`: `6px` (`rounded-md`) — Tombol kecil, badge status, chip filter, tooltip
- `--radius-md`: `8px` (`rounded-lg`) — Tombol standar, input form, select box, card item list
- `--radius-lg`: `12px` (`rounded-xl`) — Container card, Kanban column, sub-panel, table card
- `--radius-xl`: `16px` (`rounded-2xl`) — Modal dialog, slide-over panel, overview banner
- `--radius-2xl`: `20px` (`rounded-3xl`) — Large hero card, summary dashboard card
- `--radius-full`: `9999px` (`rounded-full`) — Avatar profil, switch toggle, indicator dot, progress bar

### 4.2 Elevation & Shadows
- `--shadow-sm`: `0 1px 2px rgba(0, 0, 0, 0.05)` (mode terang) / `rgba(0,0,0,0.3)` (mode gelap)
- `--shadow-md`: `0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)` — Kanban card default
- `--shadow-lg`: `0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)` — Kanban card dragging, dropdown menu
- `--shadow-xl`: `0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)` — Modal dialog, command palette

### 4.3 Transisi & Animasi
- `--transition`: `150ms cubic-bezier(0.4, 0, 0.2, 1)` — Hover tombol, link, badge
- `--transition-md`: `250ms cubic-bezier(0.4, 0, 0.2, 1)` — Modal in/out, slide-over drawer, accordion collapse

---

## 5. Pola Desain Komponen UI (Component Patterns)

### 5.1 Pipeline Kanban Board
- **Lebar Kolom:** `min-width: 290px`, `max-width: 320px`, `flex-shrink: 0`, jarak antar kolom `12px` (`gap-3`).
- **Header Kolom:** Memuat indikator warna stage (lingkaran 8px), nama stage, badge jumlah lead (`pill px-2 py-0.5 text-xs font-semibold`), dan sub-total nilai (IDR) dalam kolom.
- **Kartu Kanban (`KanbanCard`):**
  - Background `var(--bg-card)`, border `1px solid var(--border)`, radius `var(--radius-lg)` (12px).
  - Drag handle di kiri atas atau hover card.
  - Header kartu: Kode lead (`font-mono text-xs text-indigo-600 font-semibold`), badge sumber lead (dengan ikon), priority dot (Urgent=Merah, High=Orange, Medium=Kuning, Low=Biru).
  - Body kartu: Judul proyek (maksimal 2 baris, `font-semibold text-sm`), Nama Customer Legal (`text-xs text-slate-500`).
  - Footer kartu: Estimasi nilai (IDR) tebal (`font-bold text-slate-800 dark:text-slate-100`), avatar Sales PIC, indikator waktu / SLA follow-up.
  - Hover state: `transform translateY(-2px)`, `box-shadow: var(--shadow-md)`, border warna stage.

### 5.2 Tabel Data Padat (Dense Data Table)
- **Container:** Border `1px solid var(--border)`, radius `12px`, overflow hidden.
- **Header (`<thead>`):** Background `var(--bg-secondary)`, tinggi `40px`, teks `text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider`, padding `px-4 py-2.5`.
- **Baris (`<tbody> <tr>`):** Tinggi `52px` (dense) atau `60px` (standard), border-bottom `1px solid var(--border-light)`.
- **Zebra & Hover:** Baris ganjil transparan, hover background `rgba(99, 102, 241, 0.04)` (tint indigo halus).
- **Sticky Column:** Kolom aksi (kanan) dan checkbox/kode (kiri) dapat sticky bila tabel melebihi viewport.
- **Pagination Controls:** Container bawah tabel tinggi `50px`, padding `px-4 py-2`, tombol Prev/Next dengan ikon, info halaman `Halaman X dari Y (Total N data)`, select box page-size (5, 10, 25, 50).

### 5.3 Modal & Slide-over Panel
- **Backdrop:** `background: rgba(15, 23, 42, 0.65)`, `backdrop-filter: blur(4px)`.
- **Modal Dialog:** Radius `16px` (`rounded-2xl`), border `1px solid var(--border)`, padding header `px-6 py-4`, padding konten `p-6`, sticky footer `px-6 py-4 border-t var(--border) bg-slate-50/50 dark:bg-slate-900/50`.
- **Slide-over Drawer:** Muncul dari kanan, lebar default `640px` (Lead Detail / Document Preview) atau `850px` (RAB & Quotation Editor), tinggi `100vh`, header dengan tombol close [X] dan tombol fullscreen expand.

### 5.4 Tab Segmented Control
- **Gaya Segmented (Pill Switch):** Background `var(--bg-secondary)`, padding `3px`, radius `10px`. Item tab: padding `px-3.5 py-1.5`, radius `8px`, `text-xs font-semibold`. Tab aktif: background `var(--bg-card)`, shadow `0 1px 3px rgba(0,0,0,0.1)`, teks `text-slate-900 dark:text-slate-50`.
- **Gaya Underline (Sub-tabs):** Border-bottom `1px solid var(--border)`. Tab aktif: border-bottom `2px solid var(--indigo-600)`, teks `text-indigo-600 dark:text-indigo-400 font-semibold`.

### 5.5 Formulir & Input
- **Input Text / Select:** Background `var(--bg-card)`, border `1px solid var(--border)`, radius `8px`, padding `px-3.5 py-2.5`, font `14px`, focus ring `2px solid rgba(99, 102, 241, 0.25)`, border focus `var(--indigo-500)`.
- **Label:** `text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5 block`. Tanda bintang merah `*` untuk field wajib.
- **Error State:** Border `var(--red-500)`, ring error `rgba(239, 68, 68, 0.2)`, teks error `text-xs text-red-500 mt-1`.

---

## 6. Pemetaan Ikon Prototipe ke Lucide React (`lucide-react`)

| Ikon di Prototipe SVG | Komponen Lucide React yang Digunakan | Penggunaan Utama |
|---|---|---|
| `search` | `<Search size={14} />` | Search input, filter bar |
| `alertTriangle` | `<AlertTriangle size={14} />` | Peringatan deadline, SLA breach, over-budget |
| `clock` | `<Clock size={12} />` | Indikator waktu, log follow-up, durasi VN |
| `chevronLeft`, `chevronRight` | `<ChevronLeft size={14} />`, `<ChevronRight size={14} />` | Paginasi, navigasi tahap |
| `arrowRight` | `<ArrowRight size={12} />` | Aksi forward, perpindahan stage |
| `xCircle` | `<XCircle size={16} />` | Tombol batal, clear filter, status reject |
| `checkCircle` | `<CheckCircle size={14} />` | Status deal won, tugas selesai, verifikasi |
| `instagram` | `<Instagram size={12} />` | Sumber lead Instagram |
| `globe` | `<Globe size={12} />` | Sumber lead Website inquiry |
| `whatsapp` | `<MessageCircle size={12} />` | Sumber lead WhatsApp, Client Care Inbox |
| `referral` | `<Users size={12} />` | Sumber lead Klien Rekomendasi / Partner |
| `linkedin` | `<Linkedin size={12} />` | Sumber lead LinkedIn B2B |
| `target` | `<Target size={14} />` | Forecast, target penjualan, milestone |
| `grip` | `<GripVertical size={14} />` | Drag handle kartu Kanban / WBS row |
| `download` | `<Download size={14} />` | Ekspor CSV, unduh PDF Quotation / MoU |
| `sparkles` | `<Sparkles size={14} />` | Fitur AI Generate (RAB, Requirement, Bot reply) |
| `bot` | `<Bot size={14} />` | Pesan otomatis asisten AI di WhatsApp |
| `mic` | `<Mic size={14} />` | Voice Note Presales Recorder |
| `fileText` | `<FileText size={16} />` | Dokumen MoU, KAK, Surat Penawaran |
| `barChart` | `<BarChart2 size={16} />` | Laporan analitik, revenue trend |
| `printer` | `<Printer size={14} />` | Cetak dokumen resmi |

