var e=Object.defineProperty,t=(t,n)=>{let r={};for(var i in t)e(r,i,{get:t[i],enumerable:!0});return n||e(r,Symbol.toStringTag,{value:`Module`}),r};(function(){let e=document.createElement(`link`).relList;if(e&&e.supports&&e.supports(`modulepreload`))return;for(let e of document.querySelectorAll(`link[rel="modulepreload"]`))n(e);new MutationObserver(e=>{for(let t of e)if(t.type===`childList`)for(let e of t.addedNodes)e.tagName===`LINK`&&e.rel===`modulepreload`&&n(e)}).observe(document,{childList:!0,subtree:!0});function t(e){let t={};return e.integrity&&(t.integrity=e.integrity),e.referrerPolicy&&(t.referrerPolicy=e.referrerPolicy),t.credentials=e.crossOrigin===`use-credentials`?`include`:e.crossOrigin===`anonymous`?`omit`:`same-origin`,t}function n(e){if(e.ep)return;e.ep=!0;let n=t(e);fetch(e.href,n)}})();function n(e,t=10,n=0,r=11,i=1,a=`draft`,o){let s=e.filter(e=>!e.is_optional),c=0,l=0,u=0;for(let e of s){let t=e.qty*e.unit_price;c+=t,l+=e.mandays||e.qty;let n=e.unit_cost===void 0?t*.6:e.qty*e.unit_cost;u+=n}let d=Math.round(c*Math.max(0,t)/100),f=c+d,p=Math.min(f,Math.max(0,n)),m=f-p,h=Math.round(m*Math.max(0,r)/100),g=m+h,_=m>0?Math.round((m-u)/m*1e3)/10:0;return{items:e,subtotal:c,contingency_pct:t,contingency_amount:d,discount_amount:p,total_before_tax:m,tax_pct:r,tax_amount:h,grand_total:g,total_mandays:l,internal_cost:u,margin_pct:_,version:i,status:a,reviewed_by:o,reviewed_at:o?new Date().toISOString():void 0}}var r=[{category:`Master Data`,name:`Master Karyawan & Struktur Organisasi`,description:`CRUD data karyawan, hierarki departemen, divisi, jabatan, riwayat karir & export/import Excel`,qty:6,unit:`Mandays`,role:`Fullstack Dev`,unit_price:15e5,unit_cost:9e5,mandays:6,complexity:`Medium`,source:`Template`},{category:`Master Data`,name:`Master Shift, Lokasi Kerja & Hari Libur`,description:`Manajemen multi-cabang geofence GPS, shift dinamis dan kalender hari libur nasional`,qty:4,unit:`Mandays`,role:`Fullstack Dev`,unit_price:15e5,unit_cost:9e5,mandays:4,complexity:`Low`,source:`Template`},{category:`Master Data`,name:`Master Tipe Cuti & Komponen Gaji`,description:`Kebijakan kuota cuti bertahap, formula tunjangan, potongan BPJS & PPh 21`,qty:5,unit:`Mandays`,role:`Backend Dev`,unit_price:15e5,unit_cost:9e5,mandays:5,complexity:`Medium`,source:`Template`},{category:`Transaksi`,name:`Absensi (GPS Geofencing & Liveness Selfie) + Approval`,description:`Check-in/out mobile GPS validasi radius cabang, deteksi anti-fake GPS dan approval berjenjang`,qty:12,unit:`Mandays`,role:`Mobile & BE`,unit_price:16e5,unit_cost:95e4,mandays:12,complexity:`High`,source:`Template`},{category:`Transaksi`,name:`Pengajuan Cuti, Izin, Lembur + Workflow Matrix`,description:`Pengajuan multi-approval, notifikasi real-time WhatsApp & kalkulasi otomatis saldo cuti`,qty:10,unit:`Mandays`,role:`Fullstack Dev`,unit_price:16e5,unit_cost:95e4,mandays:10,complexity:`Medium`,source:`Template`},{category:`Transaksi`,name:`Payroll Engine & E-Slip Gaji Terenkripsi`,description:`Proses kalkulasi payroll batch 500+ staff, slip gaji PDF password protected & integrasi bank`,qty:20,unit:`Mandays`,role:`Senior BE`,unit_price:18e5,unit_cost:11e5,mandays:20,complexity:`High`,source:`Template`},{category:`Transaksi`,name:`Recruitment & Onboarding Portal`,description:`Penerimaan CV, jadwal interview, evaluasi kandidat, dan checklist onboarding karyawan baru`,qty:14,unit:`Mandays`,role:`Fullstack Dev`,unit_price:16e5,unit_cost:95e4,mandays:14,complexity:`Medium`,source:`Template`},{category:`Laporan`,name:`Paket Laporan Komprehensif (Absensi, Cuti, Payroll, Headcount)`,description:`12 template laporan tabular & drilldown, filter periode, export XLSX/PDF resmi`,qty:8,unit:`Mandays`,role:`Backend Dev`,unit_price:15e5,unit_cost:9e5,mandays:8,complexity:`Medium`,source:`Template`},{category:`Dashboard & KPI`,name:`Dashboard Eksekutif HR & Employee Self-Service (ESS)`,description:`Visualisasi turnover rate, kehadiran harian, statistik demografi & kalender tim`,qty:7,unit:`Mandays`,role:`Frontend Dev`,unit_price:17e5,unit_cost:1e6,mandays:7,complexity:`Medium`,source:`Template`},{category:`Mobile`,name:`Aplikasi Mobile Karyawan (Android & iOS)`,description:`React Native / Flutter app: absensi selfie, profil, notifikasi push, slip gaji & izin`,qty:25,unit:`Mandays`,role:`Mobile Dev`,unit_price:18e5,unit_cost:11e5,mandays:25,complexity:`High`,source:`Template`},{category:`Integrasi`,name:`Integrasi Mesin Fingerprint & API Bank Transfer`,description:`Sync log absensi dari mesin biometric Solution/ZKTeco dan export payroll file ke bank`,qty:6,unit:`Mandays`,role:`Integration Eng`,unit_price:18e5,unit_cost:11e5,mandays:6,complexity:`Medium`,source:`Template`},{category:`Non-Functional`,name:`Security, Multi-Role RBAC, Audit Log & Notifikasi`,description:`Enkripsi data pribadi UU PDP, otentikasi 2FA, session control & audit trail per perubahan`,qty:8,unit:`Mandays`,role:`Security & BE`,unit_price:16e5,unit_cost:95e4,mandays:8,complexity:`Medium`,source:`Template`},{category:`PM & QA`,name:`Project Management, Quality Assurance & UAT Assistance`,description:`Supervisi sprint, test automation, load testing, pendampingan UAT dan perbaikan bug`,qty:18,unit:`Mandays`,role:`PM & QA Lead`,unit_price:17e5,unit_cost:105e4,mandays:18,complexity:`Medium`,source:`Template`},{category:`Infrastruktur`,name:`Cloud Server Setup, Containerization Docker & CI/CD Pipeline`,description:`Deployment production environment, SSL TLS 1.3, backup otomatis dan monitoring`,qty:4,unit:`Mandays`,role:`DevOps Eng`,unit_price:19e5,unit_cost:115e4,mandays:4,complexity:`Medium`,source:`Template`},{category:`Training`,name:`User Manual Book, Video Tutorial & Onsite Training Admin`,description:`Pelatihan langsung untuk super admin dan user HRD, buku panduan PDF & video panduan`,qty:5,unit:`Mandays`,role:`Technical Writer`,unit_price:14e5,unit_cost:8e5,mandays:5,complexity:`Low`,source:`Template`}];function i(e){let t=[``,`Satu`,`Dua`,`Tiga`,`Empat`,`Lima`,`Enam`,`Tujuh`,`Delapan`,`Sembilan`,`Sepuluh`,`Sebelas`],n=Math.floor(Math.abs(e)),r=``;return r=n<12?t[n]:n<20?i(n-10)+` Belas`:n<100?i(Math.floor(n/10))+` Puluh `+i(n%10):n<200?`Seratus `+i(n-100):n<1e3?i(Math.floor(n/100))+` Ratus `+i(n%100):n<2e3?`Seribu `+i(n-1e3):n<1e6?i(Math.floor(n/1e3))+` Ribu `+i(n%1e3):n<1e9?i(Math.floor(n/1e6))+` Juta `+i(n%1e6):n<0xe8d4a51000?i(Math.floor(n/1e9))+` Miliar `+i(n%1e9):i(Math.floor(n/0xe8d4a51000))+` Triliun `+i(n%0xe8d4a51000),r.replace(/\s+/g,` `).trim()}function a(e){return new Intl.NumberFormat(`id-ID`,{style:`currency`,currency:`IDR`,maximumFractionDigits:0}).format(e)}function o(e){let t=i(e).trim();return t?`${t} Rupiah`:`Nol Rupiah`}var s={"30/40/30":{label:`30% DP / 40% Progress UAT / 30% Pelunasan (Standar)`,terms:[{term_no:1,name:`Down Payment (DP)`,value_type:`percent`,value:30,trigger_type:`on_signing`,trigger_label:`Saat penandatanganan kontrak / MoU`,due_days_label:`Net 7`,description:`Pembayaran uang muka untuk kick-off proyek & resource allocation`},{term_no:2,name:`Progress / UAT Milestone`,value_type:`percent`,value:40,trigger_type:`uat`,trigger_label:`Setelah penyelesaian UAT aplikasi`,due_days_label:`Net 14`,description:`Penyelesaian fitur utama dan pengujian UAT client`},{term_no:3,name:`Pelunasan Final`,value_type:`balance`,value:30,trigger_type:`bast`,trigger_label:`Setelah penandatanganan BAST`,due_days_label:`Net 14`,description:`Serah terima sistem final dan dokumen BAST`}]},"50/50":{label:`50% DP / 50% Pelunasan BAST`,terms:[{term_no:1,name:`Down Payment (DP 50%)`,value_type:`percent`,value:50,trigger_type:`on_signing`,trigger_label:`Saat penandatanganan kontrak`,due_days_label:`Net 7`,description:`Uang muka pengerjaan proyek`},{term_no:2,name:`Pelunasan (50%)`,value_type:`balance`,value:50,trigger_type:`bast`,trigger_label:`Saat penandatanganan BAST`,due_days_label:`Net 14`,description:`Pelunasan akhir pengerjaan`}]},"40/30/30":{label:`40% DP / 30% Alpha Release / 30% BAST`,terms:[{term_no:1,name:`Down Payment (40%)`,value_type:`percent`,value:40,trigger_type:`on_signing`,trigger_label:`Saat penandatanganan kontrak`,due_days_label:`Net 7`,description:`Uang muka kick-off proyek`},{term_no:2,name:`Milestone Alpha / Core Feature`,value_type:`percent`,value:30,trigger_type:`milestone`,trigger_label:`Penyelesaian modul transaksi utama`,due_days_label:`Net 14`,description:`Penyelesaian modul inti transaksi & master data`},{term_no:3,name:`Pelunasan BAST`,value_type:`balance`,value:30,trigger_type:`bast`,trigger_label:`Saat penandatanganan BAST`,due_days_label:`Net 14`,description:`Serah terima proyek`}]},"30/30/30/10":{label:`30% DP / 30% Dev / 30% UAT / 10% Retensi Garansi`,terms:[{term_no:1,name:`Down Payment (30%)`,value_type:`percent`,value:30,trigger_type:`on_signing`,trigger_label:`Saat penandatanganan kontrak`,due_days_label:`Net 7`,description:`Uang muka kick-off proyek`},{term_no:2,name:`Sprint Core Release (30%)`,value_type:`percent`,value:30,trigger_type:`milestone`,trigger_label:`Deployment staging / sprint selesai`,due_days_label:`Net 14`,description:`Penyelesaian core backend & frontend`},{term_no:3,name:`UAT & Go-Live (30%)`,value_type:`percent`,value:30,trigger_type:`go_live`,trigger_label:`Go-Live sistem di server client`,due_days_label:`Net 14`,description:`Sistem live di production`},{term_no:4,name:`Retensi Pemeliharaan (10%)`,value_type:`balance`,value:10,trigger_type:`days_after_event`,trigger_label:`90 hari masa garansi & maintenance`,due_days_label:`Net 30`,description:`Retensi garansi SLA pemeliharaan`}]},"100% Upfront":{label:`100% Pelunasan Di Muka (Paket Kecil / Konsultasi)`,terms:[{term_no:1,name:`Pelunasan Penuh 100%`,value_type:`percent`,value:100,trigger_type:`on_signing`,trigger_label:`Saat penandatanganan kontrak / SPK`,due_days_label:`Net 7`,description:`Pembayaran penuh di muka sebelum pekerjaan dimulai`}]}};function c(e,t,n=`grand_total`,r=`Custom`,i=!0){let a=e.map((e,t)=>({...e,term_no:t+1}));if(t<=0)return{calc_basis:n,preset_name:r,last_term_is_balance:i,items:a.map(e=>({...e,calculated_amount:0})),total_percent:0,total_amount:0,is_balanced:!0,remaining_amount:0,remaining_percent:0};let o=0,s=0,c=a.findIndex(e=>e.value_type===`balance`);i&&c===-1&&a.length>1&&(a[a.length-1].value_type=`balance`);let l=a.map(e=>{if(e.value_type===`percent`){let n=Math.max(0,e.value||0),r=Math.round(n/100*t);return o+=r,s+=n,{...e,calculated_amount:r}}if(e.value_type===`amount`){let n=Math.max(0,e.value||0),r=n/t*100;return o+=n,s+=r,{...e,calculated_amount:n}}return{...e,calculated_amount:0}}),u=l.findIndex(e=>e.value_type===`balance`);if(u!==-1){let e=Math.max(0,t-o),n=Math.max(0,100-s);l[u].calculated_amount=e,l[u].value=Math.round(n*100)/100,o+=e,s+=n}let d=t-o,f=100-s,p=Math.abs(d)<=1&&Math.abs(f)<=.05;return{calc_basis:n,preset_name:r,last_term_is_balance:i,items:l,total_percent:Math.round(s*100)/100,total_amount:o,is_balanced:p,remaining_amount:d,remaining_percent:Math.round(f*100)/100}}function l(e,t,n=`grand_total`){return c((s[e]||s[`30/40/30`]).terms.map((e,t)=>({...e,id:`term-${Date.now()}-${t}`,calculated_amount:0,status:`Planned`})),t,n,e,!0)}var u=[{id:`cl-1`,number:1,title:`Pasal 1: Ketentuan Umum & Definisi Operasional`,body:`Dalam Perjanjian ini yang dimaksud dengan "Pekerjaan" adalah rancang bangun dan implementasi perangkat lunak (software) sesuai Dokumen Spesifikasi Teknis dan Rencana Anggaran Biaya (RAB) terlampir. "PIHAK PERTAMA" adalah Klien pemesan sistem, dan "PIHAK KEDUA" adalah PT ASTACODE Solusindo Teknologi.`,is_active:!0,category:`Umum & Definisi`,is_mandatory:!0},{id:`cl-2`,number:2,title:`Pasal 2: Maksud, Tujuan & Penunjukan Kerjasama`,body:`PIHAK PERTAMA dengan ini secara sah menunjuk PIHAK KEDUA dan PIHAK KEDUA menyatakan menerima penunjukan tersebut sebagai penyedia jasa teknologi eksklusif untuk melaksanakan analisis kebutuhan, arsitektur sistem, pemrograman, pengujian, implementasi, dan alih pengetahuan (transfer of knowledge).`,is_active:!0,category:`Hubungan Para Pihak`,is_mandatory:!0},{id:`cl-3`,number:3,title:`Pasal 3: Ruang Lingkup Pekerjaan & Dokumen Spesifikasi`,body:`Ruang lingkup pekerjaan mencakup seluruh modul transaksional, integrasi antarmuka pemrograman aplikasi (API), basis data terpusat, pengamanan siber, dan dokumentasi sistem sebagaimana dirinci pada Bagian II (Lampiran RAB & Mandays) dokumen kesepakatan ini.`,is_active:!0,category:`Ruang Lingkup & Deliverable`,is_mandatory:!0},{id:`cl-4`,number:4,title:`Pasal 4: Metodologi Pengembangan & Jadwal Sprint`,body:`Pengembangan perangkat lunak dilaksanakan dengan kerangka kerja Agile Scrum yang dibagi dalam tahapan Sprint mingguan. Laporan progress dan demo iterasi berkala dipresentasikan oleh Project Manager ASTACODE (Asisyah Sarah Azzahra) kepada Product Owner / PIC PIHAK PERTAMA.`,is_active:!0,category:`Ruang Lingkup & Deliverable`,is_mandatory:!1},{id:`cl-5`,number:5,title:`Pasal 5: Jangka Waktu Pelaksanaan & Masa Efektif`,body:`Perjanjian ini berlaku efektif terhitung sejak penandatanganan kesepakatan ini dan pelunasan pembayaran Down Payment (DP) oleh PIHAK PERTAMA hingga seluruh kewajiban deliverable dan masa pemeliharaan berakhir.`,is_active:!0,category:`Waktu & Pelaksanaan`,is_mandatory:!0},{id:`cl-6`,number:6,title:`Pasal 6: Nilai Kontrak & Skema Termin Pembayaran (TOP)`,body:`Nilai total kontrak dan tata cara pembayaran mengikat para pihak sesuai skema Terms of Payment (TOP) bertahap yang tertuang dalam lampiran SPK. Invoice pembayaran wajib diselesaikan maksimal 14 (empat belas) hari kalender sejak faktur diterbitkan.`,is_active:!0,category:`Keuangan & Pajak`,is_mandatory:!0},{id:`cl-7`,number:7,title:`Pasal 7: Ketentuan Perpajakan (PPN 11% & Bukti Potong PPh 23)`,body:`Nilai kontrak dikenakan Pajak Pertambahan Nilai (PPN) 11% sesuai peraturan perpajakan Republik Indonesia. Pemotongan Pajak Penghasilan (PPh 23) wajib disertai bukti potong resmi (e-Bupot) yang diserahkan kepada finance ASTACODE maksimal 30 hari pasca pembayaran.`,is_active:!0,category:`Keuangan & Pajak`,is_mandatory:!0},{id:`cl-8`,number:8,title:`Pasal 8: Prosedur Pengujian, UAT & Berita Acara UAT`,body:`Pengujian penerimaan pengguna (User Acceptance Testing / UAT) dilaksanakan di lingkungan staging selambat-lambatnya 14 (empat belas) hari kerja setelah serah-terima versi kandidat rilis. UAT dianggap sukses dan sah secara hukum apabila dituangkan dalam Berita Acara UAT tertandatangan.`,is_active:!0,category:`Serah Terima & UAT`,is_mandatory:!0},{id:`cl-9`,number:9,title:`Pasal 9: Serah Terima Pekerjaan & Penandatanganan BAST`,body:`Setelah pengujian UAT disetujui tanpa temuan kritis (Severity 1), para pihak wajib menandatangani Berita Acara Serah Terima (BAST) Pekerjaan yang menandai sistem siap dideploy ke server produksi (Production Go-Live).`,is_active:!0,category:`Serah Terima & UAT`,is_mandatory:!0},{id:`cl-10`,number:10,title:`Pasal 10: Ketentuan Perubahan Ruang Lingkup (Change Request)`,body:`Setiap permintaan penambahan fitur, modul baru, atau perubahan alur bisnis yang berada di luar ruang lingkup RAB asli wajib diajukan secara tertulis sebagai Change Request (CR) dan akan diestimasi mandays serta biayanya melalui dokumen Addendum Perjanjian.`,is_active:!0,category:`Ruang Lingkup & Deliverable`,is_mandatory:!0},{id:`cl-11`,number:11,title:`Pasal 11: Hak Cipta, Kepemilikan HAKI & Source Code Escrow`,body:`Hak Cipta intelektual atas kode sumber (source code) aplikasi kustom, skema basis data, dan modul bisnis diserahkan penuh kepada PIHAK PERTAMA setelah seluruh kewajiban pembayaran 100% lunas. Komponen reusable framework internal tetap merupakan kekayaan intelektual ASTACODE yang dilisensikan non-eksklusif.`,is_active:!0,category:`HAKI & Lisensi`,is_mandatory:!0},{id:`cl-12`,number:12,title:`Pasal 12: Kerahasiaan Informasi (Non-Disclosure Agreement / NDA)`,body:`Para pihak sepakat untuk merahasiakan segala bentuk informasi bisnis, data finansial, rahasia dagang, strategi operasional, dan arsitektur teknis yang diperoleh selama berlangsungnya kerjasama hingga 3 (tiga) tahun pasca perjanjian berakhir.`,is_active:!0,category:`Kerahasiaan & Keamanan`,is_mandatory:!0},{id:`cl-13`,number:13,title:`Pasal 13: Kepatuhan Pelindungan Data Pribadi (UU PDP No. 27/2022)`,body:`Kedua belah pihak berkomitmen mematuhi ketentuan UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP). ASTACODE bertindak sebagai Pemroses Data yang menerapkan enkripsi end-to-end, hashing kata sandi, dan proteksi integritas data kredensial pengguna.`,is_active:!0,category:`Kerahasiaan & Keamanan`,is_mandatory:!0},{id:`cl-14`,number:14,title:`Pasal 14: Garansi Pemeliharaan Bug Pasca BAST (Bug-Free Warranty)`,body:`PIHAK KEDUA memberikan jaminan perbaikan cacat program / bug perangkat lunak secara cuma-cuma selama 90 (sembilan puluh) hari kalender sejak penandatanganan BAST, selama tidak disebabkan modifikasi mandiri oleh pihak ketiga tanpa persetujuan tertulis ASTACODE.`,is_active:!0,category:`Garansi & SLA`,is_mandatory:!0},{id:`cl-15`,number:15,title:`Pasal 15: Service Level Agreement (SLA) & Dukungan Helpdesk`,body:`ASTACODE menyediakan layanan dukungan teknis helpdesk pada jam kerja (Senin-Jumat, 08:30-17:30 WIB) dengan SLA respon awal: Insiden Kritis (P1) < 1 jam, Insiden Mayor (P2) < 4 jam, dan Pertanyaan Operasional (P3) < 24 jam.`,is_active:!0,category:`Garansi & SLA`,is_mandatory:!0},{id:`cl-16`,number:16,title:`Pasal 16: Keamanan Siber & Kewajiban Audit Sistem (VAPT)`,body:`Perangkat lunak dibangun dengan memperhatikan kaidah OWASP Top 10 Security Guidelines. PIHAK PERTAMA berhak melakukan audit keamanan Vulnerability Assessment and Penetration Testing (VAPT) sebelum Go-Live dengan koordinasi teknis bersama tim DevSecOps ASTACODE.`,is_active:!0,category:`Kerahasiaan & Keamanan`,is_mandatory:!1},{id:`cl-17`,number:17,title:`Pasal 17: Hak Akses, Server Infrastruktur & Cloud Deployment`,body:`PIHAK PERTAMA bertanggung jawab atas penyediaan lingkungan server produksi / akun Cloud (AWS, GCP, atau On-Premise) beserta lisensi domain dan SSL Certificate, sedangkan ASTACODE bertindak menyiapkan pipeline deployment CI/CD otomatis.`,is_active:!0,category:`Operasional & Infrastruktur`,is_mandatory:!1},{id:`cl-18`,number:18,title:`Pasal 18: Keadaan Kahar (Force Majeure)`,body:`Tidak ada pihak yang dianggap lalai atau melanggar perjanjian apabila kegagalan pelaksanaan kewajiban diakibatkan oleh kejadian di luar kendali wajar (Force Majeure), mencakup bencana alam, huru-hara, perang, kebakaran, kegagalan jaringan telekomunikasi nasional, atau kebijakan moneter darurat pemerintah.`,is_active:!0,category:`Hukum & Sengketa`,is_mandatory:!0},{id:`cl-19`,number:19,title:`Pasal 19: Sanksi Keterlambatan, Denda & Cidera Janji (Default)`,body:`Keterlambatan penyelesaian pekerjaan atau keterlambatan pembayaran termin yang bukan disebabkan keadaan kahar dikenakan denda sebesar 1‰ (satu permil) per hari kalender keterlambatan, dengan batas maksimal penalti sebesar 5% (lima persen) dari nilai termin terkait.`,is_active:!0,category:`Hukum & Sengketa`,is_mandatory:!0},{id:`cl-20`,number:20,title:`Pasal 20: Pengakhiran Perjanjian (Termination of Contract)`,body:`Perjanjian dapat diakhiri secara sepihak dengan pemberitahuan tertulis 14 hari sebelumnya apabila salah satu pihak terbukti melakukan cidera janji material dan tidak memperbaikinya dalam masa peringatan tertulis. Pekerjaan yang telah selesai dikerjakan hingga tanggal pemutusan tetap wajib dibayarkan secara proporsional.`,is_active:!0,category:`Hukum & Sengketa`,is_mandatory:!0},{id:`cl-21`,number:21,title:`Pasal 21: Penyelesaian Perselisihan, Mediasi & Domisili Hukum`,body:`Segala perselisihan yang timbul dari penafsiran atau pelaksanaan perjanjian ini diselesaikan terlebih dahulu melalui musyawarah untuk mufakat. Apabila dalam tempo 30 hari tidak tercapai kesepakatan, para pihak sepakat memilih domisili hukum tetap di Kantor Kepaniteraan Pengadilan Negeri setempat.`,is_active:!0,category:`Hukum & Sengketa`,is_mandatory:!0},{id:`cl-22`,number:22,title:`Pasal 22: Ketentuan Penutup, Keabsahan & E-Signature UU ITE`,body:`Perjanjian ini dibuat dalam bentuk dokumen elektronik yang memiliki kekuatan hukum mengikat dan sah sesuai Pasal 11 Undang-Undang No. 11 Tahun 2008 jo UU No. 1 Tahun 2024 tentang Informasi dan Transaksi Elektronik (UU ITE), dibubuhi e-Meterai resmi dan e-Signature tersertifikasi oleh para wakil yang berwenang.`,is_active:!0,category:`Pengesahan & Penutup`,is_mandatory:!0}],d=[{id:`cust-1`,code:`CUST-001`,legal_name:`PT Maju Sejahtera Industri`,brand_name:`Maju Sejahtera`,industry:`Manufaktur & Fabrikasi`,company_size:`500+ Karyawan`,website:`https://majusejahtera.co.id`,npwp:`01.234.567.8-429.000`,address:`Kawasan Industri Jababeka Blok C-12`,city:`Bekasi`,province:`Jawa Barat`,lifecycle_status:`Prospect`,first_touch_source:`Instagram`,health_score:88,contacts:[{id:`cnt-1`,name:`Budi Santoso, S.T., M.M.`,job_title:`Head of Human Resources & General Affairs`,email:`budi.santoso@majusejahtera.co.id`,whatsapp_number:`+6281298765432`,role_type:`decision_maker`,is_primary:!0},{id:`cnt-2`,name:`Siska Lestari`,job_title:`IT & Digital Transformation Lead`,email:`siska.lestari@majusejahtera.co.id`,whatsapp_number:`+6281311223344`,role_type:`technical`,is_primary:!1},{id:`cnt-3`,name:`Heru Prasetyo`,job_title:`Finance & Purchasing Manager`,email:`heru.p@majusejahtera.co.id`,whatsapp_number:`+6281765432100`,role_type:`finance`,is_primary:!1}]},{id:`cust-2`,code:`CUST-002`,legal_name:`PT Retail Nusantara Prima`,brand_name:`Nusantara Retail`,industry:`Retail & Multi-Store`,company_size:`200-500 Karyawan`,website:`https://nusantararetail.id`,npwp:`02.456.789.1-015.000`,address:`Jl. Boulevard Raya Barat No. 88, Kelapa Gading`,city:`Jakarta Utara`,province:`DKI Jakarta`,lifecycle_status:`Prospect`,first_touch_source:`Website`,health_score:75,contacts:[{id:`cnt-4`,name:`Sari Wijaya`,job_title:`VP Operations & Supply Chain`,email:`sari.wijaya@nusantararetail.id`,whatsapp_number:`+6281122334455`,role_type:`decision_maker`,is_primary:!0}]},{id:`cust-3`,code:`CUST-003`,legal_name:`PT Finan Prima Ventura`,brand_name:`FinanPrima`,industry:`Financial Technology`,company_size:`100-250 Karyawan`,website:`https://finanprima.co.id`,address:`SCBD District 8 Treasury Tower Lt. 21`,city:`Jakarta Selatan`,province:`DKI Jakarta`,lifecycle_status:`Prospect`,first_touch_source:`Referral`,health_score:92,contacts:[{id:`cnt-5`,name:`Michael Tanuwidjaja`,job_title:`Chief Technology Officer (CTO)`,email:`michael.tan@finanprima.co.id`,whatsapp_number:`+6281988776655`,role_type:`decision_maker`,is_primary:!0}]},{id:`cust-4`,code:`CUST-004`,legal_name:`PT Logistik Express Sentosa`,brand_name:`Sentosa Logistics`,industry:`Logistics & Supply Chain`,company_size:`350 Karyawan`,website:`https://sentosalogistics.com`,address:`Jl. Tanjung Priok Port Gateway No. 10`,city:`Jakarta Utara`,province:`DKI Jakarta`,lifecycle_status:`Active`,first_touch_source:`LinkedIn`,health_score:95,contacts:[{id:`cnt-6`,name:`Bambang Soediro`,job_title:`Managing Director`,email:`bambang@sentosalogistics.com`,whatsapp_number:`+6281233445566`,role_type:`decision_maker`,is_primary:!0}]}],f=n(r.map((e,t)=>({...e,id:`rab-item-${t+1}`,subtotal:e.qty*e.unit_price})),10,0,11,2,`reviewed`,`Asisyah Sarah Azzahra (Super Admin & PM)`),p={id:`quo-1`,lead_id:`lead-1`,number:`QT/2026/10/0012`,mou_number:`MOU/2026/10/0012`,version_label:`Rev. 2`,status:`Negosiasi`,valid_days:30,created_at:`2026-10-12T10:00:00Z`,sent_at:`2026-10-12T14:30:00Z`,intro_letter:`Sehubungan dengan diskusi kebutuhan transformasi digital sistem pengelolaan SDM pada PT Maju Sejahtera Industri, bersama ini kami dari ASTACODE menyampaikan penawaran resmi rancang bangun Aplikasi Human Resource Information System (HRIS) terintegrasi berbasis Web dan Mobile ESS. Solusi ini dirancang khusus untuk mengakomodasi 500+ karyawan, absensi geofencing anti-fake GPS, payroll engine terenkripsi, pengajuan cuti/izin/lembur berjenjang, integrasi biometric hardware, dan laporan komprehensif.`,rab_summary:f,top_scheme:l(`30/40/30`,f.grand_total,`grand_total`),clauses:u},m=[{id:`lead-1`,code:`LD-2026-001`,title:`Aplikasi HRD 500 Karyawan & Mobile ESS`,project_type:`Web & Mobile App`,customer_id:`cust-1`,customer:d[0],primary_contact:d[0].contacts[0],est_value:309889800,target_closing:`2026-11-20`,sales_pic:`Asisyah Sarah Azzahra`,sales_pic_avatar:`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`,priority:`Hot`,stage_id:`negotiation`,source:`Instagram`,sub_source:`Instagram Direct Message (DM)`,campaign:`Campaign HR-Tech Q4`,tags:[`HRIS`,`Enterprise`,`Payroll`,`Mobile ESS`],created_at:`2026-10-05T08:30:00Z`,last_activity_at:`2026-10-15T15:00:00Z`,days_in_stage:3,ai_summary:`Klien membutuhkan aplikasi HRD untuk 500 karyawan di pabrik Bekasi. Masalah utama: absensi manual rawan titip absen & penghitungan payroll butuh waktu 5 hari tiap bulan. Estimasi RAB 152 mandays telah direview.`,next_action:`Meeting negosiasi klausul SLA dan konfirmasi termin DP 30% dengan Pak Budi via Google Meet.`,rab:f,quotations:[p],active_quotation_id:`quo-1`,notes_count:5,documents_count:3},{id:`lead-2`,code:`LD-2026-002`,title:`ERP Retail & Multi-Warehouse Distribution`,project_type:`ERP Custom`,customer_id:`cust-2`,customer:d[1],primary_contact:d[1].contacts[0],est_value:45e7,target_closing:`2026-11-30`,sales_pic:`Sari Dewi`,sales_pic_avatar:`https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&auto=format&fit=crop&q=80`,priority:`High`,stage_id:`quotation`,source:`Website`,sub_source:`Landing Page Form`,tags:[`ERP`,`Retail`,`Inventory`,`Multi-Store`],created_at:`2026-10-08T11:00:00Z`,last_activity_at:`2026-10-14T09:20:00Z`,days_in_stage:5,ai_summary:`Sistem retail 18 cabang toko dan 2 gudang konsolidasi. Butuh real-time stock transfer & POS sync.`,next_action:`Follow up penerimaan dokumen penawaran quotation QT/2026/10/0014.`,notes_count:2,documents_count:2,quotations:[]},{id:`lead-3`,code:`LD-2026-003`,title:`Mobile Banking Core & e-Wallet Integration`,project_type:`Fintech Platform`,customer_id:`cust-3`,customer:d[2],primary_contact:d[2].contacts[0],est_value:9e8,target_closing:`2026-12-15`,sales_pic:`Budi Kurniawan`,sales_pic_avatar:`https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80`,priority:`Hot`,stage_id:`requirement`,source:`Referral`,referrer_name:`PT Mitra Solusi Finansial`,tags:[`Fintech`,`Mobile App`,`High Security`,`QRIS`],created_at:`2026-10-12T14:00:00Z`,last_activity_at:`2026-10-15T11:30:00Z`,days_in_stage:2,ai_summary:`Platform mobile banking generasi baru dengan standar keamanan ISO 27001, tokenisasi & BI-FAST.`,next_action:`Menjalankan AI Requirement Generator dari dokumen brief teknis 40 halaman.`,notes_count:4,documents_count:4,quotations:[]},{id:`lead-4`,code:`LD-2026-004`,title:`Corporate LMS & Interactive Learning Portal`,project_type:`Web Portal`,customer_id:`cust-1`,customer:d[0],primary_contact:d[0].contacts[1],est_value:25e7,target_closing:`2026-11-15`,sales_pic:`Asisyah Sarah Azzahra`,sales_pic_avatar:`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`,priority:`Medium`,stage_id:`follow_up`,source:`WhatsApp`,sub_source:`WhatsApp Inbound Sales`,tags:[`LMS`,`Video Training`,`Gamification`],created_at:`2026-10-09T09:15:00Z`,last_activity_at:`2026-10-13T16:45:00Z`,days_in_stage:6,ai_summary:`Modul pelatihan internal dan sertifikasi kepatuhan ISO 9001 untuk seluruh staf operasional.`,next_action:`Jadwalkan demo prototype sistem pembelajaran interaktif hari Jumat.`,notes_count:1,documents_count:1,quotations:[]},{id:`lead-5`,code:`LD-2026-005`,title:`Cloud POS & Kitchen Display Kitchen (KDS)`,project_type:`Point of Sale`,customer_id:`cust-2`,customer:d[1],primary_contact:d[1].contacts[0],est_value:18e7,target_closing:`2026-11-25`,sales_pic:`Sari Dewi`,sales_pic_avatar:`https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&auto=format&fit=crop&q=80`,priority:`Medium`,stage_id:`new_lead`,source:`Meta Ads`,campaign:`Ads POS Resto Oct 2026`,tags:[`POS`,`Cloud`,`Kitchen Display`],created_at:`2026-10-15T07:20:00Z`,last_activity_at:`2026-10-15T07:20:00Z`,days_in_stage:1,ai_summary:`Inbound lead dari Meta Ads. Klien memiliki 8 cabang restoran yang butuh integrasi kasir ke dapur.`,next_action:`Lakukan kontak pertama via WhatsApp dalam SLA < 30 menit.`,notes_count:0,documents_count:0,quotations:[]},{id:`lead-6`,code:`LD-2026-006`,title:`Custom Logistics & Fleet Tracking Telematics`,project_type:`IoT & Web Platform`,customer_id:`cust-4`,customer:d[3],primary_contact:d[3].contacts[0],est_value:52e7,target_closing:`2026-10-01`,sales_pic:`Budi Kurniawan`,sales_pic_avatar:`https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80`,priority:`Hot`,stage_id:`deal`,source:`LinkedIn`,sub_source:`InMail Outreach`,tags:[`Logistics`,`GPS Tracking`,`Deal Won`],created_at:`2026-09-01T10:00:00Z`,last_activity_at:`2026-10-02T13:00:00Z`,days_in_stage:14,ai_summary:`DEAL WON. Kontrak CT/2026/10/0007 ditandatangani. Proyek pengerjaan delivery sedang berlangsung.`,next_action:`Sprint 2 demo dan verifikasi invoice Termin 2 UAT.`,notes_count:8,documents_count:5,quotations:[]},{id:`lead-7`,code:`LD-2026-007`,title:`B2B Procurement Marketplace & Supplier Portal`,project_type:`Custom Web Platform`,customer_id:`cust-2`,customer:d[1],primary_contact:d[1].contacts[0],est_value:38e7,target_closing:`2026-09-20`,sales_pic:`Asisyah Sarah Azzahra`,sales_pic_avatar:`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`,priority:`Low`,stage_id:`reject`,source:`Website`,tags:[`B2B`,`Procurement`,`Rejected`],created_at:`2026-08-15T09:00:00Z`,last_activity_at:`2026-09-25T11:00:00Z`,days_in_stage:21,ai_summary:`Klien membatalkan proyek karena efisiensi anggaran internal dialihkan ke tahun depan.`,next_action:`Masukkan ke campaign nurturing Q2 2027.`,notes_count:2,documents_count:1,quotations:[]}],h=[{id:`act-1`,lead_id:`lead-1`,type:`google_meet`,title:`Google Meet: Klarifikasi Final Scope RAB & Setting TOP`,scheduled_at:`2026-10-18T14:00:00Z`,duration_minutes:60,status:`planned`,meet_link:`https://meet.google.com/xyz-asta-crm`,assigned_to:`Asisyah Sarah Azzahra`,attendees:[`budi.santoso@majusejahtera.co.id`,`siska.lestari@majusejahtera.co.id`,`asisyahsarahazz@gmail.com`],notes:`Membahas pembagian termin DP 30% dan progress UAT 40% serta integrasi mesin absensi ZKTeco.`,created_at:`2026-10-15T10:00:00Z`},{id:`act-2`,lead_id:`lead-1`,type:`call`,title:`Telepon Konfirmasi Draft Quotation Rev. 2`,scheduled_at:`2026-10-14T15:30:00Z`,duration_minutes:25,status:`completed`,outcome:`Pak Budi menyetujui total 152 mandays, meminta klausul garansi diperjelas 90 hari.`,assigned_to:`Asisyah Sarah Azzahra`,created_at:`2026-10-14T15:00:00Z`},{id:`act-3`,lead_id:`lead-1`,type:`visit`,title:`Kunjungan Onsite Pabrik Bekasi: Survey Topologi Jaringan & Hardware`,scheduled_at:`2026-10-10T10:00:00Z`,duration_minutes:180,status:`completed`,outcome:`Tim teknis presales memetakan 4 titik mesin fingerprint dan radius GPS geofencing pabrik.`,assigned_to:`Asisyah Sarah Azzahra`,created_at:`2026-10-09T08:00:00Z`},{id:`act-4`,lead_id:`lead-2`,type:`whatsapp`,title:`Follow-up WhatsApp Dokumen Quotation`,scheduled_at:`2026-10-14T11:00:00Z`,duration_minutes:10,status:`completed`,outcome:`Ibu Sari menginformasikan direksi sedang mereview budget ERP.`,assigned_to:`Sari Dewi`,created_at:`2026-10-14T10:50:00Z`}],g=[{id:`prj-1`,code:`PRJ-2026-001`,lead_id:`lead-6`,customer_id:`cust-4`,title:`Custom Logistics & Fleet Tracking Telematics Platform`,customer_name:`PT Logistik Express Sentosa`,pm_name:`Asisyah Sarah Azzahra`,contract_number:`CT/2026/10/0007`,contract_value:52e7,budget_mandays:240,actual_mandays:72,status:`Active`,start_date:`2026-10-05`,end_date:`2027-02-28`,progress_pct:35,milestones:[{id:`ms-1`,name:`Sprint 1: Architecture & Fleet Core`,target_date:`2026-10-25`,status:`Completed`,mandays_budget:60,mandays_actual:58},{id:`ms-2`,name:`Sprint 2: Telematics GPS Ingestion Engine`,target_date:`2026-11-20`,status:`In Progress`,mandays_budget:80,mandays_actual:14},{id:`ms-3`,name:`Sprint 3: Driver Mobile App & Dispatch`,target_date:`2026-12-15`,status:`Pending`,mandays_budget:60,mandays_actual:0},{id:`ms-4`,name:`UAT & BAST Deployment`,target_date:`2027-01-15`,status:`Pending`,mandays_budget:40,mandays_actual:0}],top_scheme:l(`30/40/30`,52e7,`grand_total`),warranty_period:`90 Hari Pasca BAST`,sla_level:`99.9% High Availability SLA`,esign_status:`Terverifikasi Sah UU ITE`,signed_by_client:`Bambang Soediro (Managing Director)`,signed_by_company:`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,signed_at:`2026-10-05T14:30:00Z`},{id:`prj-2`,code:`PRJ-2026-002`,lead_id:`lead-1`,customer_id:`cust-1`,title:`Enterprise ERP & Mobile ESS Biometric Tracking`,customer_name:`PT Maju Sejahtera Industri`,pm_name:`Asisyah Sarah Azzahra`,contract_number:`CT/2026/10/0012`,contract_value:2442e5,budget_mandays:152,actual_mandays:48,status:`Active`,start_date:`2026-10-15`,end_date:`2027-01-30`,progress_pct:28,milestones:[{id:`ms-21`,name:`Sprint 1: Payroll Engine & Database Core`,target_date:`2026-11-05`,status:`In Progress`,mandays_budget:45,mandays_actual:28},{id:`ms-22`,name:`Sprint 2: Geofencing Mobile ESS & Biometric`,target_date:`2026-11-30`,status:`Pending`,mandays_budget:50,mandays_actual:0},{id:`ms-23`,name:`Sprint 3: Executive KPI Dashboard & Integration`,target_date:`2026-12-25`,status:`Pending`,mandays_budget:35,mandays_actual:0},{id:`ms-24`,name:`UAT & Sertifikasi BAST`,target_date:`2027-01-20`,status:`Pending`,mandays_budget:22,mandays_actual:0}],top_scheme:l(`30/40/30`,2442e5,`grand_total`),warranty_period:`90 Hari Pasca BAST`,sla_level:`99.5% Uptime SLA`,esign_status:`Terverifikasi Sah UU ITE`,signed_by_client:`Ir. Budi Santoso (Direktur Operasional)`,signed_by_company:`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,signed_at:`2026-10-15T11:00:00Z`},{id:`prj-3`,code:`PRJ-2026-003`,lead_id:`lead-2`,customer_id:`cust-2`,title:`Omnichannel POS & Multi-Store Inventory Engine`,customer_name:`PT Retail Nusantara Prima`,pm_name:`Asisyah Sarah Azzahra`,contract_number:`CT/2026/09/0004`,contract_value:185e6,budget_mandays:110,actual_mandays:110,status:`Completed`,start_date:`2026-08-01`,end_date:`2026-10-10`,progress_pct:100,milestones:[{id:`ms-31`,name:`Sprint 1: POS Cashier Core & Offline Sync`,target_date:`2026-08-25`,status:`Completed`,mandays_budget:40,mandays_actual:40},{id:`ms-32`,name:`Sprint 2: Multi-Warehouse Inventory`,target_date:`2026-09-20`,status:`Completed`,mandays_budget:45,mandays_actual:45},{id:`ms-33`,name:`Go-Live & BAST Sign-Off`,target_date:`2026-10-08`,status:`Completed`,mandays_budget:25,mandays_actual:25}],top_scheme:l(`50/50`,185e6,`grand_total`),warranty_period:`180 Hari Pasca BAST`,sla_level:`99.9% High Availability SLA`,esign_status:`Terverifikasi Sah UU ITE`,signed_by_client:`Sari Wijaya (Chief Commercial Officer)`,signed_by_company:`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,signed_at:`2026-08-01T09:30:00Z`}],_=[{id:`inv-1`,invoice_number:`INV/2026/10/0001`,contract_number:`CT/2026/10/0007`,project_title:`Custom Logistics & Fleet Tracking Telematics Platform`,customer_name:`PT Logistik Express Sentosa`,termin_no:1,termin_name:`Down Payment (DP 30%)`,amount:140540540,tax_amount:15459460,grand_total:156e6,status:`Paid`,due_date:`2026-10-12`,issued_at:`2026-10-05`,paid_at:`2026-10-10`,trigger_event:`Penandatanganan Kontrak CT/2026/10/0007`},{id:`inv-2`,invoice_number:`INV/2026/11/0002`,contract_number:`CT/2026/10/0007`,project_title:`Custom Logistics & Fleet Tracking Telematics Platform`,customer_name:`PT Logistik Express Sentosa`,termin_no:2,termin_name:`Progress Milestone Sprint 2 (40%)`,amount:187387387,tax_amount:20612613,grand_total:208e6,status:`Draft`,due_date:`2026-11-25`,issued_at:`2026-10-15`,trigger_event:`Penyelesaian Sprint 2 Telematics Engine`}],v=[{id:`wa-1`,contact_name:`Budi Santoso`,contact_phone:`+6281298765432`,company_name:`PT Maju Sejahtera Industri`,lead_id:`lead-1`,unread_count:1,last_message:`Halo mas, untuk demo live modul payroll Kamis jam berapa ya?`,last_message_time:`14:22`,ai_mode:`auto`,needs_human:!1,avatar_bg:`#059669`,messages:[{id:`msg-1`,sender:`client`,text:`Selamat pagi tim ASTACODE, kami sudah terima draft penawaran QT/2026/10/0012 Rev. 2 nya.`,timestamp:`09:15`,status:`read`},{id:`msg-2`,sender:`agent`,text:`Selamat pagi Pak Budi. Terima kasih banyak. Apakah ada poin klausul atau rincian modul yang perlu kami jelaskan lebih lanjut?`,timestamp:`09:20`,status:`read`},{id:`msg-3`,sender:`client`,text:`Untuk modul absensi GPS, apakah sudah include anti-fake GPS dan validasi selfie biometric?`,timestamp:`11:05`,status:`read`},{id:`msg-4`,sender:`ai`,text:`Halo Pak Budi! Betul sekali, pada RAB Item No. 4 sudah termasuk modul Absensi GPS Geofencing dengan deteksi anti-fake GPS/mock location serta liveness detection selfie terintegrasi.`,timestamp:`11:06`,status:`delivered`,ai_confidence:.94,intent:`tanya_fitur_absensi`,kb_source:`KB-04: Spesifikasi Modul Mobile ESS & Biometric`},{id:`msg-5`,sender:`client`,text:`Halo mas, untuk demo live modul payroll Kamis jam berapa ya?`,timestamp:`14:22`,status:`delivered`}]},{id:`wa-2`,contact_name:`Sari Wijaya`,contact_phone:`+6281122334455`,company_name:`PT Retail Nusantara Prima`,lead_id:`lead-2`,unread_count:0,last_message:`Baik kami diskusikan internal dulu ya mas.`,last_message_time:`Kemarin`,ai_mode:`suggest`,needs_human:!1,avatar_bg:`#4338ca`,messages:[{id:`msg-201`,sender:`client`,text:`Selamat siang, untuk penawaran ERP retail kemarin, terminnya bisa dipecah 4 kali?`,timestamp:`10:00`,status:`read`},{id:`msg-202`,sender:`agent`,text:`Bisa sekali Ibu Sari. Pada sistem kami terdapat opsi skema 30% DP, 30% Sprint Core, 30% UAT, dan 10% Retensi masa pemeliharaan garansi.`,timestamp:`10:15`,status:`read`},{id:`msg-203`,sender:`client`,text:`Baik kami diskusikan internal dulu ya mas.`,timestamp:`11:30`,status:`read`}]},{id:`wa-3`,contact_name:`Kevin Hartono`,contact_phone:`+6281877665544`,company_name:`PT Kuliner Sedap Group`,lead_id:`lead-5`,unread_count:1,last_message:`Kalau saya deal minggu ini, apa bisa dapat diskon 25% dari total penawaran?`,last_message_time:`10:05`,ai_mode:`suggest`,needs_human:!0,avatar_bg:`#d97706`,messages:[{id:`msg-301`,sender:`client`,text:`Pagi ASTACODE, saya Kevin dari Kuliner Sedap yang kemarin isi form.`,timestamp:`09:40`,status:`read`},{id:`msg-302`,sender:`client`,text:`Kalau saya deal minggu ini, apa bisa dapat diskon 25% dari total penawaran?`,timestamp:`10:05`,status:`delivered`}]}],y=[{id:`new_lead`,name:`New Lead`,order:1,probability:10,color:`#3B82F6`,bgLight:`bg-blue-50`,bgDark:`dark:bg-blue-950/40`,borderLight:`border-blue-200`,borderDark:`dark:border-blue-900/60`},{id:`follow_up`,name:`Follow Up`,order:2,probability:20,color:`#8B5CF6`,bgLight:`bg-purple-50`,bgDark:`dark:bg-purple-950/40`,borderLight:`border-purple-200`,borderDark:`dark:border-purple-900/60`},{id:`requirement`,name:`Requirement`,order:3,probability:35,color:`#06B6D4`,bgLight:`bg-cyan-50`,bgDark:`dark:bg-cyan-950/40`,borderLight:`border-cyan-200`,borderDark:`dark:border-cyan-900/60`},{id:`quotation`,name:`Quotation`,order:4,probability:55,color:`#F59E0B`,bgLight:`bg-amber-50`,bgDark:`dark:bg-amber-950/40`,borderLight:`border-amber-200`,borderDark:`dark:border-amber-900/60`},{id:`negotiation`,name:`Negosiasi`,order:5,probability:75,color:`#EC4899`,bgLight:`bg-pink-50`,bgDark:`dark:bg-pink-950/40`,borderLight:`border-pink-200`,borderDark:`dark:border-pink-900/60`},{id:`deal`,name:`Deal (Won)`,order:6,probability:100,color:`#10B981`,bgLight:`bg-emerald-50`,bgDark:`dark:bg-emerald-950/40`,borderLight:`border-emerald-200`,borderDark:`dark:border-emerald-900/60`},{id:`reject`,name:`Reject (Lost)`,order:7,probability:0,color:`#EF4444`,bgLight:`bg-rose-50`,bgDark:`dark:bg-rose-950/40`,borderLight:`border-rose-200`,borderDark:`dark:border-rose-900/60`}];function ee(e,t,n){if(e.stage_id===t)return{allowed:!0};if(t===`reject`)return!n||n.trim().length===0?{allowed:!1,message:`Perpindahan ke status Reject wajib menyertakan alasan penolakan (PRD BR-08).`}:{allowed:!0};if(t===`quotation`&&(!e.rab||e.rab.items.length===0))return{allowed:!1,message:`Tahap Quotation memerlukan estimasi RAB yang sudah dibuat (PRD BR-04). Buat RAB di tab Requirement/RAB terlebih dahulu.`};if(t===`deal`){if(!e.rab||e.rab.items.length===0)return{allowed:!1,message:`Tidak dapat Deal tanpa RAB yang valid dan disepakati.`};let t=e.quotations.find(t=>t.id===e.active_quotation_id)||e.quotations[0];if(t&&!t.top_scheme.is_balanced)return{allowed:!1,message:`Termin pembayaran (Setting TOP) pada Quotation belum balance (100%). Selesaikan alokasi termin di tab Quotation.`}}return{allowed:!0}}function te(e){let t=0,n=0,r=0,i=0,a={new_lead:{count:0,total:0,weighted:0},follow_up:{count:0,total:0,weighted:0},requirement:{count:0,total:0,weighted:0},quotation:{count:0,total:0,weighted:0},negotiation:{count:0,total:0,weighted:0},deal:{count:0,total:0,weighted:0},reject:{count:0,total:0,weighted:0}},o=new Map(y.map(e=>[e.id,e]));for(let s of e){let e=o.get(s.stage_id),c=e?e.probability:0,l=Math.round(s.est_value*c/100);t+=s.est_value,n+=l,s.stage_id!==`deal`&&s.stage_id!==`reject`&&r++,s.days_in_stage>=5&&i++,a[s.stage_id]&&(a[s.stage_id].count++,a[s.stage_id].total+=s.est_value,a[s.stage_id].weighted+=l)}return{totalValue:t,weightedValue:n,totalCount:e.length,activeLeadsCount:r,staleCount:i,stageCounts:a}}var ne=`devhouse_theme`;function re(){let e=localStorage.getItem(ne);return{isDarkMode:e?e===`dark`:window.matchMedia(`(prefers-color-scheme: dark)`).matches,activeView:`pipeline`,selectedLeadId:`lead-1`,viewMode:`kanban`,searchQuery:``,selectedPicFilter:`ALL`,selectedSourceFilter:`ALL`,leads:m,customers:d,activities:h,projects:g,invoices:_,waConversations:v,activeWaId:`wa-1`}}function ie(e){let t={...e},i=new Set;function a(){return t}function o(e){t={...t,...e},i.forEach(e=>e(t))}function s(e){return i.add(e),()=>i.delete(e)}function c(){return t.leads.find(e=>e.id===t.selectedLeadId)||null}function d(){let e=!t.isDarkMode;localStorage.setItem(ne,e?`dark`:`light`),e?document.documentElement.classList.add(`dark`):document.documentElement.classList.remove(`dark`),o({isDarkMode:e})}function f(e){o({selectedLeadId:e,activeView:`lead_detail`}),window.scrollTo({top:0,behavior:`smooth`})}function p(){o({activeView:`pipeline`})}function m(e){o(e===`pipeline`?{activeView:`pipeline`,viewMode:`kanban`}:{activeView:e}),window.scrollTo({top:0,behavior:`smooth`})}function h(e){o({viewMode:e,activeView:`pipeline`})}function g(e){o({viewMode:e,activeView:`pipeline`})}function _(e,n,r){let i=t.leads.find(t=>t.id===e);if(!i)return{success:!1,message:`Lead tidak ditemukan.`};let a=ee(i,n,r);return a.allowed?(o({leads:t.leads.map(t=>t.id===e?{...t,stage_id:n,days_in_stage:0,last_activity_at:new Date().toISOString()}:t)}),{success:!0}):{success:!1,message:a.message}}function v(e){let n={id:`cust-${Date.now()}`,code:`CUST-0${String(t.customers.length+1).padStart(2,`0`)}`,legal_name:e.legal_name||`PT Pelanggan Baru`,brand_name:e.brand_name||e.legal_name,industry:e.industry||`Manufaktur & Fabrikasi`,company_size:e.company_size||`50-100 Karyawan`,website:e.website,npwp:e.npwp,address:e.address||`Jakarta`,city:e.city||`Jakarta`,province:e.province||`DKI Jakarta`,lifecycle_status:e.lifecycle_status||`Prospect`,first_touch_source:e.first_touch_source||`Website`,health_score:e.health_score??85,contacts:e.contacts&&e.contacts.length>0?e.contacts:[{id:`cnt-${Date.now()}`,name:`PIC Utama`,job_title:`Manager Operasional`,email:`pic@perusahaan.com`,whatsapp_number:`+6281234567890`,role_type:`decision_maker`,is_primary:!0}]};return o({customers:[n,...t.customers]}),n}function y(e,n){let r,i=t.customers.find(e=>n.legal_name&&e.legal_name.toLowerCase()===n.legal_name.toLowerCase());i?r=i:(r={id:`cust-${Date.now()}`,code:`CUST-0${t.customers.length+1}`,legal_name:n.legal_name||`PT Customer Baru`,brand_name:n.brand_name||n.legal_name,industry:n.industry||`Teknologi & Bisnis`,company_size:n.company_size||`50-100 Karyawan`,website:n.website,address:n.address||`Jakarta`,city:n.city||`Jakarta`,province:n.province||`DKI Jakarta`,lifecycle_status:`Prospect`,first_touch_source:e.source||`Website`,health_score:80,contacts:n.contacts&&n.contacts.length>0?n.contacts:[{id:`cnt-${Date.now()}`,name:`PIC Utama`,job_title:`Manager`,email:`contact@perusahaan.com`,whatsapp_number:`+6281234567890`,role_type:`decision_maker`,is_primary:!0}]},o({customers:[r,...t.customers]}));let a={id:`lead-${Date.now()}`,code:`LD-2026-00${t.leads.length+1}`,title:e.title||`Prospek Proyek Baru`,project_type:e.project_type||`Web Application`,customer_id:r.id,customer:r,primary_contact:r.contacts[0],est_value:e.est_value||15e7,target_closing:e.target_closing||`2026-12-31`,sales_pic:e.sales_pic||`Asisyah Sarah Azzahra`,sales_pic_avatar:`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`,priority:e.priority||`Medium`,stage_id:`new_lead`,source:e.source||`Website`,sub_source:e.sub_source,campaign:e.campaign,referrer_name:e.referrer_name,tags:e.tags||[`Inbound`],created_at:new Date().toISOString(),last_activity_at:new Date().toISOString(),days_in_stage:0,ai_summary:`Lead baru masuk ke dalam pipeline penjualan. Siap untuk tahap kualifikasi awal.`,next_action:`Hubungi PIC via WhatsApp atau jadwalkan Google Meet.`,notes_count:0,documents_count:0,quotations:[]};return o({leads:[a,...t.leads]}),a}function te(e,n){o({leads:t.leads.map(t=>t.id===e?{...t,...n,last_activity_at:new Date().toISOString()}:t)})}function re(e){o({activities:[{...e,id:`act-${Date.now()}`,created_at:new Date().toISOString()},...t.activities],leads:t.leads.map(t=>t.id===e.lead_id?{...t,last_activity_at:new Date().toISOString()}:t)})}async function ie(e,i,a){let s=n(r.map((e,t)=>{let n=1;a.complexity===`Enterprise`&&(n=1.25),a.complexity===`Basic`&&(n=.8);let r=Math.max(2,Math.round(e.qty*n));return{...e,id:`rab-gen-${Date.now()}-${t}`,qty:r,subtotal:r*e.unit_price,source:`AI`,description:`${e.description} (Disesuaikan dengan kebutuhan: "${i.slice(0,50)}...")`}}),a.contingency_pct||10,0,11,1,`draft`);return o({leads:t.leads.map(t=>t.id===e?{...t,rab:s,est_value:s.grand_total,stage_id:t.stage_id===`new_lead`||t.stage_id===`follow_up`?`requirement`:t.stage_id,ai_summary:`RAB AI berhasil digenerate berdasarkan prompt kebutuhan: "${i}". Menghasilkan 15 modul terstruktur senilai ${s.grand_total.toLocaleString(`id-ID`)} IDR.`,last_activity_at:new Date().toISOString()}:t)}),s}function b(e,r,i=10,a=0){o({leads:t.leads.map(t=>{if(t.id!==e)return t;let o=n(r,i,a,11,(t.rab?.version||1)+1,`draft`);return{...t,rab:o,est_value:o.grand_total}})})}function ae(e,n){o({leads:t.leads.map(t=>t.id!==e||!t.rab?t:{...t,rab:{...t.rab,status:`reviewed`,reviewed_by:n,reviewed_at:new Date().toISOString()}})})}function oe(e,n,r,i=`Rev. 1`){let a=t.leads.find(t=>t.id===e);if(!a||!a.rab)throw Error(`Lead or RAB not found`);let s=`QT/2026/10/00${(a.quotations?.length||0)+12}`,c=`MOU/2026/10/00${(a.quotations?.length||0)+12}`,l={id:`quo-${Date.now()}`,lead_id:e,number:s,mou_number:c,version_label:i,status:`Follow Up`,valid_days:30,created_at:new Date().toISOString(),sent_at:new Date().toISOString(),intro_letter:r,rab_summary:a.rab,top_scheme:n,clauses:u};return o({leads:t.leads.map(t=>{if(t.id!==e)return t;let n=t.quotations||[];return{...t,stage_id:`quotation`,quotations:[l,...n],active_quotation_id:l.id,last_activity_at:new Date().toISOString()}})}),l}function se(e,n,r,i){let a=t.leads.find(t=>t.id===e);if(!a)return{success:!1,message:`Lead tidak ditemukan`};if(r===`Reject`&&(!i||i.trim().length===0))return{success:!1,message:`Wajib mengisi alasan penolakan (Reject Reason).`};let s=a.quotations.find(e=>e.id===n);if(!s)return{success:!1,message:`Quotation tidak ditemukan`};let c=a.stage_id;r===`Deal`?c=`deal`:r===`Reject`?c=`reject`:r===`Negosiasi`?c=`negotiation`:r===`Follow Up`&&(c=`quotation`);let l=t.leads.map(t=>{if(t.id!==e)return t;let a=t.quotations.map(e=>e.id===n?{...e,status:r,reject_reason:i,signed_at:r===`Deal`?new Date().toISOString():e.signed_at,signed_by_client:r===`Deal`?t.primary_contact.name:e.signed_by_client,signed_by_company:r===`Deal`?`Super Admin ASTACODE`:e.signed_by_company}:e);return{...t,stage_id:c,quotations:a,last_activity_at:new Date().toISOString()}}),u=t.customers,d=t.projects,f=t.invoices;if(r===`Deal`){u=t.customers.map(e=>e.id===a.customer_id?{...e,lifecycle_status:`Active`}:e);let e=`CT/2026/10/00${t.projects.length+8}`;d=[{id:`prj-${Date.now()}`,code:`PRJ-2026-00${t.projects.length+2}`,lead_id:a.id,customer_id:a.customer_id,title:a.title,customer_name:a.customer.legal_name,pm_name:`Reza Fahlevi, PMP`,contract_number:e,contract_value:s.top_scheme.total_amount,budget_mandays:s.rab_summary.total_mandays,actual_mandays:0,status:`Active`,start_date:new Date().toISOString().split(`T`)[0],end_date:new Date(Date.now()+7776e6).toISOString().split(`T`)[0],progress_pct:0,milestones:s.top_scheme.items.map((e,t)=>({id:`ms-${Date.now()}-${t}`,name:`Milestone: ${e.name}`,target_date:new Date(Date.now()+(t+1)*30*24*3600*1e3).toISOString().split(`T`)[0],status:t===0?`In Progress`:`Pending`,mandays_budget:Math.round(s.rab_summary.total_mandays/s.top_scheme.items.length),mandays_actual:0})),top_scheme:s.top_scheme},...t.projects];let n=s.top_scheme.items[0];n&&(f=[{id:`inv-${Date.now()}`,invoice_number:`INV/2026/10/00${t.invoices.length+3}`,contract_number:e,project_title:a.title,customer_name:a.customer.legal_name,termin_no:1,termin_name:n.name,amount:Math.round(n.calculated_amount/1.11),tax_amount:Math.round(n.calculated_amount/1.11*.11),grand_total:n.calculated_amount,status:`Draft`,due_date:new Date(Date.now()+6048e5).toISOString().split(`T`)[0],issued_at:new Date().toISOString().split(`T`)[0],trigger_event:`Penandatanganan Kontrak & Kesepakatan Deal`},...t.invoices])}return o({leads:l,customers:u,projects:d,invoices:f}),{success:!0}}function ce(e,n){let r=t.projects.find(t=>t.contract_number===e);if(!r)return;let i=r.top_scheme.items.find(e=>e.term_no===n);i&&o({invoices:[{id:`inv-${Date.now()}`,invoice_number:`INV/2026/11/00${t.invoices.length+1}`,contract_number:e,project_title:r.title,customer_name:r.customer_name,termin_no:n,termin_name:i.name,amount:Math.round(i.calculated_amount/1.11),tax_amount:Math.round(i.calculated_amount/1.11*.11),grand_total:i.calculated_amount,status:`Draft`,due_date:new Date(Date.now()+12096e5).toISOString().split(`T`)[0],issued_at:new Date().toISOString().split(`T`)[0],trigger_event:`Trigger Termin ${n}: ${i.trigger_label}`},...t.invoices],projects:t.projects.map(t=>t.contract_number===e?{...t,top_scheme:{...t.top_scheme,items:t.top_scheme.items.map(e=>e.term_no===n?{...e,status:`Invoiced`,is_locked:!0}:e)}}:t)})}function le(e,n,r){o({projects:t.projects.map(t=>t.id===e?{...t,top_scheme:{...t.top_scheme,items:t.top_scheme.items.map(e=>e.id===n?{...e,...r}:e)}}:t)})}function ue(e){let n={id:`prj-${Date.now()}`,code:`PRJ-2026-${String(t.projects.length+1).padStart(3,`0`)}`,lead_id:e.lead_id||`lead-${Date.now()}`,customer_id:e.customer_id||`cust-1`,title:e.title||`Proyek Solusi Software Kustom`,customer_name:e.customer_name||`Klien ASTACODE`,pm_name:e.pm_name||`Asisyah Sarah Azzahra`,contract_number:e.contract_number||`CT/2026/10/${String(t.projects.length+10).padStart(4,`0`)}`,contract_value:e.contract_value||15e7,budget_mandays:e.budget_mandays||80,actual_mandays:e.actual_mandays||0,status:e.status||`Active`,start_date:e.start_date||new Date().toISOString().split(`T`)[0],end_date:e.end_date||new Date(Date.now()+7776e6).toISOString().split(`T`)[0],progress_pct:e.progress_pct||0,milestones:e.milestones||[{id:`ms-${Date.now()}-1`,name:`Sprint 1: System Blueprint & Core DB`,target_date:new Date(Date.now()+1728e6).toISOString().split(`T`)[0],status:`In Progress`,mandays_budget:30,mandays_actual:5},{id:`ms-${Date.now()}-2`,name:`Sprint 2: Core Feature Implementation`,target_date:new Date(Date.now()+432e7).toISOString().split(`T`)[0],status:`Pending`,mandays_budget:35,mandays_actual:0},{id:`ms-${Date.now()}-3`,name:`UAT & BAST Sign-Off`,target_date:new Date(Date.now()+6912e6).toISOString().split(`T`)[0],status:`Pending`,mandays_budget:15,mandays_actual:0}],top_scheme:e.top_scheme||l(`30/40/30`,e.contract_value||15e7,`grand_total`),warranty_period:e.warranty_period||`90 Hari Pasca BAST`,sla_level:e.sla_level||`99.5% Uptime SLA`,esign_status:e.esign_status||`Terverifikasi Sah UU ITE`,signed_by_client:e.signed_by_client||`Direktur Klien`,signed_by_company:`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,signed_at:new Date().toISOString()};return o({projects:[n,...t.projects]}),n}function x(e,n){let r={id:`msg-${Date.now()}`,sender:`agent`,text:n,timestamp:new Date().toLocaleTimeString([],{hour:`2-digit`,minute:`2-digit`}),status:`sent`};o({waConversations:t.waConversations.map(t=>t.id===e?{...t,last_message:n,last_message_time:r.timestamp,messages:[...t.messages,r]}:t)})}function de(e,n){o({waConversations:t.waConversations.map(t=>t.id===e?{...t,ai_mode:n}:t)})}return{getState:a,setState:o,subscribe:s,getSelectedLead:c,toggleDarkMode:d,navigateToLeadDetail:f,navigateBackToPipeline:p,setActiveView:m,setViewMode:h,setPipelineViewMode:g,updateLeadStage:_,addNewCustomer:v,addNewLead:y,updateLead:te,addActivity:re,generateAiRab:ie,updateLeadRabItems:b,markRabReviewed:ae,saveQuotationWithTop:oe,updateQuotationStatus:se,issueInvoiceFromTerm:ce,updateContractTopTerm:le,addNewProject:ue,sendWaMessage:x,setWaAiMode:de}}var b=ie(re()),ae={kanban:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>`,building2:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>`,briefcase:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`,dollarSign:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,shieldCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>`,messageSquare:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,barChart3:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>`,sparkles:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>`,sun:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`,moon:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>`},oe=[{title:`CRM & PENJUALAN`,items:[{id:`pipeline`,label:`Sales Pipeline`,subLabel:`Kanban & Table View`,icon:`kanban`,badgeKey:`activeLeads`,badgeColor:`#6366f1`},{id:`customers`,label:`Master Customer`,subLabel:`Account & Contacts 360°`,icon:`building2`}]},{title:`ERP & DELIVERY`,items:[{id:`projects`,label:`Proyek & Delivery`,subLabel:`Milestone & Mandays`,icon:`briefcase`}]},{title:`KONTRAK & KEUANGAN`,items:[{id:`finance_top`,label:`Invoice & Setting TOP`,subLabel:`Termin Pembayaran`,icon:`dollarSign`,badgeKey:`pendingInvoices`,badgeColor:`#f59e0b`},{id:`contracts_legal`,label:`Kontrak & Legalitas`,subLabel:`Library 22 Pasal & SPK`,icon:`shieldCheck`}]},{title:`CLIENT CARE & AI`,items:[{id:`client_care`,label:`WhatsApp Client Care`,subLabel:`AI Auto-Reply & Inbox`,icon:`messageSquare`,badgeKey:`unreadWa`,badgeColor:`#10b981`}]},{title:`EKSEKUTIF`,items:[{id:`reports`,label:`Laporan & Analytics`,subLabel:`Sales Funnel & ROI`,icon:`barChart3`}]}];function se(e){return{activeLeads:(e.leads||[]).filter(e=>e.stage_id!==`deal`&&e.stage_id!==`reject`).length,unreadWa:(e.waConversations||[]).reduce((e,t)=>e+(t.unread_count||0),0),pendingInvoices:(e.invoices||[]).filter(e=>e.status===`Draft`).length}}function ce(){let e=b.getState(),{activeView:t,isDarkMode:n}=e,r=se(e);return`
    <aside class="sidebar" id="sidebar">
      <!-- 1. Brand Header -->
      <div class="sidebar-brand-header">
        <div class="sidebar-logo-wrap" style="display:flex;align-items:center;gap:12px;">
          <!-- Rounded Purple Avatar with online green dot -->
          <div class="sidebar-logo-icon" style="position:relative;width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#ffffff;font-weight:900;font-size:14px;box-shadow:0 4px 12px rgba(99,102,241,.3);flex-shrink:0;border:1px solid rgba(255,255,255,0.3);">
            <span>AST</span>
            <span class="sidebar-logo-pulse" style="position:absolute;bottom:-2px;right:-2px;width:12px;height:12px;border-radius:50%;background:#34d399;border:2px solid #1e2337;"></span>
          </div>
          <!-- Brand Title & Tag Stack -->
          <div class="sidebar-brand-info" style="display:flex;flex-direction:column;gap:2px;">
            <div class="sidebar-brand-row" style="display:flex;align-items:center;gap:6px;">
              <span class="sidebar-brand-name" style="font-size:16px;font-weight:800;color:#ffffff;letter-spacing:0.05em;font-family:var(--font-sans);line-height:1.2;">ASTACODE</span>
              <span class="sidebar-brand-tag" style="padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;text-transform:uppercase;background:rgba(99,102,241,.25);color:#c7d2fe;border:1px solid rgba(165,180,252,.3);letter-spacing:0.04em;">ERP·CRM</span>
            </div>
            <p class="sidebar-brand-company" style="font-size:10px;font-weight:500;color:#94a3b8;letter-spacing:0.04em;margin:0;line-height:1.2;">PT ASTACODE SOLUSINDO TEKNOLOGI</p>
          </div>
        </div>
      </div>

      <!-- 2. Navigation Menu -->
      <nav class="sidebar-nav-scroll">
        ${oe.map(e=>{let n=e.items.map(e=>{let n=t===e.id,i=0;e.badgeKey===`activeLeads`&&(i=r.activeLeads),e.badgeKey===`unreadWa`&&(i=r.unreadWa),e.badgeKey===`pendingInvoices`&&(i=r.pendingInvoices);let a=e.badgeKey&&i>0?`<span class="sidebar-nav-badge" style="background:${e.badgeColor};color:#ffffff;border-radius:9999px;padding:2px 8px;font-size:10px;font-weight:700;line-height:1;">${i}</span>`:``,o=n?`<span class="sidebar-active-dot" style="width:6px;height:6px;border-radius:50%;background:#ffffff;margin-left:6px;flex-shrink:0;"></span>`:``;return`
        <button
          class="sidebar-nav-item ${n?`active`:``}"
          data-view="${e.id}"
          title="${e.label}"
        >
          <div style="display:flex;align-items:center;min-width:0;flex:1;">
            <span class="sidebar-nav-icon-wrap ${n?`active`:``}">
              ${ae[e.icon]||``}
            </span>
            <span class="sidebar-nav-label">
              <span class="sidebar-nav-label-main">${e.label}</span>
              <span class="sidebar-nav-label-sub" style="color:${n?`#e0e7ff`:`#64748b`};">${e.subLabel}</span>
            </span>
          </div>
          <span class="sidebar-nav-end">
            ${a}
            ${o}
          </span>
        </button>`}).join(``);return`
      <div class="sidebar-nav-group">
        <div class="sidebar-section-label" style="font-size:11px;font-weight:700;color:#94a3b8 !important;letter-spacing:0.05em;text-transform:uppercase;padding:6px 8px 4px;">
          ${e.title}
        </div>
        ${n}
      </div>`}).join(``)}

        <!-- AI Assistant Callout Box -->
        <div class="sidebar-ai-callout">
          <div class="sidebar-ai-callout-title">
            ${ae.sparkles}
            <span>AI Requirement & Audio Notes</span>
          </div>
          <p class="sidebar-ai-callout-desc">
            Generate mandays, transkrip voice note audio ke dokumen rangkuman, dan paket penawaran MoU.
          </p>
        </div>
      </nav>

      <!-- 3. User Footer & Theme Toggle -->
      <div class="sidebar-user-footer">
        <div class="sidebar-user-row">
          <div class="sidebar-user-avatar-wrap">
            <img
              src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80"
              alt="Asisyah Sarah Azzahra"
              class="sidebar-user-avatar"
              style="width:36px !important;height:36px !important;border-radius:50% !important;object-fit:cover !important;display:block;box-shadow:0 0 0 2px rgba(129,140,248,.5);"
              onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
            />
            <div class="sidebar-user-avatar-fallback" style="display:none;width:36px;height:36px;border-radius:50%;">AS</div>
            <span class="sidebar-user-online-dot"></span>
          </div>
          <div class="sidebar-user-info">
            <div class="sidebar-user-name" title="Asisyah Sarah Azzahra">Asisyah Sarah Azzahra</div>
            <div class="sidebar-user-role">
              <span class="sidebar-user-role-badge">Super Admin</span>
              <span>• Project Manager</span>
            </div>
          </div>
          <button class="sidebar-theme-btn" id="sidebar-theme-toggle" title="${n?`Beralih ke Light Mode`:`Beralih ke Dark Mode`}">
            ${n?ae.sun:ae.moon}
          </button>
        </div>
      </div>
    </aside>
    <!-- Mobile Overlay -->
    <div class="sidebar-mobile-overlay" id="sidebar-mobile-overlay"></div>
  `}function le(){document.querySelectorAll(`.sidebar-nav-item[data-view]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.view;b.setState({activeView:t});let n=document.getElementById(`sidebar`),r=document.getElementById(`sidebar-mobile-overlay`);n&&n.classList.remove(`mobile-open`),r&&r.classList.remove(`visible`)})});let e=document.getElementById(`sidebar-theme-toggle`);e&&e.addEventListener(`click`,()=>b.toggleDarkMode());let t=document.getElementById(`sidebar-mobile-overlay`);t&&t.addEventListener(`click`,()=>{let e=document.getElementById(`sidebar`);e&&e.classList.remove(`mobile-open`),t.classList.remove(`visible`)})}function ue(e){return e.innerHTML=ce(),le(),b.subscribe(()=>{e.innerHTML=ce(),le()})}var x={menu:`<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>`,arrowLeft:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>`,chevronRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>`,search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,kanban:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>`,table:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>`,layers:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>`,sun:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`,moon:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`},de={pipeline:`Sales Pipeline`,customers:`Master Customer`,projects:`Proyek Delivery`,finance_top:`Setting TOP`,contracts_legal:`Kontrak & Legalitas`,client_care:`WhatsApp Care`,reports:`Laporan`};function fe(){let{activeView:e,selectedLeadId:t,leads:n,viewMode:r,isDarkMode:i}=b.getState(),a=n.find(e=>e.id===t),o=``;o=e===`lead_detail`&&a?`
      <div class="header-breadcrumb">
        <button id="header-back-pipeline" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;">
          ${x.arrowLeft}
          <span>Pipeline</span>
        </button>
        <span style="color:var(--text-muted);display:flex;align-items:center;">${x.chevronRight}</span>
        <span class="badge badge-purple" style="font-family:var(--font-mono);font-weight:700;">${a.code}</span>
        <span class="header-breadcrumb-title" style="max-width:260px;" title="${a.title}">${a.title}</span>
      </div>
    `:`
      <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;flex-wrap:nowrap;">
        <h1 class="header-breadcrumb-title" style="font-size:15px;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;margin:0;">
          ${de[e]||`ASTACODE CRM`}
        </h1>
        <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.25);">
          <span class="live-dot"></span>
          <span>Live</span>
        </span>
        <span class="header-org-pill" style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:600;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-secondary);">
          <span style="color:var(--indigo-600);font-weight:800;">ASTACODE</span>
          <span style="color:var(--text-muted);opacity:0.6;">|</span>
          <span>Asisyah Sarah Azzahra (Super Admin & PM)</span>
        </span>
      </div>
    `;let s=``;return e===`pipeline`&&(s=`
      <div class="view-mode-toggle" style="display:flex;align-items:center;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-xl);padding:3px;gap:2px;">
        <button
          class="btn-view-mode ${r===`kanban`?`active`:``}"
          data-mode="kanban"
          style="display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:var(--radius-lg);border:none;font-size:12px;cursor:pointer;transition:all var(--transition);${r===`kanban`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);font-weight:600;`}"
          title="Tampilan Kanban Board"
        >
          ${x.kanban}
          <span>Kanban</span>
        </button>
        <button
          class="btn-view-mode ${r===`table`?`active`:``}"
          data-mode="table"
          style="display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:var(--radius-lg);border:none;font-size:12px;cursor:pointer;transition:all var(--transition);${r===`table`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);font-weight:600;`}"
          title="Tampilan Tabel Detail"
        >
          ${x.table}
          <span>Table</span>
        </button>
        <button
          class="btn-view-mode ${r===`workflow`?`active`:``}"
          data-mode="workflow"
          style="display:flex;align-items:center;gap:6px;padding:5px 12px;border-radius:var(--radius-lg);border:none;font-size:12px;cursor:pointer;transition:all var(--transition);${r===`workflow`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);font-weight:600;`}"
          title="Tracking Workflow & Tahap File Dokumen"
        >
          ${x.layers}
          <span>Workflow & File</span>
        </button>
      </div>
    `),`
    <header class="header" id="app-header">
      <!-- Left side: Hamburger (mobile) + Breadcrumbs / Title -->
      <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
        <button id="header-mobile-toggle" class="header-mobile-btn" title="Buka Menu Navigasi">
          ${x.menu}
        </button>
        ${o}
      </div>

      <!-- Right side: Search, View Mode, Theme Toggle, + Lead Baru -->
      <div class="header-actions">
        <!-- Quick Search Bar trigger -->
        <button id="header-search-trigger" class="header-search-btn" title="Cari cepat (Ctrl+K)" style="min-width:260px;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:7px 14px;border-radius:12px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-secondary);font-size:12px;cursor:pointer;transition:all var(--transition);">
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="color:var(--indigo-500);display:flex;align-items:center;">${x.search}</span>
            <span style="color:var(--text-muted);font-weight:500;">Cari cepat lead, customer...</span>
          </div>
          <kbd class="kbd" style="padding:2px 6px;border-radius:4px;background:var(--bg-card);border:1px solid var(--border);font-family:var(--font-mono);font-size:10px;color:var(--text-muted);box-shadow:var(--shadow-xs);">Ctrl+K</kbd>
        </button>

        <!-- View Mode Switcher -->
        ${s}

        <!-- Dark / Light Theme Toggle -->
        <button id="header-theme-toggle" class="btn-icon" title="${i?`Beralih ke Light Mode`:`Beralih ke Dark Mode`}">
          ${i?x.sun:x.moon}
        </button>

        <!-- + Lead Baru Button -->
        <button id="header-new-lead-btn" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;font-weight:700;font-size:12px;">
          ${x.plus}
          <span>+ Lead Baru</span>
        </button>
      </div>
    </header>
  `}function pe(){let e=document.getElementById(`header-mobile-toggle`);e&&e.addEventListener(`click`,()=>{let e=document.getElementById(`sidebar`),t=document.getElementById(`sidebar-mobile-overlay`);e&&e.classList.toggle(`mobile-open`),t&&t.classList.toggle(`visible`)});let t=document.getElementById(`header-back-pipeline`);t&&t.addEventListener(`click`,()=>{b.navigateBackToPipeline()});let n=document.getElementById(`header-search-trigger`);n&&n.addEventListener(`click`,()=>{window.dispatchEvent(new CustomEvent(`open-command-palette`))}),document.querySelectorAll(`.btn-view-mode[data-mode]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.mode;b.setViewMode(t)})});let r=document.getElementById(`header-theme-toggle`);r&&r.addEventListener(`click`,()=>{b.toggleDarkMode()});let i=document.getElementById(`header-new-lead-btn`);i&&i.addEventListener(`click`,()=>{window.dispatchEvent(new CustomEvent(`open-new-lead-modal`))})}function me(e){return e.innerHTML=fe(),pe(),b.subscribe(()=>{e.innerHTML=fe(),pe()})}var he=!1,ge=``,_e={search:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,close:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,arrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`};function ve(){he=!0,ge=``,ye();let e=document.getElementById(`command-palette-input`);e&&e.focus()}function S(){he=!1,ge=``;let e=document.getElementById(`command-palette-modal`);e&&(e.classList.add(`hidden`),e.innerHTML=``)}function ye(){let e=document.getElementById(`command-palette-modal`);if(e||(e=document.createElement(`div`),e.id=`command-palette-modal`,document.body.appendChild(e)),!he){e.classList.add(`hidden`),e.innerHTML=``;return}e.classList.remove(`hidden`);let{leads:t,customers:n}=b.getState(),r=ge.toLowerCase().trim(),i=t.filter(e=>e.title.toLowerCase().includes(r)||e.code.toLowerCase().includes(r)||e.customer&&e.customer.legal_name.toLowerCase().includes(r)||e.customer&&e.customer.industry&&e.customer.industry.toLowerCase().includes(r)||e.sales_pic.toLowerCase().includes(r)),o=(n||[]).filter(e=>e.legal_name.toLowerCase().includes(r)||e.brand_name&&e.brand_name.toLowerCase().includes(r)||e.code&&e.code.toLowerCase().includes(r)||e.industry&&e.industry.toLowerCase().includes(r)),s=``;s=i.length===0&&o.length===0?`
      <div style="padding:32px 16px;text-align:center;font-size:12px;color:var(--text-muted);">
        Tidak ada data yang cocok dengan "${ge}".
      </div>
    `:i.map(e=>{let t=a(e.est_value||0),n=e.code.slice(-3),r=e.customer?e.customer.legal_name:`Customer`;return`
        <div
          class="command-item"
          data-lead-id="${e.id}"
          style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:var(--radius-lg);cursor:pointer;transition:all var(--transition);border-bottom:1px solid var(--border-light);"
        >
          <div style="display:flex;align-items:center;gap:12px;min-width:0;">
            <div style="width:36px;height:36px;border-radius:var(--radius-md);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;color:var(--indigo-500);font-weight:700;font-size:12px;flex-shrink:0;">
              ${n}
            </div>
            <div style="min-width:0;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:13px;font-weight:700;color:var(--text-primary);">${e.title}</span>
                <span class="badge badge-slate" style="font-family:var(--font-mono);font-size:10px;">${e.code}</span>
              </div>
              <div style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-top:2px;">
                <span>${r}</span>
                <span>•</span>
                <span style="font-weight:700;color:var(--emerald-500);">${t}</span>
                <span>•</span>
                <span>PIC: ${e.sales_pic}</span>
              </div>
            </div>
          </div>

          <div class="command-item-action" style="display:flex;align-items:center;gap:4px;color:var(--indigo-500);font-size:12px;font-weight:600;flex-shrink:0;margin-left:12px;">
            <span>Buka Lead</span>
            ${_e.arrowRight}
          </div>
        </div>
      `}).join(``)+o.map(e=>`
      <div
        class="command-cust-item"
        data-cust-id="${e.id}"
        style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:var(--radius-lg);cursor:pointer;transition:all var(--transition);border-bottom:1px solid var(--border-light);"
      >
        <div style="display:flex;align-items:center;gap:12px;min-width:0;">
          <div style="width:36px;height:36px;border-radius:var(--radius-md);background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);display:flex;align-items:center;justify-content:center;color:#059669;font-weight:700;font-size:12px;flex-shrink:0;">
            🏢
          </div>
          <div style="min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:13px;font-weight:700;color:var(--text-primary);">${e.legal_name}</span>
              <span class="badge badge-emerald" style="font-family:var(--font-mono);font-size:10px;">${e.code}</span>
            </div>
            <div style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-top:2px;">
              <span>${e.industry}</span>
              <span>•</span>
              <span>${e.city}</span>
              <span>•</span>
              <span style="font-weight:600;color:#059669;">Skor Relasi: ${e.health_score||85}</span>
            </div>
          </div>
        </div>

        <div class="command-item-action" style="display:flex;align-items:center;gap:4px;color:#059669;font-size:12px;font-weight:600;flex-shrink:0;margin-left:12px;">
          <span>Buka Customer</span>
          ${_e.arrowRight}
        </div>
      </div>
    `).join(``),e.innerHTML=`
    <div class="command-palette-overlay" id="command-palette-backdrop">
      <div class="command-palette" style="max-width:640px;" onclick="event.stopPropagation();">
        <!-- Search Input Bar -->
        <div class="command-input-wrapper" style="padding:12px 18px;">
          <span style="color:var(--indigo-500);display:flex;align-items:center;">${_e.search}</span>
          <input
            id="command-palette-input"
            class="command-input"
            type="text"
            placeholder="Ketik untuk mencari Lead, Customer, Quotation, PIC... (Tekan Esc untuk tutup)"
            value="${ge}"
            autocomplete="off"
            style="font-size:14px;"
          />
          <button id="command-palette-close-btn" class="modal-close" title="Tutup (Esc)">
            ${_e.close}
          </button>
        </div>

        <!-- Quick Actions Bar -->
        <div style="padding:8px 16px;background:var(--bg-secondary);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;overflow-x:auto;">
          <span style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;">Aksi Cepat:</span>
          <button id="cmd-action-new-lead" class="btn btn-sm" style="background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);color:var(--indigo-500);font-weight:600;padding:3px 10px;">
            + Buat Lead Baru
          </button>
          <button id="cmd-action-setting-top" class="btn btn-sm btn-secondary" style="font-weight:500;padding:3px 10px;">
            Setting TOP Invoice
          </button>
          <button id="cmd-action-client-care" class="btn btn-sm btn-secondary" style="font-weight:500;padding:3px 10px;">
            WhatsApp Client Care
          </button>
        </div>

        <!-- Search Results List -->
        <div class="command-results" id="command-results-list" style="max-height:360px;">
          ${s}
        </div>
      </div>
    </div>
  `,be()}function be(){let e=document.getElementById(`command-palette-backdrop`);e&&e.addEventListener(`click`,()=>{S()});let t=document.getElementById(`command-palette-close-btn`);t&&t.addEventListener(`click`,()=>{S()});let n=document.getElementById(`command-palette-input`);n&&(n.addEventListener(`input`,e=>{ge=e.target.value,ye();let t=document.getElementById(`command-palette-input`);t&&(t.focus(),t.selectionStart=t.selectionEnd=t.value.length)}),n.addEventListener(`keydown`,e=>{e.key===`Escape`&&S()}));let r=document.getElementById(`cmd-action-new-lead`);r&&r.addEventListener(`click`,()=>{S(),window.dispatchEvent(new CustomEvent(`open-new-lead-modal`))});let i=document.getElementById(`cmd-action-setting-top`);i&&i.addEventListener(`click`,()=>{S(),b.setState({activeView:`finance_top`})});let a=document.getElementById(`cmd-action-client-care`);a&&a.addEventListener(`click`,()=>{S(),b.setState({activeView:`client_care`})}),document.querySelectorAll(`.command-item[data-lead-id]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.leadId;t&&(b.navigateToLeadDetail(t),S())})}),document.querySelectorAll(`.command-cust-item[data-cust-id]`).forEach(e=>{e.addEventListener(`click`,()=>{b.setActiveView(`customers`),S()})})}function xe(){window.addEventListener(`keydown`,e=>{(e.metaKey||e.ctrlKey)&&e.key.toLowerCase()===`k`?(e.preventDefault(),he?S():ve()):e.key===`Escape`&&he&&S()}),window.addEventListener(`open-command-palette`,()=>{ve()})}var Se=!1,Ce={close:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`,building:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>`},C={title:``,projectType:`Web & Mobile App`,customProjectType:``,estValue:25e7,targetClosing:`2026-12-15`,salesPic:`Asisyah Sarah Azzahra`,priority:`High`,source:`Instagram`,customSource:``,subSource:`Direct Message`,referrerName:``,tagsInput:`Custom App, HRIS`,customerMode:`new`,selectedCustomerId:``,companyName:``,industry:`Manufaktur & Fabrikasi`,customIndustry:``,companySize:`100-250 Karyawan`,address:`Kawasan Industri Sentul`,city:`Bogor`,province:`Jawa Barat`,picName:``,picJobTitle:`Direktur Operasional`,picEmail:``,picPhone:``};function we(){let{customers:e}=b.getState();Se=!0,C.selectedCustomerId=e[0]?.id||``,Ee();let t=document.getElementById(`nl-title`);t&&t.focus()}function Te(){Se=!1;let e=document.getElementById(`new-lead-modal-container`);e&&(e.classList.add(`hidden`),e.innerHTML=``)}function Ee(){let e=document.getElementById(`new-lead-modal-container`);if(e||(e=document.createElement(`div`),e.id=`new-lead-modal-container`,document.body.appendChild(e)),!Se){e.classList.add(`hidden`),e.innerHTML=``;return}e.classList.remove(`hidden`);let{customers:t}=b.getState();e.innerHTML=`
    <div class="modal-overlay" id="new-lead-modal-backdrop" style="background:rgba(15,23,42,.65);backdrop-filter:blur(4px);">
      <div class="modal modal-lg" onclick="event.stopPropagation();" style="max-height:88vh;display:flex;flex-direction:column;">
        <!-- Header -->
        <div class="modal-header" style="background:var(--bg-secondary);border-bottom:1px solid var(--border);padding:16px 24px;">
          <div>
            <h2 class="modal-title" style="font-size:16px;font-weight:800;color:var(--text-primary);">
              Tambah Lead & Registrasi Customer Baru
            </h2>
            <p style="font-size:12px;color:var(--text-muted);margin-top:2px;">
              Data customer otomatis terintegrasi ke Master Customer 360° (PRD Section 8.2).
            </p>
          </div>
          <button id="nl-close-btn" class="modal-close" title="Tutup Modal (Esc)">
            ${Ce.close}
          </button>
        </div>

        <!-- Form Body -->
        <form id="new-lead-form" class="modal-body" style="padding:24px;overflow-y:auto;display:flex;flex-direction:column;gap:24px;">
          
          <!-- SECTION 1: Lead Info -->
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:6px;">
              <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--indigo-500);">
                1. Informasi Lead & Kebutuhan Proyek
              </h3>
            </div>

            <div class="form-group">
              <label class="form-label" for="nl-title">Judul Lead / Kebutuhan Solusi*:</label>
              <input
                id="nl-title"
                name="title"
                type="text"
                required
                class="form-input"
                placeholder="Contoh: Aplikasi HRIS & Payroll 500 Karyawan"
                value="${C.title}"
              />
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;">
              <div class="form-group">
                <label class="form-label" for="nl-project-type">Tipe Proyek:</label>
                <select id="nl-project-type" name="projectType" class="form-select">
                  <option value="Web & Mobile App" ${C.projectType===`Web & Mobile App`?`selected`:``}>Web & Mobile App</option>
                  <option value="ERP Custom" ${C.projectType===`ERP Custom`?`selected`:``}>ERP Custom</option>
                  <option value="Fintech Platform" ${C.projectType===`Fintech Platform`?`selected`:``}>Fintech Platform</option>
                  <option value="Point of Sale (POS)" ${C.projectType===`Point of Sale (POS)`?`selected`:``}>Point of Sale (POS)</option>
                  <option value="Web Portal / LMS" ${C.projectType===`Web Portal / LMS`?`selected`:``}>Web Portal / LMS</option>
                  <option value="Integration & API" ${C.projectType===`Integration & API`?`selected`:``}>Integration & API</option>
                  <option value="Consulting & UI/UX" ${C.projectType===`Consulting & UI/UX`?`selected`:``}>Consulting & UI/UX</option>
                  <option value="Lainnya" ${C.projectType===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                </select>

                <div id="nl-custom-project-type-wrap" style="display:${C.projectType===`Lainnya`?`block`:`none`};margin-top:6px;">
                  <input
                    id="nl-custom-project-type"
                    name="customProjectType"
                    type="text"
                    class="form-input"
                    placeholder="Ketik tipe proyek kustom..."
                    value="${C.customProjectType}"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="nl-est-value">Estimasi Nilai Proyek (IDR):</label>
                <input
                  id="nl-est-value"
                  name="estValue"
                  type="number"
                  min="0"
                  step="1000000"
                  required
                  class="form-input"
                  style="font-family:var(--font-mono);font-weight:600;"
                  value="${C.estValue}"
                />
              </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px;">
              <div class="form-group">
                <label class="form-label" for="nl-sales-pic">PIC Penanggung Jawab (PM):</label>
                <select id="nl-sales-pic" name="salesPic" class="form-select" style="font-weight:600;">
                  <option value="Asisyah Sarah Azzahra" ${C.salesPic===`Asisyah Sarah Azzahra`?`selected`:``}>Asisyah Sarah Azzahra (Super Admin & PM)</option>
                  <option value="Sari Dewi" ${C.salesPic===`Sari Dewi`?`selected`:``}>Sari Dewi (Presales Engineer)</option>
                  <option value="Budi Kurniawan" ${C.salesPic===`Budi Kurniawan`?`selected`:``}>Budi Kurniawan (Technical Consultant)</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="nl-priority">Prioritas:</label>
                <select id="nl-priority" name="priority" class="form-select">
                  <option value="Hot" ${C.priority===`Hot`?`selected`:``}>🔥 Hot</option>
                  <option value="High" ${C.priority===`High`?`selected`:``}>High</option>
                  <option value="Medium" ${C.priority===`Medium`?`selected`:``}>Medium</option>
                  <option value="Low" ${C.priority===`Low`?`selected`:``}>Low</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="nl-target-closing">Target Closing:</label>
                <input
                  id="nl-target-closing"
                  name="targetClosing"
                  type="date"
                  required
                  class="form-input"
                  value="${C.targetClosing}"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="nl-tags">Tags (pisahkan koma):</label>
              <input
                id="nl-tags"
                name="tagsInput"
                type="text"
                class="form-input"
                placeholder="Contoh: Custom App, HRIS, Sentul"
                value="${C.tagsInput}"
              />
            </div>
          </div>

          <!-- SECTION 2: Lead Source Channel -->
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:6px;">
              <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--indigo-500);">
                2. Sumber Channel Akuisisi (Lead Source)
              </h3>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;">
              <div class="form-group">
                <label class="form-label" for="nl-source">Channel Utama*:</label>
                <select id="nl-source" name="source" class="form-select" style="font-weight:600;">
                  <option value="Instagram" ${C.source===`Instagram`?`selected`:``}>Instagram</option>
                  <option value="Website" ${C.source===`Website`?`selected`:``}>Website</option>
                  <option value="WhatsApp" ${C.source===`WhatsApp`?`selected`:``}>WhatsApp</option>
                  <option value="Referral" ${C.source===`Referral`?`selected`:``}>Referral</option>
                  <option value="LinkedIn" ${C.source===`LinkedIn`?`selected`:``}>LinkedIn</option>
                  <option value="Meta Ads" ${C.source===`Meta Ads`?`selected`:``}>Meta Ads</option>
                  <option value="Google Ads" ${C.source===`Google Ads`?`selected`:``}>Google Ads</option>
                  <option value="Event/Webinar" ${C.source===`Event/Webinar`?`selected`:``}>Event / Webinar</option>
                  <option value="Lainnya" ${C.source===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                </select>

                <div id="nl-custom-source-wrap" style="display:${C.source===`Lainnya`?`block`:`none`};margin-top:6px;">
                  <input
                    id="nl-custom-source"
                    name="customSource"
                    type="text"
                    class="form-input"
                    placeholder="Ketik nama channel sumber kustom..."
                    value="${C.customSource}"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="nl-sub-source">Sub-Channel / Campaign:</label>
                <input
                  id="nl-sub-source"
                  name="subSource"
                  type="text"
                  class="form-input"
                  placeholder="Mis. DM Instagram, Ads Campaign Q4..."
                  value="${C.subSource}"
                />
              </div>
            </div>

            <div id="nl-referrer-wrap" class="form-group" style="display:${C.source===`Referral`?`block`:`none`};">
              <label class="form-label" for="nl-referrer">Nama Referrer (Pemberi Referensi):</label>
              <input
                id="nl-referrer"
                name="referrerName"
                type="text"
                class="form-input"
                placeholder="Nama klien atau partner yang mereferensikan"
                value="${C.referrerName}"
              />
            </div>
          </div>

          <!-- SECTION 3: Customer Details -->
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:6px;">
              <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--indigo-500);">
                3. Detail Customer & PIC Utama
              </h3>

              <div style="display:flex;align-items:center;gap:6px;">
                <button
                  type="button"
                  id="nl-btn-mode-new"
                  class="btn btn-xs ${C.customerMode===`new`?`btn-primary`:`btn-secondary`}"
                  style="font-weight:600;"
                >
                  Customer Baru
                </button>
                <button
                  type="button"
                  id="nl-btn-mode-existing"
                  class="btn btn-xs ${C.customerMode===`existing`?`btn-primary`:`btn-secondary`}"
                  style="font-weight:600;"
                >
                  Pilih Existing
                </button>
              </div>
            </div>

            <!-- Existing Customer Mode -->
            <div id="nl-existing-customer-section" style="display:${C.customerMode===`existing`?`block`:`none`};">
              <div class="form-group">
                <label class="form-label" for="nl-selected-customer">Pilih Master Customer:</label>
                <select id="nl-selected-customer" name="selectedCustomerId" class="form-select">
                  ${t.map(e=>`
                    <option value="${e.id}" ${e.id===C.selectedCustomerId?`selected`:``}>
                      ${e.legal_name} (${e.industry})
                    </option>
                  `).join(``)}
                </select>
              </div>
            </div>

            <!-- New Customer Mode -->
            <div id="nl-new-customer-section" style="display:${C.customerMode===`new`?`flex`:`none`};flex-direction:column;gap:12px;">
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;">
                <div class="form-group">
                  <label class="form-label" for="nl-company-name">Nama Perusahaan Customer*:</label>
                  <input
                    id="nl-company-name"
                    name="companyName"
                    type="text"
                    class="form-input"
                    placeholder="Contoh: PT Sinar Abadi Sentosa"
                    value="${C.companyName}"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label" for="nl-industry">Sektor Industri:</label>
                  <select id="nl-industry" name="industry" class="form-select">
                    <option value="Manufaktur & Fabrikasi" ${C.industry===`Manufaktur & Fabrikasi`?`selected`:``}>Manufaktur & Fabrikasi</option>
                    <option value="Retail & Multi-Store" ${C.industry===`Retail & Multi-Store`?`selected`:``}>Retail & Multi-Store</option>
                    <option value="Financial Technology" ${C.industry===`Financial Technology`?`selected`:``}>Financial Technology</option>
                    <option value="Food & Beverage / Restoran" ${C.industry===`Food & Beverage / Restoran`?`selected`:``}>Food & Beverage / Restoran</option>
                    <option value="Healthcare & Farmasi" ${C.industry===`Healthcare & Farmasi`?`selected`:``}>Healthcare & Farmasi</option>
                    <option value="Logistics & Supply Chain" ${C.industry===`Logistics & Supply Chain`?`selected`:``}>Logistics & Supply Chain</option>
                    <option value="Pendidikan / Edutech" ${C.industry===`Pendidikan / Edutech`?`selected`:``}>Pendidikan / Edutech</option>
                    <option value="Lainnya" ${C.industry===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                  </select>

                  <div id="nl-custom-industry-wrap" style="display:${C.industry===`Lainnya`?`block`:`none`};margin-top:6px;">
                    <input
                      id="nl-custom-industry"
                      name="customIndustry"
                      type="text"
                      class="form-input"
                      placeholder="Ketik sektor industri kustom..."
                      value="${C.customIndustry}"
                    />
                  </div>
                </div>
              </div>

              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px;">
                <div class="form-group">
                  <label class="form-label" for="nl-pic-name">Nama PIC Utama*:</label>
                  <input
                    id="nl-pic-name"
                    name="picName"
                    type="text"
                    class="form-input"
                    placeholder="Bapak / Ibu..."
                    value="${C.picName}"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label" for="nl-pic-job">Jabatan PIC:</label>
                  <input
                    id="nl-pic-job"
                    name="picJobTitle"
                    type="text"
                    class="form-input"
                    placeholder="CTO / HR Manager / Direktur..."
                    value="${C.picJobTitle}"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label" for="nl-pic-phone">No. WhatsApp / HP*:</label>
                  <input
                    id="nl-pic-phone"
                    name="picPhone"
                    type="tel"
                    class="form-input"
                    style="font-family:var(--font-mono);"
                    placeholder="+6281234567890"
                    value="${C.picPhone}"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer Actions -->
          <div class="modal-footer" style="padding:16px 0 0 0;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:flex-end;gap:10px;">
            <button type="button" id="nl-cancel-btn" class="btn btn-secondary" style="font-weight:600;">
              Batal
            </button>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
              ${Ce.plus}
              <span>Simpan Lead & Buka Workspace</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  `,De()}function De(){let e=document.getElementById(`new-lead-modal-backdrop`);e&&e.addEventListener(`click`,()=>Te());let t=document.getElementById(`nl-close-btn`);t&&t.addEventListener(`click`,()=>Te());let n=document.getElementById(`nl-cancel-btn`);n&&n.addEventListener(`click`,()=>Te());let r=document.getElementById(`nl-project-type`),i=document.getElementById(`nl-custom-project-type-wrap`);r&&i&&r.addEventListener(`change`,e=>{C.projectType=e.target.value,i.style.display=e.target.value===`Lainnya`?`block`:`none`});let a=document.getElementById(`nl-source`),o=document.getElementById(`nl-custom-source-wrap`),s=document.getElementById(`nl-referrer-wrap`);a&&a.addEventListener(`change`,e=>{C.source=e.target.value,o&&(o.style.display=e.target.value===`Lainnya`?`block`:`none`),s&&(s.style.display=e.target.value===`Referral`?`block`:`none`)});let c=document.getElementById(`nl-industry`),l=document.getElementById(`nl-custom-industry-wrap`);c&&l&&c.addEventListener(`change`,e=>{C.industry=e.target.value,l.style.display=e.target.value===`Lainnya`?`block`:`none`});let u=document.getElementById(`nl-btn-mode-new`),d=document.getElementById(`nl-btn-mode-existing`),f=document.getElementById(`nl-new-customer-section`),p=document.getElementById(`nl-existing-customer-section`);u&&d&&f&&p&&(u.addEventListener(`click`,()=>{C.customerMode=`new`,u.className=`btn btn-xs btn-primary`,d.className=`btn btn-xs btn-secondary`,f.style.display=`flex`,p.style.display=`none`}),d.addEventListener(`click`,()=>{C.customerMode=`existing`,d.className=`btn btn-xs btn-primary`,u.className=`btn btn-xs btn-secondary`,f.style.display=`none`,p.style.display=`block`}));let m=document.getElementById(`new-lead-form`);m&&m.addEventListener(`submit`,e=>{e.preventDefault();let t=document.getElementById(`nl-title`)?.value.trim();if(!t){alert(`Judul lead / kebutuhan wajib diisi.`);return}let n=document.getElementById(`nl-project-type`)?.value,r=document.getElementById(`nl-custom-project-type`)?.value.trim(),i=n===`Lainnya`?r||`Custom Project`:n,a=Number(document.getElementById(`nl-est-value`)?.value||0),o=document.getElementById(`nl-sales-pic`)?.value,s=document.getElementById(`nl-priority`)?.value,c=document.getElementById(`nl-target-closing`)?.value,l=document.getElementById(`nl-tags`)?.value||``,u=document.getElementById(`nl-source`)?.value,d=document.getElementById(`nl-custom-source`)?.value.trim(),f=u===`Lainnya`?d||`Lainnya`:u,p=document.getElementById(`nl-sub-source`)?.value.trim(),m=document.getElementById(`nl-referrer`)?.value.trim(),h={};if(C.customerMode===`new`){let e=document.getElementById(`nl-company-name`)?.value.trim(),t=document.getElementById(`nl-pic-name`)?.value.trim(),n=document.getElementById(`nl-pic-phone`)?.value.trim(),r=document.getElementById(`nl-pic-job`)?.value.trim();if(!e){alert(`Nama perusahaan customer wajib diisi.`);return}if(!t){alert(`Nama PIC customer wajib diisi.`);return}let i=document.getElementById(`nl-industry`)?.value,a=document.getElementById(`nl-custom-industry`)?.value.trim();h={legal_name:e,industry:i===`Lainnya`?a||`General Industry`:i,company_size:C.companySize,address:C.address,city:C.city,province:C.province,contacts:[{id:`cnt-${Date.now()}`,name:t,job_title:r||`PIC Proyek`,email:`${t.toLowerCase().replace(/\s+/g,`.`)}@perusahaan.com`,whatsapp_number:n||`+6281234567890`,role_type:`decision_maker`,is_primary:!0}]}}else{let e=document.getElementById(`nl-selected-customer`)?.value,{customers:t}=b.getState(),n=t.find(t=>t.id===e);n&&(h=n)}let g=l.split(`,`).map(e=>e.trim()).filter(Boolean),_=b.addNewLead({title:t,project_type:i,est_value:a,target_closing:c,sales_pic:o,priority:s,source:f,sub_source:p,referrer_name:m,tags:g},h);Te(),b.navigateToLeadDetail(_.id),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Lead ${_.code} berhasil dibuat! Membuka halaman lead...`,type:`success`}}))})}function Oe(){window.addEventListener(`open-new-lead-modal`,()=>{we()}),window.addEventListener(`keydown`,e=>{e.key===`Escape`&&Se&&Te()})}var ke=t({bindPipelineKanbanEvents:()=>Fe,mount:()=>Ie,renderPipelineKanban:()=>Pe}),Ae=``,w=`ALL`,je=null,Me=``,T={search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,alertTriangle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>`,clock:`<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,chevronLeft:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>`,chevronRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>`,arrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`,xCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>`,instagram:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>`,globe:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,whatsapp:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,referral:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,linkedin:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>`,target:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,grip:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>`};function Ne(e){switch(e){case`Instagram`:return T.instagram;case`Website`:return T.globe;case`WhatsApp`:return T.whatsapp;case`Referral`:return T.referral;case`LinkedIn`:return T.linkedin;case`Meta Ads`:case`Google Ads`:return T.target;default:return T.globe}}function Pe(){let{leads:e,selectedPicFilter:t,selectedSourceFilter:n}=b.getState(),r=Array.from(new Set(e.map(e=>e.sales_pic))),i=[`Instagram`,`Website`,`WhatsApp`,`Referral`,`LinkedIn`,`Meta Ads`,`Google Ads`,`Event/Webinar`,`Lainnya`],o=Ae.toLowerCase().trim(),s=e.filter(e=>{let r=o===``||e.title.toLowerCase().includes(o)||e.customer&&e.customer.legal_name.toLowerCase().includes(o)||e.customer&&e.customer.industry&&e.customer.industry.toLowerCase().includes(o)||e.code.toLowerCase().includes(o)||e.sales_pic&&e.sales_pic.toLowerCase().includes(o),i=t===`ALL`||e.sales_pic===t,a=n===`ALL`||e.source===n,s=w===`ALL`||e.priority===w;return r&&i&&a&&s}),c=te(s),l=y.map((e,t)=>{let n=s.filter(t=>t.stage_id===e.id),r=n.reduce((e,t)=>e+(t.est_value||0),0),i=n.length===0?`
        <div style="height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed var(--border);border-radius:var(--radius-lg);text-align:center;padding:12px;color:var(--text-muted);font-size:11px;">
          Belum ada lead
        </div>
      `:n.map(n=>{let r=(n.days_in_stage||0)>=5,i=n.customer?n.customer.legal_name:`Customer`,o=n.sales_pic?n.sales_pic.split(` `)[0]:`PIC`,s=``;n.priority===`Hot`?s=`<span class="badge badge-hot" style="font-size:10px;font-weight:800;">🔥 HOT</span>`:n.priority===`High`&&(s=`<span class="badge badge-high" style="font-size:10px;font-weight:700;">HIGH</span>`);let c=r?`<span class="badge badge-amber" title="${n.days_in_stage} hari di stage ini" style="font-size:10px;display:inline-flex;align-items:center;gap:3px;">
                ${T.clock}
                <span>${n.days_in_stage}d</span>
              </span>`:``;return`
            <div
              class="kanban-card"
              draggable="true"
              data-lead-id="${n.id}"
              data-current-stage="${e.id}"
              title="Drag & drop ke kolom stage lain atau klik untuk buka detail lead"
              style="padding:12px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);cursor:grab;transition:all var(--transition);position:relative;margin-bottom:10px;user-select:none;"
            >
              <!-- Priority & Lead Code Row -->
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:5px;">
                  <span class="drag-handle" title="Tahan dan drag kartu ini" style="display:inline-flex;align-items:center;color:var(--text-muted);opacity:0.5;cursor:grab;">
                    ${T.grip}
                  </span>
                  <span class="badge badge-slate" style="font-family:var(--font-mono);font-size:10px;font-weight:700;">
                    ${n.code}
                  </span>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                  ${s}
                  ${c}
                </div>
              </div>

              <!-- Title & Company -->
              <h4 style="font-size:12px;font-weight:700;color:var(--text-primary);line-height:1.3;margin-bottom:3px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                ${n.title}
              </h4>
              <p style="font-size:11px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:8px;">
                ${i}
              </p>

              <!-- Est Value & Project Type -->
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-family:var(--font-mono);font-weight:800;font-size:12px;color:var(--emerald-500);">
                  ${a(n.est_value||0)}
                </span>
                <span style="font-size:10px;color:var(--text-muted);max-width:90px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  ${n.project_type||`Custom App`}
                </span>
              </div>

              <!-- Lead Source & Sales PIC -->
              <div style="display:flex;align-items:center;justify-content:space-between;padding-top:6px;border-top:1px solid var(--border-light);margin-bottom:8px;">
                <div style="display:flex;align-items:center;gap:4px;padding:2px 6px;border-radius:var(--radius-full);background:var(--bg-secondary);font-size:10px;color:var(--text-secondary);">
                  ${Ne(n.source)}
                  <span>${n.source}</span>
                </div>

                <div style="display:flex;align-items:center;gap:5px;">
                  <span style="font-size:11px;color:var(--text-secondary);font-weight:500;">
                    ${o}
                  </span>
                  <img
                    src="${n.sales_pic_avatar||`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`}"
                    alt="${n.sales_pic}"
                    style="width:20px;height:20px;border-radius:var(--radius-full);object-fit:cover;border:1px solid var(--border);"
                  />
                </div>
              </div>

              <!-- Stage Shift Quick Action Buttons -->
              <div style="display:flex;align-items:center;justify-content:space-between;padding-top:6px;border-top:1px solid var(--border-light);">
                <button
                  class="btn-shift-prev"
                  data-lead-id="${n.id}"
                  data-current-stage="${e.id}"
                  data-stage-idx="${t}"
                  ${t===0?`disabled style="opacity:0.2;cursor:not-allowed;"`:``}
                  title="Kembali ke stage sebelumnya"
                  style="border:none;background:transparent;color:var(--text-muted);cursor:pointer;padding:2px;"
                >
                  ${T.chevronLeft}
                </button>

                <span style="font-size:10px;font-weight:700;color:var(--indigo-500);display:flex;align-items:center;gap:2px;">
                  <span>Detail</span>
                  ${T.arrowRight}
                </span>

                <button
                  class="btn-shift-next"
                  data-lead-id="${n.id}"
                  data-current-stage="${e.id}"
                  data-stage-idx="${t}"
                  ${t===y.length-1?`disabled style="opacity:0.2;cursor:not-allowed;"`:``}
                  title="Maju ke stage berikutnya"
                  style="border:none;background:transparent;color:var(--text-muted);cursor:pointer;padding:2px;"
                >
                  ${T.chevronRight}
                </button>
              </div>
            </div>
          `}).join(``);return`
      <div
        class="kanban-column"
        data-stage-id="${e.id}"
        style="width:280px;min-width:280px;background:var(--bg-secondary);border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;height:100%;overflow:hidden;transition:all 0.2s ease;"
      >
        <!-- Column Header -->
        <div style="padding:12px;background:var(--bg-card);border-bottom:1px solid var(--border);border-top-left-radius:var(--radius-xl);border-top-right-radius:var(--radius-xl);">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:var(--radius-full);background-color:${e.color};display:inline-block;"></span>
              <span style="font-size:12px;font-weight:800;color:var(--text-primary);letter-spacing:-.01em;">
                ${e.name}
              </span>
            </div>
            <span class="badge badge-slate" style="font-size:10px;font-weight:700;">
              ${n.length}
            </span>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">
            <span>${a(r)}</span>
            <span style="font-size:10px;font-weight:700;color:var(--indigo-500);">${e.probability}% win</span>
          </div>
        </div>

        <!-- Cards Body -->
        <div class="kanban-cards-scroll" data-stage-id="${e.id}" style="flex:1;overflow-y:auto;padding:10px;min-height:160px;transition:background 0.2s ease;">
          ${i}
        </div>
      </div>
    `}).join(``),u=``;return je&&(u=`
      <div class="modal-overlay" id="reject-modal-backdrop" style="background:rgba(15,23,42,.65);backdrop-filter:blur(4px);z-index:100;">
        <div class="modal modal-md" onclick="event.stopPropagation();" style="max-width:440px;padding:20px;display:flex;flex-direction:column;gap:16px;">
          <div style="display:flex;align-items:center;gap:12px;color:var(--red-500);">
            <div style="width:40px;height:40px;border-radius:var(--radius-lg);background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              ${T.xCircle}
            </div>
            <div>
              <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);">
                Tandai Lead Sebagai Reject (Lost)
              </h3>
              <p style="font-size:11px;color:var(--text-muted);">${je.title}</p>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="reject-reason-input">Alasan Penolakan (Wajib Diisi)*:</label>
            <textarea
              id="reject-reason-input"
              rows="3"
              class="form-textarea"
              placeholder="Contoh: Klien memilih vendor lain, budget di luar rate card, atau proyek ditunda internal..."
            >${Me}</textarea>
          </div>

          <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding-top:8px;">
            <button id="btn-cancel-reject" class="btn btn-secondary btn-sm" style="font-weight:600;">
              Batal
            </button>
            <button id="btn-confirm-reject" class="btn btn-danger btn-sm" style="font-weight:700;">
              Konfirmasi Reject
            </button>
          </div>
        </div>
      </div>
    `),`
    <div class="kanban-page-wrapper" style="flex:1;display:flex;flex-direction:column;height:100%;overflow:hidden;background:var(--bg-primary);">
      <!-- Top KPI Summary & Filter Bar -->
      <div style="padding:12px 20px;background:var(--bg-card);border-bottom:1px solid var(--border);display:flex;flex-direction:column;gap:10px;">
        <!-- KPI Metrics -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;font-size:12px;">
          <div style="display:flex;align-items:center;gap:20px;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="color:var(--text-muted);font-weight:500;">Total Pipeline:</span>
              <strong style="font-size:14px;font-family:var(--font-mono);color:var(--text-primary);">${a(c.totalValue)}</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;border-left:1px solid var(--border);padding-left:16px;">
              <span style="color:var(--text-muted);font-weight:500;">Weighted Value:</span>
              <strong style="font-size:14px;font-family:var(--font-mono);color:var(--indigo-500);">${a(c.weightedValue)}</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;border-left:1px solid var(--border);padding-left:16px;">
              <span style="color:var(--text-muted);font-weight:500;">Total Peluang:</span>
              <strong style="color:var(--text-primary);">${c.totalCount} Leads (${c.activeLeadsCount} Aktif)</strong>
            </div>
          </div>

          ${c.staleCount>0?`
            <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:var(--radius-md);background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:var(--amber-500);font-size:11px;font-weight:700;">
              ${T.alertTriangle}
              <span>${c.staleCount} Lead Stale (≥5 hari tanpa aktivitas)</span>
            </div>
          `:``}
        </div>

        <!-- Filter Controls Row -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;padding-top:6px;border-top:1px solid var(--border-light);">
          <!-- Search input -->
          <div style="position:relative;flex:1;max-width:360px;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
              ${T.search}
            </span>
            <input
              id="kanban-search-input"
              type="text"
              class="form-input"
              style="padding-left:32px;font-size:12px;padding-top:6px;padding-bottom:6px;"
              placeholder="Filter cepat judul, perusahaan, kode lead..."
              value="${Ae}"
            />
          </div>

          <!-- Select dropdowns -->
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
            <div style="display:flex;align-items:center;gap:4px;">
              <span style="color:var(--text-muted);font-size:11px;">PIC:</span>
              <select id="kanban-filter-pic" class="form-select" style="font-size:11px;padding:4px 8px;width:auto;">
                <option value="ALL" ${t===`ALL`?`selected`:``}>Semua PIC</option>
                ${r.map(e=>`<option value="${e}" ${e===t?`selected`:``}>${e}</option>`).join(``)}
              </select>
            </div>

            <div style="display:flex;align-items:center;gap:4px;">
              <span style="color:var(--text-muted);font-size:11px;">Sumber:</span>
              <select id="kanban-filter-source" class="form-select" style="font-size:11px;padding:4px 8px;width:auto;">
                <option value="ALL" ${n===`ALL`?`selected`:``}>Semua Sumber</option>
                ${i.map(e=>`<option value="${e}" ${e===n?`selected`:``}>${e}</option>`).join(``)}
              </select>
            </div>

            <div style="display:flex;align-items:center;gap:4px;">
              <span style="color:var(--text-muted);font-size:11px;">Prioritas:</span>
              <select id="kanban-filter-priority" class="form-select" style="font-size:11px;padding:4px 8px;width:auto;">
                <option value="ALL" ${w===`ALL`?`selected`:``}>Semua Prioritas</option>
                <option value="Hot" ${w===`Hot`?`selected`:``}>🔥 Hot</option>
                <option value="High" ${w===`High`?`selected`:``}>High</option>
                <option value="Medium" ${w===`Medium`?`selected`:``}>Medium</option>
                <option value="Low" ${w===`Low`?`selected`:``}>Low</option>
              </select>
            </div>

            ${Ae||t!==`ALL`||n!==`ALL`||w!==`ALL`?`
              <button id="kanban-reset-filters" class="btn btn-ghost btn-xs" style="color:var(--indigo-500);font-weight:700;">
                Reset Filter
              </button>
            `:``}
          </div>
        </div>
      </div>

      <!-- Horizontal Kanban Columns Container -->
      <div style="flex:1;overflow-x:auto;overflow-y:hidden;padding:16px;">
        <div style="display:flex;align-items:flex-start;gap:12px;height:100%;min-width:max-content;padding-bottom:8px;">
          ${l}
        </div>
      </div>

      <!-- Reject Modal Container -->
      ${u}
    </div>
  `}function Fe(e){let t=e.querySelector(`#kanban-search-input`);t&&t.addEventListener(`input`,t=>{Ae=t.target.value,e.innerHTML=Pe(),Fe(e);let n=e.querySelector(`#kanban-search-input`);n&&(n.focus(),n.selectionStart=n.selectionEnd=n.value.length)});let n=e.querySelector(`#kanban-filter-pic`);n&&n.addEventListener(`change`,e=>{b.setSelectedPicFilter(e.target.value)});let r=e.querySelector(`#kanban-filter-source`);r&&r.addEventListener(`change`,e=>{b.setSelectedSourceFilter(e.target.value)});let i=e.querySelector(`#kanban-filter-priority`);i&&i.addEventListener(`change`,t=>{w=t.target.value,e.innerHTML=Pe(),Fe(e)});let a=e.querySelector(`#kanban-reset-filters`);a&&a.addEventListener(`click`,()=>{Ae=``,w=`ALL`,b.setSelectedPicFilter(`ALL`),b.setSelectedSourceFilter(`ALL`)});let o=null,s=null,c=!1;e.querySelectorAll(`.kanban-card[data-lead-id]`).forEach(t=>{t.addEventListener(`dragstart`,e=>{o=t.dataset.leadId,s=t.dataset.currentStage,c=!0,t.classList.add(`is-dragging`),e.dataTransfer&&(e.dataTransfer.effectAllowed=`move`,e.dataTransfer.setData(`text/plain`,t.dataset.leadId))}),t.addEventListener(`dragend`,()=>{t.classList.remove(`is-dragging`),e.querySelectorAll(`.kanban-column`).forEach(e=>{e.classList.remove(`drag-target-active`)}),setTimeout(()=>{c=!1,o=null,s=null},120)})}),e.querySelectorAll(`.kanban-column[data-stage-id]`).forEach(t=>{let n=t.dataset.stageId;t.addEventListener(`dragover`,e=>{e.preventDefault(),e.dataTransfer&&(e.dataTransfer.dropEffect=`move`),t.classList.contains(`drag-target-active`)||t.classList.add(`drag-target-active`)}),t.addEventListener(`dragleave`,e=>{t.contains(e.relatedTarget)||t.classList.remove(`drag-target-active`)}),t.addEventListener(`drop`,r=>{r.preventDefault(),t.classList.remove(`drag-target-active`);let i=r.dataTransfer&&r.dataTransfer.getData(`text/plain`)||o,a=n;if(!i||!a||a===s)return;let c=y.find(e=>e.id===a),l=c?c.name:a;if(a===`reject`){let{leads:t}=b.getState();je=t.find(e=>e.id===i),Me=``,e.innerHTML=Pe(),Fe(e);return}let u=b.updateLeadStage(i,a);u.success?window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Lead berhasil dipindahkan ke tahap "${l}"!`,type:`success`}})):window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:u.message||`Tidak dapat memindahkan stage.`,type:`warning`}}))})}),e.querySelectorAll(`.kanban-card[data-lead-id]`).forEach(e=>{e.addEventListener(`click`,t=>{if(c||t.target.closest(`.btn-shift-prev`)||t.target.closest(`.btn-shift-next`)||t.target.closest(`.drag-handle`))return;let n=e.dataset.leadId;n&&b.navigateToLeadDetail(n)})}),e.querySelectorAll(`.btn-shift-prev`).forEach(e=>{e.addEventListener(`click`,t=>{t.stopPropagation();let n=e.dataset.leadId,r=parseInt(e.dataset.stageIdx,10);if(r>0){let e=y[r-1],t=b.updateLeadStage(n,e.id);t.success||alert(t.message)}})}),e.querySelectorAll(`.btn-shift-next`).forEach(t=>{t.addEventListener(`click`,n=>{n.stopPropagation();let r=t.dataset.leadId,i=parseInt(t.dataset.stageIdx,10);if(i<y.length-1){let t=y[i+1];if(t.id===`reject`){let{leads:t}=b.getState();je=t.find(e=>e.id===r),Me=``,e.innerHTML=Pe(),Fe(e);return}let n=b.updateLeadStage(r,t.id);n.success||alert(n.message)}})});let l=e.querySelector(`#btn-cancel-reject`);l&&l.addEventListener(`click`,()=>{je=null,e.innerHTML=Pe(),Fe(e)});let u=e.querySelector(`#btn-confirm-reject`);u&&u.addEventListener(`click`,()=>{let t=e.querySelector(`#reject-reason-input`)?.value.trim();if(!t)alert(`Alasan penolakan wajib diisi (PRD BR-08).`);else if(je){let e=b.updateLeadStage(je.id,`reject`,t);e.success?(window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Lead ${je.code} ditandai Reject (Lost).`,type:`warning`}})),je=null):alert(e.message)}})}function Ie(e){return e.innerHTML=Pe(),Fe(e),b.subscribe(()=>{let t=b.getState();t.activeView===`pipeline`&&t.viewMode===`kanban`&&(e.innerHTML=Pe(),Fe(e))})}function Le(e,t,n){let r=e.length,i=Math.max(1,Math.ceil(r/n)),a=Math.min(Math.max(1,t),i),o=(a-1)*n,s=Math.min(o+n,r);return{data:e.slice(o,s),pagination:{currentPage:a,pageSize:n,totalItems:r,totalPages:i,startIndex:o,endIndex:s}}}var Re={chevronLeft:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>`,chevronRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>`};function ze(e,t={}){let{currentPage:n=1,totalPages:r=1,totalItems:i=0,startIndex:a=0,endIndex:o=0,pageSize:s=10}=e||{};if(!i||i===0)return``;let c=t.pageSizeOptions||[5,10,20],l=t.paginationId||`default-pagination`,u=i>0?a+1:0,d=o,f=`
    <div style="display:flex;align-items:center;gap:6px;margin-left:8px;border-left:1px solid var(--border);padding-left:12px;">
      <span style="color:var(--text-muted);font-size:12px;">Tampilkan:</span>
      <select class="form-select pagination-page-size" data-pagination-id="${l}" style="width:auto;padding:2px 8px;font-size:12px;font-weight:600;border-radius:var(--radius-sm);">
        ${c.map(e=>`
          <option value="${e}" ${e===s?`selected`:``}>${e} / hal</option>
        `).join(``)}
      </select>
    </div>
  `,p=Array.from({length:r}).map((e,t)=>{let r=t+1,i=r===n;return`
      <button
        class="pagination-btn pagination-page-btn ${i?`active`:``}"
        data-pagination-id="${l}"
        data-page="${r}"
        style="${i?`background:#4f46e5;border-color:#4f46e5;color:#ffffff;font-weight:700;box-shadow:0 1px 3px rgba(79,70,229,0.3);`:``}"
      >
        ${r}
      </button>
    `}).join(``);return`
    <div class="pagination-controls-wrapper" data-pagination-container="${l}" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;background:var(--bg-card);border-top:1px solid var(--border);font-size:12px;">
      <!-- Left: Entries Counter & PageSize -->
      <div style="display:flex;align-items:center;gap:8px;color:var(--text-muted);">
        <span>
          Menampilkan <strong style="color:var(--text-primary);">${u}</strong> - <strong style="color:var(--text-primary);">${d}</strong> dari <strong style="color:var(--text-primary);">${i}</strong> entri
        </span>
        ${f}
      </div>

      <!-- Right: Pagination Buttons -->
      <div class="pagination" style="display:flex;align-items:center;gap:4px;">
        <button
          class="pagination-btn pagination-prev-btn"
          data-pagination-id="${l}"
          data-page="${n-1}"
          ${n<=1?`disabled`:``}
          title="Halaman Sebelumnya"
        >
          ${Re.chevronLeft}
        </button>

        ${p}

        <button
          class="pagination-btn pagination-next-btn"
          data-pagination-id="${l}"
          data-page="${n+1}"
          ${n>=r?`disabled`:``}
          title="Halaman Berikutnya"
        >
          ${Re.chevronRight}
        </button>
      </div>
    </div>
  `}function Be(e,t,n,r=`default-pagination`){if(!e)return;e.querySelectorAll(`button[data-pagination-id="${r}"][data-page]`).forEach(e=>{e.addEventListener(`click`,n=>{if(n.preventDefault(),e.disabled)return;let r=parseInt(e.dataset.page,10);!isNaN(r)&&typeof t==`function`&&t(r)})});let i=e.querySelector(`select.pagination-page-size[data-pagination-id="${r}"]`);i&&i.addEventListener(`change`,e=>{let t=parseInt(e.target.value,10);!isNaN(t)&&typeof n==`function`&&n(t)})}var Ve=t({bindPipelineTableEvents:()=>Xe,mount:()=>Ze,renderPipelineTable:()=>Ye}),He=``,Ue=`ALL`,We=1,Ge=5,Ke={search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,download:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>`,arrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`},qe=[];function Je(e){let t=[`Kode`,`Judul`,`Customer`,`Nilai (IDR)`,`Stage`,`Sumber`,`PIC`,`Target Closing`],n=(e||[]).map(e=>[e.code,`"${(e.title||``).replace(/"/g,`""`)}"`,`"${(e.customer?e.customer.legal_name:``).replace(/"/g,`""`)}"`,e.est_value||0,e.stage_id,e.source,e.sales_pic,e.target_closing||``]),r=`data:text/csv;charset=utf-8,﻿`+[t.join(`,`),...n.map(e=>e.join(`,`))].join(`
`),i=encodeURI(r),a=document.createElement(`a`);a.setAttribute(`href`,i),a.setAttribute(`download`,`ASTACODE_Pipeline_Leads_${new Date().toISOString().split(`T`)[0]}.csv`),document.body.appendChild(a),a.click(),document.body.removeChild(a)}function Ye(){let{leads:e}=b.getState(),t=He.toLowerCase().trim(),n=e.filter(e=>{let n=t===``||e.title.toLowerCase().includes(t)||e.customer&&e.customer.legal_name.toLowerCase().includes(t)||e.customer&&e.customer.industry&&e.customer.industry.toLowerCase().includes(t)||e.code.toLowerCase().includes(t)||e.sales_pic&&e.sales_pic.toLowerCase().includes(t),r=Ue===`ALL`||e.stage_id===Ue;return n&&r});qe=n;let{data:r,pagination:i}=Le(n,We,Ge),o=r.length===0?`
      <tr>
        <td colspan="9" style="text-align:center;padding:48px 16px;color:var(--text-muted);font-size:12px;">
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
            <span>Tidak ada lead yang cocok dengan kriteria pencarian dan filter saat ini.</span>
            <button id="btn-reset-table-filter" class="btn btn-secondary btn-sm" style="font-weight:700;margin-top:4px;">
              ↺ Reset Filter & Pencarian
            </button>
          </div>
        </td>
      </tr>
    `:r.map(e=>{let t=y.find(t=>t.id===e.stage_id)||y[0],n=e.customer?e.customer.legal_name:`-`,r=e.customer?e.customer.industry:`-`;return`
          <tr
            class="pipeline-table-row"
            data-lead-id="${e.id}"
            style="cursor:pointer;border-bottom:1px solid var(--border-light);"
          >
            <!-- 1. KODE -->
            <td style="padding:10px 14px;font-family:var(--font-mono);font-weight:700;color:var(--text-primary);font-size:12px;">
              ${e.code}
            </td>

            <!-- 2. KEBUTUHAN / PROYEK -->
            <td style="padding:10px 14px;">
              <div style="font-weight:700;color:var(--text-primary);font-size:13px;line-height:1.3;">
                ${e.title}
              </div>
              <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                ${e.project_type||`Web & Mobile App`}
              </div>
            </td>

            <!-- 3. CUSTOMER & INDUSTRI -->
            <td style="padding:10px 14px;">
              <div style="font-weight:600;color:var(--text-primary);font-size:12px;">
                ${n}
              </div>
              <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">
                ${r}
              </div>
            </td>

            <!-- 4. NILAI ESTIMASI -->
            <td style="padding:10px 14px;font-family:var(--font-mono);font-weight:800;color:#16a34a;font-size:12px;font-variant-numeric:tabular-nums;">
              ${a(e.est_value||0)}
            </td>

            <!-- 5. STAGE -->
            <td style="padding:10px 14px;">
              <span
                style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:var(--radius-full);font-size:11px;font-weight:700;background:${t.color}15;color:${t.color};border:1px solid ${t.color}35;"
              >
                <span style="width:6px;height:6px;border-radius:var(--radius-full);background-color:${t.color};"></span>
                <span>${t.name}</span>
              </span>
            </td>

            <!-- 6. SUMBER -->
            <td style="padding:10px 14px;">
              <span class="badge badge-slate" style="font-size:11px;font-weight:500;padding:2px 8px;border-radius:var(--radius-sm);background:var(--bg-secondary);border:1px solid var(--border);">
                ${e.source}
              </span>
            </td>

            <!-- 7. SALES PIC -->
            <td style="padding:10px 14px;">
              <div style="display:flex;align-items:center;gap:8px;">
                <img
                  src="${e.sales_pic_avatar||`https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80`}"
                  alt="${e.sales_pic}"
                  style="width:28px;height:28px;border-radius:var(--radius-full);object-fit:cover;border:1px solid var(--border);flex-shrink:0;"
                />
                <span style="font-size:12px;color:var(--text-secondary);font-weight:500;white-space:nowrap;">
                  ${e.sales_pic}
                </span>
              </div>
            </td>

            <!-- 8. UMUR -->
            <td style="padding:10px 14px;font-family:var(--font-mono);font-size:11px;color:var(--text-muted);white-space:nowrap;">
              ${e.days_in_stage||0} hari
            </td>

            <!-- 9. AKSI -->
            <td style="padding:10px 14px;text-align:right;">
              <button
                class="btn btn-sm btn-action-open"
                data-lead-id="${e.id}"
                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;font-size:11px;font-weight:700;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.25);color:var(--indigo-600);border-radius:var(--radius-full);cursor:pointer;transition:all var(--transition);"
              >
                <span>Buka</span>
                ${Ke.arrowRight}
              </button>
            </td>
          </tr>
        `}).join(``),s=ze(i,{pageSizeOptions:[5,10,20],paginationId:`pipeline-table-pagination`});return`
    <div class="animate-fade-in" style="flex:1;display:flex;flex-direction:column;height:100%;overflow:hidden;padding:20px;gap:16px;background:var(--bg-primary);">
      <!-- Top Filter & Actions Header -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:14px 18px;border-radius:var(--radius-xl);border:1px solid var(--border);box-shadow:var(--shadow-sm);">
        <div style="display:flex;align-items:center;gap:10px;flex:1;max-width:480px;">
          <div style="position:relative;flex:1;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
              ${Ke.search}
            </span>
            <input
              id="table-search-input"
              type="text"
              class="form-input"
              placeholder="Cari lead, customer, kode, PIC..."
              value="${He}"
              style="padding-left:32px;font-size:12px;"
            />
          </div>

          <select id="table-stage-filter" class="form-select" style="width:auto;font-size:12px;font-weight:600;">
            <option value="ALL" ${Ue===`ALL`?`selected`:``}>Semua Stage</option>
            ${y.map(e=>`
              <option value="${e.id}" ${e.id===Ue?`selected`:``}>
                ${e.name} (${e.probability}%)
              </option>
            `).join(``)}
          </select>
        </div>

        <button id="table-export-csv-btn" class="btn btn-secondary btn-sm" style="display:flex;align-items:center;gap:6px;font-weight:600;">
          ${Ke.download}
          <span>Export CSV</span>
        </button>
      </div>

      <!-- Dense Table Card Container -->
      <div style="flex:1;display:flex;flex-direction:column;background:var(--bg-card);border-radius:var(--radius-xl);border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="flex:1;overflow-x:auto;overflow-y:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:12px;">
            <thead>
              <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border);color:var(--text-muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.05em;position:sticky;top:0;z-index:2;">
                <th style="padding:10px 14px;text-align:left;">Kode</th>
                <th style="padding:10px 14px;text-align:left;">Kebutuhan / Proyek</th>
                <th style="padding:10px 14px;text-align:left;">Customer & Industri</th>
                <th style="padding:10px 14px;text-align:left;">Nilai Estimasi</th>
                <th style="padding:10px 14px;text-align:left;">Stage</th>
                <th style="padding:10px 14px;text-align:left;">Sumber</th>
                <th style="padding:10px 14px;text-align:left;">Sales PIC</th>
                <th style="padding:10px 14px;text-align:left;">Umur</th>
                <th style="padding:10px 14px;text-align:right;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              ${o}
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        ${s}
      </div>
    </div>
  `}function Xe(e){let t=e.querySelector(`#table-search-input`);t&&t.addEventListener(`input`,t=>{He=t.target.value,We=1,e.innerHTML=Ye(),Xe(e);let n=e.querySelector(`#table-search-input`);n&&(n.focus(),n.selectionStart=n.selectionEnd=n.value.length)});let n=e.querySelector(`#table-stage-filter`);n&&n.addEventListener(`change`,t=>{Ue=t.target.value,We=1,e.innerHTML=Ye(),Xe(e)});let r=e.querySelector(`#btn-reset-table-filter`);r&&r.addEventListener(`click`,()=>{He=``,Ue=`ALL`,We=1,e.innerHTML=Ye(),Xe(e)});let i=e.querySelector(`#table-export-csv-btn`);i&&i.addEventListener(`click`,()=>{Je(qe)}),e.querySelectorAll(`.pipeline-table-row[data-lead-id]`).forEach(e=>{e.addEventListener(`click`,t=>{if(t.target.closest(`.btn-action-open`))return;let n=e.dataset.leadId;n&&b.navigateToLeadDetail(n)})}),e.querySelectorAll(`.btn-action-open[data-lead-id]`).forEach(e=>{e.addEventListener(`click`,t=>{t.stopPropagation();let n=e.dataset.leadId;n&&b.navigateToLeadDetail(n)})}),Be(e,t=>{We=t,e.innerHTML=Ye(),Xe(e)},t=>{Ge=t,We=1,e.innerHTML=Ye(),Xe(e)},`pipeline-table-pagination`)}function Ze(e){return e.innerHTML=Ye(),Xe(e),b.subscribe(()=>{let t=b.getState();t.activeView===`pipeline`&&t.viewMode===`table`&&(e.innerHTML=Ye(),Xe(e))})}var Qe=t({bindPipelineWorkflowEvents:()=>at,mount:()=>ot,renderPipelineWorkflowTracker:()=>it}),E=`workflow`,$e=`all`,et=1,tt=5,nt={layers:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>`,checkCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`,video:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2"/></svg>`,phone:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>`,fileText:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>`,download:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>`},rt=[{id:`pf-1`,code:`AST-2026-001`,title:`Aplikasi HRD 500 Karyawan`,customer:`PT Maju Sejahtera Industri`,fileName:`Kerangka_Acuan_Kerja_HRIS_PT_Maju.pdf`,category:`Brief / KAK Klien`,size:`2.4 MB`,date:`12 Okt 2026`,uploader:`Budi Santoso (Klien)`,status:`Terverifikasi Presales`},{id:`pf-2`,code:`AST-2026-001`,title:`Aplikasi HRD 500 Karyawan`,customer:`PT Maju Sejahtera Industri`,fileName:`Quotation_MoU_QT-2026-10-0012_Rev2.pdf`,category:`Quotation + MoU (1 File)`,size:`1.8 MB`,date:`15 Okt 2026`,uploader:`Asisyah Sarah Azzahra (ASTACODE)`,status:`Negosiasi Termin TOP`},{id:`pf-3`,code:`AST-2026-002`,title:`ERP Retail & Multi-Warehouse`,customer:`PT Retail Nusantara Prima`,fileName:`Spesifikasi_Kebutuhan_POS_18_Cabang.docx`,category:`Dokumen Kebutuhan`,size:`3.1 MB`,date:`14 Okt 2026`,uploader:`Sari Wijaya (Klien)`,status:`Review Presales`},{id:`pf-4`,code:`AST-2026-003`,title:`Mobile Banking Core & e-Wallet`,customer:`PT Finan Prima Ventura`,fileName:`Non_Disclosure_Agreement_Signed.pdf`,category:`Legal NDA`,size:`1.2 MB`,date:`12 Okt 2026`,uploader:`Legal Team`,status:`Signed & Valid`},{id:`pf-5`,code:`AST-2026-006`,title:`Custom Logistics & Fleet Tracking`,customer:`PT Logistik Express Sentosa`,fileName:`Kontrak_Kerja_Sama_CT-2026-10-0007.pdf`,category:`Kontrak Resmi`,size:`4.5 MB`,date:`02 Okt 2026`,uploader:`M. Reza Fahlevi`,status:`Deal Signed`}];function it(){let{leads:e,activities:t}=b.getState(),{data:n,pagination:r}=Le(t.filter(e=>$e===`meet`?e.type===`google_meet`:$e===`call`?e.type===`call`:$e!==`wa`||e.type===`whatsapp`),et,tt),i=y.filter(e=>e.id!==`reject`).map(t=>{let n=e.filter(e=>e.stage_id===t.id),r=n.reduce((e,t)=>e+(t.est_value||0),0);return`
      <div style="flex:1;min-width:120px;padding:12px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:space-between;box-shadow:var(--shadow-sm);">
        <div style="width:42px;height:42px;border-radius:var(--radius-full);background:linear-gradient(135deg, var(--indigo-500), var(--violet-600));color:#ffffff;font-family:var(--font-mono);font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(99,102,241,.3);">
          ${t.probability}%
        </div>
        <div style="margin-top:10px;">
          <div style="font-size:12px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            ${t.name}
          </div>
          <div style="font-size:11px;font-weight:700;color:var(--indigo-500);margin-top:2px;">
            ${n.length} Lead Aktif
          </div>
          <div style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);margin-top:2px;">
            ${a(r)}
          </div>
        </div>
      </div>
    `}).join(``),o=``;if(E===`workflow`)o=`
      <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- Continuous Workflow Track Rail -->
        <div style="padding:16px 20px;background:var(--bg-secondary);border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
              <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);">
                Track Record Workflow Pipeline ASTACODE
              </span>
              <p style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                Visualisasi jalur konversi penjualan software house berdasarkan persentase peluang closing.
              </p>
            </div>
            <span class="badge badge-purple" style="font-family:var(--font-mono);font-weight:700;">
              Total ${e.length} Leads Terpantau
            </span>
          </div>

          <div style="display:flex;flex-wrap:wrap;gap:10px;padding-top:6px;">
            ${i}
          </div>
        </div>

        <!-- 3 Enterprise Gate Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:16px;">
          <div class="card" style="padding:16px;display:flex;flex-direction:column;gap:8px;">
            <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--indigo-500);letter-spacing:.05em;">
              Gate: Ke Tahap Quotation
            </span>
            <h4 style="font-size:13px;font-weight:700;color:var(--text-primary);">
              Wajib Memiliki RAB yang Telah Direview
            </h4>
            <p style="font-size:11px;color:var(--text-secondary);line-height:1.6;">
              Sistem mengunci stage Quotation hingga minimal 1 modul RAB digenerate dan diverifikasi status "Reviewed" oleh Presales (PRD BR-04).
            </p>
            <div style="margin-top:auto;padding-top:8px;color:var(--emerald-500);font-weight:700;font-size:11px;display:flex;align-items:center;gap:6px;">
              ${nt.checkCircle}
              <span>Aturan Validasi Aktif</span>
            </div>
          </div>

          <div class="card" style="padding:16px;display:flex;flex-direction:column;gap:8px;">
            <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--amber-500);letter-spacing:.05em;">
              Gate: Setting TOP Quotation
            </span>
            <h4 style="font-size:13px;font-weight:700;color:var(--text-primary);">
              Termin Pembayaran Wajib Balance 100%
            </h4>
            <p style="font-size:11px;color:var(--text-secondary);line-height:1.6;">
              Penawaran tidak dapat digenerate final atau dikirim ke klien sebelum persentase atau nominal termin seimbang dengan nilai kontrak (PRD BR-15).
            </p>
            <div style="margin-top:auto;padding-top:8px;color:var(--emerald-500);font-weight:700;font-size:11px;display:flex;align-items:center;gap:6px;">
              ${nt.checkCircle}
              <span>Aturan Validasi Aktif</span>
            </div>
          </div>

          <div class="card" style="padding:16px;display:flex;flex-direction:column;gap:8px;">
            <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--emerald-500);letter-spacing:.05em;">
              Gate: Konversi DEAL (Won)
            </span>
            <h4 style="font-size:13px;font-weight:700;color:var(--text-primary);">
              Auto-Spawn Proyek ERP & Jadwal Invoice
            </h4>
            <p style="font-size:11px;color:var(--text-secondary);line-height:1.6;">
              Perubahan status ke Deal otomatis mengubah customer menjadi Active, membuat record proyek, dan menerbitkan invoice termin 1 ke modul Finance (PRD BR-07).
            </p>
            <div style="margin-top:auto;padding-top:8px;color:var(--emerald-500);font-weight:700;font-size:11px;display:flex;align-items:center;gap:6px;">
              ${nt.checkCircle}
              <span>Aturan Validasi Aktif</span>
            </div>
          </div>
        </div>
      </div>
    `;else if(E===`timeline`){let e=n.map(e=>{let t=e.type===`google_meet`,n=t?nt.video:nt.phone;return`
        <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-card);border:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;font-size:12px;">
          <div style="width:36px;height:36px;border-radius:var(--radius-md);background:${t?`rgba(16,185,129,.1)`:`rgba(99,102,241,.1)`};color:${t?`var(--emerald-500)`:`var(--indigo-500)`};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            ${n}
          </div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;">
              <span style="font-weight:700;color:var(--text-primary);">${e.title}</span>
              <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-muted);">${new Date(e.scheduled_at).toLocaleDateString(`id-ID`)}</span>
            </div>
            <p style="color:var(--text-secondary);margin-bottom:6px;line-height:1.4;">${e.notes||e.outcome||`-`}</p>
            <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:var(--indigo-500);font-weight:600;">
              <span>PIC: ${e.assigned_to}</span>
              ${e.meet_link?`<a href="${e.meet_link}" target="_blank" rel="noreferrer" style="text-decoration:underline;">Buka Link Meet</a>`:``}
            </div>
          </div>
        </div>
      `}).join(``),t=ze(r,{pageSizeOptions:[3,5,10],paginationId:`workflow-timeline-pagination`});o=`
      <div style="display:flex;flex-direction:column;gap:14px;">
        <!-- Filter Row -->
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
          <span style="color:var(--text-muted);font-weight:500;">Filter Tipe:</span>
          <button class="btn btn-xs btn-filter-act ${$e===`all`?`btn-primary`:`btn-secondary`}" data-act-type="all">Semua</button>
          <button class="btn btn-xs btn-filter-act ${$e===`meet`?`btn-primary`:`btn-secondary`}" data-act-type="meet">📹 Google Meet</button>
          <button class="btn btn-xs btn-filter-act ${$e===`call`?`btn-primary`:`btn-secondary`}" data-act-type="call">📞 Telepon</button>
          <button class="btn btn-xs btn-filter-act ${$e===`wa`?`btn-primary`:`btn-secondary`}" data-act-type="wa">💬 WhatsApp</button>
        </div>

        <!-- Activities List -->
        <div style="display:flex;flex-direction:column;gap:10px;">
          ${e}
        </div>

        ${t}
      </div>
    `}else E===`files`&&(o=`
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-sm);">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:12px;">
            <thead>
              <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border);color:var(--text-muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">
                <th style="padding:10px 14px;text-align:left;">Nama Berkas Dokumen</th>
                <th style="padding:10px 14px;text-align:left;">Kategori</th>
                <th style="padding:10px 14px;text-align:left;">Lead / Klien</th>
                <th style="padding:10px 14px;text-align:left;">Ukuran</th>
                <th style="padding:10px 14px;text-align:left;">Tanggal Unggah</th>
                <th style="padding:10px 14px;text-align:left;">Status</th>
                <th style="padding:10px 14px;text-align:right;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              ${rt.map(e=>`
                <tr style="border-bottom:1px solid var(--border-light);transition:background var(--transition);" class="hover-row">
                  <td style="padding:10px 14px;">
                    <div style="display:flex;align-items:center;gap:8px;font-weight:700;color:var(--text-primary);">
                      <span style="color:var(--indigo-500);display:flex;align-items:center;">${nt.fileText}</span>
                      <span>${e.fileName}</span>
                    </div>
                  </td>
                  <td style="padding:10px 14px;color:var(--text-secondary);font-weight:500;">
                    ${e.category}
                  </td>
                  <td style="padding:10px 14px;">
                    <div style="font-weight:700;color:var(--text-primary);">${e.code}</div>
                    <div style="font-size:11px;color:var(--text-muted);">${e.customer}</div>
                  </td>
                  <td style="padding:10px 14px;font-family:var(--font-mono);color:var(--text-muted);">
                    ${e.size}
                  </td>
                  <td style="padding:10px 14px;font-family:var(--font-mono);color:var(--text-muted);">
                    ${e.date}
                  </td>
                  <td style="padding:10px 14px;">
                    <span class="badge badge-purple" style="font-size:10px;font-weight:700;">
                      ${e.status}
                    </span>
                  </td>
                  <td style="padding:10px 14px;text-align:right;">
                    <button
                      class="btn-download-file btn btn-ghost btn-xs"
                      data-file-name="${e.fileName}"
                      style="color:var(--indigo-500);font-weight:700;display:inline-flex;align-items:center;gap:4px;"
                    >
                      ${nt.download}
                      <span>Unduh</span>
                    </button>
                  </td>
                </tr>
              `).join(``)}
            </tbody>
          </table>
        </div>
      </div>
    `);return`
    <div style="flex:1;overflow-y:auto;padding:20px;background:var(--bg-primary);display:flex;flex-direction:column;gap:16px;">
      <!-- Main Container Header -->
      <div style="background:var(--bg-card);border-radius:var(--radius-xl);border:1px solid var(--border);padding:16px 20px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:var(--radius-lg);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);color:var(--indigo-500);display:flex;align-items:center;justify-content:center;">
              ${nt.layers}
            </div>
            <div>
              <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);letter-spacing:-.01em;">
                Tracking Workflow Sales Pipeline & Timeline Aktivitas
              </h3>
              <p style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                Pantau kepatuhan SLA tahapan, timeline follow-up terpadu, dan repositori berkas file dokumen lead.
              </p>
            </div>
          </div>

          <!-- Tab Switcher -->
          <div style="display:flex;align-items:center;gap:4px;background:var(--bg-secondary);padding:3px;border-radius:var(--radius-lg);border:1px solid var(--border);font-size:12px;font-weight:700;">
            <button class="btn-tab-workflow ${E===`workflow`?`active`:``}" data-tab="workflow" style="padding:6px 12px;border:none;border-radius:var(--radius-md);cursor:pointer;${E===`workflow`?`background:var(--bg-card);color:var(--indigo-500);box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);`}">
              Workflow & Gate Rules
            </button>
            <button class="btn-tab-workflow ${E===`timeline`?`active`:``}" data-tab="timeline" style="padding:6px 12px;border:none;border-radius:var(--radius-md);cursor:pointer;${E===`timeline`?`background:var(--bg-card);color:var(--indigo-500);box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);`}">
              Timeline Follow-Up (${t.length})
            </button>
            <button class="btn-tab-workflow ${E===`files`?`active`:``}" data-tab="files" style="padding:6px 12px;border:none;border-radius:var(--radius-md);cursor:pointer;${E===`files`?`background:var(--bg-card);color:var(--indigo-500);box-shadow:var(--shadow-sm);`:`background:transparent;color:var(--text-muted);`}">
              Tahap File & Dokumen (${rt.length})
            </button>
          </div>
        </div>

        <!-- Rendered Tab Content -->
        ${o}
      </div>
    </div>
  `}function at(e){e.querySelectorAll(`.btn-tab-workflow[data-tab]`).forEach(t=>{t.addEventListener(`click`,()=>{E=t.dataset.tab,e.innerHTML=it(),at(e)})}),e.querySelectorAll(`.btn-filter-act[data-act-type]`).forEach(t=>{t.addEventListener(`click`,()=>{$e=t.dataset.actType,et=1,e.innerHTML=it(),at(e)})}),e.querySelectorAll(`.btn-download-file[data-file-name]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.fileName;window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Mengunduh berkas ${t}...`,type:`info`}}))})}),Be(e,t=>{et=t,e.innerHTML=it(),at(e)},t=>{tt=t,et=1,e.innerHTML=it(),at(e)},`workflow-timeline-pagination`)}function ot(e){return e.innerHTML=it(),at(e),b.subscribe(()=>{let t=b.getState();t.activeView===`pipeline`&&t.viewMode===`workflow`&&(e.innerHTML=it(),at(e))})}var st=!1,ct=0,lt=null,ut=null,dt=null,D=null,ft=[{id:`vn-1`,title:`Voice Note: Notulen Meeting Teknis Modul Payroll & Geofence`,durationSeconds:42,recordedAt:`15 Okt 2026, 14:15`,transcript:`Pak Budi menjelaskan bahwa pabrik di Bekasi punya 4 pintu masuk shift malam. Validasi GPS harus anti fake GPS dengan selfie liveness. Untuk payroll, harus ada pembulatan pajak PPh 21 dan kirim slip gaji password tanggal 25 tiap bulan.`,aiSummaryDoc:{title:`Ringkasan Eksekutif Kebutuhan Teknis (Hasil Analisis Voice Note)`,executiveSummary:`Klien PT Maju Sejahtera Industri membutuhkan sistem absensi shift geofencing anti-fake GPS dan engine payroll otomatis dengan distribusi slip gaji terenkripsi setiap tanggal 25.`,clientNeeds:[`Geofencing GPS radius 50 meter di 4 gerbang pabrik Bekasi`,`Liveness detection selfie anti-mock location`,`Batch processing payroll 500+ karyawan otomatis setiap tanggal 25`,`Slip gaji PDF terenkripsi dengan password tanggal lahir karyawan`],technicalScope:[`Modul Transaksi No. 4: Absensi GPS Geofencing (12 Mandays)`,`Modul Transaksi No. 6: Payroll Engine Terenkripsi (20 Mandays)`,`Modul Non-Functional: Enkripsi data UU PDP & ISO 27001 (8 Mandays)`],actionItems:[{task:`Verifikasi koordinat titik GPS 4 gerbang pabrik`,pic:`Asisyah Sarah Azzahra`,due:`20 Okt 2026`},{task:`Masukkan item enkripsi slip gaji ke draft RAB Rev. 2`,pic:`Presales Team`,due:`18 Okt 2026`},{task:`Kirim penawaran final dengan skema termin 30/40/30`,pic:`Asisyah Sarah Azzahra`,due:`21 Okt 2026`}]}}],pt=[{id:`f-1`,name:`Kerangka_Acuan_Kerja_HRIS_PT_Maju.pdf`,size:`2.4 MB`,type:`document`,uploadedAt:`12 Okt 2026`},{id:`f-2`,name:`Rekaman_Diskusi_Zoom_Discovery_HRD.mp3`,size:`8.7 MB`,type:`audio`,uploadedAt:`14 Okt 2026`},{id:`f-3`,name:`Form_Kompensasi_Benefit_Karyawan.xlsx`,size:`1.1 MB`,type:`document`,uploadedAt:`15 Okt 2026`}],O={mic:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>`,square:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><rect width="18" height="18" x="3" y="3" rx="2"/></svg>`,play:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="6 3 20 12 6 21 6 3"/></svg>`,pause:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><rect width="4" height="16" x="6" y="4"/><rect width="4" height="16" x="14" y="4"/></svg>`,sparkles:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>`,eye:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`,fileText:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>`,fileAudio:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 22h.5c.5 0 1-.2 1.4-.6.4-.4.6-.9.6-1.4V7.5L14.5 2H6c-.5 0-1 .2-1.4.6C4.2 3 4 3.5 4 4v3"/><polyline points="14 2 14 8 20 8"/><path d="M10 20v-1a2 2 0 1 1 4 0v1a2 2 0 1 1-4 0Z"/><path d="M6 20v-1a2 2 0 1 0-4 0v1a2 2 0 1 0 4 0Z"/><path d="M2 19v-3a6 6 0 0 1 12 0v3"/></svg>`,upload:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>`,download:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>`,close:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`};function mt(e){let t=Math.floor(e/60),n=e%60;return`${t.toString().padStart(2,`0`)}:${n.toString().padStart(2,`0`)}`}function k(e,t,n){let r=ft.map(e=>{let t=ut===e.id,n=dt===e.id;return`
      <div style="padding:14px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-xl);display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <button
              class="btn-audio-play"
              data-vn-id="${e.id}"
              style="width:32px;height:32px;border-radius:var(--radius-full);background:var(--indigo-500);color:#ffffff;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:var(--shadow-sm);"
              title="${t?`Pause`:`Play`}"
            >
              ${t?O.pause:O.play}
            </button>
            <div>
              <h4 style="font-size:12px;font-weight:700;color:var(--text-primary);">${e.title}</h4>
              <div style="display:flex;align-items:center;gap:6px;font-size:10px;color:var(--text-muted);font-family:var(--font-mono);margin-top:2px;">
                <span>⏱ ${e.durationSeconds} detik</span>
                <span>•</span>
                <span>${e.recordedAt}</span>
              </div>
            </div>
          </div>

          <div style="display:flex;align-items:center;gap:6px;">
            <button
              class="btn-vn-summarize btn btn-sm btn-primary"
              data-vn-id="${e.id}"
              ${n?`disabled`:``}
              style="font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"
            >
              ${O.sparkles}
              <span>${n?`AI Meringkas Audio...`:`Transkrip & Ringkas via AI`}</span>
            </button>

            ${e.aiSummaryDoc?`
              <button
                class="btn-preview-summary btn btn-sm btn-secondary"
                data-vn-id="${e.id}"
                style="font-size:11px;font-weight:700;color:var(--indigo-500);border-color:var(--indigo-500);display:inline-flex;align-items:center;gap:4px;"
              >
                ${O.eye}
                <span>Lihat Rangkuman</span>
              </button>
            `:``}
          </div>
        </div>

        ${e.transcript?`
          <div style="padding:10px 12px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);font-size:12px;color:var(--text-secondary);line-height:1.5;">
            <strong style="color:var(--indigo-500);font-size:11px;display:block;margin-bottom:2px;">Transkrip Suara:</strong>
            "${e.transcript}"
          </div>
        `:``}
      </div>
    `}).join(``),i=pt.map(e=>{let t=e.type===`audio`||e.name.endsWith(`.mp3`),n=t?`#8b5cf6`:`#ef4444`,r=t?`<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${n}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 22h.5c.5 0 1-.2 1.4-.6.4-.4.6-.9.6-1.4V7.5L14.5 2H6c-.5 0-1 .2-1.4.6C4.2 3 4 3.5 4 4v3"/><polyline points="14 2 14 8 20 8"/><path d="M10 20v-1a2 2 0 1 1 4 0v1a2 2 0 1 1-4 0Z"/><path d="M6 20v-1a2 2 0 1 0-4 0v1a2 2 0 1 0 4 0Z"/><path d="M2 19v-3a6 6 0 0 1 12 0v3"/></svg>`:`<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${n}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>`;return`
      <div style="padding:14px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius-xl);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
          <span style="display:flex;align-items:center;flex-shrink:0;padding:6px;background:${t?`rgba(139,92,246,0.1)`:`rgba(239,68,68,0.1)`};border-radius:var(--radius-md);">
            ${r}
          </span>
          <div style="min-width:0;">
            <p style="font-size:12px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${e.name}">
              ${e.name}
            </p>
            <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">
              ${e.size} • ${e.uploadedAt}
            </span>
          </div>
        </div>

        <button
          class="btn-file-download btn btn-ghost btn-xs"
          data-file-name="${e.name}"
          title="Download Berkas"
          style="color:var(--indigo-500);padding:6px;border-radius:var(--radius-md);"
        >
          ${O.download}
        </button>
      </div>
    `}).join(``),a=``;return D&&(a=`
      <div class="modal-overlay" id="summary-doc-modal-backdrop" style="background:rgba(15,23,42,.65);backdrop-filter:blur(4px);z-index:100;">
        <div class="modal modal-lg" onclick="event.stopPropagation();" style="max-height:85vh;padding:24px;display:flex;flex-direction:column;gap:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="color:var(--indigo-500);">${O.sparkles}</span>
              <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);">
                ${D.title}
              </h3>
            </div>
            <button id="btn-close-summary-modal" class="modal-close" title="Tutup">
              ${O.close}
            </button>
          </div>

          <div style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;font-size:12px;">
            <div style="padding:14px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);">
              <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--indigo-500);display:block;margin-bottom:4px;">
                1. Ringkasan Eksekutif Hasil Voice Note:
              </span>
              <p style="color:var(--text-primary);line-height:1.6;">
                ${D.executiveSummary}
              </p>
            </div>

            <div>
              <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--text-secondary);display:block;margin-bottom:6px;">
                2. Poin-Poin Kebutuhan Klien:
              </span>
              <ul style="list-style-type:disc;padding-left:18px;color:var(--text-secondary);display:flex;flex-direction:column;gap:4px;">
                ${D.clientNeeds.map(e=>`<li>${e}</li>`).join(``)}
              </ul>
            </div>

            <div>
              <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--text-secondary);display:block;margin-bottom:6px;">
                3. Dampak Terhadap Scope & Kontrak:
              </span>
              <ul style="list-style-type:disc;padding-left:18px;color:var(--text-secondary);display:flex;flex-direction:column;gap:4px;">
                ${D.technicalScope.map(e=>`<li>${e}</li>`).join(``)}
              </ul>
            </div>

            <div>
              <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:var(--text-secondary);display:block;margin-bottom:6px;">
                4. Action Items & PIC Tindak Lanjut:
              </span>
              <div style="border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
                ${D.actionItems.map(e=>`
                  <div style="padding:10px 14px;display:flex;align-items:center;justify-content:space-between;background:var(--bg-secondary);border-bottom:1px solid var(--border-light);">
                    <div>
                      <strong style="color:var(--text-primary);display:block;">${e.task}</strong>
                      <span style="font-size:10px;color:var(--text-muted);">PIC: ${e.pic}</span>
                    </div>
                    <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--indigo-500);">
                      Target: ${e.due}
                    </span>
                  </div>
                `).join(``)}
              </div>
            </div>
          </div>

          <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding-top:12px;border-top:1px solid var(--border);">
            <button id="btn-dismiss-summary" class="btn btn-secondary btn-sm" style="font-weight:600;">
              Tutup
            </button>
            <button id="btn-save-summary-note" class="btn btn-primary btn-sm" style="font-weight:700;">
              Simpan ke Catatan Internal Lead
            </button>
          </div>
        </div>
      </div>
    `),`
    <div style="display:flex;flex-direction:column;gap:20px;">
      <!-- 1. Voice Note Recorder Section -->
      <div class="card" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border);padding-bottom:12px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:var(--radius-lg);background:linear-gradient(135deg, var(--red-500), var(--indigo-600));color:#ffffff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(239,68,68,.3);">
              ${O.mic}
            </div>
            <div>
              <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);">
                Fitur Voice Note & AI Audio-to-Document
              </h3>
              <p style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                Rekam suara langsung atau unggah berkas audio → AI otomatis meringkas notulen menjadi poin-poin dokumen terstruktur.
              </p>
            </div>
          </div>

          <!-- Record Trigger Button -->
          <div>
            ${st?`
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:var(--radius-md);background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--red-500);font-family:var(--font-mono);font-size:12px;font-weight:700;">
                  <span style="width:8px;height:8px;border-radius:var(--radius-full);background-color:var(--red-500);animation:pulse 1s infinite;"></span>
                  <span id="recording-timer-badge">Merekam: ${mt(ct)}</span>
                </div>
                <button id="btn-stop-vn-record" class="btn btn-secondary" style="font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px;background:var(--slate-800);color:#ffffff;border-color:var(--slate-700);">
                  <span>⏹ Berhenti & Simpan</span>
                </button>
              </div>
            `:`
              <button id="btn-start-vn-record" class="btn btn-danger" style="font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                <span>🎙 Mulai Rekam Voice Note</span>
              </button>
            `}
          </div>
        </div>

        <!-- Voice Notes Stream -->
        <div style="display:flex;flex-direction:column;gap:12px;">
          ${r}
        </div>
      </div>

      <!-- 2. Attached Files & Repositories -->
      <div class="card" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:12px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="color:var(--indigo-500);">${O.fileText}</span>
            <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);">
              Lampiran Berkas Dokumen & Audio Terkait Proyek
            </h3>
          </div>

          <label class="btn btn-secondary btn-sm" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-weight:700;">
            ${O.upload}
            <span>+ Unggah Berkas Baru</span>
            <input type="file" id="file-upload-input" style="display:none;" />
          </label>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:12px;">
          ${i}
        </div>
      </div>

      <!-- 3. AI Summary Modal Preview -->
      ${a}
    </div>
  `}function A(e,t){let n=e.querySelector(`#btn-start-vn-record`);n&&n.addEventListener(`click`,()=>{st=!0,ct=0,lt=setInterval(()=>{ct++;let t=e.querySelector(`#recording-timer-badge`);t&&(t.textContent=`Merekam: ${mt(ct)}`)},1e3),e.innerHTML=k(),A(e,t)});let r=e.querySelector(`#btn-stop-vn-record`);r&&r.addEventListener(`click`,()=>{st=!1,lt&&clearInterval(lt);let n=ct>0?ct:15;ft=[{id:`vn-${Date.now()}`,title:`Voice Note: Rekaman Suara Sales Baru (${new Date().toLocaleTimeString([],{hour:`2-digit`,minute:`2-digit`})})`,durationSeconds:n,recordedAt:new Date().toLocaleString(`id-ID`,{dateStyle:`medium`,timeStyle:`short`}),transcript:`Klien mengonfirmasi persetujuan termin pembayaran 30% Down Payment dan 40% UAT. Meminta klausul garansi SLA diperpanjang dari 60 hari menjadi 90 hari.`},...ft],e.innerHTML=k(),A(e,t),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Voice Note berhasil direkam & disimpan!`,type:`success`}}))}),e.querySelectorAll(`.btn-audio-play[data-vn-id]`).forEach(n=>{n.addEventListener(`click`,()=>{let r=n.dataset.vnId;ut=ut===r?null:r,e.innerHTML=k(),A(e,t)})}),e.querySelectorAll(`.btn-vn-summarize[data-vn-id]`).forEach(n=>{n.addEventListener(`click`,()=>{let r=n.dataset.vnId,i=ft.find(e=>e.id===r);i&&(dt=r,e.innerHTML=k(),A(e,t),setTimeout(()=>{let n={title:`Dokumen Hasil Transkrip & Rangkuman AI - ${i.title}`,executiveSummary:`Berdasarkan rekaman suara berdurasi ${i.durationSeconds} detik, klien menyetujui seluruh parameter scope inti dan meminta penyesuaian klausul garansi serta penjadwalan meeting lanjutan.`,clientNeeds:[`Penyetujuan skema termin pembayaran 30/40/30 sesuai penawaran`,`Penyesuaian Pasal 11 MoU: Masa garansi pemeliharaan 90 hari kalender`,`Penyediaan PIC pendampingan onsite saat hari pertama UAT`],technicalScope:[`Klausul MoU Pasal 11: Warranty SLA 90 hari`,`Setting TOP Termin 1 DP 30% dan Termin 2 UAT 40%`],actionItems:[{task:`Perbarui Pasal 11 MoU pada dokumen Quotation`,pic:`Legal Officer`,due:`Besok`},{task:`Kirim draft kontrak final bertanda tangan`,pic:`Asisyah Sarah Azzahra`,due:`22 Okt 2026`}]};i.aiSummaryDoc=n,dt=null,D=n,e.innerHTML=k(),A(e,t),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Transkrip & Ringkasan Dokumen AI berhasil digenerate!`,type:`success`}}))},1e3))})}),e.querySelectorAll(`.btn-preview-summary[data-vn-id]`).forEach(n=>{n.addEventListener(`click`,()=>{let r=n.dataset.vnId,i=ft.find(e=>e.id===r);i&&i.aiSummaryDoc&&(D=i.aiSummaryDoc,e.innerHTML=k(),A(e,t))})}),[e.querySelector(`#btn-close-summary-modal`),e.querySelector(`#btn-dismiss-summary`),e.querySelector(`#summary-doc-modal-backdrop`)].forEach(n=>{n&&n.addEventListener(`click`,()=>{D=null,e.innerHTML=k(),A(e,t)})});let i=e.querySelector(`#btn-save-summary-note`);i&&i.addEventListener(`click`,()=>{D&&typeof t==`function`&&t(`[Hasil AI Audio Summary] ${D.executiveSummary}`),D=null,e.innerHTML=k(),A(e,t),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Rangkuman dokumen berhasil disimpan ke Catatan Internal Lead!`,type:`success`}}))});let a=e.querySelector(`#file-upload-input`);a&&a.addEventListener(`change`,n=>{let r=n.target.files;if(!r||r.length===0)return;let i=r[0],a=i.type.startsWith(`audio/`)||i.name.endsWith(`.mp3`)||i.name.endsWith(`.wav`);pt=[{id:`f-${Date.now()}`,name:i.name,size:`${(i.size/1048576).toFixed(1)} MB`,type:a?`audio`:`document`,uploadedAt:`Hari ini`},...pt],e.innerHTML=k(),A(e,t),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Berkas "${i.name}" berhasil diunggah!`,type:`success`}}))}),e.querySelectorAll(`.btn-file-download[data-file-name]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.fileName;window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Mengunduh berkas ${t}...`,type:`info`}}))})})}var ht=t({bindLeadDetailPageEvents:()=>z,mount:()=>B,renderLeadDetailPage:()=>R}),j=`overview`,gt=`Client ingin aplikasi HRD untuk 500 karyawan di pabrik Bekasi, ada absensi GPS geofencing & anti-fake GPS selfie, cuti/izin/lembur berjenjang, payroll engine batch 500 staf, mobile app ESS Android/iOS, integrasi biometric fingerprint dan bank transfer.`,_t=`Web & Mobile`,vt=`Enterprise`,M=10,yt=!0,bt=!0,xt=!1,St=0,Ct=[{name:`Kerangka_Acuan_Kerja_HRIS_PT_Maju.pdf`,size:`2.4 MB`}],N=!0,P=`30/40/30`,F=`grand_total`,I=null,wt=``,Tt=`pendahuluan`,Et=!1,Dt=`2026-10-20T14:00`,Ot=60,kt=[`Klien sangat concern dengan integrasi mesin fingerprint lama merk ZKTeco.`,`Pak Budi meminta garansi SLA bugs minimal 90 hari setelah BAST.`,`PIC Finance menginginkan termin pembayaran dibagi menjadi DP 30%, Progress 40%, dan Pelunasan BAST 30%.`],At=[{id:`lsf-1`,stage:`New Lead`,category:`Brief KAK / Kerangka Acuan Kerja`,name:`Kerangka_Acuan_Kerja_HRIS_PT_Maju.pdf`,size:`2.4 MB`,date:`12 Okt 2026`,uploader:`Budi Santoso (Klien)`,verified:!0,type:`pdf`},{id:`lsf-2`,stage:`Follow Up`,category:`Notulen & Voice Note Audio`,name:`Voice_Note_Notulen_Meeting_Teknis.mp3`,size:`4.2 MB`,date:`14 Okt 2026`,uploader:`Asisyah Sarah Azzahra (PM)`,verified:!0,type:`audio`},{id:`lsf-3`,stage:`Requirement`,category:`FSD & AI Mandays RAB Specification`,name:`FSD_HRIS_500_Employee_System_Spec_v1.pdf`,size:`3.1 MB`,date:`15 Okt 2026`,uploader:`System AI Presales`,verified:!0,type:`pdf`},{id:`lsf-4`,stage:`Quotation`,category:`Penawaran Resmi + 22 Klausul MoU`,name:`Quotation_MoU_QT-2026-10-0012_Rev2.pdf`,size:`1.8 MB`,date:`15 Okt 2026`,uploader:`Asisyah Sarah Azzahra (PM)`,verified:!0,type:`pdf`},{id:`lsf-5`,stage:`Deal`,category:`Draft Kontrak SPK E-Sign & BAST`,name:`SPK_ASTACODE_PT_Maju_Sejahtera_Signed.pdf`,size:`2.7 MB`,date:`16 Okt 2026`,uploader:`Legal Counsel ASTACODE`,verified:!0,type:`pdf`}],jt={new_lead:`Verifikasi kelengkapan KAK/Brief dan PIC`,follow_up:`Jadwalkan Discovery Google Meet & Notulen Audio`,requirement:`Generate & Review Estimasi Mandays RAB Presales`,quotation:`Pastikan Setting TOP Balance 100% & 22 Klausul MoU`,negotiation:`Finalisasi diskon dan approval termin pembayaran`,deal:`Penandatanganan SPK Digital & Auto-generate Proyek ERP`,reject:`Catatan evaluasi penolakan tersimpan di CRM`},L={arrowLeft:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>`,arrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`,building:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>`,video:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2"/></svg>`,messageSquare:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,fileCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/></svg>`,checkCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`,sparkles:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`,trash:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>`,close:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,phone:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>`,fileText:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>`,paperclip:`<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>`,sliders:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/></svg>`,eye:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`,lock:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,shieldCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>`,dollarSign:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,printer:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>`,download:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>`,alertTriangle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>`};function R(){let{leads:e,selectedLeadId:t,activities:n}=b.getState(),r=e.find(e=>e.id===t);if(!r)return`
      <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;padding:32px;color:var(--text-muted);">
        <p>Lead tidak ditemukan atau belum dipilih.</p>
        <button id="btn-back-not-found" class="btn btn-primary btn-sm">
          Kembali ke Pipeline
        </button>
      </div>
    `;let i=r.rab,s=i?i.grand_total:r.est_value;if(!I){let e=r.quotations&&r.quotations[0];I=e?e.top_scheme.items:l(`30/40/30`,s).items}c(I,s,F,P,!0);let d=y.find(e=>e.id===r.stage_id)||y[0],f=n.filter(e=>e.lead_id===r.id),p=y.findIndex(e=>e.id===r.stage_id),m=r.stage_id===`reject`?0:Math.min(100,Math.max(5,p/(y.length-2)*100)),h=y.map((e,t)=>{let n=t<p&&r.stage_id!==`reject`,i=e.id===r.stage_id,a=e.id===`reject`,o=``;return o=i?a?`background:#dc2626;color:#ffffff;box-shadow:0 0 0 4px rgba(220,38,38,0.25);border:none;`:`background:#4f46e5;color:#ffffff;box-shadow:0 0 0 4px rgba(99,102,241,0.25);border:none;`:n?`background:#10b981;color:#ffffff;box-shadow:0 0 0 2px rgba(16,185,129,0.3);border:none;`:a?`background:var(--bg-secondary);color:#ef4444;border:1px solid rgba(239,68,68,0.3);`:`background:var(--bg-card);color:var(--text-muted);border:2px dashed #cbd5e1;`,`
      <button
        class="btn-stage-node"
        data-stage-id="${e.id}"
        style="display:flex;flex-direction:column;align-items:center;border:none;background:transparent;cursor:pointer;gap:6px;transition:all var(--transition);position:relative;z-index:2;min-width:60px;"
        title="Validasi & pindah ke: ${e.name} (${e.probability}%)"
      >
        <div style="width:42px;height:42px;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;font-family:var(--font-mono);font-size:11px;font-weight:800;transition:all var(--transition);${o}">
          <span>${e.probability}%</span>
          ${n?`<span style="font-size:9px;line-height:1;">✓</span>`:``}
        </div>
        <div style="text-align:center;">
          <div style="font-size:11px;font-weight:${i?`800`:`600`};color:${i?`var(--indigo-600)`:`var(--text-primary)`};white-space:nowrap;">
            ${e.name}
          </div>
          <span style="font-size:10px;color:var(--text-muted);font-family:var(--font-mono);">
            ${i?`<span style="color:var(--indigo-600);font-weight:700;">Aktif</span>`:n?`Selesai`:`Target`}
          </span>
        </div>
      </button>
    `}).join(``),g=[{id:`overview`,label:`Overview`},{id:`activity`,label:`Timeline & Meet`,badge:f.length},{id:`files`,label:`Tahap File & Dokumen`,badge:At.length},{id:`requirement`,label:`✨ Requirement (AI)`},{id:`rab`,label:`RAB / Mandays`,badge:i?.items?i.items.length:0},{id:`quotation`,label:`Quotation & Setting TOP`,badge:`MoU`},{id:`contacts`,label:`Kontak & Stakeholder`},{id:`notes`,label:`Voice Notes & Dokumen AI`,badge:kt.length},{id:`history`,label:`Audit Timeline`}],_=``;if(j===`overview`)_=`
      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(360px, 1fr));gap:24px;">
        <!-- Left 2 Cols: Summary & Next Action -->
        <div style="display:flex;flex-direction:column;gap:20px;grid-column:span 2;">
          <!-- AI Presales Assistant: Next Best Action Card -->
          <div style="padding:18px 20px;border-radius:var(--radius-2xl);background:linear-gradient(135deg, rgba(99,102,241,.08), rgba(139,92,246,.08));border:1px solid rgba(99,102,241,.25);box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:flex-start;gap:14px;">
              <div style="width:38px;height:38px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#ffffff;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(99,102,241,0.3);">
                ${L.sparkles}
              </div>
              <div style="flex:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                  <h4 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--indigo-600);letter-spacing:.05em;margin:0;">
                    AI PRESALES ASSISTANT: NEXT BEST ACTION
                  </h4>
                  <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:9999px;background:rgba(99,102,241,0.15);color:var(--indigo-600);">
                    Confidence 94%
                  </span>
                </div>
                <p style="font-size:12px;font-weight:600;color:var(--text-primary);margin-top:6px;line-height:1.5;">
                  ${r.next_action||`Jadwalkan Google Meet teknis untuk konfirmasi detail modul dan skema termin pembayaran.`}
                </p>
                <div style="display:flex;align-items:center;gap:10px;margin-top:12px;">
                  <button id="btn-quick-schedule-meet" class="btn btn-primary" style="font-size:12px;font-weight:700;padding:7px 14px;border-radius:8px;">
                    Buka Jadwal Google Meet
                  </button>
                  <button id="btn-quick-goto-quotation" class="btn btn-secondary" style="font-size:12px;font-weight:700;padding:7px 14px;border-radius:8px;">
                    Review Dokumen Quotation
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Ringkasan Kebutuhan & Peluang -->
          <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:14px;">
            <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin:0;">
              Ringkasan Kebutuhan & Peluang
            </h3>
            <p style="font-size:12px;color:var(--text-secondary);line-height:1.6;background:var(--bg-secondary);padding:14px;border-radius:var(--radius-lg);border:1px solid var(--border);margin:0;">
              ${r.ai_summary||r.title}
            </p>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:12px;padding-top:4px;">
              <div style="padding:12px 14px;background:var(--bg-secondary);border-radius:var(--radius-lg);border:1px solid var(--border);">
                <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Nilai Estimasi</div>
                <div style="font-size:13px;font-family:var(--font-mono);font-weight:800;color:#16a34a;margin-top:2px;">
                  ${a(r.est_value||0)}
                </div>
              </div>
              <div style="padding:12px 14px;background:var(--bg-secondary);border-radius:var(--radius-lg);border:1px solid var(--border);">
                <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Total Mandays</div>
                <div style="font-size:13px;font-family:var(--font-mono);font-weight:800;color:var(--indigo-600);margin-top:2px;">
                  ${i?`${i.total_mandays} Mandays`:`152 Mandays`}
                </div>
              </div>
              <div style="padding:12px 14px;background:var(--bg-secondary);border-radius:var(--radius-lg);border:1px solid var(--border);">
                <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Sumber Pertama</div>
                <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-top:2px;">
                  ${r.source}
                </div>
              </div>
              <div style="padding:12px 14px;background:var(--bg-secondary);border-radius:var(--radius-lg);border:1px solid var(--border);">
                <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Target Closing</div>
                <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-top:2px;">
                  ${r.target_closing||`-`}
                </div>
              </div>
            </div>
          </div>

          <!-- Mini Timeline of Recent Activities -->
          <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin:0;">
                Aktivitas & Riwayat Terakhir
              </h3>
              <button id="btn-see-all-activities" style="border:none;background:none;font-size:11px;font-weight:700;color:var(--indigo-600);cursor:pointer;">
                Lihat Semua (${f.length})
              </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;">
              ${f.map(e=>`
                <div style="padding:12px 14px;border-radius:var(--radius-lg);background:var(--bg-secondary);border:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;">
                  <div style="width:32px;height:32px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    ${e.type===`google_meet`?L.video:L.phone}
                  </div>
                  <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                      <h5 style="font-size:12px;font-weight:700;color:var(--text-primary);margin:0;">${e.title}</h5>
                      <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">${new Date(e.scheduled_at).toLocaleDateString(`id-ID`)}</span>
                    </div>
                    <p style="font-size:11px;color:var(--text-secondary);margin:3px 0 0 0;">${e.notes||`-`}</p>
                    ${e.meet_link?`<a href="${e.meet_link}" target="_blank" rel="noreferrer" style="font-size:11px;color:#10b981;font-weight:700;text-decoration:underline;margin-top:4px;display:inline-block;">Buka Google Meet (${e.meet_link})</a>`:``}
                  </div>
                </div>
              `).join(``)}
            </div>
          </div>
        </div>

        <!-- Right 1 Col: Master Customer 360° Profile -->
        <div style="display:flex;flex-direction:column;gap:20px;">
          <!-- MASTER CUSTOMER 360° Card -->
          <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:14px;box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:10px;">
              <h3 style="font-size:12px;font-weight:800;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin:0;">
                MASTER CUSTOMER 360°
              </h3>
              <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:800;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);">
                ${r.customer&&r.customer.lifecycle_status||`Prospect`}
              </span>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
              <div>
                <span style="color:var(--text-muted);font-size:11px;display:block;">Nama Legal:</span>
                <p style="font-weight:800;color:var(--text-primary);margin:2px 0 0 0;">
                  ${r.customer?r.customer.legal_name:`PT Retail Nusantara Prima`}
                </p>
              </div>

              <div>
                <span style="color:var(--text-muted);font-size:11px;display:block;">Industri & Ukuran:</span>
                <p style="font-weight:600;color:var(--text-secondary);margin:2px 0 0 0;">
                  ${r.customer?`${r.customer.industry} • ${r.customer.company_size}`:`Retail • Enterprise`}
                </p>
              </div>

              <div>
                <span style="color:var(--text-muted);font-size:11px;display:block;">Alamat & Kota:</span>
                <p style="color:var(--text-secondary);margin:2px 0 0 0;line-height:1.4;">
                  ${r.customer?`${r.customer.address}, ${r.customer.city}, ${r.customer.province}`:`-`}
                </p>
              </div>

              <div>
                <span style="color:var(--text-muted);font-size:11px;display:block;">NPWP:</span>
                <p style="font-family:var(--font-mono);font-weight:700;color:var(--text-secondary);margin:2px 0 0 0;">
                  ${r.customer?.npwp||`01.234.567.8-429.000`}
                </p>
              </div>

              <div>
                <span style="color:var(--text-muted);font-size:11px;display:block;">First-Touch Source Channel:</span>
                <p style="font-weight:800;color:var(--indigo-600);margin:2px 0 0 0;">
                  ${r.customer?.first_touch_source||r.source} (Permanent Attribution)
                </p>
              </div>
            </div>

            <!-- PIC Utama Box -->
            <div style="border-top:1px solid var(--border);padding-top:12px;">
              <span style="font-size:10px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;display:block;margin-bottom:6px;">
                PIC Utama
              </span>
              <div style="padding:12px;border-radius:var(--radius-lg);background:var(--bg-secondary);border:1px solid var(--border);display:flex;flex-direction:column;gap:3px;font-size:12px;">
                <strong style="color:var(--text-primary);">${r.primary_contact?r.primary_contact.name:`-`}</strong>
                <span style="color:var(--text-muted);font-size:11px;">${r.primary_contact?r.primary_contact.job_title:`-`}</span>
                <span style="font-family:var(--font-mono);font-weight:700;color:#10b981;margin-top:4px;">
                  ${r.primary_contact?r.primary_contact.whatsapp_number:`-`}
                </span>
              </div>
            </div>
          </div>

          <!-- Tags & Metadata Peluang -->
          <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:12px;">
            <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin:0;">
              Tags & Metadata Peluang
            </h3>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
              ${(r.tags||[`ERP`,`HRIS`,`Custom`]).map(e=>`
                <span style="padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;background:var(--bg-secondary);color:var(--text-secondary);border:1px solid var(--border);">
                  #${e}
                </span>
              `).join(``)}
            </div>
          </div>
        </div>
      </div>
    `;else if(j===`files`)_=`
      <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:12px;">
          <div>
            <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">Tahap File & Dokumen Proyek</h3>
            <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0 0;">Dokumen teknis dan legalitas wajib pada setiap milestone pipeline (PRD Section 10).</p>
          </div>
          <button class="btn btn-secondary btn-sm" style="font-weight:700;">+ Unggah Dokumen</button>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;">
          ${At.map(e=>`
            <div style="padding:14px 16px;border-radius:var(--radius-lg);background:var(--bg-secondary);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:14px;">
              <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(99,102,241,0.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                  ${L.fileText}
                </div>
                <div>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <strong style="font-size:12px;color:var(--text-primary);">${e.name}</strong>
                    <span style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:4px;background:rgba(16,185,129,0.1);color:#10b981;">Terverifikasi</span>
                  </div>
                  <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                    ${e.stage} • ${e.category} • ${e.size} • Diunggah oleh ${e.uploader}
                  </div>
                </div>
              </div>
              <div style="display:flex;gap:6px;">
                <button class="btn btn-secondary btn-xs" style="font-weight:700;">Preview</button>
                <button class="btn btn-primary btn-xs" style="font-weight:700;">Unduh</button>
              </div>
            </div>
          `).join(``)}
        </div>
      </div>
    `;else if(j===`requirement`)_=`
      <div class="animate-fade-in" style="max-width:820px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">
        <div class="card" style="padding:24px;border-radius:16px;background:var(--bg-card);border:1px solid var(--border);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:18px;">
          <!-- Header Card -->
          <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg, #4f46e5, #7c3aed);color:#ffffff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(99,102,241,0.25);flex-shrink:0;">
                ${L.sparkles}
              </div>
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;letter-spacing:-0.01em;">
                  AI Requirement-to-RAB Generator
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">
                  Input prompt dan/atau upload dokumen RFP/Brief — Hasil modul terstruktur & mandays editable.
                </p>
              </div>
            </div>
            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);">
              PRD Section 9
            </span>
          </div>

          <!-- Prompt Text Box -->
          <div style="display:flex;flex-direction:column;gap:6px;">
            <label for="req-prompt-input" style="font-size:12px;font-weight:700;color:var(--text-primary);">
              Prompt / Deskripsi Kebutuhan Solusi* :
            </label>
            <textarea
              id="req-prompt-input"
              rows="4"
              class="form-textarea focus-ring-indigo"
              style="width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:12px;line-height:1.6;font-family:inherit;resize:vertical;"
              placeholder="Ketik kebutuhan aplikasi, modul, pengguna, atau alur bisnis secara bebas..."
            >${gt}</textarea>
          </div>

          <!-- Upload Document Dropzone Box -->
          <div style="display:flex;flex-direction:column;gap:8px;">
            <label style="font-size:12px;font-weight:700;color:var(--text-primary);">
              Upload Dokumen Kebutuhan / RFP / KAK (Opsional / Bersama Prompt):
            </label>
            <input type="file" id="req-file-input" style="display:none;" accept=".pdf,.docx,.xlsx,.doc" multiple />
            <div
              id="req-dropzone-box"
              class="dropzone-box"
              style="padding:18px;border-radius:12px;border:2px dashed #cbd5e1;background:#f8fafc;text-align:center;display:flex;flex-direction:column;align-items:center;gap:8px;cursor:pointer;"
            >
              <span style="color:var(--text-muted);display:flex;align-items:center;">
                ${L.paperclip}
              </span>
              <p style="font-size:12px;color:var(--text-secondary);margin:0;">
                Drag & drop PDF, DOCX, XLSX brief RFP (maksimal 25MB)
              </p>
              <div id="req-uploaded-files-list" style="display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-top:4px;">
                ${Ct.map((e,t)=>`
                  <span
                    class="badge-file-pill"
                    style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:8px;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);font-size:11px;font-family:var(--font-mono);font-weight:600;"
                  >
                    <span>${e.name}</span>
                    <span style="color:var(--text-muted);font-size:10px;">(${e.size})</span>
                  </span>
                `).join(``)}
              </div>
            </div>
          </div>

          <!-- Sub-Card: Parameter Estimasi AI -->
          <div style="padding:16px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--text-primary);">
              <span style="color:var(--indigo-600);display:flex;align-items:center;">${L.sliders}</span>
              <span>🎚 Parameter Estimasi AI</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:12px;">
              <div>
                <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px;">Platform Target:</label>
                <select id="req-platform-select" class="form-select focus-ring-indigo" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;font-weight:600;">
                  <option value="Web & Mobile" ${_t===`Web & Mobile`?`selected`:``}>Web & Mobile ESS</option>
                  <option value="Web" ${_t===`Web`?`selected`:``}>Web Portal Saja</option>
                  <option value="Mobile" ${_t===`Mobile`?`selected`:``}>Mobile Apps Saja</option>
                </select>
              </div>

              <div>
                <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px;">Kompleksitas Solusi:</label>
                <select id="req-complexity-select" class="form-select focus-ring-indigo" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;font-weight:600;">
                  <option value="Enterprise" ${vt===`Enterprise`?`selected`:``}>Enterprise (500+ Staff)</option>
                  <option value="Standard" ${vt===`Standard`?`selected`:``}>Standard</option>
                  <option value="Basic" ${vt===`Basic`?`selected`:``}>Basic MVP</option>
                </select>
              </div>

              <div>
                <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:4px;">Contingency Biaya (%):</label>
                <input
                  type="number"
                  id="req-contingency-input"
                  class="form-input focus-ring-indigo"
                  value="${M}"
                  min="0"
                  max="50"
                  style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-card);color:var(--text-primary);font-size:12px;font-family:var(--font-mono);font-weight:700;"
                />
              </div>
            </div>

            <div style="display:flex;align-items:center;gap:24px;padding-top:4px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-primary);cursor:pointer;font-weight:500;">
                <input type="checkbox" id="req-chk-uat" ${yt?`checked`:``} style="accent-color:var(--indigo-600);cursor:pointer;" />
                <span>Termasuk Fase QA & UAT</span>
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-primary);cursor:pointer;font-weight:500;">
                <input type="checkbox" id="req-chk-training" ${bt?`checked`:``} style="accent-color:var(--indigo-600);cursor:pointer;" />
                <span>Termasuk Training & Manual Book</span>
              </label>
            </div>
          </div>

          <!-- Progress Bar when Generating -->
          ${xt?`
            <div class="animate-fade-in" style="padding:16px;border-radius:12px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.25);display:flex;flex-direction:column;gap:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;font-weight:700;color:var(--indigo-600);">
                <span style="display:flex;align-items:center;gap:8px;">
                  <span class="animate-spin" style="color:var(--amber-500);display:inline-flex;">${L.sparkles}</span>
                  <span>Memproses Requirement AI: Ekstraksi Scope, Breakdown Modul & Mandays...</span>
                </span>
                <span style="font-family:var(--font-mono);">${St}%</span>
              </div>
              <div style="width:100%;height:8px;border-radius:9999px;background:rgba(99,102,241,0.2);overflow:hidden;">
                <div
                  style="height:100%;width:${St}%;background:linear-gradient(90deg, #4f46e5, #7c3aed);transition:width 0.3s ease;border-radius:9999px;"
                ></div>
              </div>
            </div>
          `:``}

          <!-- Footer Card & Action -->
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding-top:8px;">
            <span style="font-size:12px;color:var(--text-muted);">
              Rate Card Software House: Rp 1.500.000 - Rp 1.900.000 / Mandays
            </span>

            <button
              id="btn-trigger-ai-rab"
              type="button"
              ${xt?`disabled`:``}
              class="btn btn-hover-lift"
              style="padding:10px 20px;border-radius:12px;background:linear-gradient(135deg, #4f46e5, #6366f1, #7c3aed);color:#ffffff;font-size:12px;font-weight:800;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(79,70,229,0.35);transition:all var(--transition);${xt?`opacity:0.6;cursor:not-allowed;`:``}"
            >
              <span class="${xt?`animate-spin`:``}" style="color:${xt?`var(--amber-400)`:`#fde047`};display:inline-flex;">
                ${L.sparkles}
              </span>
              <span>${xt?`Sedang Menganalisis...`:`✨ Generate RAB/Mandays Otomatis`}</span>
            </button>
          </div>
        </div>
      </div>
    `;else if(j===`rab`){let e=(i?i.items:[]).map((e,t)=>`
      <tr class="animate-slide-up" style="border-bottom:1px solid var(--border-light);">
        <td style="padding:10px 14px;font-family:var(--font-mono);font-size:11px;color:var(--text-muted);width:36px;text-align:center;">${t+1}</td>
        <td style="padding:10px 14px;width:130px;">
          <select
            class="rab-item-category-select form-select"
            data-item-id="${e.id}"
            style="width:100%;padding:4px 6px;font-size:11px;font-weight:600;border-radius:6px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-primary);outline:none;"
          >
            <option value="Master Data" ${e.category===`Master Data`?`selected`:``}>Master Data</option>
            <option value="Core Module" ${e.category===`Core Module`||e.category===`Transaksi`?`selected`:``}>Core Module</option>
            <option value="Transaksi" ${e.category===`Transaksi`?`selected`:``}>Transaksi</option>
            <option value="Laporan" ${e.category===`Laporan`?`selected`:``}>Laporan</option>
            <option value="Dashboard & KPI" ${e.category===`Dashboard & KPI`?`selected`:``}>Dashboard & KPI</option>
            <option value="Mobile" ${e.category===`Mobile`?`selected`:``}>Mobile</option>
            <option value="Integrasi" ${e.category===`Integrasi`?`selected`:``}>Integrasi</option>
            <option value="UI/UX & QA" ${e.category===`UI/UX & QA`||e.category===`PM & QA`?`selected`:``}>UI/UX & QA</option>
            <option value="PM & QA" ${e.category===`PM & QA`?`selected`:``}>PM & QA</option>
            <option value="Non-Functional" ${e.category===`Non-Functional`?`selected`:``}>Non-Functional</option>
            <option value="Infrastruktur" ${e.category===`Infrastruktur`?`selected`:``}>Infrastruktur</option>
            <option value="Training" ${e.category===`Training`?`selected`:``}>Training</option>
          </select>
        </td>
        <td style="padding:10px 14px;">
          <input
            type="text"
            class="rab-item-name-input"
            data-item-id="${e.id}"
            value="${(e.name||``).replace(/"/g,`&quot;`)}"
            style="width:100%;font-weight:700;color:var(--text-primary);font-size:12px;background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;outline:none;"
          />
          <input
            type="text"
            class="rab-item-desc-input"
            data-item-id="${e.id}"
            value="${(e.description||``).replace(/"/g,`&quot;`)}"
            placeholder="Deskripsi ruang lingkup modul..."
            style="width:100%;font-size:11px;color:var(--text-muted);background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;margin-top:2px;outline:none;"
          />
        </td>
        <td style="padding:10px 14px;text-align:center;width:80px;">
          <input
            type="number"
            min="1"
            class="rab-item-qty-input form-input"
            data-item-id="${e.id}"
            value="${e.qty}"
            style="width:65px;font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);padding:4px 6px;text-align:center;font-size:12px;border-radius:6px;border:1.5px solid var(--indigo-500);background:var(--bg-card);outline:none;"
          />
        </td>
        <td style="padding:10px 14px;width:95px;">
          <select
            class="rab-item-unit-select form-select"
            data-item-id="${e.id}"
            style="width:100%;padding:4px 6px;font-size:11px;font-weight:600;border-radius:6px;"
          >
            <option value="Mandays" ${e.unit===`Mandays`?`selected`:``}>Mandays</option>
            <option value="Paket" ${e.unit===`Paket`?`selected`:``}>Paket</option>
            <option value="Modul" ${e.unit===`Modul`?`selected`:``}>Modul</option>
            <option value="Bulan" ${e.unit===`Bulan`?`selected`:``}>Bulan</option>
            <option value="Lot" ${e.unit===`Lot`?`selected`:``}>Lot</option>
            <option value="Jam" ${e.unit===`Jam`?`selected`:``}>Jam</option>
          </select>
        </td>
        ${N?`
          <td style="padding:10px 14px;width:130px;">
            <input
              type="number"
              step="50000"
              class="rab-item-price-input form-input"
              data-item-id="${e.id}"
              value="${e.unit_price}"
              style="width:100%;font-family:var(--font-mono);font-weight:600;text-align:right;padding:4px 8px;font-size:11px;border-radius:6px;border:1px solid var(--border);background:var(--bg-card);outline:none;"
            />
          </td>
          <td style="padding:10px 14px;text-align:right;font-family:var(--font-mono);font-weight:800;color:var(--text-primary);font-size:12px;width:140px;">
            ${a(e.subtotal)}
          </td>
          <td style="padding:10px 14px;text-align:center;width:95px;">
            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:6px;${e.complexity===`High`?`background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.25);`:e.complexity===`Medium`?`background:rgba(245,158,11,0.12);color:#d97706;border:1px solid rgba(245,158,11,0.25);`:`background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.25);`}">
              ${e.complexity||`Medium`}
            </span>
          </td>
        `:`
          <td style="padding:10px 14px;text-align:center;width:140px;">
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:9999px;font-size:10px;font-weight:700;background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.25);">
              ${L.checkCircle}
              <span>Termasuk Paket</span>
            </span>
          </td>
        `}
        <td style="padding:10px 14px;text-align:center;width:40px;">
          <button class="btn-delete-rab-item btn btn-ghost btn-xs" data-item-id="${e.id}" style="color:#ef4444;padding:4px;cursor:pointer;" title="Hapus modul">
            ${L.trash}
          </button>
        </td>
      </tr>
    `).join(``);_=`
      <div class="animate-fade-in" style="display:flex;flex-direction:column;gap:16px;">
        <!-- Top Toolbar -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:14px 18px;border-radius:var(--radius-xl);border:1px solid var(--border);box-shadow:var(--shadow-xs);">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:var(--radius-lg);background:rgba(99,102,241,.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">
              RAB
            </div>
            <div>
              <div style="display:flex;align-items:center;gap:8px;">
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Tabel Rencana Anggaran Biaya (RAB) & Mandays
                </h3>
                <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;${i?.status===`reviewed`?`background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.25);`:`background:rgba(245,158,11,0.12);color:#d97706;border:1px solid rgba(245,158,11,0.25);`}">
                  ${i?.status===`reviewed`?`✔ Reviewed by Presales`:`Draft RAB`}
                </span>
                <span style="font-size:11px;font-family:var(--font-mono);color:var(--text-muted);">
                  Rev. ${i?.version||1}
                </span>
              </div>
              <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">
                ${i?`${i.total_mandays} Total Mandays • ${i.items.length} Modul Terstruktur`:`Belum digenerate`}
              </p>
            </div>
          </div>

          <div style="display:flex;align-items:center;gap:8px;">
            <!-- Internal View vs Client View toggle -->
            <button
              id="btn-toggle-internal-view"
              type="button"
              class="btn btn-sm"
              style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer;transition:all var(--transition);${N?`background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.3);`:`background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.3);`}"
            >
              ${N?`${L.eye}<span>👁 Tampilan Internal (Lengkap + Harga Satuan)</span>`:`${L.lock}<span>🔒 Tampilan Client (Harga Satuan Disembunyikan)</span>`}
            </button>

            <button id="btn-open-add-module" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:4px;font-weight:700;border:1px solid var(--border);">
              ${L.plus}
              <span>+ Tambah Item</span>
            </button>

            <button
              id="btn-toggle-rab-reviewed"
              type="button"
              class="btn btn-sm"
              style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:6px 14px;border-radius:10px;cursor:pointer;transition:all var(--transition);${i?.status===`reviewed`?`background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.3);`:`background:#10b981;color:#ffffff;border:none;box-shadow:0 2px 8px rgba(16,185,129,0.3);`}"
            >
              ${L.checkCircle}
              <span>${i?.status===`reviewed`?`✔ Sudah Di-Review Presales`:`✓ Tandai Reviewed`}</span>
            </button>
          </div>
        </div>

        <!-- Client Mode Banner -->
        ${N?``:`
          <div style="padding:12px 16px;border-radius:12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#047857;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="color:#10b981;display:flex;align-items:center;">${L.shieldCheck}</span>
              <span>
                <strong>Mode Tampilan Client Aktif:</strong> Harga satuan & subtotal per item disembunyikan dari tabel. Ringkasan subtotal & total investasi penawaran disajikan utuh di bagian bawah.
              </span>
            </div>
            <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;background:var(--bg-card);border:1px solid rgba(16,185,129,0.3);color:#059669;">
              Client-Safe Mode
            </span>
          </div>
        `}

        <!-- Editable Spreadsheet Table -->
        <div class="card" style="border-radius:16px;border:1px solid var(--border);overflow:hidden;box-shadow:var(--shadow-xs);">
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
              <thead>
                <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border);color:var(--text-muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">
                  <th style="padding:10px 14px;width:36px;text-align:center;">No</th>
                  <th style="padding:10px 14px;text-align:left;width:130px;">Kategori</th>
                  <th style="padding:10px 14px;text-align:left;">Item / Modul & Ruang Lingkup</th>
                  <th style="padding:10px 14px;text-align:center;width:80px;">Qty</th>
                  <th style="padding:10px 14px;text-align:left;width:95px;">Satuan</th>
                  ${N?`
                    <th style="padding:10px 14px;text-align:right;width:130px;">Harga Satuan (Rp)</th>
                    <th style="padding:10px 14px;text-align:right;width:140px;">Subtotal (Rp)</th>
                    <th style="padding:10px 14px;text-align:center;width:95px;">Kompleksitas</th>
                  `:`
                    <th style="padding:10px 14px;text-align:center;width:140px;">Status Deliverable</th>
                  `}
                  <th style="padding:10px 14px;text-align:center;width:40px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                ${e||`<tr><td colspan="${N?9:7}" style="text-align:center;padding:28px;color:var(--text-muted);">Belum ada modul RAB. Generate otomatis lewat tab Requirement AI.</td></tr>`}
              </tbody>
            </table>
          </div>
        </div>

        <!-- Total and Calculations Box (Shown at the bottom) -->
        ${i?`
          <div class="card" style="padding:20px;border-radius:16px;border:1px solid var(--border);background:var(--bg-card);box-shadow:var(--shadow-xs);">
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:24px;">
              <!-- Left Column: Scope Info or Internal Financial Analysis -->
              <div style="display:flex;flex-direction:column;gap:12px;">
                <h4 style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:0;">
                  ${N?`Informasi Analisis Finansial Internal`:`Ringkasan Spesifikasi Penawaran Klien`}
                </h4>

                ${N?`
                  <div style="padding:14px;border-radius:12px;background:var(--bg-secondary);border:1px solid var(--border);display:flex;flex-direction:column;gap:8px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-muted);">Total Estimasi Mandays:</span>
                      <span style="font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">${i.total_mandays} Mandays</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-muted);">Estimasi Cost Internal Dev:</span>
                      <span style="font-family:var(--font-mono);color:var(--text-primary);">${a(i.internal_cost||Math.round(i.subtotal*.6))}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-muted);">Gross Margin Proyek:</span>
                      <span style="font-family:var(--font-mono);font-weight:800;color:#10b981;">${i.margin_pct||40}%</span>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);padding-top:4px;border-top:1px solid var(--border);">
                      *Seluruh harga satuan mengacu pada rate card software house ASTACODE standar tahun 2026.
                    </div>
                  </div>
                `:`
                  <div style="padding:14px;border-radius:12px;background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2);display:flex;flex-direction:column;gap:8px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-secondary);">Total Modul Ruang Lingkup:</span>
                      <span style="font-weight:700;color:var(--indigo-600);">${i.items.length} Modul Terintegrasi</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-secondary);">Layanan Penyertaan:</span>
                      <span style="font-weight:600;color:var(--text-primary);">QA Testing, UAT & Pelatihan Admin</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-secondary);">Garansi Bug & Pemeliharaan:</span>
                      <span style="font-weight:700;color:#10b981;">90 Hari SLA Pasca-BAST</span>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);padding-top:4px;border-top:1px solid rgba(99,102,241,0.15);">
                      Format resmi penawaran harga terpadu (All-in Solution) oleh PT ASTACODE SOLUSINDO TEKNOLOGI.
                    </div>
                  </div>
                `}
              </div>

              <!-- Right Column: Cost Summary & Bottom Subtotal -->
              <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;">
                <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-bottom:1px solid var(--border);">
                  <span style="color:var(--text-muted);font-weight:600;">Subtotal Modul & Solusi:</span>
                  <span style="font-family:var(--font-mono);font-weight:700;color:var(--text-primary);">${a(i.subtotal)}</span>
                </div>

                <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-bottom:1px solid var(--border);">
                  <span style="color:var(--text-muted);">Contingency Buffer (${i.contingency_pct}%):</span>
                  <span style="font-family:var(--font-mono);">${a(i.contingency_amount)}</span>
                </div>

                <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-bottom:1px solid var(--border);">
                  <span style="color:var(--text-muted);">Diskon Khusus:</span>
                  <span style="font-family:var(--font-mono);color:#ef4444;">${i.discount_amount>0?`-${a(i.discount_amount)}`:`Rp 0`}</span>
                </div>

                <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-bottom:1px solid var(--border);">
                  <span style="color:var(--text-muted);font-weight:600;">Total Sebelum Pajak:</span>
                  <span style="font-family:var(--font-mono);font-weight:700;color:var(--text-primary);">${a(i.total_before_tax)}</span>
                </div>

                <div style="display:flex;justify-content:space-between;padding-bottom:6px;border-bottom:1px solid var(--border);">
                  <span style="color:var(--text-muted);">PPN 11%:</span>
                  <span style="font-family:var(--font-mono);">${a(i.tax_amount)}</span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:12px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.25);margin-top:4px;">
                  <span style="font-size:13px;font-weight:800;color:var(--indigo-600);">GRAND TOTAL PENAWARAN:</span>
                  <span style="font-size:15px;font-family:var(--font-mono);font-weight:800;color:var(--indigo-600);">${a(i.grand_total)}</span>
                </div>
              </div>
            </div>
          </div>
        `:``}
      </div>
    `}else if(j===`quotation`){let e=r.quotations?r.quotations[0]:null,t=F===`grand_total`?i?i.grand_total:s:i?i.total_before_tax:s,n=c(I||[],t,F,P,!0),l=(I||[]).map((e,t)=>`
      <tr class="animate-slide-up" style="border-bottom:1px solid var(--border-light);">
        <td style="padding:10px 12px;font-family:var(--font-mono);font-weight:700;color:var(--text-muted);text-align:center;">
          ${t+1}
        </td>

        <td style="padding:10px 12px;">
          <input
            type="text"
            class="top-term-name-input"
            data-idx="${t}"
            value="${(e.name||``).replace(/"/g,`&quot;`)}"
            style="width:100%;font-weight:700;color:var(--text-primary);font-size:12px;background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;outline:none;"
          />
          <input
            type="text"
            class="top-term-desc-input"
            data-idx="${t}"
            placeholder="Keterangan deliverable..."
            value="${(e.description||``).replace(/"/g,`&quot;`)}"
            style="width:100%;font-size:11px;color:var(--text-muted);background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;margin-top:2px;outline:none;"
          />
        </td>

        <td style="padding:10px 12px;width:120px;">
          <select
            class="top-term-type-select form-select"
            data-idx="${t}"
            style="width:100%;padding:4px 6px;font-size:11px;font-weight:600;border-radius:6px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-primary);outline:none;"
          >
            <option value="percent" ${e.value_type===`percent`?`selected`:``}>Persen (%)</option>
            <option value="amount" ${e.value_type===`amount`?`selected`:``}>Nominal (Rp)</option>
            <option value="balance" ${e.value_type===`balance`?`selected`:``}>Sisa Otomatis</option>
          </select>
        </td>

        <td style="padding:10px 12px;text-align:center;width:110px;">
          ${e.value_type===`balance`?`
            <span style="font-size:11px;font-family:var(--font-mono);color:var(--text-muted);font-style:italic;">
              Auto Sisa (${n.items[t]?.value||0}%)
            </span>
          `:`
            <input
              type="number"
              min="0"
              class="top-term-val-input form-input"
              data-idx="${t}"
              value="${e.value}"
              style="width:80px;font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);padding:4px 6px;text-align:center;font-size:12px;border-radius:6px;border:1px solid var(--border);background:var(--bg-secondary);outline:none;"
            />
          `}
        </td>

        <td style="padding:10px 12px;text-align:right;font-family:var(--font-mono);font-weight:800;color:#10b981;font-size:12px;width:140px;">
          ${a(n.items[t]?.calculated_amount||0)}
        </td>

        <td style="padding:10px 12px;">
          <input
            type="text"
            class="top-term-trigger-input"
            data-idx="${t}"
            placeholder="Mis. Saat tanda tangan, UAT selesai, BAST..."
            value="${(e.trigger_label||``).replace(/"/g,`&quot;`)}"
            style="width:100%;font-size:11px;color:var(--text-secondary);background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;outline:none;"
          />
        </td>

        <td style="padding:10px 12px;width:100px;">
          <input
            type="text"
            class="top-term-due-input"
            data-idx="${t}"
            placeholder="Net 14"
            value="${(e.due_days_label||``).replace(/"/g,`&quot;`)}"
            style="width:100%;font-family:var(--font-mono);font-size:11px;color:var(--text-muted);background:transparent;border:none;border-bottom:1px solid transparent;padding:2px 0;outline:none;"
          />
        </td>

        <td style="padding:10px 12px;text-align:center;width:50px;">
          <button
            type="button"
            class="btn-delete-top-term btn btn-ghost btn-xs"
            data-idx="${t}"
            style="color:#ef4444;padding:4px;cursor:pointer;"
            title="Hapus termin"
          >
            ${L.trash}
          </button>
        </td>
      </tr>
    `).join(``);_=`
      <div class="animate-fade-in" style="display:flex;flex-direction:column;gap:20px;">
        
        <!-- 1. Card Setting TOP (Terms of Payment / Termin Pembayaran) -->
        <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:2px solid rgba(99,102,241,0.35);background:var(--bg-card);box-shadow:var(--shadow-sm);display:flex;flex-direction:column;gap:18px;">
          
          <!-- Header Setting TOP & Dropdown Preset -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding-bottom:14px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:38px;height:38px;border-radius:var(--radius-xl);background:linear-gradient(135deg, #4f46e5, #7c3aed);color:#ffffff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(79,70,229,0.3);font-size:18px;font-weight:900;flex-shrink:0;">
                $
              </div>
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Setting TOP (Terms of Payment / Termin Pembayaran) Quotation
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:3px 0 0 0;">
                  Diatur langsung pada penawaran: persen/nominal dinamis, validasi balance 100%, otomatis masuk ke dokumen & invoice (PRD Section 8.11.1 & 10.6).
                </p>
              </div>
            </div>

            <!-- Skema Preset Dropdown -->
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:12px;font-weight:700;color:var(--text-secondary);white-space:nowrap;">Pilih Skema Preset:</span>
              <select
                id="top-preset-select"
                class="form-select"
                style="padding:6px 12px;border-radius:10px;border:1px solid var(--border);background:var(--bg-secondary);font-size:12px;font-weight:700;color:var(--text-primary);outline:none;"
              >
                <option value="30/40/30" ${P===`30/40/30`?`selected`:``}>30% DP / 40% UAT / 30% BAST (Standar)</option>
                <option value="50/50" ${P===`50/50`?`selected`:``}>50% DP / 50% BAST</option>
                <option value="40/30/30" ${P===`40/30/30`?`selected`:``}>40% DP / 30% Sit/UAT / 30% Go-Live</option>
                <option value="30/30/30/10" ${P===`30/30/30/10`?`selected`:``}>30% DP / 30% Dev / 30% UAT / 10% Retensi</option>
                <option value="100% Upfront" ${P===`100% Upfront`?`selected`:``}>100% Pelunasan Di Muka</option>
                <option value="Custom" ${P===`Custom`?`selected`:``}>Custom (Skema Kustom...)</option>
              </select>
            </div>
          </div>

          <!-- Summary Bar & Balance Validation -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;padding:12px 16px;border-radius:12px;background:var(--bg-secondary);border:1px solid var(--border);font-size:12px;">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
              <div>
                <span style="font-size:11px;color:var(--text-muted);display:block;">Nilai Kontrak Acuan:</span>
                <strong style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-primary);">
                  ${a(t)}
                </strong>
              </div>

              <div style="border-left:1px solid var(--border);padding-left:16px;">
                <span style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:2px;">Basis Perhitungan:</span>
                <div style="display:flex;align-items:center;gap:14px;">
                  <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:600;color:var(--text-primary);">
                    <input type="radio" name="top_calc_basis" value="grand_total" ${F===`grand_total`?`checked`:``} style="accent-color:var(--indigo-600);cursor:pointer;" />
                    <span>Grand Total (Termasuk PPN)</span>
                  </label>
                  <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:600;color:var(--text-primary);">
                    <input type="radio" name="top_calc_basis" value="before_tax" ${F===`before_tax`?`checked`:``} style="accent-color:var(--indigo-600);cursor:pointer;" />
                    <span>Sebelum Pajak</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Balance Status Badge -->
            <div>
              ${n.is_balanced?`
                <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:10px;background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.3);font-weight:800;font-size:12px;font-family:var(--font-mono);">
                  ${L.checkCircle}
                  <span>✓ BALANCE 100% (${a(n.total_amount)})</span>
                </div>
              `:`
                <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:10px;background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.3);font-weight:800;font-size:12px;font-family:var(--font-mono);">
                  ${L.alertTriangle}
                  <span>⚠ BELUM BALANCE! Sisa: ${a(n.remaining_amount)} (${n.remaining_percent}%)</span>
                </div>
              `}
            </div>
          </div>

          <!-- 8-Column Terms Table -->
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
              <thead>
                <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border);color:var(--text-muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">
                  <th style="padding:10px 12px;width:50px;text-align:center;">Term</th>
                  <th style="padding:10px 12px;text-align:left;">Nama Termin & Deskripsi</th>
                  <th style="padding:10px 12px;text-align:left;width:120px;">Tipe Nilai</th>
                  <th style="padding:10px 12px;text-align:center;width:110px;">Input Nilai</th>
                  <th style="padding:10px 12px;text-align:right;width:140px;">Hasil Nominal (Rp)</th>
                  <th style="padding:10px 12px;text-align:left;">Pemicu Tagihan (Trigger)</th>
                  <th style="padding:10px 12px;text-align:left;width:100px;">Jatuh Tempo</th>
                  <th style="padding:10px 12px;text-align:center;width:50px;">Hapus</th>
                </tr>
              </thead>
              <tbody>
                ${l||`<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text-muted);">Belum ada termin pembayaran. Tambahkan termin baru.</td></tr>`}
              </tbody>
            </table>
          </div>

          <!-- Bottom Row: Add Term & Note -->
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding-top:4px;">
            <button id="btn-add-top-term" type="button" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
              ${L.plus}
              <span>+ Tambah Termin Baru</span>
            </button>

            <div style="font-size:11px;color:var(--text-muted);font-style:italic;">
              *Perubahan termin otomatis diwariskan ke Halaman RAB dan Pasal 5 MoU.
            </div>
          </div>
        </div>

        <!-- 2. Quotation Status Updater -->
        <div class="card" style="padding:18px 20px;border-radius:var(--radius-xl);border:1px solid var(--border);background:var(--bg-card);box-shadow:var(--shadow-xs);">
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
            <div>
              <h3 style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin:0;">
                Status Dokumen Quotation & MoU
              </h3>
              <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                <span style="font-size:12px;font-family:var(--font-mono);font-weight:700;color:var(--text-primary);">
                  ${e?.number||`QT/2026/10/0012`} (${e?.version_label||`Rev. 2`})
                </span>
                <span style="font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);">
                  Status: ${e?.status||`Negosiasi`}
                </span>
              </div>
            </div>

            <!-- Status Switcher Buttons -->
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
              <span style="font-size:12px;font-weight:600;color:var(--text-muted);margin-right:4px;">Ubah Status:</span>
              ${[`Lead`,`Follow Up`,`Negosiasi`,`Deal`,`Reject`].map(t=>`
                <button
                  type="button"
                  class="btn-quo-status-action btn btn-sm"
                  data-status="${t}"
                  style="font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;${e?.status===t?`background:var(--indigo-600);color:#ffffff;`:t===`Deal`?`background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.3);`:t===`Reject`?`background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.3);`:`background:var(--bg-secondary);color:var(--text-secondary);border:1px solid var(--border);`}"
                >
                  ${t===`Deal`?`🏆 DEAL`:t}
                </button>
              `).join(``)}
            </div>
          </div>
        </div>

        <!-- 3. Document Package Preview (1 File Terpadu: 4 Bagian) -->
        <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);background:var(--bg-card);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
          
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border);padding-bottom:14px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="color:var(--indigo-600);display:flex;align-items:center;">${L.fileText}</span>
              <span style="font-size:13px;font-weight:800;color:var(--text-primary);">
                Preview Dokumen Paket Penawaran (1 File Terpadu: 4 Bagian)
              </span>
            </div>

            <!-- Part Switcher Tabs -->
            <div style="display:flex;align-items:center;gap:4px;background:var(--bg-secondary);padding:3px;border-radius:10px;border:1px solid var(--border);">
              <button
                type="button"
                class="btn-doc-preview-tab"
                data-preview-tab="pendahuluan"
                style="padding:5px 12px;border-radius:8px;border:none;cursor:pointer;font-size:11px;font-weight:700;transition:all var(--transition);${Tt===`pendahuluan`?`background:var(--bg-card);color:var(--indigo-600);box-shadow:var(--shadow-xs);`:`background:transparent;color:var(--text-muted);`}"
              >
                1. Cover & Pendahuluan
              </button>
              <button
                type="button"
                class="btn-doc-preview-tab"
                data-preview-tab="rab"
                style="padding:5px 12px;border-radius:8px;border:none;cursor:pointer;font-size:11px;font-weight:700;transition:all var(--transition);${Tt===`rab`?`background:var(--bg-card);color:var(--indigo-600);box-shadow:var(--shadow-xs);`:`background:transparent;color:var(--text-muted);`}"
              >
                2. RAB & Termin TOP
              </button>
              <button
                type="button"
                class="btn-doc-preview-tab"
                data-preview-tab="mou"
                style="padding:5px 12px;border-radius:8px;border:none;cursor:pointer;font-size:11px;font-weight:700;transition:all var(--transition);${Tt===`mou`?`background:var(--bg-card);color:var(--indigo-600);box-shadow:var(--shadow-xs);`:`background:transparent;color:var(--text-muted);`}"
              >
                3. Kumpulan 22 Pasal MoU
              </button>
              <button
                type="button"
                class="btn-doc-preview-tab"
                data-preview-tab="ttd"
                style="padding:5px 12px;border-radius:8px;border:none;cursor:pointer;font-size:11px;font-weight:700;transition:all var(--transition);${Tt===`ttd`?`background:var(--bg-card);color:var(--indigo-600);box-shadow:var(--shadow-xs);`:`background:transparent;color:var(--text-muted);`}"
              >
                4. Halaman Tanda Tangan
              </button>
            </div>

            <!-- Action buttons: Print & Generate -->
            <div style="display:flex;align-items:center;gap:8px;">
              <button
                id="btn-print-quotation-doc"
                type="button"
                class="btn btn-secondary btn-sm"
                style="padding:6px 10px;"
                title="Cetak Dokumen"
              >
                ${L.printer}
              </button>
              <button
                id="btn-generate-quotation-doc"
                type="button"
                class="btn btn-primary btn-sm"
                style="display:inline-flex;align-items:center;gap:6px;font-weight:700;"
              >
                ${L.download}
                <span>Generate & Simpan Versi</span>
              </button>
            </div>
          </div>

          <!-- A4 Document Simulation Container -->
          <div style="background:#f1f5f9;padding:24px;border-radius:var(--radius-xl);display:flex;justify-content:center;">
            <div style="width:100%;max-width:760px;background:#ffffff;color:#1e293b;box-shadow:0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);border-radius:12px;padding:32px;display:flex;flex-direction:column;gap:20px;font-size:12px;border:1px solid #e2e8f0;">
              
              <!-- Kop Surat ASTACODE -->
              <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid #4f46e5;padding-bottom:16px;">
                <div style="display:flex;align-items:center;gap:12px;">
                  <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg, #4f46e5, #7c3aed);color:#ffffff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;letter-spacing:-0.05em;">
                    AST
                  </div>
                  <div>
                    <h2 style="font-size:15px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;">
                      PT ASTACODE SOLUSINDO TEKNOLOGI
                    </h2>
                    <p style="font-size:10px;color:#64748b;margin:2px 0 0 0;">
                      Enterprise Software House • Mobile & Web Cloud ERP Architecture
                    </p>
                  </div>
                </div>

                <div style="text-align:right;font-family:var(--font-mono);font-size:11px;">
                  <p style="font-weight:700;color:#0f172a;margin:0;">
                    ${e?.number||`QT/2026/10/0012`}
                  </p>
                  <p style="color:#64748b;margin:2px 0 0 0;">
                    Tanggal: ${new Date().toLocaleDateString(`id-ID`,{dateStyle:`long`})}
                  </p>
                  <p style="color:#64748b;margin:2px 0 0 0;">Masa Berlaku: 30 Hari</p>
                </div>
              </div>

              <!-- Part 1: Cover & Pendahuluan -->
              ${Tt===`pendahuluan`?`
                <div style="display:flex;flex-direction:column;gap:16px;line-height:1.6;">
                  <div>
                    <p style="font-weight:700;margin:0;">Kepada Yth:</p>
                    <p style="font-weight:700;color:#0f172a;margin:2px 0 0 0;">
                      ${r.primary_contact?r.primary_contact.name:`Bpk/Ibu PIC`} (${r.primary_contact?r.primary_contact.job_title:`Stakeholder`})
                    </p>
                    <p style="color:#334155;margin:2px 0 0 0;">${r.customer?r.customer.legal_name:`Customer`}</p>
                    <p style="color:#64748b;font-size:11px;margin:2px 0 0 0;">
                      ${r.customer&&r.customer.address||``}${r.customer&&r.customer.city?`, ${r.customer.city}`:``}
                    </p>
                  </div>

                  <div>
                    <p style="font-weight:800;color:#0f172a;margin:0;">
                      Perihal: Penawaran Rancang Bangun & Implementasi ${r.title}
                    </p>
                  </div>

                  <div>
                    <textarea
                      id="intro-letter-textarea"
                      rows="4"
                      style="width:100%;padding:10px;border-radius:8px;border:1px solid #cbd5e1;background:#f8fafc;font-size:12px;color:#334155;font-family:inherit;line-height:1.6;outline:none;"
                    >${wt||e?.intro_letter||`Sehubungan dengan pembicaraan mengenai pengembangan sistem informasi, bersama ini kami dari ASTACODE menyampaikan penawaran resmi untuk proyek ${r.title} pada ${r.customer?r.customer.legal_name:`Klien`}. Penawaran ini mencakup modul terpadu, metodologi agile, estimasi mandays presisi, serta SLA pemeliharaan profesional.`}</textarea>
                  </div>

                  <div style="padding:14px;border-radius:12px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.25);display:flex;flex-direction:column;gap:4px;">
                    <span style="font-weight:700;color:#3730a3;">Ringkasan Nilai Investasi Penawaran:</span>
                    <span style="font-size:16px;font-family:var(--font-mono);font-weight:800;color:#4338ca;">
                      ${a(i?i.grand_total:s)}
                    </span>
                    <span style="font-size:11px;color:#475569;font-style:italic;">
                      Terbilang: ${o(i?i.grand_total:s)}
                    </span>
                  </div>

                  <div style="padding-top:20px;display:flex;justify-content:space-between;align-items:flex-end;">
                    <div>
                      <p style="margin:0;font-weight:600;">Hormat kami,</p>
                      <p style="font-weight:800;color:#0f172a;margin:40px 0 0 0;">Asisyah Sarah Azzahra</p>
                      <p style="font-size:11px;color:#64748b;margin:2px 0 0 0;">Super Admin & Lead Presales PM • ASTACODE</p>
                    </div>
                  </div>
                </div>
              `:``}

              <!-- Part 2: RAB & Termin TOP -->
              ${Tt===`rab`?`
                <div style="display:flex;flex-direction:column;gap:18px;">
                  <div style="border-bottom:1px solid #e2e8f0;padding-bottom:8px;">
                    <h3 style="font-weight:800;font-size:13px;color:#0f172a;margin:0;">
                      BAGIAN II: RENCANA ANGGARAN BIAYA & RUANG LINGKUP MODUL
                    </h3>
                    <p style="font-size:11px;color:#64748b;margin:2px 0 0 0;">
                      Detail spesifikasi teknis dan alokasi mandays per kategori pekerjaan.
                    </p>
                  </div>

                  <table style="width:100%;border-collapse:collapse;border:1px solid #cbd5e1;font-size:11px;">
                    <thead>
                      <tr style="background:#f1f5f9;font-weight:700;color:#334155;">
                        <th style="border:1px solid #cbd5e1;padding:6px;width:32px;text-align:center;">No</th>
                        <th style="border:1px solid #cbd5e1;padding:6px;text-align:left;">Kategori & Modul Fitur</th>
                        <th style="border:1px solid #cbd5e1;padding:6px;width:70px;text-align:center;">Qty</th>
                        <th style="border:1px solid #cbd5e1;padding:6px;width:110px;text-align:right;">Harga Satuan</th>
                        <th style="border:1px solid #cbd5e1;padding:6px;width:120px;text-align:right;">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${(i?i.items:[]).map((e,t)=>`
                        <tr>
                          <td style="border:1px solid #cbd5e1;padding:6px;text-align:center;font-family:var(--font-mono);">${t+1}</td>
                          <td style="border:1px solid #cbd5e1;padding:6px;">
                            <strong style="color:#0f172a;">${e.name}</strong>
                            <div style="font-size:10px;color:#64748b;">${e.description||``}</div>
                          </td>
                          <td style="border:1px solid #cbd5e1;padding:6px;text-align:center;font-family:var(--font-mono);">
                            ${e.qty} ${e.unit}
                          </td>
                          <td style="border:1px solid #cbd5e1;padding:6px;text-align:right;font-family:var(--font-mono);">
                            ${a(e.unit_price)}
                          </td>
                          <td style="border:1px solid #cbd5e1;padding:6px;text-align:right;font-family:var(--font-mono);font-weight:700;">
                            ${a(e.subtotal)}
                          </td>
                        </tr>
                      `).join(``)}
                    </tbody>
                    <tfoot>
                      <tr style="background:#f8fafc;font-weight:800;">
                        <td colspan="4" style="border:1px solid #cbd5e1;padding:8px;text-align:right;">
                          Grand Total (Termasuk PPN 11%):
                        </td>
                        <td style="border:1px solid #cbd5e1;padding:8px;text-align:right;font-family:var(--font-mono);color:#4338ca;">
                          ${a(i?i.grand_total:s)}
                        </td>
                      </tr>
                    </tfoot>
                  </table>

                  <!-- Termin TOP Table inside document -->
                  <div style="display:flex;flex-direction:column;gap:8px;padding-top:8px;">
                    <h4 style="font-weight:800;font-size:12px;color:#0f172a;text-transform:uppercase;margin:0;">
                      Tabel Skema Termin Pembayaran (Terms of Payment):
                    </h4>
                    <table style="width:100%;border-collapse:collapse;border:1px solid #cbd5e1;font-size:11px;">
                      <thead>
                        <tr style="background:rgba(99,102,241,0.08);color:#312e81;font-weight:700;">
                          <th style="border:1px solid #cbd5e1;padding:6px;text-align:center;width:45px;">Termin</th>
                          <th style="border:1px solid #cbd5e1;padding:6px;text-align:left;">Nama & Tahapan Deliverable</th>
                          <th style="border:1px solid #cbd5e1;padding:6px;text-align:center;width:75px;">Porsi (%)</th>
                          <th style="border:1px solid #cbd5e1;padding:6px;text-align:right;width:130px;">Nominal (IDR)</th>
                          <th style="border:1px solid #cbd5e1;padding:6px;text-align:left;">Pemicu Invoice & Jatuh Tempo</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${n.items.map((e,t)=>`
                          <tr>
                            <td style="border:1px solid #cbd5e1;padding:6px;text-align:center;font-weight:700;">
                              ${t+1}
                            </td>
                            <td style="border:1px solid #cbd5e1;padding:6px;">
                              <strong style="color:#0f172a;">${e.name}</strong>
                              <div style="font-size:10px;color:#64748b;">${e.description||``}</div>
                            </td>
                            <td style="border:1px solid #cbd5e1;padding:6px;text-align:center;font-family:var(--font-mono);">
                              ${e.value}%
                            </td>
                            <td style="border:1px solid #cbd5e1;padding:6px;text-align:right;font-family:var(--font-mono);font-weight:700;color:#047857;">
                              ${a(e.calculated_amount)}
                            </td>
                            <td style="border:1px solid #cbd5e1;padding:6px;">
                              <div>${e.trigger_label}</div>
                              <span style="font-size:10px;font-family:var(--font-mono);color:#64748b;">
                                Tempo: ${e.due_days_label}
                              </span>
                            </td>
                          </tr>
                        `).join(``)}
                      </tbody>
                    </table>
                  </div>
                </div>
              `:``}

              <!-- Part 3: Kumpulan 22 Pasal MoU -->
              ${Tt===`mou`?`
                <div style="display:flex;flex-direction:column;gap:14px;">
                  <div style="text-align:center;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
                    <h3 style="font-weight:900;font-size:14px;color:#0f172a;margin:0;">
                      MEMORANDUM OF UNDERSTANDING (MoU)
                    </h3>
                    <p style="font-family:var(--font-mono);font-size:11px;color:#64748b;margin:4px 0 0 0;">
                      Nomor: ${e?.mou_number||`MOU/2026/10/0012`}
                    </p>
                  </div>

                  <div style="display:flex;flex-direction:column;gap:12px;max-height:420px;overflow-y:auto;padding-right:6px;">
                    ${(u||[]).map(e=>`
                      <div style="padding-top:8px;border-top:1px solid #f1f5f9;">
                        <h4 style="font-weight:700;font-size:12px;color:#0f172a;margin:0;">${e.title}</h4>
                        <div style="font-size:11px;color:#334155;text-align:justify;margin-top:4px;line-height:1.6;">
                          ${e.number===5?`
                            Nilai total pekerjaan disepakati sebesar <strong style="font-family:var(--font-mono);">${a(i?i.grand_total:s)}</strong> (${o(i?i.grand_total:s)}).
                            Pembayaran wajib diselesaikan melalui Termin Pembayaran:
                            ${n.items.map((e,t)=>`
                              <div style="padding-left:14px;color:#0f172a;font-weight:600;">
                                • Termin ${t+1} (${e.name}): ${e.value}% (${a(e.calculated_amount)}) - ${e.trigger_label} (${e.due_days_label}).
                              </div>
                            `).join(``)}
                          `:e.body}
                        </div>
                      </div>
                    `).join(``)}
                  </div>
                </div>
              `:``}

              <!-- Part 4: Halaman Tanda Tangan -->
              ${Tt===`ttd`?`
                <div style="display:flex;flex-direction:column;gap:20px;padding-top:10px;">
                  <div style="border-bottom:1px solid #e2e8f0;padding-bottom:8px;">
                    <h3 style="font-weight:800;font-size:13px;color:#0f172a;margin:0;">
                      BAGIAN IV: PENGESAHAN & PENANDATANGANAN KONTRAK
                    </h3>
                    <p style="font-size:11px;color:#64748b;margin:2px 0 0 0;">
                      Dokumen sah berkekuatan hukum sesuai dengan UU ITE dan UU PDP Republik Indonesia.
                    </p>
                  </div>

                  <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:24px;padding-top:10px;">
                    <!-- Pihak Pertama (Klien) -->
                    <div style="padding:16px;border-radius:12px;border:1px solid #cbd5e1;text-align:center;display:flex;flex-direction:column;gap:10px;">
                      <span style="font-weight:800;color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">
                        PIHAK PERTAMA (KLIEN)
                      </span>
                      <strong style="color:#0f172a;">${r.customer?r.customer.legal_name:`Customer`}</strong>
                      <div style="height:90px;display:flex;align-items:center;justify-content:center;border:2px dashed #cbd5e1;border-radius:8px;color:#94a3b8;font-family:var(--font-mono);font-size:10px;">
                        [Area Tanda Tangan & E-Meterai Rp 10.000]
                      </div>
                      <div>
                        <strong style="color:#0f172a;display:block;">${r.primary_contact?r.primary_contact.name:`PIC`}</strong>
                        <span style="font-size:11px;color:#64748b;">${r.primary_contact?r.primary_contact.job_title:`Stakeholder`}</span>
                      </div>
                    </div>

                    <!-- Pihak Kedua (ASTACODE) -->
                    <div style="padding:16px;border-radius:12px;border:1px solid #cbd5e1;text-align:center;display:flex;flex-direction:column;gap:10px;">
                      <span style="font-weight:800;color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">
                        PIHAK KEDUA (ASTACODE)
                      </span>
                      <strong style="color:#0f172a;">PT ASTACODE SOLUSINDO TEKNOLOGI</strong>
                      <div style="height:90px;display:flex;align-items:center;justify-content:center;border:2px dashed #818cf8;background:rgba(99,102,241,0.06);border-radius:8px;color:var(--indigo-600);font-family:var(--font-mono);font-size:10px;font-weight:800;">
                        ✔ Certified E-Signature & QR Code Verifikasi
                      </div>
                      <div>
                        <strong style="color:#0f172a;display:block;">Asisyah Sarah Azzahra</strong>
                        <span style="font-size:11px;color:#64748b;">Super Admin & Lead Presales PM</span>
                      </div>
                    </div>
                  </div>
                </div>
              `:``}

            </div>
          </div>

        </div>

      </div>
    `}else j===`activity`?_=`
      <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">Timeline Aktivitas & Notulen Follow-Up</h3>
          <button id="btn-open-meet-modal" class="btn btn-primary btn-sm" style="font-weight:700;">
            + Jadwalkan Aktivitas / Meet
          </button>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
          ${f.map(e=>`
      <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-card);border:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;font-size:12px;">
        <div style="width:36px;height:36px;border-radius:var(--radius-md);background:rgba(99,102,241,.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          ${e.type===`google_meet`?L.video:L.messageSquare}
        </div>
        <div style="flex:1;">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <strong style="color:var(--text-primary);font-size:13px;">${e.title}</strong>
            <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-muted);">${new Date(e.scheduled_at).toLocaleDateString(`id-ID`)}</span>
          </div>
          <p style="color:var(--text-secondary);margin:4px 0 0 0;">${e.notes||`-`}</p>
          ${e.meet_link?`<a href="${e.meet_link}" target="_blank" rel="noreferrer" style="font-size:11px;color:var(--indigo-600);font-weight:700;text-decoration:underline;margin-top:4px;display:inline-block;">Buka Google Meet</a>`:``}
        </div>
      </div>
    `).join(``)||`<div style="text-align:center;padding:24px;color:var(--text-muted);font-size:12px;">Belum ada aktivitas tercatat untuk lead ini.</div>`}
        </div>
      </div>
    `:j===`contacts`?_=`
      <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:16px;">
        <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">Kontak & Stakeholder Proyek</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:14px;">
          <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-secondary);border:1px solid var(--border);display:flex;flex-direction:column;gap:6px;">
            <strong style="color:var(--text-primary);">${r.primary_contact?.name||`PIC`}</strong>
            <span style="font-size:11px;color:var(--text-muted);">${r.primary_contact?.job_title||`Stakeholder`}</span>
            <span style="font-family:var(--font-mono);font-size:11px;color:#10b981;">${r.primary_contact?.whatsapp_number||`-`}</span>
            <span style="font-size:11px;color:var(--indigo-600);">${r.primary_contact?.email||`-`}</span>
          </div>
          <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-secondary);border:1px solid var(--border);display:flex;flex-direction:column;gap:6px;">
            <strong style="color:var(--text-primary);">Asisyah Sarah Azzahra</strong>
            <span style="font-size:11px;color:var(--text-muted);">Lead Presales & Project Manager (Internal ASTACODE)</span>
            <span style="font-family:var(--font-mono);font-size:11px;color:#10b981;">0812-9988-7766</span>
            <span style="font-size:11px;color:var(--indigo-600);">asisyahsarahazz@gmail.com</span>
          </div>
        </div>
      </div>
    `:j===`notes`?_=`
      <div style="display:flex;flex-direction:column;gap:20px;">
        <!-- Embedded Voice Note & Audio-to-Document Manager -->
        <div id="voicenote-manager-mount">
          ${k(r.id,r.title,r.customer?r.customer.legal_name:`Customer`)}
        </div>

        <!-- Internal Notes List -->
        <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:12px;">
          <h4 style="font-size:12px;font-weight:800;text-transform:uppercase;color:var(--text-muted);letter-spacing:.05em;margin:0;">
            Catatan Internal Tim Sales & PM
          </h4>

          <div style="display:flex;gap:8px;">
            <input
              id="new-note-text-input"
              type="text"
              class="form-input"
              placeholder="Tambahkan catatan internal penting mengenai lead ini..."
              style="font-size:12px;flex:1;"
            />
            <button id="btn-add-internal-note" class="btn btn-primary btn-sm" style="font-weight:700;">
              Simpan
            </button>
          </div>

          <div style="display:flex;flex-direction:column;gap:8px;padding-top:6px;">
            ${kt.map(e=>`
              <div style="padding:10px 14px;border-radius:var(--radius-md);background:var(--bg-secondary);border:1px solid var(--border);font-size:12px;color:var(--text-secondary);border-left:3px solid var(--indigo-600);">
                ${e}
              </div>
            `).join(``)}
          </div>
        </div>
      </div>
    `:j===`history`&&(_=`
      <div class="card" style="padding:22px;border-radius:var(--radius-xl);border:1px solid var(--border);display:flex;flex-direction:column;gap:14px;">
        <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">Audit Trail & Riwayat Perubahan Status</h3>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
          <div style="padding:12px;border-radius:8px;background:var(--bg-secondary);border:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;font-weight:700;">
              <span>Tahap diubah ke: ${d.name}</span>
              <span style="font-family:var(--font-mono);color:var(--text-muted);">2026-10-22 10:15 WIB</span>
            </div>
            <p style="color:var(--text-muted);margin:4px 0 0 0;font-size:11px;">Oleh Asisyah Sarah Azzahra (PM)</p>
          </div>
          <div style="padding:12px;border-radius:8px;background:var(--bg-secondary);border:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;font-weight:700;">
              <span>RAB AI Presales Berhasil Di-generate</span>
              <span style="font-family:var(--font-mono);color:var(--text-muted);">2026-10-21 14:30 WIB</span>
            </div>
            <p style="color:var(--text-muted);margin:4px 0 0 0;font-size:11px;">Total 152 Mandays dikalkulasi dengan PPN 11%</p>
          </div>
        </div>
      </div>
    `);let v=``;return Et&&(v=`
      <div class="modal-overlay" id="meet-modal-backdrop" style="position:fixed;inset:0;background:rgba(15,23,42,.65);backdrop-filter:blur(4px);z-index:100;display:flex;align-items:center;justify-content:center;">
        <div class="modal modal-md" onclick="event.stopPropagation();" style="width:100%;max-width:520px;padding:22px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border);box-shadow:var(--shadow-xl);display:flex;flex-direction:column;gap:14px;">
          <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:10px;">
            <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">Jadwalkan Meeting / Aktivitas Baru</h3>
            <button id="btn-close-meet-modal" class="modal-close" style="border:none;background:transparent;cursor:pointer;color:var(--text-muted);">${L.close}</button>
          </div>

          <div class="form-group">
            <label class="form-label" style="font-weight:700;font-size:12px;">Judul Aktivitas*:</label>
            <input id="meet-modal-title" type="text" class="form-input" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:12px;" value="${`Diskusi Scope & Termin ${r.title}`}" />
          </div>

          <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:12px;">
            <div class="form-group">
              <label class="form-label" style="font-weight:700;font-size:12px;">Waktu Pertemuan*:</label>
              <input id="meet-modal-date" type="datetime-local" class="form-input" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:12px;" value="${Dt}" />
            </div>
            <div class="form-group">
              <label class="form-label" style="font-weight:700;font-size:12px;">Durasi (Menit):</label>
              <input id="meet-modal-duration" type="number" class="form-input" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:12px;" value="${Ot}" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" style="font-weight:700;font-size:12px;">Peserta Email (pisahkan koma):</label>
            <input id="meet-modal-attendees" type="text" class="form-input" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:12px;" value="${r.primary_contact?r.primary_contact.email:`client@email.com`}" />
          </div>

          <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding-top:10px;border-top:1px solid var(--border);">
            <button id="btn-cancel-meet" class="btn btn-secondary btn-sm">Batal</button>
            <button id="btn-submit-meet" class="btn btn-primary btn-sm" style="font-weight:700;">Simpan & Buat Link Meet</button>
          </div>
        </div>
      </div>
    `),`
    <div style="flex:1;display:flex;flex-direction:column;height:100%;overflow-y:auto;background:var(--bg-primary);">
      
      <!-- 1. Header Workspace Bar (Point 3) -->
      <div style="padding:20px 24px;background:var(--bg-card);border-bottom:1px solid var(--border);display:flex;flex-direction:column;gap:18px;box-shadow:var(--shadow-xs);">
        
        <!-- Navigation & Title Row with Company Metadata -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <!-- Back to Pipeline Button with exact label requested -->
            <button id="btn-lead-back-pipeline" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 12px;border-radius:10px;">
              ${L.arrowLeft}
              <span>← Kembali ke Pipeline</span>
            </button>

            <div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span style="font-family:var(--font-mono);font-weight:800;font-size:11px;padding:2px 8px;border-radius:6px;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);">
                  ${r.code}
                </span>
                <h1 style="font-size:18px;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;margin:0;">
                  ${r.title}
                </h1>
                <span style="font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.25);">
                  ${r.priority}
                </span>
              </div>

              <!-- Metadata Line with Building Icon, PIC, and Bold Green Value (Point 3) -->
              <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--text-muted);margin-top:6px;flex-wrap:wrap;">
                <span style="font-weight:700;color:var(--text-primary);display:inline-flex;align-items:center;gap:6px;">
                  <span style="color:var(--indigo-600);">${L.building}</span>
                  <span>${r.customer?r.customer.legal_name:`Customer`}</span>
                </span>
                <span>•</span>
                <span>PIC: ${r.primary_contact?r.primary_contact.name:`-`} (${r.primary_contact?r.primary_contact.job_title:`-`})</span>
                <span>•</span>
                <span style="font-family:var(--font-mono);font-weight:800;color:#16a34a;">
                  ${a(r.est_value||0)}
                </span>
              </div>
            </div>
          </div>

          <!-- Quick Actions Bar -->
          <div style="display:flex;align-items:center;gap:8px;">
            <button id="btn-header-meet" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:7px 12px;border-radius:10px;">
              ${L.video}
              <span>Jadwalkan Meet</span>
            </button>
            <button id="btn-header-wa" class="btn btn-secondary btn-sm" style="color:var(--emerald-600);display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:7px 12px;border-radius:10px;">
              ${L.messageSquare}
              <span>WhatsApp Chat</span>
            </button>
            <button id="btn-header-quotation" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:7px 14px;border-radius:10px;">
              ${L.fileCheck}
              <span>Quotation & TOP</span>
            </button>
          </div>
        </div>

        <!-- 2. Track Record Pipeline Section (Point 4) -->
        <div style="display:flex;flex-direction:column;gap:10px;">
          
          <!-- Outer Header Row: Track Record Title & Badges OUTSIDE/ABOVE card -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;font-size:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-weight:800;font-size:11px;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.05em;">
                TRACK RECORD PIPELINE:
              </span>
              <div style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:9999px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);color:var(--indigo-600);font-weight:700;font-family:var(--font-mono);font-size:11px;">
                <span style="width:6px;height:6px;border-radius:50%;background:var(--indigo-600);display:inline-block;"></span>
                <span>
                  ${r.stage_id===`reject`?`0% • REJECT`:`${y.find(e=>e.id===r.stage_id)?.probability||10}% WORKFLOW CAPAIAN`}
                </span>
              </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:11px;font-weight:600;color:var(--text-secondary);">
                Tahap Saat Ini: <strong style="color:var(--text-primary);">${d.name}</strong>
              </span>
              <span style="font-size:10px;font-family:var(--font-mono);font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(245,158,11,0.1);color:#d97706;border:1px solid rgba(245,158,11,0.25);">
                SLA: ${r.days_in_stage||3} Hari
              </span>
            </div>
          </div>

          <!-- Stepper White Card Container -->
          <div class="card" style="padding:20px;border-radius:var(--radius-xl);border:1px solid var(--border);position:relative;overflow:hidden;background:var(--bg-card);box-shadow:var(--shadow-xs);">
            
            <!-- Horizontal Continuous Progress Track Background Line -->
            <div style="position:absolute;top:38px;left:45px;right:45px;height:3px;background:var(--border);z-index:1;"></div>
            
            <!-- Active Colored Progress Line -->
            ${r.stage_id===`reject`?``:`
              <div style="position:absolute;top:38px;left:45px;height:3px;background:linear-gradient(90deg, #3b82f6, #6366f1, #10b981);z-index:1;width:${m}%;transition:width 0.4s ease;"></div>
            `}

            <!-- Circular Milestone Nodes -->
            <div style="display:grid;grid-template-columns:repeat(7, 1fr);gap:8px;position:relative;z-index:2;overflow-x:auto;">
              ${h}
            </div>

            <!-- Bottom Gate Aturan Bisnis & Action Buttons Row (Point 4) -->
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;font-size:12px;">
              <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:11px;font-weight:700;color:var(--text-muted);">
                  Gate Aturan Bisnis:
                </span>
                <span style="font-size:11px;font-weight:700;color:var(--text-primary);">
                  ${jt[r.stage_id]||`Aturan bisnis tahapan terverifikasi.`}
                </span>
              </div>

              <div style="display:flex;align-items:center;gap:8px;">
                <button id="btn-gate-next-step" class="btn btn-primary btn-sm" style="font-weight:700;padding:6px 14px;border-radius:8px;display:inline-flex;align-items:center;gap:6px;">
                  <span>Lanjut Step Berikutnya</span>
                  ${L.arrowRight}
                </button>
                <button id="btn-gate-reject" class="btn btn-outline" style="font-weight:700;padding:6px 12px;border-radius:8px;color:#ef4444;border-color:rgba(239,68,68,0.3);background:transparent;">
                  Tandai Reject
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- 3. Navigation Tabs Row (Point 5 - 8 Specific Tabs) -->
        <div style="display:flex;align-items:center;gap:4px;overflow-x:auto;padding-top:4px;border-top:1px solid var(--border-light);font-size:12px;font-weight:700;">
          ${g.map(e=>`
            <button
              class="btn-detail-tab"
              data-tab="${e.id}"
              style="padding:10px 16px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid ${j===e.id?`var(--indigo-600)`:`transparent`};color:${j===e.id?`var(--indigo-600)`:`var(--text-muted)`};white-space:nowrap;display:inline-flex;align-items:center;gap:6px;transition:all var(--transition);"
            >
              <span>${e.label}</span>
              ${e.badge===void 0?``:`<span class="badge" style="font-size:9px;padding:1px 6px;border-radius:9999px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text-secondary);">${e.badge}</span>`}
            </button>
          `).join(``)}
        </div>
      </div>

      <!-- Tab Content Area -->
      <div style="padding:24px;flex:1;">
        ${_}
      </div>

      <!-- Modals -->
      ${v}
    </div>
  `}function z(e){let t=e.querySelector(`#btn-lead-back-pipeline`);t&&t.addEventListener(`click`,()=>{b.navigateBackToPipeline()});let n=e.querySelector(`#btn-back-not-found`);n&&n.addEventListener(`click`,()=>{b.navigateBackToPipeline()}),e.querySelectorAll(`.btn-stage-node`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.stageId,n=b.getState(),r=b.updateLeadStage(n.selectedLeadId,t);r.success||alert(r.message)})});let r=e.querySelector(`#btn-gate-next-step`);r&&r.addEventListener(`click`,()=>{let e=b.getState(),t=(e.leads||[]).find(t=>t.id===e.selectedLeadId);if(!t)return;let n=[`new_lead`,`follow_up`,`requirement`,`quotation`,`negotiation`,`deal`],r=n.indexOf(t.stage_id);if(r!==-1&&r<n.length-1){let e=n[r+1],i=b.updateLeadStage(t.id,e);i.success||alert(i.message)}});let i=e.querySelector(`#btn-gate-reject`);i&&i.addEventListener(`click`,()=>{if(prompt(`Masukkan alasan penolakan lead / status reject:`,`Budget Klien Over / Tidak Masuk`)){let e=b.getState();b.updateLeadStage(e.selectedLeadId,`reject`)}}),e.querySelectorAll(`.btn-detail-tab`).forEach(t=>{t.addEventListener(`click`,()=>{j=t.dataset.tab,R(e),B(e)})});let a=e.querySelector(`#btn-header-meet`);a&&a.addEventListener(`click`,()=>{Et=!0,B(e)});let o=e.querySelector(`#btn-quick-schedule-meet`);Mt(o)&&o.addEventListener(`click`,()=>{Et=!0,B(e)});let s=e.querySelector(`#btn-open-meet-modal`);s&&s.addEventListener(`click`,()=>{Et=!0,B(e)});let u=e.querySelector(`#btn-close-meet-modal`);u&&u.addEventListener(`click`,()=>{Et=!1,B(e)});let d=e.querySelector(`#btn-cancel-meet`);d&&d.addEventListener(`click`,()=>{Et=!1,B(e)});let f=e.querySelector(`#btn-submit-meet`);f&&f.addEventListener(`click`,()=>{let t=e.querySelector(`#meet-modal-title`),n=e.querySelector(`#meet-modal-date`),r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId);i&&b.addActivity({lead_id:i.id,type:`google_meet`,title:t?t.value:`Diskusi Scope ${i.title}`,scheduled_at:n?new Date(n.value).toISOString():new Date().toISOString(),duration_minutes:60,status:`planned`,meet_link:`https://meet.google.com/ast-${Math.random().toString(36).substring(2,6)}-${Math.random().toString(36).substring(2,6)}`,assigned_to:i.sales_pic,attendees:[i.primary_contact?i.primary_contact.email:`client@email.com`],notes:`Google Calendar Event & Meet link dibuat otomatis via CRM.`}),Et=!1,j=`activity`,B(e)});let p=e.querySelector(`#btn-header-wa`);p&&p.addEventListener(`click`,()=>{b.setState({activeView:`client_care`})});let m=e.querySelector(`#btn-header-quotation`);m&&m.addEventListener(`click`,()=>{j=`quotation`,B(e)});let h=e.querySelector(`#btn-quick-goto-quotation`);h&&h.addEventListener(`click`,()=>{j=`quotation`,B(e)});let g=e.querySelector(`#btn-see-all-activities`);g&&g.addEventListener(`click`,()=>{j=`activity`,B(e)});let _=e.querySelector(`#btn-trigger-ai-rab`);_&&_.addEventListener(`click`,async()=>{let t=e.querySelector(`#req-prompt-input`),n=t?t.value.trim():gt.trim();!n&&Ct.length===0?alert(`Wajib mengisi prompt kebutuhan atau mengunggah dokumen (PRD Section 9.1).`):(xt=!0,St=15,e.innerHTML=R(),z(e),setTimeout(()=>{St=45,e.innerHTML=R(),z(e)},350),setTimeout(()=>{St=80,e.innerHTML=R(),z(e)},750),setTimeout(async()=>{St=100,e.innerHTML=R(),z(e);let t=b.getState();I=l(`30/40/30`,(await b.generateAiRab(t.selectedLeadId,n||gt,{platform:_t,complexity:vt,include_uat:yt,include_training:bt,contingency_pct:M})).grand_total,`grand_total`).items,xt=!1,j=`rab`,e.innerHTML=R(),z(e),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`✨ Estimasi RAB & Mandays AI berhasil digenerate! Modul terstruktur kini editable.`,type:`success`}}))},1200))});let v=e.querySelector(`#req-prompt-input`);v&&v.addEventListener(`input`,e=>{gt=e.target.value});let y=e.querySelector(`#req-dropzone-box`),ee=e.querySelector(`#req-file-input`);y&&ee&&(y.addEventListener(`click`,()=>{ee.click()}),ee.addEventListener(`change`,t=>{t.target.files&&t.target.files.length>0&&(Array.from(t.target.files).forEach(e=>{Ct.push({name:e.name,size:`${(e.size/1048576).toFixed(1)} MB`})}),e.innerHTML=R(),z(e))}),y.addEventListener(`dragover`,e=>{e.preventDefault(),y.classList.add(`drag-over`)}),y.addEventListener(`dragleave`,e=>{e.preventDefault(),y.classList.remove(`drag-over`)}),y.addEventListener(`drop`,t=>{t.preventDefault(),y.classList.remove(`drag-over`),t.dataTransfer&&t.dataTransfer.files&&t.dataTransfer.files.length>0&&(Array.from(t.dataTransfer.files).forEach(e=>{Ct.push({name:e.name,size:`${(e.size/1048576).toFixed(1)} MB`})}),e.innerHTML=R(),z(e))}));let te=e.querySelector(`#req-platform-select`);te&&te.addEventListener(`change`,e=>{_t=e.target.value});let ne=e.querySelector(`#req-complexity-select`);ne&&ne.addEventListener(`change`,e=>{vt=e.target.value});let re=e.querySelector(`#req-contingency-input`);re&&re.addEventListener(`input`,e=>{M=Number(e.target.value)||0});let ie=e.querySelector(`#req-chk-uat`);ie&&ie.addEventListener(`change`,e=>{yt=e.target.checked});let ae=e.querySelector(`#req-chk-training`);ae&&ae.addEventListener(`change`,e=>{bt=e.target.checked});let oe=e.querySelector(`#btn-toggle-internal-view`);oe&&oe.addEventListener(`click`,()=>{N=!N,e.innerHTML=R(),z(e)});let se=e.querySelector(`#btn-open-add-module`);se&&se.addEventListener(`click`,()=>{let t=b.getState(),n=(t.leads||[]).find(e=>e.id===t.selectedLeadId);if(!n)return;let r=n.rab?.items?[...n.rab.items]:[],i={id:`rab-item-${Date.now()}`,category:`Transaksi`,name:`Modul Kustom Baru`,description:`Deskripsi modul kustom yang ditambahkan manual oleh presales`,qty:4,unit:`Mandays`,role:`Fullstack Dev`,unit_price:15e5,unit_cost:9e5,subtotal:6e6,mandays:4,complexity:`Medium`,source:`Manual`};r.push(i),b.updateLeadRabItems(n.id,r,M,0),e.innerHTML=R(),z(e)}),e.querySelectorAll(`.btn-delete-rab-item[data-item-id]`).forEach(t=>{t.addEventListener(`click`,n=>{n.stopPropagation();let r=t.dataset.itemId,i=b.getState(),a=(i.leads||[]).find(e=>e.id===i.selectedLeadId);if(!a||!a.rab?.items)return;if(a.rab.items.length<=1){alert(`Minimal harus tersisa 1 modul dalam RAB.`);return}let o=a.rab.items.filter(e=>e.id!==r);b.updateLeadRabItems(a.id,o,M,0),e.innerHTML=R(),z(e)})}),e.querySelectorAll(`.rab-item-name-input`).forEach(e=>{e.addEventListener(`change`,t=>{let n=e.dataset.itemId,r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId);if(!i||!i.rab?.items)return;let a=i.rab.items.map(e=>e.id===n?{...e,name:t.target.value}:e);b.updateLeadRabItems(i.id,a,M,0)})}),e.querySelectorAll(`.rab-item-desc-input`).forEach(e=>{e.addEventListener(`change`,t=>{let n=e.dataset.itemId,r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId);if(!i||!i.rab?.items)return;let a=i.rab.items.map(e=>e.id===n?{...e,description:t.target.value}:e);b.updateLeadRabItems(i.id,a,M,0)})}),e.querySelectorAll(`.rab-item-qty-input`).forEach(t=>{t.addEventListener(`change`,n=>{let r=t.dataset.itemId,i=Math.max(1,Number(n.target.value)||1),a=b.getState(),o=(a.leads||[]).find(e=>e.id===a.selectedLeadId);if(!o||!o.rab?.items)return;let s=o.rab.items.map(e=>e.id===r?{...e,qty:i,subtotal:i*e.unit_price,mandays:e.unit===`Mandays`?i:e.mandays}:e);b.updateLeadRabItems(o.id,s,M,0),e.innerHTML=R(),z(e)})}),e.querySelectorAll(`.rab-item-unit-select`).forEach(e=>{e.addEventListener(`change`,t=>{let n=e.dataset.itemId,r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId);if(!i||!i.rab?.items)return;let a=i.rab.items.map(e=>e.id===n?{...e,unit:t.target.value}:e);b.updateLeadRabItems(i.id,a,M,0)})}),e.querySelectorAll(`.rab-item-category-select`).forEach(e=>{e.addEventListener(`change`,t=>{let n=e.dataset.itemId,r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId);if(!i||!i.rab?.items)return;let a=i.rab.items.map(e=>e.id===n?{...e,category:t.target.value}:e);b.updateLeadRabItems(i.id,a,M,0)})}),e.querySelectorAll(`.rab-item-price-input`).forEach(t=>{t.addEventListener(`change`,n=>{let r=t.dataset.itemId,i=Math.max(0,Number(n.target.value)||0),a=b.getState(),o=(a.leads||[]).find(e=>e.id===a.selectedLeadId);if(!o||!o.rab?.items)return;let s=o.rab.items.map(e=>e.id===r?{...e,unit_price:i,subtotal:e.qty*i}:e);b.updateLeadRabItems(o.id,s,M,0),e.innerHTML=R(),z(e)})});let ce=e.querySelector(`#btn-toggle-rab-reviewed`);ce&&ce.addEventListener(`click`,()=>{let t=b.getState();b.markRabReviewed(t.selectedLeadId,`Asisyah Sarah Azzahra (Super Admin & PM)`),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`✓ Status RAB ditandai Reviewed by Presales! Gate aturan bisnis Requirement terpenuhi.`,type:`success`}})),e.innerHTML=R(),z(e)});let le=e.querySelector(`#top-preset-select`);le&&le.addEventListener(`change`,t=>{P=t.target.value;let n=b.getState(),r=(n.leads||[]).find(e=>e.id===n.selectedLeadId),i=F===`grand_total`?r?.rab?r.rab.grand_total:r?.est_value||0:r?.rab?r.rab.total_before_tax:r?.est_value||0;I=l(P,i,F).items,e.innerHTML=R(),z(e)}),e.querySelectorAll(`input[name="top_calc_basis"]`).forEach(t=>{t.addEventListener(`change`,t=>{F=t.target.value,e.innerHTML=R(),z(e)})});let ue=e.querySelector(`#btn-add-top-term`);ue&&ue.addEventListener(`click`,()=>{I||=[];let t={id:`term-${Date.now()}`,term_no:I.length+1,name:`Termin ${I.length+1}`,description:`Penyelesaian milestone tahapan proyek`,value_type:`percent`,value:10,calculated_amount:0,trigger_type:`milestone`,trigger_label:`Penyelesaian milestone`,due_days_label:`Net 14`,status:`Planned`};I.push(t),e.innerHTML=R(),z(e)}),e.querySelectorAll(`.btn-delete-top-term`).forEach(t=>{t.addEventListener(`click`,()=>{let n=Number(t.dataset.idx);!I||I.length<=1?alert(`Minimal harus memiliki 1 termin pembayaran.`):(I.splice(n,1),e.innerHTML=R(),z(e))})}),e.querySelectorAll(`.top-term-name-input`).forEach(e=>{e.addEventListener(`change`,()=>{let t=Number(e.dataset.idx);I&&I[t]&&(I[t].name=e.value)})}),e.querySelectorAll(`.top-term-desc-input`).forEach(e=>{e.addEventListener(`change`,()=>{let t=Number(e.dataset.idx);I&&I[t]&&(I[t].description=e.value)})}),e.querySelectorAll(`.top-term-type-select`).forEach(t=>{t.addEventListener(`change`,()=>{let n=Number(t.dataset.idx);I&&I[n]&&(I[n].value_type=t.value,e.innerHTML=R(),z(e))})}),e.querySelectorAll(`.top-term-val-input`).forEach(t=>{t.addEventListener(`change`,()=>{let n=Number(t.dataset.idx);I&&I[n]&&(I[n].value=Number(t.value)||0,e.innerHTML=R(),z(e))})}),e.querySelectorAll(`.top-term-trigger-input`).forEach(e=>{e.addEventListener(`change`,()=>{let t=Number(e.dataset.idx);I&&I[t]&&(I[t].trigger_label=e.value)})}),e.querySelectorAll(`.top-term-due-input`).forEach(e=>{e.addEventListener(`change`,()=>{let t=Number(e.dataset.idx);I&&I[t]&&(I[t].due_days_label=e.value)})}),e.querySelectorAll(`.btn-quo-status-action`).forEach(t=>{t.addEventListener(`click`,()=>{let n=t.dataset.status,r=b.getState(),i=(r.leads||[]).find(e=>e.id===r.selectedLeadId),a=i&&i.quotations&&i.quotations[0],o=a?a.id:`quo-1`;if(n===`Reject`){let t=prompt(`Masukkan alasan penolakan lead:`,`Budget Klien Over / Tidak Masuk`);t&&(b.updateQuotationStatus(r.selectedLeadId,o,`Reject`,t),e.innerHTML=R(),z(e))}else{let t=b.updateQuotationStatus(r.selectedLeadId,o,n);t.success?(n===`Deal`&&alert(`Selamat! Status DEAL berhasil dikonfirmasi.
- Master Customer diubah ke status ACTIVE
- Proyek ERP Delivery otomatis dibuat
- Jadwal Invoice termin pembayaran otomatis diterbitkan ke Finance.`),e.innerHTML=R(),z(e)):alert(t.message)}})}),e.querySelectorAll(`.btn-doc-preview-tab`).forEach(t=>{t.addEventListener(`click`,()=>{Tt=t.dataset.previewTab,e.innerHTML=R(),z(e)})});let x=e.querySelector(`#btn-print-quotation-doc`);x&&x.addEventListener(`click`,()=>{window.print()});let de=e.querySelector(`#btn-generate-quotation-doc`);de&&de.addEventListener(`click`,()=>{let t=b.getState(),n=(t.leads||[]).find(e=>e.id===t.selectedLeadId);if(!n)return;let r=F===`grand_total`?n.rab?n.rab.grand_total:n.est_value:n.rab?n.rab.total_before_tax:n.est_value,i=c(I||[],r,F,P,!0);if(!i.is_balanced){alert(`Setting TOP harus balance (Total 100%) sebelum quotation dapat digenerate (PRD Section 8.11.1 BR-15).`);return}let a=e.querySelector(`#intro-letter-textarea`);a&&(wt=a.value);let o=b.saveQuotationWithTop(n.id,i,wt,`Rev. 2`);window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`✨ Quotation paket ${o.number} berhasil digenerate lengkap dengan Setting TOP dan 22 Pasal MoU!`,type:`success`}})),e.innerHTML=R(),z(e)});let fe=e.querySelector(`#intro-letter-textarea`);fe&&fe.addEventListener(`input`,e=>{wt=e.target.value});let pe=e.querySelector(`#btn-add-internal-note`);pe&&pe.addEventListener(`click`,()=>{let t=e.querySelector(`#new-note-text-input`);t&&t.value.trim()&&(kt.unshift(t.value.trim()),t.value=``,B(e))});let me=e.querySelector(`#btn-action-deal`);me&&me.addEventListener(`click`,()=>{let e=b.getState(),t=(e.leads||[]).find(t=>t.id===e.selectedLeadId),n=t&&t.quotations&&t.quotations[0],r=b.updateQuotationStatus(e.selectedLeadId,n?n.id:`quo-1`,`Deal`);r.success?alert(`Selamat! Status DEAL berhasil dikonfirmasi.
- Master Customer diubah ke status ACTIVE
- Proyek ERP Delivery otomatis dibuat
- Jadwal Invoice termin pembayaran otomatis diterbitkan ke Finance.`):alert(r.message)});let he=e.querySelector(`#btn-action-reject`);he&&he.addEventListener(`click`,()=>{let e=prompt(`Masukkan alasan penolakan lead:`,`Budget Klien Over / Tidak Masuk`);if(e){let t=b.getState(),n=(t.leads||[]).find(e=>e.id===t.selectedLeadId),r=n&&n.quotations&&n.quotations[0];b.updateQuotationStatus(t.selectedLeadId,r?r.id:`quo-1`,`Reject`,e)}}),A(e.querySelector(`#voicenote-manager-mount`)||e)}function Mt(e){return e!=null}function B(e){return e.innerHTML=R(),z(e),b.subscribe(()=>{e.innerHTML=R(),z(e)})}var Nt=t({bindCustomersListEvents:()=>Wt,mount:()=>Kt,renderCustomersList:()=>Ut}),Pt=``,Ft=`ALL`,It=`ALL`,Lt=`ALL`,Rt=1,zt=5,Bt=null,Vt=!1,V={legalName:``,brandName:``,industry:`Manufaktur & Fabrikasi`,customIndustry:``,companySize:`50-100 Karyawan`,customCompanySize:``,source:`Website`,customSource:``,website:``,npwp:``,city:``,province:``,address:``,picName:``,picJobTitle:``,picPhone:``,picEmail:``,picRole:`decision_maker`,customPicRole:``},H={building:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>`,search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,users:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,mapPin:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>`,eye:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`,x:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,externalLink:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>`,phone:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>`,arrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>`};function Ht(){V={legalName:``,brandName:``,industry:`Manufaktur & Fabrikasi`,customIndustry:``,companySize:`50-100 Karyawan`,customCompanySize:``,source:`Website`,customSource:``,website:``,npwp:``,city:``,province:``,address:``,picName:``,picJobTitle:``,picPhone:``,picEmail:``,picRole:`decision_maker`,customPicRole:``}}function Ut(){let{customers:e,leads:t}=b.getState(),n=Array.from(new Set(e.map(e=>e.industry))).filter(Boolean),r=Array.from(new Set(e.map(e=>e.first_touch_source))).filter(Boolean),i=Pt.toLowerCase().trim(),{data:a,pagination:o}=Le(e.filter(e=>{let t=i===``||e.legal_name.toLowerCase().includes(i)||e.code.toLowerCase().includes(i)||e.brand_name&&e.brand_name.toLowerCase().includes(i)||e.contacts&&e.contacts.some(e=>e.name.toLowerCase().includes(i)),n=Ft===`ALL`||e.industry===Ft,r=It===`ALL`||e.lifecycle_status===It,a=Lt===`ALL`||e.first_touch_source===Lt;return t&&n&&r&&a}),Rt,zt),s=e.find(e=>e.id===Bt)||null;return`
    <div style="flex:1;display:flex;flex-direction:column;height:100%;overflow:hidden;background:var(--bg-canvas);padding:24px;gap:16px;">
      
      <!-- Top Header Card -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:20px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
        <div>
          <div style="display:flex;align-items:center;gap:8px;">
            <h1 style="font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;margin:0;">
              Master Data Customer (Accounts & Contacts 360°)
            </h1>
            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
              PRD Section 8.3
            </span>
          </div>
          <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0 0;">
            Tabel terpadu master customer industri software house dengan atribusi kanal pertama (First-Touch Attribution).
          </p>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
          <div style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);font-weight:700;color:var(--text-secondary);font-size:12px;">
            Total ${e.length} Akun Customer
          </div>
          <button
            id="cust-open-add-modal-btn"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-700));color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.25);transition:all var(--transition);"
          >
            ${H.plus}
            <span>+ Tambah Customer</span>
          </button>
        </div>
      </div>

      <!-- Filter and Search Bar -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:16px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
        <div style="display:flex;align-items:center;gap:12px;flex:1;max-width:440px;">
          <div style="position:relative;width:100%;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
              ${H.search}
            </span>
            <input
              id="cust-search-input"
              type="text"
              placeholder="Cari customer, PIC, kode akun, brand..."
              value="${Pt}"
              style="width:100%;padding:8px 12px 8px 36px;border-radius:var(--radius-xl);font-size:12px;background:var(--bg-surface);border:1px solid transparent;color:var(--text-primary);outline:none;transition:border-color var(--transition);"
            />
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;overflow-x:auto;font-size:12px;">
          <!-- Status Filter -->
          <select
            id="cust-status-filter"
            style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);color:var(--text-secondary);outline:none;font-weight:500;font-size:12px;cursor:pointer;"
          >
            <option value="ALL" ${It===`ALL`?`selected`:``}>Semua Status</option>
            <option value="Prospect" ${It===`Prospect`?`selected`:``}>Prospect</option>
            <option value="Active" ${It===`Active`?`selected`:``}>Active</option>
            <option value="Dormant" ${It===`Dormant`?`selected`:``}>Dormant</option>
          </select>

          <!-- Industry Filter -->
          <select
            id="cust-industry-filter"
            style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);color:var(--text-secondary);outline:none;font-weight:500;font-size:12px;cursor:pointer;"
          >
            <option value="ALL" ${Ft===`ALL`?`selected`:``}>Semua Industri</option>
            ${n.map(e=>`
                <option value="${e}" ${Ft===e?`selected`:``}>
                  ${e}
                </option>
              `).join(``)}
          </select>

          <!-- Source Filter -->
          <select
            id="cust-source-filter"
            style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);color:var(--text-secondary);outline:none;font-weight:500;font-size:12px;cursor:pointer;"
          >
            <option value="ALL" ${Lt===`ALL`?`selected`:``}>Semua Sumber Pertama</option>
            ${r.map(e=>`
                <option value="${e}" ${Lt===e?`selected`:``}>
                  ${e}
                </option>
              `).join(``)}
          </select>
        </div>
      </div>

      <!-- Customer Table Container -->
      <div style="flex:1;display:flex;flex-direction:column;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);overflow:hidden;">
        <div style="flex:1;overflow:auto;">
          <table style="width:100%;text-align:left;border-collapse:collapse;font-size:12px;">
            <thead>
              <tr style="background:var(--bg-surface);border-bottom:1px solid var(--border-light);color:var(--text-muted);font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:0.05em;position:sticky;top:0;z-index:10;backdrop-filter:blur(4px);">
                <th style="padding:12px 16px;width:110px;">Kode Akun</th>
                <th style="padding:12px 16px;">Nama Perusahaan & Brand</th>
                <th style="padding:12px 16px;">Sektor Industri & Skala</th>
                <th style="padding:12px 16px;">PIC Utama & Kontak</th>
                <th style="padding:12px 16px;">Lokasi / Kota</th>
                <th style="padding:12px 16px;">First-Touch Source</th>
                <th style="padding:12px 16px;">Status & Health</th>
                <th style="padding:12px 16px;text-align:center;">Peluang</th>
                <th style="padding:12px 16px;text-align:right;">Aksi</th>
              </tr>
            </thead>
            <tbody style="border-top:1px solid var(--border-light);">
              ${a.length===0?`
                <tr>
                  <td colspan="9" style="padding:48px 16px;text-align:center;color:var(--text-muted);">
                    Tidak ada data customer yang sesuai dengan filter pencarian.
                  </td>
                </tr>
              `:a.map(e=>{let n=t.filter(t=>t.customer_id===e.id),r=e.contacts&&e.contacts.find(e=>e.is_primary)||e.contacts&&e.contacts[0],i=e.lifecycle_status===`Active`;return`
                        <tr
                          class="cust-table-row"
                          data-cust-id="${e.id}"
                          style="border-bottom:1px solid var(--border-light);transition:background var(--transition);"
                        >
                          <!-- Code -->
                          <td style="padding:12px 16px;font-family:var(--font-mono);font-weight:700;color:var(--text-secondary);">
                            <span style="padding:2px 8px;border-radius:var(--radius-sm);background:var(--bg-surface);color:var(--indigo-600);">
                              ${e.code}
                            </span>
                          </td>

                          <!-- Company Name & Brand -->
                          <td style="padding:12px 16px;">
                            <div style="font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
                              <span style="color:var(--indigo-500);display:flex;flex-shrink:0;">${H.building}</span>
                              <span>${e.legal_name}</span>
                            </div>
                            ${e.brand_name&&e.brand_name!==e.legal_name?`
                              <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                Brand: <span style="font-weight:500;color:var(--text-secondary);">${e.brand_name}</span>
                              </div>
                            `:``}
                          </td>

                          <!-- Industry & Scale -->
                          <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:var(--text-primary);">
                              ${e.industry||`-`}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                              ${e.company_size||`-`}
                            </div>
                          </td>

                          <!-- Primary Contact -->
                          <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:var(--text-primary);display:flex;align-items:center;gap:4px;">
                              <span style="color:var(--emerald-500);display:flex;">${H.users}</span>
                              <span>${r?.name||`-`}</span>
                              <span style="font-size:10px;color:var(--text-muted);">(${r?.job_title||`PIC`})</span>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);margin-top:2px;">
                              📱 ${r?.whatsapp_number||`-`}
                            </div>
                          </td>

                          <!-- Location -->
                          <td style="padding:12px 16px;color:var(--text-secondary);">
                            <div style="display:flex;align-items:center;gap:4px;">
                              <span style="color:var(--text-muted);display:flex;">${H.mapPin}</span>
                              <span>${e.city||`Jakarta`}</span>
                            </div>
                            <div style="font-size:10px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">
                              ${e.province||`DKI Jakarta`}
                            </div>
                          </td>

                          <!-- First-Touch Source -->
                          <td style="padding:12px 16px;">
                            <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
                              ${e.first_touch_source||`Website`}
                            </span>
                          </td>

                          <!-- Status & Health -->
                          <td style="padding:12px 16px;">
                            <div>
                              <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;${i?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:`background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);`}">
                                ${e.lifecycle_status}
                              </span>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;margin-top:4px;font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">
                              <span>Health:</span>
                              <span style="font-weight:700;color:var(--emerald-600);">${e.health_score??85}%</span>
                            </div>
                          </td>

                          <!-- Related Leads -->
                          <td style="padding:12px 16px;text-align:center;">
                            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-family:var(--font-mono);font-weight:700;font-size:12px;background:var(--bg-surface);color:var(--text-secondary);">
                              ${n.length} Lead
                            </span>
                          </td>

                          <!-- Actions -->
                          <td style="padding:12px 16px;text-align:right;">
                            <button
                              class="cust-view-profile-btn"
                              data-cust-id="${e.id}"
                              style="padding:5px 10px;border-radius:var(--radius-md);background:rgba(99,102,241,.1);color:var(--indigo-600);font-weight:700;font-size:11px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:background var(--transition);"
                            >
                              ${H.eye}
                              <span>Profil 360°</span>
                            </button>
                          </td>
                        </tr>
                      `}).join(``)}
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div style="border-top:1px solid var(--border-light);">
          ${ze(o,[5,10,20],`customers-list-pagination`)}
        </div>
      </div>

      <!-- ADD CUSTOMER MODAL WITH "LAINNYA" CUSTOM INPUTS -->
      ${Vt?`
        <div id="add-cust-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);overflow-y:auto;">
          <div style="width:100%;max-width:640px;background:var(--bg-card);border-radius:var(--radius-2xl);box-shadow:var(--shadow-xl);border:1px solid var(--border-light);margin:32px 0;overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-light);background:var(--bg-surface);">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--indigo-600);display:flex;">${H.building}</span>
                <h3 style="font-weight:700;font-size:14px;color:var(--text-primary);margin:0;">
                  Tambah Customer Baru (Master Data)
                </h3>
              </div>
              <button
                id="add-cust-modal-close-btn"
                style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;"
              >
                ${H.x}
              </button>
            </div>

            <form id="add-cust-form" style="padding:24px;display:flex;flex-direction:column;gap:16px;max-height:80vh;overflow-y:auto;font-size:12px;">
              <!-- 1. Informasi Perusahaan -->
              <div style="display:flex;flex-direction:column;gap:12px;">
                <h4 style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--indigo-600);border-bottom:1px solid var(--border-light);padding-bottom:4px;margin:0;">
                  1. Informasi Perusahaan
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Nama Legal Perusahaan*:</label>
                    <input
                      id="form-cust-legal-name"
                      type="text"
                      required
                      placeholder="Contoh: PT Sinar Abadi Nusantara"
                      value="${V.legalName}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Nama Brand / Merek Dagang:</label>
                    <input
                      id="form-cust-brand-name"
                      type="text"
                      placeholder="Contoh: SinarAbadi.id (Opsional)"
                      value="${V.brandName}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <!-- Sektor Industri with Lainnya -->
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Sektor Industri:</label>
                    <select
                      id="form-cust-industry"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    >
                      <option value="Manufaktur & Fabrikasi" ${V.industry===`Manufaktur & Fabrikasi`?`selected`:``}>Manufaktur & Fabrikasi</option>
                      <option value="Retail & Multi-Store" ${V.industry===`Retail & Multi-Store`?`selected`:``}>Retail & Multi-Store</option>
                      <option value="Financial Technology" ${V.industry===`Financial Technology`?`selected`:``}>Financial Technology</option>
                      <option value="Healthcare & Farmasi" ${V.industry===`Healthcare & Farmasi`?`selected`:``}>Healthcare & Farmasi</option>
                      <option value="Logistics & Supply Chain" ${V.industry===`Logistics & Supply Chain`?`selected`:``}>Logistics & Supply Chain</option>
                      <option value="Food & Beverage / Restoran" ${V.industry===`Food & Beverage / Restoran`?`selected`:``}>Food & Beverage / Restoran</option>
                      <option value="Pendidikan / Edutech" ${V.industry===`Pendidikan / Edutech`?`selected`:``}>Pendidikan / Edutech</option>
                      <option value="Lainnya" ${V.industry===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                    </select>

                    ${V.industry===`Lainnya`?`
                      <div style="margin-top:6px;padding:10px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                        <label style="display:block;font-size:11px;font-weight:700;color:var(--indigo-600);margin-bottom:4px;">Ketik Sektor Industri Kustom:</label>
                        <input
                          id="form-cust-custom-industry"
                          type="text"
                          required
                          placeholder="Misal: Energi Terbarukan & Smart Grid..."
                          value="${V.customIndustry}"
                          style="width:100%;padding:6px 10px;border-radius:var(--radius-md);background:var(--bg-card);border:1px solid var(--indigo-400);color:var(--text-primary);outline:none;"
                        />
                      </div>
                    `:``}
                  </div>

                  <!-- Skala Perusahaan with Lainnya -->
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Skala Karyawan / Ukuran:</label>
                    <select
                      id="form-cust-company-size"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    >
                      <option value="1-20 Karyawan" ${V.companySize===`1-20 Karyawan`?`selected`:``}>1-20 Karyawan (Small)</option>
                      <option value="20-50 Karyawan" ${V.companySize===`20-50 Karyawan`?`selected`:``}>20-50 Karyawan (Medium)</option>
                      <option value="50-100 Karyawan" ${V.companySize===`50-100 Karyawan`?`selected`:``}>50-100 Karyawan (Growing)</option>
                      <option value="100-500 Karyawan" ${V.companySize===`100-500 Karyawan`?`selected`:``}>100-500 Karyawan (Mid-Market)</option>
                      <option value="500+ Karyawan" ${V.companySize===`500+ Karyawan`?`selected`:``}>500+ Karyawan (Enterprise)</option>
                      <option value="Lainnya" ${V.companySize===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                    </select>

                    ${V.companySize===`Lainnya`?`
                      <div style="margin-top:6px;padding:10px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                        <label style="display:block;font-size:11px;font-weight:700;color:var(--indigo-600);margin-bottom:4px;">Ketik Skala Karyawan Kustom:</label>
                        <input
                          id="form-cust-custom-company-size"
                          type="text"
                          required
                          placeholder="Misal: 1000+ Multi-cabang Nasional..."
                          value="${V.customCompanySize}"
                          style="width:100%;padding:6px 10px;border-radius:var(--radius-md);background:var(--bg-card);border:1px solid var(--indigo-400);color:var(--text-primary);outline:none;"
                        />
                      </div>
                    `:``}
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <!-- First Touch Source with Lainnya -->
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Sumber Kanal Pertama (First-Touch Source):</label>
                    <select
                      id="form-cust-source"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    >
                      <option value="Website" ${V.source===`Website`?`selected`:``}>Website</option>
                      <option value="Instagram" ${V.source===`Instagram`?`selected`:``}>Instagram</option>
                      <option value="WhatsApp" ${V.source===`WhatsApp`?`selected`:``}>WhatsApp</option>
                      <option value="Referral" ${V.source===`Referral`?`selected`:``}>Referral</option>
                      <option value="LinkedIn" ${V.source===`LinkedIn`?`selected`:``}>LinkedIn</option>
                      <option value="Meta Ads" ${V.source===`Meta Ads`?`selected`:``}>Meta Ads</option>
                      <option value="Google Ads" ${V.source===`Google Ads`?`selected`:``}>Google Ads</option>
                      <option value="Event/Webinar" ${V.source===`Event/Webinar`?`selected`:``}>Event / Webinar</option>
                      <option value="Lainnya" ${V.source===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                    </select>

                    ${V.source===`Lainnya`?`
                      <div style="margin-top:6px;padding:10px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                        <label style="display:block;font-size:11px;font-weight:700;color:var(--indigo-600);margin-bottom:4px;">Ketik Sumber Kanal Kustom:</label>
                        <input
                          id="form-cust-custom-source"
                          type="text"
                          required
                          placeholder="Misal: Pameran Industri Hannover Messe..."
                          value="${V.customSource}"
                          style="width:100%;padding:6px 10px;border-radius:var(--radius-md);background:var(--bg-card);border:1px solid var(--indigo-400);color:var(--text-primary);outline:none;"
                        />
                      </div>
                    `:``}
                  </div>

                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Website URL:</label>
                    <input
                      id="form-cust-website"
                      type="text"
                      placeholder="https://perusahaan.co.id"
                      value="${V.website}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Kota:</label>
                    <input
                      id="form-cust-city"
                      type="text"
                      placeholder="Contoh: Surabaya"
                      value="${V.city}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>

                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Provinsi:</label>
                    <input
                      id="form-cust-province"
                      type="text"
                      placeholder="Contoh: Jawa Timur"
                      value="${V.province}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                </div>
              </div>

              <!-- 2. Kontak PIC Utama -->
              <div style="display:flex;flex-direction:column;gap:12px;padding-top:8px;">
                <h4 style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--indigo-600);border-bottom:1px solid var(--border-light);padding-bottom:4px;margin:0;">
                  2. Kontak PIC Utama
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Nama PIC Utama*:</label>
                    <input
                      id="form-cust-pic-name"
                      type="text"
                      required
                      placeholder="Contoh: Ir. Hendra Gunawan"
                      value="${V.picName}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>

                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Jabatan / Job Title:</label>
                    <input
                      id="form-cust-pic-job"
                      type="text"
                      placeholder="Contoh: VP Information Technology"
                      value="${V.picJobTitle}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Nomor WhatsApp*:</label>
                    <input
                      id="form-cust-pic-phone"
                      type="text"
                      required
                      placeholder="+628123456789"
                      value="${V.picPhone}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                    />
                  </div>

                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <label style="font-weight:600;color:var(--text-secondary);">Email PIC:</label>
                    <input
                      id="form-cust-pic-email"
                      type="email"
                      placeholder="hendra@perusahaan.co.id"
                      value="${V.picEmail}"
                      style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                    />
                  </div>
                </div>

                <!-- Peran PIC with Lainnya -->
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:600;color:var(--text-secondary);">Peran dalam Komite Keputusan:</label>
                  <select
                    id="form-cust-pic-role"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  >
                    <option value="decision_maker" ${V.picRole===`decision_maker`?`selected`:``}>Decision Maker (Direktur / VP)</option>
                    <option value="influencer" ${V.picRole===`influencer`?`selected`:``}>Influencer / Champion</option>
                    <option value="technical" ${V.picRole===`technical`?`selected`:``}>Technical Evaluator / Lead Architect</option>
                    <option value="finance" ${V.picRole===`finance`?`selected`:``}>Finance / Procurement Lead</option>
                    <option value="Lainnya" ${V.picRole===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                  </select>

                  ${V.picRole===`Lainnya`?`
                    <div style="margin-top:6px;padding:10px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                      <label style="display:block;font-size:11px;font-weight:700;color:var(--indigo-600);margin-bottom:4px;">Ketik Peran PIC Kustom:</label>
                      <input
                        id="form-cust-custom-pic-role"
                        type="text"
                        required
                        placeholder="Misal: Konsultan Eksternal / Auditor..."
                        value="${V.customPicRole}"
                        style="width:100%;padding:6px 10px;border-radius:var(--radius-md);background:var(--bg-card);border:1px solid var(--indigo-400);color:var(--text-primary);outline:none;"
                      />
                    </div>
                  `:``}
                </div>
              </div>

              <!-- Submit Buttons -->
              <div style="padding-top:16px;display:flex;justify-content:flex-end;gap:8px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="add-cust-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Simpan Customer
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}

      <!-- 360-DEGREE CUSTOMER DETAIL DRAWER -->
      ${s?`
        <div id="cust-drawer-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;justify-content:flex-end;background:rgba(15,23,42,.4);backdrop-filter:blur(2px);">
          <div style="width:100%;max-width:560px;height:100%;background:var(--bg-card);box-shadow:var(--shadow-xl);border-left:1px solid var(--border-light);display:flex;flex-direction:column;overflow:hidden;animation:slideInRight 0.25s ease;">
            <!-- Drawer Header -->
            <div style="padding:20px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;background:var(--bg-surface);">
              <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;">
                  ${s.legal_name.charAt(3)||`C`}
                </div>
                <div>
                  <h3 style="font-weight:800;font-size:14px;color:var(--text-primary);margin:0;">
                    ${s.legal_name}
                  </h3>
                  <p style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);margin:2px 0 0 0;">
                    ${s.code} • ${s.industry||`Umum`}
                  </p>
                </div>
              </div>
              <button
                id="cust-drawer-close-btn"
                style="padding:8px;border-radius:var(--radius-lg);color:var(--text-muted);background:none;border:none;cursor:pointer;"
              >
                ${H.x}
              </button>
            </div>

            <!-- Drawer Body -->
            <div style="flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:20px;font-size:12px;">
              <!-- Account Overview Cards -->
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);">
                  <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;">Status Lifecycle</span>
                  <p style="font-weight:700;color:var(--text-primary);margin:4px 0 0 0;">
                    ${s.lifecycle_status}
                  </p>
                </div>
                <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);">
                  <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;">Attribution Source</span>
                  <p style="font-weight:700;color:var(--indigo-600);margin:4px 0 0 0;">
                    ${s.first_touch_source}
                  </p>
                </div>
              </div>

              <!-- Address & Legal -->
              <div style="display:flex;flex-direction:column;gap:8px;">
                <h4 style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);font-size:10px;margin:0;">
                  Alamat & Legalitas
                </h4>
                <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:6px;">
                  <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);">Kota & Provinsi:</span>
                    <span style="font-weight:600;color:var(--text-primary);">${s.city||`-`}, ${s.province||`-`}</span>
                  </div>
                  <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);">Alamat Kantor:</span>
                    <span style="color:var(--text-secondary);max-width:240px;text-align:right;">${s.address||`-`}</span>
                  </div>
                  ${s.npwp?`
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-muted);">NPWP:</span>
                      <span style="font-family:var(--font-mono);color:var(--text-secondary);">${s.npwp}</span>
                    </div>
                  `:``}
                  ${s.website?`
                    <div style="display:flex;justify-content:space-between;">
                      <span style="color:var(--text-muted);">Website:</span>
                      <a href="${s.website}" target="_blank" rel="noreferrer" style="color:var(--indigo-600);text-decoration:none;display:flex;align-items:gap;gap:4px;">
                        <span>${s.website}</span>
                        ${H.externalLink}
                      </a>
                    </div>
                  `:``}
                </div>
              </div>

              <!-- Contacts List -->
              <div style="display:flex;flex-direction:column;gap:12px;">
                <h4 style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);font-size:10px;margin:0;">
                  Daftar Stakeholder & Kontak (${s.contacts?s.contacts.length:0})
                </h4>
                <div style="display:flex;flex-direction:column;gap:8px;">
                  ${(s.contacts||[]).map(e=>`
                    <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;">
                      <div>
                        <div style="display:flex;align-items:center;gap:8px;">
                          <span style="font-weight:700;color:var(--text-primary);">
                            ${e.name}
                          </span>
                          ${e.is_primary?`
                            <span style="padding:2px 6px;border-radius:var(--radius-sm);font-size:9px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);">
                              PIC Utama
                            </span>
                          `:``}
                        </div>
                        <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">${e.job_title} • ${e.role_type}</p>
                        <div style="margin-top:4px;display:flex;align-items:center;gap:12px;font-size:11px;font-family:var(--font-mono);color:var(--text-secondary);">
                          <span>📱 ${e.whatsapp_number}</span>
                          <span>✉ ${e.email}</span>
                        </div>
                      </div>
                      <a
                        href="https://wa.me/${e.whatsapp_number.replace(/\\D/g,``)}"
                        target="_blank"
                        rel="noreferrer"
                        style="padding:6px 12px;border-radius:var(--radius-lg);background:var(--emerald-500);color:#fff;font-weight:700;font-size:11px;display:flex;align-items:center;gap:4px;text-decoration:none;"
                      >
                        ${H.phone}
                        <span>Chat WA</span>
                      </a>
                    </div>
                  `).join(``)}
                </div>
              </div>

              <!-- Associated Leads -->
              <div style="display:flex;flex-direction:column;gap:12px;">
                <h4 style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);font-size:10px;margin:0;">
                  Peluang Proyek Terkait (${t.filter(e=>e.customer_id===s.id).length})
                </h4>
                <div style="display:flex;flex-direction:column;gap:8px;">
                  ${t.filter(e=>e.customer_id===s.id).map(e=>`
                    <div
                      class="cust-drawer-lead-item"
                      data-lead-id="${e.id}"
                      style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);cursor:pointer;transition:all var(--transition);display:flex;align-items:center;justify-content:space-between;"
                    >
                      <div>
                        <div style="display:flex;align-items:center;gap:8px;">
                          <span style="font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--indigo-600);">
                            ${e.code}
                          </span>
                          <span style="font-weight:700;color:var(--text-primary);">
                            ${e.title}
                          </span>
                        </div>
                        <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0 0;">
                          Stage: <strong style="color:var(--text-primary);">${e.stage_id.toUpperCase()}</strong> • Target: ${e.target_closing||`-`}
                        </p>
                      </div>
                      <span style="color:var(--text-muted);display:flex;">
                        ${H.arrowRight}
                      </span>
                    </div>
                  `).join(``)}
                </div>
              </div>
            </div>
          </div>
        </div>
      `:``}
    </div>
  `}function Wt(e){let t=e.querySelector(`#cust-search-input`);t&&t.addEventListener(`input`,t=>{Pt=t.target.value,Rt=1,U(e)});let n=e.querySelector(`#cust-status-filter`);n&&n.addEventListener(`change`,t=>{It=t.target.value,Rt=1,U(e)});let r=e.querySelector(`#cust-industry-filter`);r&&r.addEventListener(`change`,t=>{Ft=t.target.value,Rt=1,U(e)});let i=e.querySelector(`#cust-source-filter`);i&&i.addEventListener(`change`,t=>{Lt=t.target.value,Rt=1,U(e)});let a=e.querySelector(`#cust-open-add-modal-btn`);a&&a.addEventListener(`click`,()=>{Vt=!0,U(e)});let o=e.querySelector(`#add-cust-modal-close-btn`),s=e.querySelector(`#add-cust-cancel-btn`);o&&o.addEventListener(`click`,()=>{Vt=!1,U(e)}),s&&s.addEventListener(`click`,()=>{Vt=!1,U(e)});let c=e.querySelector(`#add-cust-modal-backdrop`);c&&c.addEventListener(`click`,t=>{t.target===c&&(Vt=!1,U(e))});let l=e.querySelector(`#form-cust-industry`);l&&l.addEventListener(`change`,t=>{V.industry=t.target.value,U(e)});let u=e.querySelector(`#form-cust-custom-industry`);u&&u.addEventListener(`input`,e=>{V.customIndustry=e.target.value});let d=e.querySelector(`#form-cust-company-size`);d&&d.addEventListener(`change`,t=>{V.companySize=t.target.value,U(e)});let f=e.querySelector(`#form-cust-custom-company-size`);f&&f.addEventListener(`input`,e=>{V.customCompanySize=e.target.value});let p=e.querySelector(`#form-cust-source`);p&&p.addEventListener(`change`,t=>{V.source=t.target.value,U(e)});let m=e.querySelector(`#form-cust-custom-source`);m&&m.addEventListener(`input`,e=>{V.customSource=e.target.value});let h=e.querySelector(`#form-cust-pic-role`);h&&h.addEventListener(`change`,t=>{V.picRole=t.target.value,U(e)});let g=e.querySelector(`#form-cust-custom-pic-role`);g&&g.addEventListener(`input`,e=>{V.customPicRole=e.target.value}),Gt(e,`#form-cust-legal-name`,e=>V.legalName=e),Gt(e,`#form-cust-brand-name`,e=>V.brandName=e),Gt(e,`#form-cust-website`,e=>V.website=e),Gt(e,`#form-cust-city`,e=>V.city=e),Gt(e,`#form-cust-province`,e=>V.province=e),Gt(e,`#form-cust-pic-name`,e=>V.picName=e),Gt(e,`#form-cust-pic-job`,e=>V.picJobTitle=e),Gt(e,`#form-cust-pic-phone`,e=>V.picPhone=e),Gt(e,`#form-cust-pic-email`,e=>V.picEmail=e);let _=e.querySelector(`#add-cust-form`);_&&_.addEventListener(`submit`,t=>{if(t.preventDefault(),!V.legalName.trim()||!V.picName.trim()){alert(`Nama perusahaan dan nama PIC wajib diisi.`);return}let n=V.industry===`Lainnya`?V.customIndustry.trim()||`Industri Kustom`:V.industry,r=V.companySize===`Lainnya`?V.customCompanySize.trim()||`Custom Size`:V.companySize,i=V.source===`Lainnya`?V.customSource.trim()||`Lainnya`:V.source,a=V.picRole===`Lainnya`?V.customPicRole.trim()||`custom_role`:V.picRole;b.addNewCustomer({legal_name:V.legalName.trim(),brand_name:V.brandName.trim()||V.legalName.trim(),industry:n,company_size:r,first_touch_source:i,website:V.website.trim()||void 0,npwp:V.npwp.trim()||void 0,city:V.city.trim()||`Jakarta`,province:V.province.trim()||`DKI Jakarta`,address:V.address.trim()||`Alamat Perusahaan`,lifecycle_status:`Prospect`,health_score:90,contacts:[{id:`cnt-${Date.now()}`,name:V.picName.trim(),job_title:V.picJobTitle.trim()||`Manager`,whatsapp_number:V.picPhone.trim()||`+6281234567890`,email:V.picEmail.trim()||`pic@perusahaan.com`,role_type:a,is_primary:!0}]}),Ht(),Vt=!1,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Customer baru berhasil ditambahkan ke Master Data!`,type:`success`}})),U(e)}),e.querySelectorAll(`.cust-view-profile-btn[data-cust-id]`).forEach(t=>{t.addEventListener(`click`,n=>{n.stopPropagation(),Bt=t.dataset.custId,U(e)})});let v=e.querySelector(`#cust-drawer-close-btn`);v&&v.addEventListener(`click`,()=>{Bt=null,U(e)});let y=e.querySelector(`#cust-drawer-backdrop`);y&&y.addEventListener(`click`,t=>{t.target===y&&(Bt=null,U(e))}),e.querySelectorAll(`.cust-drawer-lead-item[data-lead-id]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.leadId;Bt=null,b.navigateToLeadDetail(t)})}),Be(e,t=>{Rt=t,U(e)},t=>{zt=t,Rt=1,U(e)},`customers-list-pagination`)}function Gt(e,t,n){let r=e.querySelector(t);r&&r.addEventListener(`input`,e=>n(e.target.value))}function U(e){e.innerHTML=Ut(),Wt(e)}function Kt(e){return U(e),b.subscribe(()=>{b.getState().activeView===`customers`&&U(e)})}var qt=t({bindProjectsListEvents:()=>fn,mount:()=>pn,renderProjectsList:()=>dn}),W=`dashboard`,Jt=``,Yt=`ALL`,Xt=1,Zt=4,Qt=!1,$t=``,en=``,tn=120,nn=21e7,rn=`90 Hari Pasca BAST`,an=`99.5% Uptime SLA`,on=null,sn=!1,cn=``,ln=new Date(Date.now()+2592e6).toISOString().split(`T`)[0],un=30,G={layers:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`,search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,zap:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,target:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,users:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,dollarSign:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,shieldCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>`,messageSquare:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,barChart:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>`,chevronRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>`,trendingUp:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>`,flame:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>`,activity:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>`,checkCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`,calendar:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>`,clock:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,printer:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>`,fileText:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>`,x:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`};function dn(){let{projects:e,invoices:t,customers:n,leads:r,waConversations:i}=b.getState(),o=e.reduce((e,t)=>e+t.contract_value,0),s=e.reduce((e,t)=>e+t.budget_mandays,0),c=e.reduce((e,t)=>e+t.actual_mandays,0),l=s>0?Math.round(c/s*100):0,u=e.length>0?Math.round(e.reduce((e,t)=>e+t.progress_pct,0)/e.length):0,d=t.filter(e=>e.status===`Paid`).reduce((e,t)=>e+t.grand_total,0),f=t.filter(e=>e.status===`Draft`||e.status===`Sent`).reduce((e,t)=>e+t.grand_total,0),p=i.reduce((e,t)=>e+t.unread_count,0),m=Jt.toLowerCase().trim(),{data:h,pagination:g}=Le(e.filter(e=>{let t=m===``||e.title.toLowerCase().includes(m)||e.code.toLowerCase().includes(m)||e.customer_name.toLowerCase().includes(m)||e.pm_name.toLowerCase().includes(m),n=Yt===`ALL`||e.status===Yt;return t&&n}),Xt,Zt),_=e.flatMap(e=>e.milestones.map(t=>({...t,projectTitle:e.title,projectCode:e.code,customerName:e.customer_name}))).sort((e,t)=>new Date(e.target_date).getTime()-new Date(t.target_date).getTime()),v=e.find(e=>e.id===on)||null;return`
    <div style="flex:1;display:flex;flex-direction:column;height:100%;overflow-y:auto;background:var(--bg-canvas);padding:24px;gap:24px;">
      
      <!-- Header & Global Tab Switcher -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;background:var(--bg-card);padding:20px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:38px;height:38px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-500));display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:var(--shadow-xs);">
            ${G.layers}
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;">
              <h1 style="font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;margin:0;">
                ASTACODE Proyek & Delivery Command Center
              </h1>
              <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
                Portal Operasional
              </span>
            </div>
            <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
              Pusat kendali eksekusi sistem software house, pelacakan alokasi mandays, jadwal rilis sprint, dan pintu gerbang utama ke modul ERP.
            </p>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
          <!-- Tab Mode Buttons -->
          <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:12px;font-weight:600;">
            <button
              class="prj-tab-btn ${W===`dashboard`?`active`:``}"
              data-tab="dashboard"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${W===`dashboard`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Portal & Dashboard
            </button>
            <button
              class="prj-tab-btn ${W===`projects`?`active`:``}"
              data-tab="projects"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${W===`projects`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Daftar Proyek (${e.length})
            </button>
            <button
              class="prj-tab-btn ${W===`mandays`?`active`:``}"
              data-tab="mandays"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${W===`mandays`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Resource & Mandays
            </button>
            <button
              class="prj-tab-btn ${W===`timeline`?`active`:``}"
              data-tab="timeline"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${W===`timeline`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Roadmap Sprint
            </button>
          </div>

          <button
            id="prj-open-new-modal-btn"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-700));color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.25);transition:all var(--transition);"
          >
            ${G.plus}
            <span>+ Inisiasi Proyek Baru</span>
          </button>
        </div>
      </div>

      <!-- TAB 1: PORTAL DASHBOARD UTAMA -->
      ${W===`dashboard`?`
        <div style="display:flex;flex-direction:column;gap:24px;">
          <!-- Welcome & Status Banner -->
          <div style="position:relative;overflow:hidden;border-radius:var(--radius-2xl);background:linear-gradient(135deg, #1e1b4b, #0f172a);color:#fff;padding:24px;border:1px solid rgba(99,102,241,.3);box-shadow:var(--shadow-lg);">
            <div style="position:relative;z-index:2;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
              <div style="max-width:580px;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;align-items:center;gap:8px;">
                  <span style="padding:2px 10px;border-radius:var(--radius-full);font-size:10px;font-weight:800;background:rgba(99,102,241,.2);color:#a5b4fc;border:1px solid rgba(99,102,241,.3);">
                    ASTACODE COMMAND CENTER
                  </span>
                  <span style="display:flex;align-items:center;gap:4px;font-size:11px;color:#34d399;font-weight:700;">
                    <span style="width:8px;height:8px;border-radius:var(--radius-full);background:#34d399;"></span>
                    Sistem Operasional Normal
                  </span>
                </div>
                <h2 style="font-size:20px;font-weight:800;letter-spacing:-0.01em;margin:4px 0 0 0;">
                  Selamat Datang, Asisyah Sarah Azzahra
                </h2>
                <p style="font-size:12px;color:rgba(199,210,254,.8);line-height:1.5;margin:0;">
                  Seluruh jalur proyek <em>delivery</em>, alokasi <em>mandays</em>, dan jadwal pengujian UAT berada dalam status terpantau. Gunakan portal ini sebagai gerbang pusat untuk beralih cepat ke modul operasional lainnya.
                </p>
              </div>

              <!-- Quick Health Meter Box -->
              <div style="display:flex;align-items:center;gap:16px;background:rgba(255,255,255,.05);backdrop-filter:blur(8px);padding:16px;border-radius:var(--radius-xl);border:1px solid rgba(255,255,255,.1);font-size:12px;font-family:var(--font-mono);">
                <div style="text-align:center;">
                  <span style="font-size:10px;color:#a5b4fc;text-transform:uppercase;display:block;">Portofolio Proyek</span>
                  <span style="font-size:18px;font-weight:900;color:#fff;">${e.length} Proyek</span>
                </div>
                <div style="width:1px;height:32px;background:rgba(255,255,255,.1);"></div>
                <div style="text-align:center;">
                  <span style="font-size:10px;color:#a5b4fc;text-transform:uppercase;display:block;">SLA Kepatuhan</span>
                  <span style="font-size:18px;font-weight:900;color:#34d399;">99.8%</span>
                </div>
                <div style="width:1px;height:32px;background:rgba(255,255,255,.1);"></div>
                <div style="text-align:center;">
                  <span style="font-size:10px;color:#a5b4fc;text-transform:uppercase;display:block;">Critical Bug</span>
                  <span style="font-size:18px;font-weight:900;color:#c7d2fe;">0 (Nol)</span>
                </div>
              </div>
            </div>
          </div>

          <!-- PORTAL GATEWAY KE MENU LAIN -->
          <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);display:flex;align-items:center;gap:8px;margin:0;">
                  <span style="color:var(--amber-500);display:flex;">${G.zap}</span>
                  <span>Portal Gateway Eksekutif (Akses Cepat Antar Menu ERP)</span>
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                  Pusat navigasi terpadu untuk membuka langsung data spesifik pada modul terkait dalam satu klik.
                </p>
              </div>
              <span style="font-size:11px;font-weight:700;color:var(--indigo-600);">
                6 Portal Terintegrasi
              </span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:16px;">
              <!-- 1. Pipeline Presales CRM -->
              <div
                class="prj-gateway-card"
                data-route="pipeline"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                      ${G.target}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:900;color:var(--indigo-600);background:rgba(99,102,241,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      ${r.length} Leads
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    Pipeline Presales & CRM
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Kelola kanban leads baru, requirement AI, kalkulator RAB presales, hingga status deal penawaran.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--indigo-600);">
                  <span>Buka Pipeline Kanban</span>
                  ${G.chevronRight}
                </div>
              </div>

              <!-- 2. Master Customer -->
              <div
                class="prj-gateway-card"
                data-route="customers"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(16,185,129,.1);color:var(--emerald-600);display:flex;align-items:center;justify-content:center;">
                      ${G.users}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:900;color:var(--emerald-600);background:rgba(16,185,129,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      ${n.length} Klien
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    Master Customer & PIC
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Direktori lengkap korporasi klien, PIC pengambil keputusan, skor kesehatan relasi, dan riwayat deal.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--emerald-600);">
                  <span>Buka Tabel Customer</span>
                  ${G.chevronRight}
                </div>
              </div>

              <!-- 3. Setting TOP & Invoice -->
              <div
                class="prj-gateway-card"
                data-route="finance_top"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(245,158,11,.1);color:var(--amber-600);display:flex;align-items:center;justify-content:center;">
                      ${G.dollarSign}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:11px;font-weight:900;color:var(--amber-600);background:rgba(245,158,11,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      ${a(f)}
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    Finansial TOP & Invoice
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Otomasi faktur penagihan termin DP, UAT, dan BAST pasca verifikasi pencapaian milestone delivery.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--amber-600);">
                  <span>Buka Billing & Invoice</span>
                  ${G.chevronRight}
                </div>
              </div>

              <!-- 4. Kontrak & Legalitas 22 MoU -->
              <div
                class="prj-gateway-card"
                data-route="contracts_legal"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                      ${G.shieldCheck}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:900;color:var(--indigo-600);background:rgba(99,102,241,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      22 Pasal Baku
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    Manajemen Kontrak & Legalitas
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Dokumen 4-Part resmi, repositori 22 klausul software house, addendum SPK, serta sertifikasi E-Sign UU ITE.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--indigo-600);">
                  <span>Buka Kontrak & MoU</span>
                  ${G.chevronRight}
                </div>
              </div>

              <!-- 5. WhatsApp Client Care -->
              <div
                class="prj-gateway-card"
                data-route="client_care"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(16,185,129,.1);color:var(--emerald-600);display:flex;align-items:center;justify-content:center;">
                      ${G.messageSquare}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:11px;font-weight:900;color:var(--emerald-600);background:rgba(16,185,129,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      ${p>0?`${p} Chat Masuk`:`Aktif`}
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    WhatsApp Client Care & Helpdesk
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Saluran komunikasi instan PIC klien terintegrasi AI auto-suggest dan pelaporan kendala teknis.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--emerald-600);">
                  <span>Buka WhatsApp Inbox</span>
                  ${G.chevronRight}
                </div>
              </div>

              <!-- 6. Laporan Eksekutif -->
              <div
                class="prj-gateway-card"
                data-route="reports"
                style="cursor:pointer;background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:16px;box-shadow:var(--shadow-xs);transition:all var(--transition);display:flex;flex-direction:column;justify-content:space-between;"
              >
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="width:34px;height:34px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                      ${G.barChart}
                    </div>
                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:900;color:var(--indigo-600);background:rgba(99,102,241,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                      Analitik C-Level
                    </span>
                  </div>
                  <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:10px 0 4px 0;">
                    Laporan & Analisis Kinerja
                  </h4>
                  <p style="font-size:11px;color:var(--text-muted);line-height:1.4;margin:0;">
                    Evaluasi rasio profitabilitas proyek, efisiensi resource developer, dan proyeksi kas software house.
                  </p>
                </div>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:700;color:var(--indigo-600);">
                  <span>Buka Laporan Eksekutif</span>
                  ${G.chevronRight}
                </div>
              </div>
            </div>
          </div>

          <!-- TOP STAT METRIC CARDS -->
          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;">
            <!-- Nilai Portofolio Delivery -->
            <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
                <span>Nilai Portofolio Delivery</span>
                <span style="color:var(--emerald-500);display:flex;">${G.dollarSign}</span>
              </div>
              <p style="font-size:20px;font-weight:900;font-family:var(--font-mono);color:var(--text-primary);margin:0;">
                ${a(o)}
              </p>
              <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--emerald-600);font-weight:600;">
                ${G.trendingUp}
                <span>Terikat SPK & Kontrak Sah</span>
              </div>
            </div>

            <!-- Mandays Burn Rate -->
            <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
                <span>Mandays Terpakai / Budget</span>
                <span style="color:var(--amber-500);display:flex;">${G.flame}</span>
              </div>
              <p style="font-size:20px;font-weight:900;font-family:var(--font-mono);color:var(--text-primary);margin:0;">
                ${c} / ${s} MD
              </p>
              <div style="width:100%;background:var(--bg-surface);height:6px;border-radius:var(--radius-full);overflow:hidden;">
                <div style="background:var(--amber-500);height:100%;border-radius:var(--radius-full);width:${Math.min(l,100)}%;"></div>
              </div>
              <p style="font-size:10px;color:var(--text-muted);font-family:var(--font-mono);margin:0;">
                Burn Rate: <strong>${l}%</strong> • Efisiensi Terkendali
              </p>
            </div>

            <!-- Rata-rata Progres Fisik -->
            <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
                <span>Rata-Rata Progres Fisik</span>
                <span style="color:var(--indigo-500);display:flex;">${G.activity}</span>
              </div>
              <p style="font-size:20px;font-weight:900;font-family:var(--font-mono);color:var(--indigo-600);margin:0;">
                ${u}%
              </p>
              <div style="width:100%;background:var(--bg-surface);height:6px;border-radius:var(--radius-full);overflow:hidden;">
                <div style="background:var(--indigo-600);height:100%;border-radius:var(--radius-full);width:${u}%;"></div>
              </div>
              <p style="font-size:10px;color:var(--text-muted);margin:0;">
                ${e.filter(e=>e.status===`Active`).length} Proyek Sedang Dalam Sprint Aktif
              </p>
            </div>

            <!-- Status Penagihan Termin -->
            <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
                <span>Realisasi Kas Termin (TOP)</span>
                <span style="color:var(--emerald-500);display:flex;">${G.checkCircle}</span>
              </div>
              <p style="font-size:20px;font-weight:900;font-family:var(--font-mono);color:var(--emerald-600);margin:0;">
                ${a(d)}
              </p>
              <p style="font-size:11px;color:var(--text-muted);margin:0;">
                Termin Terbayar: <strong>${t.filter(e=>e.status===`Paid`).length} Faktur</strong>
              </p>
            </div>
          </div>

          <!-- DETAIL BREAKDOWN PROYEK & KUALITAS DELIVERY -->
          <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
            <!-- Left: Monitoring Proyek Berjalan & Mandays -->
            <div style="display:flex;flex-direction:column;gap:16px;">
              <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
                  <div>
                    <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                      Status Eksekusi Proyek & Pemanfaatan Mandays
                    </h3>
                    <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                      Perbandingan kapasitas tim teknis terhadap tenggat waktu penyelesaian deliverable.
                    </p>
                  </div>
                  <button
                    class="prj-switch-tab-btn"
                    data-tab="projects"
                    style="font-size:12px;font-weight:700;color:var(--indigo-600);background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:4px;"
                  >
                    <span>Lihat Rincian</span>
                    ${G.chevronRight}
                  </button>
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;">
                  ${e.map(e=>{let t=e.budget_mandays>0?Math.round(e.actual_mandays/e.budget_mandays*100):0;return`
                      <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:12px;">
                        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;">
                          <div>
                            <div style="display:flex;align-items:center;gap:8px;">
                              <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--indigo-600);background:rgba(99,102,241,.1);padding:2px 8px;border-radius:var(--radius-sm);">
                                ${e.code}
                              </span>
                              <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:0;">
                                ${e.title}
                              </h4>
                            </div>
                            <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0 0;">
                              Klien: <strong style="color:var(--text-secondary);">${e.customer_name}</strong> • PM: ${e.pm_name}
                            </p>
                          </div>

                          <div style="text-align:right;">
                            <span style="font-family:var(--font-mono);font-size:12px;font-weight:900;color:var(--emerald-600);">
                              ${a(e.contract_value)}
                            </span>
                            <span style="display:block;font-size:10px;color:var(--text-muted);">
                              Target BAST: ${e.end_date||`-`}
                            </span>
                          </div>
                        </div>

                        <!-- Visual Progress & Mandays Bar -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:12px;padding-top:4px;">
                          <div style="display:flex;flex-direction:column;gap:4px;">
                            <div style="display:flex;justify-content:space-between;font-size:11px;">
                              <span style="color:var(--text-muted);font-weight:500;">Progress Fisik:</span>
                              <span style="font-weight:700;color:var(--text-primary);">${e.progress_pct}%</span>
                            </div>
                            <div style="width:100%;background:var(--border-light);height:8px;border-radius:var(--radius-full);overflow:hidden;">
                              <div style="background:linear-gradient(90deg, var(--indigo-500), var(--emerald-500));height:100%;border-radius:var(--radius-full);width:${e.progress_pct}%;"></div>
                            </div>
                          </div>

                          <div style="display:flex;flex-direction:column;gap:4px;">
                            <div style="display:flex;justify-content:space-between;font-size:11px;">
                              <span style="color:var(--text-muted);font-weight:500;">Burn Mandays:</span>
                              <span style="font-family:var(--font-mono);font-weight:700;color:var(--text-primary);">
                                ${e.actual_mandays} / ${e.budget_mandays} MD (${t}%)
                              </span>
                            </div>
                            <div style="width:100%;background:var(--border-light);height:8px;border-radius:var(--radius-full);overflow:hidden;">
                              <div style="height:100%;border-radius:var(--radius-full);width:${Math.min(t,100)}%;background:${t>90?`var(--rose-500)`:t>70?`var(--amber-500)`:`var(--indigo-500)`};"></div>
                            </div>
                          </div>
                        </div>

                        <!-- Milestones status pills -->
                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;padding-top:4px;">
                          <span style="font-size:10px;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Sprint:</span>
                          ${(e.milestones||[]).map(e=>`
                            <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:var(--radius-md);${e.status===`Completed`?`background:rgba(16,185,129,.1);color:var(--emerald-600);`:e.status===`In Progress`?`background:rgba(99,102,241,.1);color:var(--indigo-600);`:`background:var(--bg-canvas);color:var(--text-muted);`}">
                              ${e.name.split(`:`)[0]} (${e.status})
                            </span>
                          `).join(``)}
                        </div>
                      </div>
                    `}).join(``)}
                </div>
              </div>
            </div>

            <!-- Right: Upcoming Milestones & SLA Checklist -->
            <div style="display:flex;flex-direction:column;gap:16px;">
              <!-- Upcoming Milestones Feed -->
              <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
                  <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);display:flex;align-items:center;gap:8px;margin:0;">
                    <span style="color:var(--indigo-500);display:flex;">${G.calendar}</span>
                    <span>Jadwal Milestone Terdekat</span>
                  </h3>
                  <span style="font-size:10px;font-weight:700;color:var(--text-muted);">Timeline</span>
                </div>

                <div style="display:flex;flex-direction:column;gap:12px;">
                  ${_.slice(0,4).map(e=>`
                    <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:4px;font-size:12px;">
                      <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">
                          ${e.name}
                        </span>
                        <span style="padding:2px 6px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;${e.status===`Completed`?`background:rgba(16,185,129,.1);color:var(--emerald-600);`:e.status===`In Progress`?`background:rgba(99,102,241,.1);color:var(--indigo-600);`:`background:var(--bg-card);color:var(--text-muted);`}">
                          ${e.status}
                        </span>
                      </div>
                      <p style="font-size:11px;color:var(--text-muted);margin:0;">
                        ${e.projectCode} • ${e.customerName}
                      </p>
                      <div style="display:flex;align-items:center;justify-content:space-between;font-size:10px;color:var(--text-muted);padding-top:4px;border-top:1px solid var(--border-light);">
                        <span>Target: ${e.target_date}</span>
                        <span style="font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">${e.mandays_budget} Mandays</span>
                      </div>
                    </div>
                  `).join(``)}
                </div>
              </div>

              <!-- Delivery Assurance & SLA Checklist -->
              <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:12px;">
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);display:flex;align-items:center;gap:8px;margin:0;">
                  <span style="color:var(--emerald-500);display:flex;">${G.shieldCheck}</span>
                  <span>Kepatuhan Kualitas & SLA</span>
                </h3>

                <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;">
                  <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:var(--radius-lg);background:rgba(16,185,129,.08);color:var(--emerald-700);">
                    <span style="display:flex;align-items:center;gap:6px;font-weight:500;">
                      <span style="color:var(--emerald-600);display:flex;">${G.checkCircle}</span>
                      <span>Zero Critical Vulnerabilities (OWASP)</span>
                    </span>
                    <span style="font-weight:800;font-size:10px;">PASS</span>
                  </div>

                  <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:var(--radius-lg);background:rgba(16,185,129,.08);color:var(--emerald-700);">
                    <span style="display:flex;align-items:center;gap:6px;font-weight:500;">
                      <span style="color:var(--emerald-600);display:flex;">${G.checkCircle}</span>
                      <span>CI/CD Automated Test</span>
                    </span>
                    <span style="font-weight:800;font-size:10px;">PASS</span>
                  </div>

                  <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:var(--radius-lg);background:rgba(16,185,129,.08);color:var(--emerald-700);">
                    <span style="display:flex;align-items:center;gap:6px;font-weight:500;">
                      <span style="color:var(--emerald-600);display:flex;">${G.checkCircle}</span>
                      <span>UAT Document & BAST Sign-Off</span>
                    </span>
                    <span style="font-weight:800;font-size:10px;">READY</span>
                  </div>

                  <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);color:var(--indigo-700);">
                    <span style="display:flex;align-items:center;gap:6px;font-weight:500;">
                      <span style="color:var(--indigo-600);display:flex;">${G.clock}</span>
                      <span>Helpdesk SLA Uptime Target</span>
                    </span>
                    <span style="font-weight:800;font-size:10px;">99.8%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `:``}

      <!-- TAB 2: DAFTAR PROYEK & DETAIL EKSEKUSI -->
      ${W===`projects`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <!-- Filter and Search Bar -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:16px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
            <div style="position:relative;flex:1;max-width:440px;">
              <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
                ${G.search}
              </span>
              <input
                id="prj-search-input"
                type="text"
                placeholder="Cari judul proyek, PM, nama klien, kode PRJ..."
                value="${Jt}"
                style="width:100%;padding:8px 12px 8px 36px;border-radius:var(--radius-xl);font-size:12px;background:var(--bg-surface);border:1px solid transparent;color:var(--text-primary);outline:none;"
              />
            </div>

            <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
              <span style="color:var(--text-muted);font-weight:500;">Filter Status:</span>
              <select
                id="prj-status-filter"
                style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);color:var(--text-secondary);outline:none;font-weight:500;font-size:12px;cursor:pointer;"
              >
                <option value="ALL" ${Yt===`ALL`?`selected`:``}>Semua Status Proyek</option>
                <option value="Active" ${Yt===`Active`?`selected`:``}>Active</option>
                <option value="Completed" ${Yt===`Completed`?`selected`:``}>Completed</option>
                <option value="UAT" ${Yt===`UAT`?`selected`:``}>UAT</option>
                <option value="Planning" ${Yt===`Planning`?`selected`:``}>Planning</option>
              </select>
            </div>
          </div>

          <!-- Projects Cards List -->
          <div style="display:flex;flex-direction:column;gap:16px;">
            ${h.length===0?`
              <div style="background:var(--bg-card);padding:48px;text-align:center;color:var(--text-muted);border-radius:var(--radius-2xl);border:1px solid var(--border-light);">
                Tidak ada proyek yang sesuai dengan kriteria pencarian.
              </div>
            `:h.map(e=>`
                  <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:24px;display:flex;flex-direction:column;gap:16px;box-shadow:var(--shadow-xs);">
                    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px;">
                      <div>
                        <div style="display:flex;align-items:center;gap:8px;">
                          <span style="font-family:var(--font-mono);font-size:12px;font-weight:700;padding:2px 8px;border-radius:var(--radius-sm);background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
                            ${e.code}
                          </span>
                          <h3 style="font-weight:800;font-size:16px;color:var(--text-primary);margin:0;">
                            ${e.title}
                          </h3>
                          <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:800;${e.status===`Active`?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:`background:var(--bg-surface);color:var(--text-secondary);border:1px solid var(--border-light);`}">
                            ${e.status.toUpperCase()}
                          </span>
                        </div>
                        <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0 0;">
                          Klien: <strong style="color:var(--text-primary);">${e.customer_name}</strong> • Penanggung Jawab PM: <strong>${e.pm_name}</strong>
                        </p>
                      </div>

                      <div style="display:flex;align-items:center;gap:16px;font-size:12px;font-family:var(--font-mono);">
                        <div>
                          <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Nilai Kontrak</span>
                          <p style="font-weight:700;color:var(--emerald-600);margin:2px 0 0 0;">
                            ${a(e.contract_value)}
                          </p>
                        </div>
                        <div style="border-left:1px solid var(--border-light);padding-left:16px;">
                          <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Mandays Burn</span>
                          <p style="font-weight:700;color:var(--indigo-600);margin:2px 0 0 0;">
                            ${e.actual_mandays} / ${e.budget_mandays} MD
                          </p>
                        </div>
                        <div style="border-left:1px solid var(--border-light);padding-left:16px;">
                          <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Progres</span>
                          <p style="font-weight:700;color:var(--text-primary);margin:2px 0 0 0;">${e.progress_pct}%</p>
                        </div>
                      </div>
                    </div>

                    <!-- Progress Bar -->
                    <div style="width:100%;height:10px;border-radius:var(--radius-full);background:var(--bg-surface);overflow:hidden;">
                      <div style="height:100%;background:linear-gradient(90deg, var(--indigo-500), var(--emerald-500));border-radius:var(--radius-full);width:${e.progress_pct}%;"></div>
                    </div>

                    <!-- Milestones List -->
                    <div style="padding-top:4px;">
                      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <h4 style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin:0;">
                          Milestones & Deliverable Sprints (${(e.milestones||[]).length}):
                        </h4>
                        <button
                          class="prj-add-milestone-btn"
                          data-project-id="${e.id}"
                          style="font-size:11px;font-weight:700;color:var(--indigo-600);background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:4px;"
                        >
                          ${G.plus}
                          <span>Tambah Sprint Milestone</span>
                        </button>
                      </div>

                      <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:12px;font-size:12px;">
                        ${(e.milestones||[]).map(e=>`
                          <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:6px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                              <span style="font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                ${e.name}
                              </span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--text-muted);">
                              <span>Target: ${e.target_date}</span>
                              <span style="font-weight:600;${e.status===`Completed`?`color:var(--emerald-600);`:e.status===`In Progress`?`color:var(--indigo-600);`:`color:var(--text-muted);`}">
                                ${e.status}
                              </span>
                            </div>
                            <div style="font-size:10px;color:var(--text-muted);font-family:var(--font-mono);">
                              Alokasi: ${e.mandays_budget} Mandays
                            </div>
                          </div>
                        `).join(``)}
                      </div>
                    </div>

                    <!-- Actions footer -->
                    <div style="padding-top:12px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:12px;">
                      <div style="font-size:11px;color:var(--text-muted);">
                        Garansi: <strong>${e.warranty_period||`90 Hari`}</strong> • SLA: ${e.sla_level||`99.5%`}
                      </div>

                      <div style="display:flex;align-items:center;gap:8px;">
                        <button
                          class="prj-link-to-view"
                          data-route="finance_top"
                          style="padding:6px 12px;border-radius:var(--radius-lg);font-size:12px;font-weight:600;color:var(--text-secondary);background:var(--bg-surface);border:1px solid var(--border-light);cursor:pointer;display:flex;align-items:center;gap:4px;"
                        >
                          <span style="color:var(--amber-500);display:flex;">${G.dollarSign}</span>
                          <span>Lihat Tagihan TOP</span>
                        </button>
                        <button
                          class="prj-link-to-view"
                          data-route="contracts_legal"
                          style="padding:6px 12px;border-radius:var(--radius-lg);font-size:12px;font-weight:600;color:var(--indigo-600);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);cursor:pointer;display:flex;align-items:center;gap:4px;"
                        >
                          ${G.fileText}
                          <span>Buka SPK Kontrak</span>
                        </button>
                      </div>
                    </div>
                  </div>
                `).join(``)}

            <!-- Pagination Controls -->
            <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);overflow:hidden;box-shadow:var(--shadow-xs);">
              ${ze(g,[2,4,8],`projects-list-pagination`)}
            </div>
          </div>
        </div>
      `:``}

      <!-- TAB 3: RESOURCE & MANDAYS VELOCITY -->
      ${W===`mandays`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:24px;display:flex;flex-direction:column;gap:20px;box-shadow:var(--shadow-xs);">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border-light);padding-bottom:16px;">
              <div>
                <h3 style="font-size:16px;font-weight:800;color:var(--text-primary);margin:0;">
                  Analisis Efisiensi Mandays & Kapasitas Engineering
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                  Pemantauan konsumsi mandays aktual developer vs estimasi awal AI Requirement & RAB ASTACODE.
                </p>
              </div>

              <div style="font-family:var(--font-mono);font-size:12px;font-weight:700;padding:6px 12px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);color:var(--indigo-600);">
                Total Alokasi: ${s} Mandays
              </div>
            </div>

            <!-- Mandays Table -->
            <div style="border-radius:var(--radius-xl);border:1px solid var(--border-light);overflow:hidden;font-size:12px;">
              <table style="width:100%;text-align:left;border-collapse:collapse;">
                <thead style="background:var(--bg-surface);font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">
                  <tr>
                    <th style="padding:12px 14px;">Kode & Proyek</th>
                    <th style="padding:12px 14px;">Klien</th>
                    <th style="padding:12px 14px;text-align:right;">Budget MD</th>
                    <th style="padding:12px 14px;text-align:right;">Actual MD</th>
                    <th style="padding:12px 14px;text-align:right;">Sisa Mandays</th>
                    <th style="padding:12px 14px;text-align:right;">Burn Rate</th>
                    <th style="padding:12px 14px;text-align:center;">Status Anggaran</th>
                  </tr>
                </thead>
                <tbody style="border-top:1px solid var(--border-light);">
                  ${e.map(e=>{let t=e.budget_mandays-e.actual_mandays,n=e.budget_mandays>0?Math.round(e.actual_mandays/e.budget_mandays*100):0;return`
                      <tr style="border-bottom:1px solid var(--border-light);transition:background var(--transition);">
                        <td style="padding:12px 14px;">
                          <span style="font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">${e.code}</span>
                          <span style="display:block;font-weight:600;color:var(--text-primary);margin-top:2px;">${e.title}</span>
                        </td>
                        <td style="padding:12px 14px;color:var(--text-secondary);">${e.customer_name}</td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-weight:700;">${e.budget_mandays} MD</td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">${e.actual_mandays} MD</td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-weight:700;color:var(--emerald-600);">${t} MD</td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-weight:700;">${n}%</td>
                        <td style="padding:12px 14px;text-align:center;">
                          <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;${n<=80?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:n<=100?`background:rgba(245,158,11,.1);color:var(--amber-600);border:1px solid rgba(245,158,11,.2);`:`background:rgba(239,68,68,.1);color:var(--rose-600);border:1px solid rgba(239,68,68,.2);`}">
                            ${n<=80?`Optimal`:n<=100?`On Track`:`Overrun`}
                          </span>
                        </td>
                      </tr>
                    `}).join(``)}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      `:``}

      <!-- TAB 4: ROADMAP SPRINT & JADWAL BAST -->
      ${W===`timeline`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:24px;display:flex;flex-direction:column;gap:20px;box-shadow:var(--shadow-xs);">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border-light);padding-bottom:16px;">
              <div>
                <h3 style="font-size:16px;font-weight:800;color:var(--text-primary);margin:0;">
                  Roadmap Eksekusi Sprint & Rilis Deliverable ASTACODE
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                  Urutan deliverable per tanggal target penyerahan kode, pengujian UAT, dan BAST.
                </p>
              </div>

              <button
                id="prj-print-roadmap-btn"
                style="padding:8px 14px;border-radius:var(--radius-xl);font-size:12px;font-weight:700;color:#fff;background:var(--indigo-600);border:none;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:var(--shadow-xs);"
              >
                ${G.printer}
                <span>Cetak Roadmap</span>
              </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">
              ${_.map((e,t)=>`
                <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;font-size:12px;">
                  <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:28px;height:28px;border-radius:var(--radius-md);background:rgba(99,102,241,.1);color:var(--indigo-600);font-family:var(--font-mono);font-weight:700;display:flex;align-items:center;justify-content:center;font-size:12px;">
                      ${t+1}
                    </div>
                    <div>
                      <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:0;">${e.name}</h4>
                      <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">
                        ${e.projectTitle} • Klien: ${e.customerName}
                      </p>
                    </div>
                  </div>

                  <div style="display:flex;align-items:center;gap:16px;font-size:12px;font-family:var(--font-mono);">
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;display:block;">Target Selesai</span>
                      <span style="font-weight:700;color:var(--text-primary);">${e.target_date}</span>
                    </div>
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;display:block;">Alokasi</span>
                      <span style="font-weight:700;color:var(--indigo-600);">${e.mandays_budget} Mandays</span>
                    </div>
                    <span style="padding:4px 10px;border-radius:var(--radius-full);font-size:10px;font-weight:700;${e.status===`Completed`?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:e.status===`In Progress`?`background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);`:`background:var(--bg-card);color:var(--text-muted);`}">
                      ${e.status}
                    </span>
                  </div>
                </div>
              `).join(``)}
            </div>
          </div>
        </div>
      `:``}

      <!-- MODAL: INISIASI PROYEK BARU -->
      ${Qt?`
        <div id="new-prj-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);overflow-y:auto;">
          <div style="width:100%;max-width:520px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Inisiasi Proyek Baru (Proyek & Delivery)
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">ASTACODE Software House Project Management</p>
              </div>
              <button id="new-prj-modal-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${G.x}
              </button>
            </div>

            <form id="new-prj-form" style="display:flex;flex-direction:column;gap:14px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Pilih Klien:</label>
                <select
                  id="new-prj-cust-select"
                  style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-weight:600;"
                >
                  ${n.map(e=>`
                    <option value="${e.id}" ${e.id===$t?`selected`:``}>
                      ${e.legal_name} (${e.code})
                    </option>
                  `).join(``)}
                </select>
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Judul Proyek Sistem:</label>
                <input
                  id="new-prj-title-input"
                  type="text"
                  required
                  placeholder="Contoh: Modern ERP & Supply Chain Automation"
                  value="${en}"
                  style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                />
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Nilai Kontrak (IDR):</label>
                  <input
                    id="new-prj-contract-input"
                    type="number"
                    min="0"
                    step="1000000"
                    required
                    value="${nn}"
                    style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                  />
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Budget Mandays:</label>
                  <input
                    id="new-prj-mandays-input"
                    type="number"
                    min="1"
                    required
                    value="${tn}"
                    style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                  />
                </div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Garansi Bug Pasca BAST:</label>
                  <input
                    id="new-prj-warranty-input"
                    type="text"
                    value="${rn}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  />
                </div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Komitmen SLA:</label>
                  <input
                    id="new-prj-sla-input"
                    type="text"
                    value="${an}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  />
                </div>
              </div>

              <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="new-prj-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Simpan Proyek
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}

      <!-- MODAL: TAMBAH MILESTONE SPRINT -->
      ${sn&&v?`
        <div id="add-ms-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
          <div style="width:100%;max-width:440px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Tambah Sprint Milestone ke ${v.code}
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">${v.title}</p>
              </div>
              <button id="add-ms-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${G.x}
              </button>
            </div>

            <form id="add-ms-form" style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Nama Milestone / Sprint:</label>
                <input
                  id="add-ms-name-input"
                  type="text"
                  required
                  placeholder="Contoh: Sprint 4: Payment Gateway & UAT Sign-off"
                  value="${cn}"
                  style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                />
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Target Tanggal:</label>
                  <input
                    id="add-ms-target-date-input"
                    type="date"
                    required
                    value="${ln}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  />
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Budget Mandays:</label>
                  <input
                    id="add-ms-mandays-input"
                    type="number"
                    min="1"
                    required
                    value="${un}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                  />
                </div>
              </div>

              <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="add-ms-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Simpan Milestone
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}
    </div>
  `}function fn(e){e.querySelectorAll(`.prj-tab-btn[data-tab]`).forEach(t=>{t.addEventListener(`click`,()=>{W=t.dataset.tab,K(e)})}),e.querySelectorAll(`.prj-switch-tab-btn[data-tab]`).forEach(t=>{t.addEventListener(`click`,()=>{W=t.dataset.tab,K(e)})}),e.querySelectorAll(`.prj-gateway-card[data-route]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.route;t&&(t===`pipeline`&&b.setPipelineViewMode(`kanban`),b.setActiveView(t))})}),e.querySelectorAll(`.prj-link-to-view[data-route]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=e.dataset.route;t&&b.setActiveView(t)})});let t=e.querySelector(`#prj-search-input`);t&&t.addEventListener(`input`,t=>{Jt=t.target.value,Xt=1,K(e)});let n=e.querySelector(`#prj-status-filter`);n&&n.addEventListener(`change`,t=>{Yt=t.target.value,Xt=1,K(e)});let r=e.querySelector(`#prj-open-new-modal-btn`);r&&r.addEventListener(`click`,()=>{let{customers:t}=b.getState();!$t&&t.length>0&&($t=t[0].id),Qt=!0,K(e)});let i=e.querySelector(`#new-prj-modal-close-btn`),a=e.querySelector(`#new-prj-cancel-btn`);i&&i.addEventListener(`click`,()=>{Qt=!1,K(e)}),a&&a.addEventListener(`click`,()=>{Qt=!1,K(e)});let o=e.querySelector(`#new-prj-modal-backdrop`);o&&o.addEventListener(`click`,t=>{t.target===o&&(Qt=!1,K(e))});let s=e.querySelector(`#new-prj-cust-select`);s&&s.addEventListener(`change`,e=>$t=e.target.value);let c=e.querySelector(`#new-prj-title-input`);c&&c.addEventListener(`input`,e=>en=e.target.value);let l=e.querySelector(`#new-prj-contract-input`);l&&l.addEventListener(`input`,e=>nn=Number(e.target.value));let u=e.querySelector(`#new-prj-mandays-input`);u&&u.addEventListener(`input`,e=>tn=Number(e.target.value));let d=e.querySelector(`#new-prj-warranty-input`);d&&d.addEventListener(`input`,e=>rn=e.target.value);let f=e.querySelector(`#new-prj-sla-input`);f&&f.addEventListener(`input`,e=>an=e.target.value);let p=e.querySelector(`#new-prj-form`);p&&p.addEventListener(`submit`,t=>{t.preventDefault();let{customers:n}=b.getState(),r=n.find(e=>e.id===$t)||n[0],i=b.addNewProject({title:en||`Proyek Solusi Baru`,customer_id:r.id,customer_name:r.legal_name,contract_value:nn||1e8,budget_mandays:tn||90,actual_mandays:0,status:`Active`,pm_name:`Asisyah Sarah Azzahra`,warranty_period:rn,sla_level:an,progress_pct:0});Qt=!1,en=``,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Proyek baru "${i.code}: ${i.title}" berhasil diinisiasi ke sistem ASTACODE!`,type:`success`}})),K(e)}),e.querySelectorAll(`.prj-add-milestone-btn[data-project-id]`).forEach(t=>{t.addEventListener(`click`,()=>{on=t.dataset.projectId,sn=!0,K(e)})});let m=e.querySelector(`#add-ms-close-btn`),h=e.querySelector(`#add-ms-cancel-btn`);m&&m.addEventListener(`click`,()=>{sn=!1,on=null,K(e)}),h&&h.addEventListener(`click`,()=>{sn=!1,on=null,K(e)});let g=e.querySelector(`#add-ms-modal-backdrop`);g&&g.addEventListener(`click`,t=>{t.target===g&&(sn=!1,on=null,K(e))});let _=e.querySelector(`#add-ms-name-input`);_&&_.addEventListener(`input`,e=>cn=e.target.value);let v=e.querySelector(`#add-ms-target-date-input`);v&&v.addEventListener(`input`,e=>ln=e.target.value);let y=e.querySelector(`#add-ms-mandays-input`);y&&y.addEventListener(`input`,e=>un=Number(e.target.value));let ee=e.querySelector(`#add-ms-form`);ee&&ee.addEventListener(`submit`,t=>{t.preventDefault();let{projects:n}=b.getState(),r=n.find(e=>e.id===on);if(!r||!cn.trim())return;let i={id:`ms-${Date.now()}`,name:cn.trim(),target_date:ln,status:`Pending`,mandays_budget:un,mandays_actual:0};r.milestones||=[],r.milestones.push(i),sn=!1,cn=``,on=null,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Milestone "${i.name}" berhasil ditambahkan ke ${r.code}!`,type:`success`}})),K(e)});let te=e.querySelector(`#prj-print-roadmap-btn`);te&&te.addEventListener(`click`,()=>{window.print()}),Be(e,t=>{Xt=t,K(e)},t=>{Zt=t,Xt=1,K(e)},`projects-list-pagination`)}function K(e){e.innerHTML=dn(),fn(e)}function pn(e){return K(e),b.subscribe(()=>{b.getState().activeView===`projects`&&K(e)})}var mn=t({bindSettingTopInvoiceEvents:()=>xn,mount:()=>Cn,renderSettingTopInvoicePage:()=>bn}),hn=`top_setting`,gn=``,_n=1,vn=5,yn={lock:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,fileCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/></svg>`};function bn(){let{projects:e,invoices:t}=b.getState();!gn&&e.length>0&&(gn=e[0].id);let n=e.find(e=>e.id===gn)||e[0]||null,{data:r,pagination:i}=Le(t,_n,vn);return`
    <div style="flex:1;display:flex;flex-direction:column;height:100%;overflow-y:auto;background:var(--bg-canvas);padding:24px;gap:24px;">
      
      <!-- Header -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:20px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
        <div>
          <div style="display:flex;align-items:center;gap:8px;">
            <h1 style="font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;margin:0;">
              Modul Kontrak, Invoicing & Setting TOP (Termin Pembayaran)
            </h1>
            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
              PRD Section 8.11.1
            </span>
          </div>
          <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0 0;">
            Setting TOP diwariskan langsung dari Quotation yang disetujui (Deal) dan mengendalikan jadwal penerbitan invoice per termin.
          </p>
        </div>

        <!-- View Switcher -->
        <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:12px;font-weight:600;">
          <button
            class="top-tab-btn ${hn===`top_setting`?`active`:``}"
            data-tab="top_setting"
            style="padding:6px 14px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${hn===`top_setting`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
          >
            Setting TOP per Kontrak
          </button>
          <button
            class="top-tab-btn ${hn===`invoices`?`active`:``}"
            data-tab="invoices"
            style="padding:6px 14px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${hn===`invoices`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
          >
            Daftar Invoice Terbit (${t.length})
          </button>
        </div>
      </div>

      <!-- TAB 1: SETTING TOP PER KONTRAK -->
      ${hn===`top_setting`&&n?`
        <div style="display:flex;flex-direction:column;gap:24px;">
          <!-- Project Selector Card -->
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
              <div>
                <span style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;display:block;">
                  Pilih Kontrak / Proyek Aktif:
                </span>
                <select
                  id="top-project-select"
                  style="display:block;margin-top:6px;padding:8px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);font-size:12px;font-weight:700;color:var(--text-primary);outline:none;cursor:pointer;"
                >
                  ${e.map(e=>`
                    <option value="${e.id}" ${e.id===n.id?`selected`:``}>
                      ${e.contract_number} • ${e.title} (${e.customer_name})
                    </option>
                  `).join(``)}
                </select>
              </div>

              <div style="display:flex;align-items:center;gap:24px;font-size:12px;">
                <div>
                  <span style="color:var(--text-muted);font-size:11px;display:block;">Nilai Kontrak Total:</span>
                  <p style="font-family:var(--font-mono);font-weight:800;font-size:15px;color:var(--indigo-600);margin:2px 0 0 0;">
                    ${a(n.contract_value)}
                  </p>
                </div>
                <div style="border-left:1px solid var(--border-light);padding-left:16px;">
                  <span style="color:var(--text-muted);font-size:11px;display:block;">Skema Termin TOP:</span>
                  <p style="font-weight:700;color:var(--text-primary);margin:2px 0 0 0;">
                    ${n.top_scheme.preset_name} (${(n.top_scheme.items||[]).length} Termin)
                  </p>
                </div>
                <div style="border-left:1px solid var(--border-light);padding-left:16px;">
                  <span style="color:var(--text-muted);font-size:11px;display:block;">Status Kontrak:</span>
                  <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);display:inline-block;margin-top:2px;">
                    AKTIF BERJALAN
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Setting TOP Schedule Table -->
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Jadwal Termin Pembayaran Kontrak ${n.contract_number}
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                  🔒 Termin yang sudah diterbitkan invoice terkunci demi kepatuhan akuntansi (PRD BR-19).
                </p>
              </div>
            </div>

            <div style="overflow-x:auto;">
              <table style="width:100%;text-align:left;border-collapse:collapse;font-size:12px;">
                <thead>
                  <tr style="background:var(--bg-surface);border-bottom:1px solid var(--border-light);color:var(--text-muted);font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">
                    <th style="padding:12px 14px;width:48px;text-align:center;">No</th>
                    <th style="padding:12px 14px;">Nama Termin</th>
                    <th style="padding:12px 14px;width:90px;">Porsi (%)</th>
                    <th style="padding:12px 14px;width:150px;">Nominal (IDR)</th>
                    <th style="padding:12px 14px;">Pemicu Tagihan (Trigger)</th>
                    <th style="padding:12px 14px;width:120px;">Status Termin</th>
                    <th style="padding:12px 14px;text-align:right;">Aksi Finance</th>
                  </tr>
                </thead>
                <tbody style="border-top:1px solid var(--border-light);">
                  ${(n.top_scheme.items||[]).map((e,t)=>{let r=e.status===`Invoiced`||e.is_locked||t===0;return`
                      <tr style="border-bottom:1px solid var(--border-light);transition:background var(--transition);">
                        <td style="padding:12px 14px;text-align:center;font-family:var(--font-mono);font-weight:700;color:var(--text-muted);">
                          ${t+1}
                        </td>
                        <td style="padding:12px 14px;">
                          <div style="font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
                            <span>${e.name}</span>
                            ${r?`
                              <span title="Terkunci: Invoice sudah terbit" style="color:var(--text-muted);display:flex;">
                                ${yn.lock}
                              </span>
                            `:``}
                          </div>
                          <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">${e.description||``}</div>
                        </td>
                        <td style="padding:12px 14px;font-family:var(--font-mono);font-weight:700;">${e.value}%</td>
                        <td style="padding:12px 14px;font-family:var(--font-mono);font-weight:800;color:var(--emerald-600);">
                          ${a(e.calculated_amount)}
                        </td>
                        <td style="padding:12px 14px;color:var(--text-secondary);">
                          <div>${e.trigger_label||`-`}</div>
                          <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">${e.due_days_label||`-`}</span>
                        </td>
                        <td style="padding:12px 14px;">
                          ${t===0?`
                            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:800;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);">
                              ✔ LUNAS (PAID)
                            </span>
                          `:r?`
                            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:800;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
                              🔒 INVOICED
                            </span>
                          `:`
                            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(245,158,11,.1);color:var(--amber-600);border:1px solid rgba(245,158,11,.2);">
                              PLANNED
                            </span>
                          `}
                        </td>
                        <td style="padding:12px 14px;text-align:right;">
                          ${t===0?`
                            <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">INV/2026/10/0001</span>
                          `:r?`
                            <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">Invoice Terbit</span>
                          `:`
                            <button
                              class="top-issue-invoice-btn"
                              data-contract-no="${n.contract_number}"
                              data-term-no="${e.term_no}"
                              style="padding:6px 14px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-700));color:#fff;font-weight:700;font-size:11px;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(99,102,241,.25);"
                            >
                              Terbitkan Invoice
                            </button>
                          `}
                        </td>
                      </tr>
                    `}).join(``)}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      `:``}

      <!-- TAB 2: DAFTAR INVOICE TERBIT -->
      ${hn===`invoices`?`
        <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
            <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
              Daftar Rekapitulasi Invoice Termin Terbit
            </h3>
            <span style="font-size:12px;color:var(--text-muted);">Total ${t.length} Faktur Tagihan</span>
          </div>

          <div style="overflow-x:auto;">
            <table style="width:100%;text-align:left;border-collapse:collapse;font-size:12px;">
              <thead>
                <tr style="background:var(--bg-surface);border-bottom:1px solid var(--border-light);color:var(--text-muted);font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:0.05em;">
                  <th style="padding:12px 14px;">No. Invoice</th>
                  <th style="padding:12px 14px;">Kontrak & Proyek</th>
                  <th style="padding:12px 14px;">Customer</th>
                  <th style="padding:12px 14px;">Termin</th>
                  <th style="padding:12px 14px;">Grand Total (Rp)</th>
                  <th style="padding:12px 14px;">Jatuh Tempo</th>
                  <th style="padding:12px 14px;">Status</th>
                </tr>
              </thead>
              <tbody style="border-top:1px solid var(--border-light);">
                ${r.length===0?`
                  <tr>
                    <td colspan="7" style="padding:32px;text-align:center;color:var(--text-muted);">
                      Belum ada invoice yang diterbitkan.
                    </td>
                  </tr>
                `:r.map(e=>`
                    <tr style="border-bottom:1px solid var(--border-light);transition:background var(--transition);">
                      <td style="padding:12px 14px;font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">
                        ${e.invoice_number}
                      </td>
                      <td style="padding:12px 14px;">
                        <div style="font-weight:700;color:var(--text-primary);">${e.project_title}</div>
                        <div style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);margin-top:2px;">${e.contract_number}</div>
                      </td>
                      <td style="padding:12px 14px;font-weight:500;color:var(--text-secondary);">
                        ${e.customer_name}
                      </td>
                      <td style="padding:12px 14px;font-weight:600;color:var(--text-primary);">
                        ${e.termin_name}
                      </td>
                      <td style="padding:12px 14px;font-family:var(--font-mono);font-weight:800;color:var(--emerald-600);">
                        ${a(e.grand_total)}
                      </td>
                      <td style="padding:12px 14px;font-family:var(--font-mono);color:var(--text-muted);">${e.due_date}</td>
                      <td style="padding:12px 14px;">
                        <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:800;${e.status===`Paid`?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:`background:rgba(245,158,11,.1);color:var(--amber-600);border:1px solid rgba(245,158,11,.2);`}">
                          ${e.status.toUpperCase()}
                        </span>
                      </td>
                    </tr>
                  `).join(``)}
              </tbody>
            </table>
          </div>

          <div style="border-top:1px solid var(--border-light);">
            ${ze(i,[5,10,20],`top-invoices-pagination`)}
          </div>
        </div>
      `:``}
    </div>
  `}function xn(e){e.querySelectorAll(`.top-tab-btn[data-tab]`).forEach(t=>{t.addEventListener(`click`,()=>{hn=t.dataset.tab,Sn(e)})});let t=e.querySelector(`#top-project-select`);t&&t.addEventListener(`change`,t=>{gn=t.target.value,Sn(e)}),e.querySelectorAll(`.top-issue-invoice-btn[data-contract-no]`).forEach(t=>{t.addEventListener(`click`,()=>{let n=t.dataset.contractNo,r=Number(t.dataset.termNo);b.issueInvoiceFromTerm(n,r),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Invoice untuk Termin ${r} Kontrak ${n} berhasil diterbitkan!`,type:`success`}})),Sn(e)})}),Be(e,t=>{_n=t,Sn(e)},t=>{vn=t,_n=1,Sn(e)},`top-invoices-pagination`)}function Sn(e){e.innerHTML=bn(),xn(e)}function Cn(e){return Sn(e),b.subscribe(()=>{b.getState().activeView===`finance_top`&&Sn(e)})}var wn=t({bindContractsLegalEvents:()=>rr,mount:()=>ir,renderContractsLegalPage:()=>nr}),q=`contracts`,Tn=``,En=`all`,J=[...u],Dn=`all`,On=null,kn=!1,An=``,jn=``,Mn=1,Nn=4,Pn=1,Fn=6,In=null,Ln=null,Rn=null,zn=``,Bn=35e6,Vn=!1,Hn=``,Un=``,Wn=2442e5,Gn=152,Kn=`30/40/30`,qn=``,Jn=`90 Hari`,Yn=``,Xn=`99.5% Uptime SLA`,Zn=``,Qn=new Date().toISOString().split(`T`)[0],$n=new Date(Date.now()+7776e6).toISOString().split(`T`)[0],Y=`all`,er=``,tr=!1,X={shieldCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>`,search:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,dollarSign:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,clock:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,fileCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/></svg>`,building:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>`,eye:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`,fileText:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>`,penTool:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>`,copy:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>`,edit:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>`,printer:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>`,rotateCcw:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>`,checkCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`,x:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,check:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,lock:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,award:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>`};function nr(){let{projects:e,customers:t}=b.getState();!er&&e.length>0&&(er=e[0].id),!Hn&&t.length>0&&(Hn=t[0].id);let n=e.find(e=>e.id===er)||e[0]||{},r=e.find(e=>e.id===In)||null,o=e.find(e=>e.id===Ln)||null,s=e.find(e=>e.id===Rn)||null,c=J.find(e=>e.id===On)||null,l=e.reduce((e,t)=>e+t.contract_value,0),u=e.filter(e=>e.status===`Active`).length,d=e.filter(e=>e.status===`Completed`).length,f=Tn.toLowerCase().trim(),{data:p,pagination:m}=Le(e.filter(e=>{let t=f===``||e.title.toLowerCase().includes(f)||e.contract_number.toLowerCase().includes(f)||e.customer_name.toLowerCase().includes(f),n=En===`all`||e.status===En;return t&&n}),Mn,Nn),h=[`all`,...Array.from(new Set(J.map(e=>e.category||`Umum`)))],{data:g,pagination:_}=Le(J.filter(e=>{let t=f===``||e.title.toLowerCase().includes(f)||e.body.toLowerCase().includes(f)||(e.category||``).toLowerCase().includes(f),n=Dn===`all`||e.category===Dn;return t&&n}),Pn,Fn);return`
    <div style="flex:1;display:flex;flex-direction:column;height:100%;overflow-y:auto;background:var(--bg-canvas);padding:24px;gap:24px;">
      
      <!-- Header & Tab Switcher -->
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;background:var(--bg-card);padding:20px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-500));display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:var(--shadow-xs);">
            ${X.shieldCheck}
          </div>
          <div>
            <h1 style="font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.01em;margin:0;">
              ASTACODE Legal & Manajemen Kontrak (22 Pasal MoU)
            </h1>
            <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
              Pengelolaan kontrak proyek software house, repositori klausul baku 22 pasal, generator berkas 4-bagian, dan pengesahan e-Signature UU ITE.
            </p>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
          <!-- Tabs Navigation -->
          <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:12px;font-weight:600;">
            <button
              class="legal-tab-btn ${q===`contracts`?`active`:``}"
              data-tab="contracts"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${q===`contracts`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Kontrak Aktif (${e.length})
            </button>
            <button
              class="legal-tab-btn ${q===`clauses`?`active`:``}"
              data-tab="clauses"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${q===`clauses`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Library 22 Pasal MoU
            </button>
            <button
              class="legal-tab-btn ${q===`mou_preview`?`active`:``}"
              data-tab="mou_preview"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${q===`mou_preview`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Dokumen 4-Part MoU/SPK
            </button>
            <button
              class="legal-tab-btn ${q===`esign_audit`?`active`:``}"
              data-tab="esign_audit"
              style="padding:6px 12px;border-radius:var(--radius-lg);border:none;cursor:pointer;transition:all var(--transition);${q===`esign_audit`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Audit E-Sign UU ITE
            </button>
          </div>

          <button
            id="legal-open-new-contract-btn"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-xl);background:linear-gradient(135deg, var(--indigo-600), var(--indigo-700));color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.25);transition:all var(--transition);"
          >
            ${X.plus}
            <span>+ Buat SPK / Kontrak</span>
          </button>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;">
        <div style="padding:16px 20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:4px;">
          <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
            <span>Total Nilai Kontrak</span>
            <span style="color:var(--indigo-500);display:flex;">${X.dollarSign}</span>
          </div>
          <p style="font-size:18px;font-weight:900;font-family:var(--font-mono);color:var(--text-primary);margin:0;">
            ${a(l)}
          </p>
          <p style="font-size:10px;color:var(--text-muted);margin:0;">Total portofolio perjanjian aktif</p>
        </div>

        <div style="padding:16px 20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:4px;">
          <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
            <span>Kontrak Berjalan</span>
            <span style="color:var(--amber-500);display:flex;">${X.clock}</span>
          </div>
          <p style="font-size:18px;font-weight:900;font-family:var(--font-mono);color:var(--amber-600);margin:0;">
            ${u} Proyek
          </p>
          <p style="font-size:10px;color:var(--text-muted);margin:0;">Termin & deliverable sedang berjalan</p>
        </div>

        <div style="padding:16px 20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:4px;">
          <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
            <span>Kontrak Selesai / BAST</span>
            <span style="color:var(--emerald-500);display:flex;">${X.fileCheck}</span>
          </div>
          <p style="font-size:18px;font-weight:900;font-family:var(--font-mono);color:var(--emerald-600);margin:0;">
            ${d} Selesai
          </p>
          <p style="font-size:10px;color:var(--text-muted);margin:0;">BAST disetujui 100%</p>
        </div>

        <div style="padding:16px 20px;border-radius:var(--radius-2xl);background:var(--bg-card);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:4px;">
          <div style="display:flex;align-items:center;justify-content:space-between;color:var(--text-muted);font-size:12px;">
            <span>Standar Klausul MoU</span>
            <span style="color:var(--indigo-500);display:flex;">${X.shieldCheck}</span>
          </div>
          <p style="font-size:18px;font-weight:900;font-family:var(--font-mono);color:var(--indigo-600);margin:0;">
            ${J.length} Pasal Terdaftar
          </p>
          <p style="font-size:10px;color:var(--emerald-600);font-weight:600;margin:0;">100% Terverifikasi UU ITE & PDP</p>
        </div>
      </div>

      <!-- TAB 1: DAFTAR KONTRAK & SPK AKTIF -->
      ${q===`contracts`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <!-- Filter Bar -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:16px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="position:relative;width:280px;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
                  ${X.search}
                </span>
                <input
                  id="legal-search-contract-input"
                  type="text"
                  placeholder="Cari nomor SPK, judul kontrak, atau klien..."
                  value="${Tn}"
                  style="width:100%;padding:8px 12px 8px 36px;border-radius:var(--radius-xl);font-size:12px;background:var(--bg-surface);border:1px solid transparent;color:var(--text-primary);outline:none;"
                />
              </div>

              <!-- Status Filter Buttons -->
              <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:11px;">
                ${[`all`,`Active`,`Completed`,`UAT`].map(e=>`
                  <button
                    class="legal-status-filter-btn ${En===e?`active`:``}"
                    data-status="${e}"
                    style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${En===e?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
                  >
                    ${e===`all`?`Semua Status`:e}
                  </button>
                `).join(``)}
              </div>
            </div>

            <div style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:6px;font-weight:500;">
              <span style="color:var(--indigo-600);display:flex;">${X.shieldCheck}</span>
              <span>Penanggung Jawab Legal: <strong>Asisyah Sarah Azzahra (PM ASTACODE)</strong></span>
            </div>
          </div>

          <!-- Contracts Grid -->
          <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(400px, 1fr));gap:16px;">
            ${p.map(e=>`
              <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;display:flex;flex-direction:column;justify-content:space-between;gap:16px;box-shadow:var(--shadow-xs);">
                <div>
                  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                    <div>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:var(--indigo-600);padding:2px 8px;border-radius:var(--radius-sm);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);">
                          ${e.contract_number}
                        </span>
                        <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:800;${e.status===`Active`?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:`background:var(--bg-surface);color:var(--text-secondary);border:1px solid var(--border-light);`}">
                          ${e.status.toUpperCase()}
                        </span>
                      </div>
                      <h3 style="font-weight:800;font-size:14px;color:var(--text-primary);margin:8px 0 2px 0;">
                        ${e.title}
                      </h3>
                      <p style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;margin:0;">
                        <span style="display:flex;">${X.building}</span>
                        <span>${e.customer_name}</span>
                      </p>
                    </div>

                    <div style="text-align:right;">
                      <span style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;display:block;">Nilai Kontrak</span>
                      <p style="font-family:var(--font-mono);font-weight:900;font-size:14px;color:var(--emerald-600);margin:2px 0 0 0;">
                        ${a(e.contract_value)}
                      </p>
                    </div>
                  </div>

                  <!-- Contract Matrix -->
                  <div style="margin-top:14px;padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:11px;">
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Garansi Bug:</span>
                      <span style="font-weight:600;color:var(--text-primary);">${e.warranty_period||`90 Hari Pasca BAST`}</span>
                    </div>
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">SLA Komitmen:</span>
                      <span style="font-weight:600;color:var(--text-primary);">${e.sla_level||`99.5% Uptime SLA`}</span>
                    </div>
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Masa Berlaku:</span>
                      <span style="font-family:var(--font-mono);color:var(--text-secondary);">${e.start_date} s/d ${e.end_date}</span>
                    </div>
                    <div>
                      <span style="color:var(--text-muted);font-size:10px;text-transform:uppercase;font-weight:700;display:block;">Keabsahan:</span>
                      <span style="color:var(--emerald-600);font-weight:700;display:flex;align-items:center;gap:4px;">
                        ${X.shieldCheck}
                        <span>${e.esign_status||`Terverifikasi Sah UU ITE`}</span>
                      </span>
                    </div>
                  </div>

                  <!-- Termin TOP Bar -->
                  <div style="display:flex;flex-direction:column;gap:6px;padding-top:12px;font-size:11px;">
                    <div style="display:flex;justify-content:space-between;color:var(--text-muted);">
                      <span>Skema Termin TOP: <strong>${e.top_scheme.preset_name}</strong></span>
                      <span>Progress Fisik: ${e.progress_pct}%</span>
                    </div>
                    <div style="display:flex;gap:6px;">
                      ${(e.top_scheme.items||[]).map((e,t)=>`
                        <div style="flex:1;padding:6px;border-radius:var(--radius-md);text-align:center;font-family:var(--font-mono);font-size:10px;font-weight:700;${e.status===`Paid`?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.3);`:e.status===`Invoiced`?`background:rgba(245,158,11,.1);color:var(--amber-600);border:1px solid rgba(245,158,11,.3);`:`background:var(--bg-surface);color:var(--text-muted);border:1px solid var(--border-light);`}">
                          <div>T${t+1}: ${e.value}%</div>
                          <div style="font-weight:500;font-size:9px;">${e.status}</div>
                        </div>
                      `).join(``)}
                    </div>
                  </div>
                </div>

                <!-- Footer Buttons -->
                <div style="padding-top:12px;border-top:1px solid var(--border-light);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;">
                  <div style="font-size:11px;color:var(--text-muted);">
                    PM: <strong>${e.pm_name}</strong>
                  </div>

                  <div style="display:flex;align-items:center;gap:6px;">
                    <button
                      class="legal-detail-btn"
                      data-project-id="${e.id}"
                      style="padding:6px 10px;border-radius:var(--radius-lg);font-size:11px;font-weight:700;color:var(--text-secondary);background:var(--bg-surface);border:1px solid var(--border-light);cursor:pointer;display:flex;align-items:center;gap:4px;"
                    >
                      ${X.eye}
                      <span>Detail</span>
                    </button>
                    <button
                      class="legal-open-doc-btn"
                      data-project-id="${e.id}"
                      style="padding:6px 10px;border-radius:var(--radius-lg);font-size:11px;font-weight:700;color:var(--indigo-600);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);cursor:pointer;display:flex;align-items:center;gap:4px;"
                    >
                      ${X.fileText}
                      <span>4-Part</span>
                    </button>
                    <button
                      class="legal-esign-btn"
                      data-project-id="${e.id}"
                      style="padding:6px 10px;border-radius:var(--radius-lg);font-size:11px;font-weight:700;color:var(--emerald-600);background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);cursor:pointer;display:flex;align-items:center;gap:4px;"
                    >
                      ${X.penTool}
                      <span>E-Sign</span>
                    </button>
                    <button
                      class="legal-addendum-btn"
                      data-project-id="${e.id}"
                      style="padding:6px 8px;border-radius:var(--radius-lg);font-size:11px;font-weight:600;color:var(--text-muted);background:none;border:none;cursor:pointer;"
                    >
                      + Addendum
                    </button>
                  </div>
                </div>
              </div>
            `).join(``)}
          </div>

          <!-- Pagination -->
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);overflow:hidden;box-shadow:var(--shadow-xs);">
            ${ze(m,[2,4,8],`legal-contracts-pagination`)}
          </div>
        </div>
      `:``}

      <!-- TAB 2: LIBRARY 22 PASAL MOU SOFTWARE HOUSE -->
      ${q===`clauses`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <!-- Controls Bar -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:16px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="position:relative;width:280px;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);display:flex;align-items:center;">
                  ${X.search}
                </span>
                <input
                  id="legal-search-clause-input"
                  type="text"
                  placeholder="Cari pasal, kata kunci HAKI, SLA, garansi..."
                  value="${Tn}"
                  style="width:100%;padding:8px 12px 8px 36px;border-radius:var(--radius-xl);font-size:12px;background:var(--bg-surface);border:1px solid transparent;color:var(--text-primary);outline:none;"
                />
              </div>

              <span style="font-size:12px;color:var(--text-muted);font-family:var(--font-mono);">
                Total: <strong>${J.length} Pasal</strong>
              </span>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
              <button
                id="legal-reset-clauses-btn"
                style="padding:6px 12px;border-radius:var(--radius-xl);font-size:12px;font-weight:600;color:var(--text-secondary);background:var(--bg-surface);border:1px solid var(--border-light);cursor:pointer;display:flex;align-items:center;gap:6px;"
              >
                ${X.rotateCcw}
                <span>Reset Baku</span>
              </button>
              <button
                id="legal-export-clauses-btn"
                style="padding:6px 12px;border-radius:var(--radius-xl);font-size:12px;font-weight:700;color:var(--indigo-600);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);cursor:pointer;display:flex;align-items:center;gap:6px;"
              >
                ${X.printer}
                <span>Export 22 Pasal</span>
              </button>
              <button
                id="legal-add-clause-btn"
                style="padding:6px 14px;border-radius:var(--radius-xl);font-size:12px;font-weight:700;color:#fff;background:var(--indigo-600);border:none;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(99,102,241,.25);"
              >
                ${X.plus}
                <span>+ Tambah Pasal</span>
              </button>
            </div>
          </div>

          <!-- Category Filter Pills -->
          <div style="display:flex;align-items:center;gap:6px;overflow-x:auto;padding-bottom:4px;font-size:11px;">
            ${h.map(e=>`
              <button
                class="legal-cat-btn ${Dn===e?`active`:``}"
                data-cat="${e}"
                style="padding:6px 12px;border-radius:var(--radius-xl);white-space:nowrap;border:none;cursor:pointer;transition:all var(--transition);${Dn===e?`background:var(--indigo-600);color:#fff;font-weight:700;`:`background:var(--bg-card);color:var(--text-secondary);border:1px solid var(--border-light);`}"
              >
                ${e===`all`?`Semua Pasal (${J.length})`:e}
              </button>
            `).join(``)}
          </div>

          <!-- Clauses Grid -->
          <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(400px, 1fr));gap:16px;">
            ${g.map(e=>`
              <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:20px;display:flex;flex-direction:column;justify-content:space-between;gap:12px;box-shadow:var(--shadow-xs);">
                <div>
                  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;border-bottom:1px solid var(--border-light);padding-bottom:10px;">
                    <div>
                      <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(99,102,241,.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,.2);">
                        ${e.category||`Umum`}
                      </span>
                      <h4 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:6px 0 0 0;">
                        ${e.title}
                      </h4>
                    </div>

                    <div style="display:flex;align-items:center;gap:4px;">
                      <button
                        class="clause-copy-btn"
                        data-clause-id="${e.id}"
                        style="padding:6px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;"
                        title="Salin isi pasal ke clipboard"
                      >
                        ${X.copy}
                      </button>
                      <button
                        class="clause-edit-btn"
                        data-clause-id="${e.id}"
                        style="padding:6px;border-radius:var(--radius-md);color:var(--indigo-600);background:none;border:none;cursor:pointer;"
                        title="Edit redaksional pasal"
                      >
                        ${X.edit}
                      </button>
                    </div>
                  </div>

                  <p style="font-size:12px;color:var(--text-secondary);line-height:1.6;margin:12px 0 0 0;text-align:justify;">
                    ${e.body}
                  </p>
                </div>

                <div style="padding-top:10px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;">
                  <button
                    class="clause-toggle-mandatory-btn"
                    data-clause-id="${e.id}"
                    style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;border:none;cursor:pointer;${e.is_mandatory===!1?`background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);`:`background:rgba(239,68,68,.1);color:var(--rose-600);border:1px solid rgba(239,68,68,.2);`}"
                  >
                    ${e.is_mandatory===!1?`○ Dapat Dinegosiasikan`:`● Klausul Wajib`}
                  </button>

                  <span style="color:var(--emerald-600);font-weight:600;display:flex;align-items:center;gap:4px;">
                    ${X.checkCircle}
                    <span>Terverifikasi Legal ASTACODE</span>
                  </span>
                </div>
              </div>
            `).join(``)}
          </div>

          <!-- Pagination -->
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);overflow:hidden;box-shadow:var(--shadow-xs);">
            ${ze(_,[4,6,12,22],`legal-clauses-pagination`)}
          </div>
        </div>
      `:``}

      <!-- TAB 3: GENERATOR DOKUMEN 4-PART MOU & SPK -->
      ${q===`mou_preview`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <!-- Controls Bar -->
          <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;background:var(--bg-card);padding:16px;border-radius:var(--radius-2xl);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:12px;font-weight:700;color:var(--text-secondary);">Pilih Kontrak / Proyek:</span>
              <select
                id="doc-project-select"
                style="padding:8px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);font-size:12px;font-weight:600;color:var(--text-primary);outline:none;cursor:pointer;"
              >
                ${e.map(e=>`
                  <option value="${e.id}" ${e.id===n.id?`selected`:``}>
                    ${e.contract_number} - ${e.customer_name} (${e.title.slice(0,35)}...)
                  </option>
                `).join(``)}
              </select>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
              <!-- Part filter buttons -->
              <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:11px;font-weight:600;">
                <button class="doc-part-btn ${Y===`all`?`active`:``}" data-part="all" style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${Y===`all`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}">Semua 4 Bagian</button>
                <button class="doc-part-btn ${Y===`surat`?`active`:``}" data-part="surat" style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${Y===`surat`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}">1. Surat</button>
                <button class="doc-part-btn ${Y===`rab`?`active`:``}" data-part="rab" style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${Y===`rab`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}">2. Scope & RAB</button>
                <button class="doc-part-btn ${Y===`klausul`?`active`:``}" data-part="klausul" style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${Y===`klausul`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}">3. 22 Pasal MoU</button>
                <button class="doc-part-btn ${Y===`esign`?`active`:``}" data-part="esign" style="padding:4px 10px;border-radius:var(--radius-lg);border:none;cursor:pointer;${Y===`esign`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}">4. E-Sign Sah</button>
              </div>

              <button
                id="doc-print-btn"
                style="padding:8px 14px;border-radius:var(--radius-xl);font-size:12px;font-weight:700;color:#fff;background:var(--indigo-600);border:none;cursor:pointer;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(99,102,241,.25);"
              >
                ${X.printer}
                <span>Cetak Dokumen Resmi</span>
              </button>
            </div>
          </div>

          <!-- Official Paper Layout Viewport -->
          <div style="background:var(--bg-card);border-radius:var(--radius-3xl);border:1px solid var(--border-light);padding:40px;box-shadow:var(--shadow-md);display:flex;flex-direction:column;gap:36px;max-width:880px;margin:0 auto;position:relative;">
            
            <!-- PART 1: SURAT PENGANTAR RESMI -->
            ${Y===`all`||Y===`surat`?`
              <div style="display:flex;flex-direction:column;gap:20px;border-bottom:1px solid var(--border-light);padding-bottom:32px;">
                <!-- Letterhead -->
                <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid var(--indigo-600);padding-bottom:16px;">
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                      <div style="width:32px;height:32px;border-radius:var(--radius-md);background:var(--indigo-600);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:16px;">
                        A
                      </div>
                      <h2 style="font-size:16px;font-weight:900;letter-spacing:-0.01em;color:var(--text-primary);margin:0;">
                        PT ASTACODE SOLUSINDO TEKNOLOGI
                      </h2>
                    </div>
                    <p style="font-size:11px;color:var(--text-muted);margin:0;">
                      Enterprise Software Engineering • Cloud Architecture • Cyber Security Integration
                    </p>
                    <p style="font-size:10px;color:var(--text-muted);margin:0;">
                      Grand Slipi Tower Lt. 28, Jl. Letjen S. Parman, Jakarta Barat 11480 • www.astacode.com
                    </p>
                  </div>

                  <div style="text-align:right;font-size:12px;font-family:var(--font-mono);">
                    <p style="font-weight:700;color:var(--text-primary);margin:0;">
                      Nomor: ${n.contract_number||`SPK/2026/10/0042`}
                    </p>
                    <p style="color:var(--text-muted);font-size:11px;margin:2px 0 0 0;">
                      Tanggal: ${n.start_date||`15 Oktober 2026`}
                    </p>
                    <span style="display:inline-block;padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);margin-top:4px;">
                      Sah & Tersertifikasi Digital
                    </span>
                  </div>
                </div>

                <!-- Recipient Info -->
                <div style="font-size:12px;display:flex;flex-direction:column;gap:2px;">
                  <p style="font-weight:700;color:var(--text-secondary);margin:0;">Kepada Yth,</p>
                  <p style="font-weight:800;font-size:14px;color:var(--text-primary);margin:0;">
                    Direksi / Manajemen ${n.customer_name}
                  </p>
                  <p style="color:var(--text-muted);margin:4px 0 0 0;">
                    Perihal: <strong>Perjanjian Kerja Sama Pengembangan Sistem (${n.title})</strong>
                  </p>
                </div>

                <div style="font-size:12px;line-height:1.7;color:var(--text-secondary);display:flex;flex-direction:column;gap:12px;text-align:justify;">
                  <p style="margin:0;">Dengan hormat,</p>
                  <p style="margin:0;">
                    Menindaklanjuti kesepakatan ruang lingkup analisis kebutuhan bisnis dan spesifikasi teknis arsitektur perangkat lunak, bersama surat ini kami dari <strong>PT ASTACODE SOLUSINDO TEKNOLOGI</strong> menyampaikan dokumen kesepakatan kerja sama (Memorandum of Understanding & Surat Perjanjian Kerja) untuk pelaksanaan proyek <strong>${n.title}</strong>.
                  </p>
                  <p style="margin:0;">
                    Pengembangan ini dirancang secara komprehensif mencakup deliverable modul transaksional, integrasi REST API, modul pengamanan siber, garansi pemeliharaan cacat program selama <strong>${n.warranty_period||`90 Hari`}</strong> pasca BAST, serta komitmen <strong>${n.sla_level||`99.5% Uptime SLA`}</strong> dengan kepatuhan penuh terhadap UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).
                  </p>
                </div>
              </div>
            `:``}

            <!-- PART 2: RINCIAN SCOPE RAB & TERMIN TOP -->
            ${Y===`all`||Y===`rab`?`
              <div style="display:flex;flex-direction:column;gap:16px;border-bottom:1px solid var(--border-light);padding-bottom:32px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                  <div>
                    <span style="font-size:10px;text-transform:uppercase;font-weight:800;color:var(--indigo-600);display:block;">BAGIAN II</span>
                    <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:2px 0 0 0;">
                      Rencana Anggaran Biaya (RAB), Deliverables & Termin Pembayaran
                    </h3>
                  </div>
                  <span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:var(--text-muted);">
                    Alokasi: ${n.budget_mandays} Mandays
                  </span>
                </div>

                <!-- Summary Table -->
                <div style="border-radius:var(--radius-xl);border:1px solid var(--border-light);overflow:hidden;font-size:12px;">
                  <table style="width:100%;text-align:left;border-collapse:collapse;">
                    <thead style="background:var(--bg-surface);font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">
                      <tr>
                        <th style="padding:12px 14px;">Komponen Kesepakatan</th>
                        <th style="padding:12px 14px;">Rincian / Scope</th>
                        <th style="padding:12px 14px;text-align:right;">Nilai / Alokasi</th>
                      </tr>
                    </thead>
                    <tbody style="border-top:1px solid var(--border-light);color:var(--text-secondary);">
                      <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:12px 14px;font-weight:600;">Paket Solusi Sistem</td>
                        <td style="padding:12px 14px;">${n.title}</td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-weight:700;">${n.budget_mandays} Mandays</td>
                      </tr>
                      <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:12px 14px;font-weight:600;">Skema Termin (TOP)</td>
                        <td style="padding:12px 14px;">${n.top_scheme.preset_name} (${(n.top_scheme.items||[]).length} Tahap)</td>
                        <td style="padding:12px 14px;text-align:right;color:var(--indigo-600);font-weight:700;">Bertahap per Milestone</td>
                      </tr>
                      <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:12px 14px;font-weight:600;">Garansi & SLA Pasca BAST</td>
                        <td style="padding:12px 14px;">${n.warranty_period||`90 Hari Pasca BAST`} • ${n.sla_level||`99.5% Uptime`}</td>
                        <td style="padding:12px 14px;text-align:right;color:var(--emerald-600);font-weight:700;">Termasuk dalam Kontrak</td>
                      </tr>
                      <tr style="background:rgba(99,102,241,.08);font-weight:700;">
                        <td style="padding:12px 14px;color:var(--indigo-700);">Total Investasi Proyek (Termasuk PPN 11%)</td>
                        <td style="padding:12px 14px;color:var(--text-muted);font-weight:400;font-style:italic;">
                          "${i(n.contract_value)} Rupiah"
                        </td>
                        <td style="padding:12px 14px;text-align:right;font-family:var(--font-mono);font-size:14px;color:var(--emerald-600);">
                          ${a(n.contract_value)}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Termin breakdown pills -->
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;font-size:12px;">
                  ${(n.top_scheme.items||[]).map((e,t)=>`
                    <div style="padding:12px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:4px;">
                      <div style="display:flex;justify-content:space-between;font-weight:700;font-size:11px;">
                        <span>Termin ${t+1} (${e.value}%)</span>
                        <span style="color:var(--emerald-600);">${e.status}</span>
                      </div>
                      <p style="font-size:10px;color:var(--text-muted);margin:0;">${e.name}</p>
                      <p style="font-family:var(--font-mono);font-weight:700;color:var(--text-primary);margin:2px 0 0 0;">
                        ${a(n.contract_value*e.value/100)}
                      </p>
                    </div>
                  `).join(``)}
                </div>
              </div>
            `:``}

            <!-- PART 3: KUMPULAN 22 PASAL STANDAR MOU -->
            ${Y===`all`||Y===`klausul`?`
              <div style="display:flex;flex-direction:column;gap:16px;border-bottom:1px solid var(--border-light);padding-bottom:32px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                  <div>
                    <span style="font-size:10px;text-transform:uppercase;font-weight:800;color:var(--indigo-600);display:block;">BAGIAN III</span>
                    <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:2px 0 0 0;">
                      Kumpulan 22 Pasal Standar Syarat & Ketentuan MoU Software House
                    </h3>
                  </div>
                  <span style="font-size:12px;font-family:var(--font-mono);color:var(--text-muted);">22 Klausul Hukum Baku</span>
                </div>

                <div style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
                  ${J.map(e=>`
                    <div style="padding:14px 16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:4px;">
                      <h4 style="font-weight:800;color:var(--text-primary);margin:0;">
                        ${e.title}
                      </h4>
                      <p style="color:var(--text-secondary);line-height:1.6;text-align:justify;font-size:11px;margin:2px 0 0 0;">
                        ${e.body}
                      </p>
                    </div>
                  `).join(``)}
                </div>
              </div>
            `:``}

            <!-- PART 4: LEMBAR PENGESAHAN DUA BELAH PIHAK & E-SIGN -->
            ${Y===`all`||Y===`esign`?`
              <div style="display:flex;flex-direction:column;gap:20px;padding-top:8px;">
                <div style="text-align:center;">
                  <span style="font-size:10px;text-transform:uppercase;font-weight:800;color:var(--indigo-600);display:block;">BAGIAN IV</span>
                  <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:2px 0 0 0;">
                    Lembar Pengesahan Para Pihak (Certified Digital Signature UU ITE & UU PDP)
                  </h3>
                  <p style="font-size:11px;color:var(--text-muted);margin:4px 0 0 0;">
                    Dokumen ini ditandatangani secara elektronik dan berkekuatan hukum tetap mengikat kedua belah pihak.
                  </p>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;padding-top:12px;">
                  <!-- Pihak Pertama (Klien) -->
                  <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-surface);border:1px solid var(--border-light);text-align:center;display:flex;flex-direction:column;gap:12px;">
                    <span style="font-size:10px;text-transform:uppercase;font-weight:700;color:var(--text-muted);letter-spacing:0.05em;display:block;">
                      PIHAK PERTAMA (KLIEN)
                    </span>
                    <p style="font-weight:800;font-size:12px;color:var(--text-primary);margin:0;">
                      ${n.customer_name}
                    </p>

                    <!-- Signature Box -->
                    <div style="height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed rgba(16,185,129,.4);background:rgba(16,185,129,.05);border-radius:var(--radius-xl);gap:4px;padding:8px;">
                      <div style="display:flex;align-items:center;gap:6px;color:var(--emerald-600);font-weight:700;font-size:11px;">
                        ${X.checkCircle}
                        <span>Certified Digital Signature</span>
                      </div>
                      <span style="font-size:9px;font-family:var(--font-mono);color:var(--text-muted);">
                        E-Meterai Peruri Rp10.000 Terbubuhi • UU ITE Pasal 11
                      </span>
                    </div>

                    <div>
                      <p style="font-weight:800;font-size:12px;color:var(--text-primary);margin:0;">
                        ${n.signed_by_client||`Ir. Budi Santoso`}
                      </p>
                      <p style="font-size:10px;color:var(--text-muted);margin:2px 0 0 0;">Direktur Utama / Perwakilan Berwenang</p>
                    </div>
                  </div>

                  <!-- Pihak Kedua (ASTACODE) -->
                  <div style="padding:20px;border-radius:var(--radius-2xl);background:var(--bg-surface);border:1px solid var(--border-light);text-align:center;display:flex;flex-direction:column;gap:12px;">
                    <span style="font-size:10px;text-transform:uppercase;font-weight:700;color:var(--text-muted);letter-spacing:0.05em;display:block;">
                      PIHAK KEDUA (ASTACODE)
                    </span>
                    <p style="font-weight:800;font-size:12px;color:var(--text-primary);margin:0;">
                      PT ASTACODE SOLUSINDO TEKNOLOGI
                    </p>

                    <!-- Signature Box -->
                    <div style="height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed rgba(99,102,241,.4);background:rgba(99,102,241,.05);border-radius:var(--radius-xl);gap:4px;padding:8px;">
                      <div style="display:flex;align-items:center;gap:6px;color:var(--indigo-600);font-weight:700;font-size:11px;">
                        ${X.shieldCheck}
                        <span>Certified E-Sign ASTACODE</span>
                      </div>
                      <span style="font-size:9px;font-family:var(--font-mono);color:var(--text-muted);">
                        Digital ID: ASTA-ESIGN-${n.id} • SHA-256 Valid
                      </span>
                    </div>

                    <div>
                      <p style="font-weight:800;font-size:12px;color:var(--text-primary);margin:0;">
                        Asisyah Sarah Azzahra
                      </p>
                      <p style="font-size:10px;color:var(--text-muted);margin:2px 0 0 0;">Super Admin & Project Manager</p>
                    </div>
                  </div>
                </div>
              </div>
            `:``}
          </div>
        </div>
      `:``}

      <!-- TAB 4: AUDIT TRAIL E-SIGNATURE -->
      ${q===`esign_audit`?`
        <div style="display:flex;flex-direction:column;gap:16px;">
          <div style="background:var(--bg-card);border-radius:var(--radius-2xl);border:1px solid var(--border-light);padding:24px;display:flex;flex-direction:column;gap:20px;box-shadow:var(--shadow-xs);">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:16px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);display:flex;align-items:center;gap:8px;margin:0;">
                  <span style="color:var(--emerald-500);display:flex;">${X.shieldCheck}</span>
                  <span>Kepatuhan Keabsahan Hukum Elektronik (UU ITE & UU PDP)</span>
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0 0;">
                  Seluruh dokumen kontrak, penawaran harga (RAB), dan klausul MoU di ASTACODE dirancang dengan standar sertifikasi elektronik tertinggi.
                </p>
              </div>

              <span style="padding:4px 12px;border-radius:var(--radius-full);font-size:11px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);">
                100% Compliance Verified
              </span>
            </div>

            <!-- Compliance 3 Cards -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;font-size:12px;">
              <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;color:var(--indigo-600);font-weight:700;">
                  ${X.lock}
                  <span>UU ITE Pasal 11</span>
                </div>
                <h4 style="font-weight:700;color:var(--text-primary);margin:0;">Kekuatan Pembuktian Hukum</h4>
                <p style="color:var(--text-muted);font-size:11px;line-height:1.5;margin:0;">
                  Tanda tangan elektronik memiliki akibat hukum dan kekuatan hukum yang sah setara dengan tanda tangan basah di atas kertas bermeterai.
                </p>
              </div>

              <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;color:var(--indigo-600);font-weight:700;">
                  ${X.award}
                  <span>UU PDP No. 27/2022</span>
                </div>
                <h4 style="font-weight:700;color:var(--text-primary);margin:0;">Kerahasiaan Data Pribadi</h4>
                <p style="color:var(--text-muted);font-size:11px;line-height:1.5;margin:0;">
                  Semua identitas PIC klien, tanda tangan, nomor telepon, dan data finansial dienkripsi berstandar perbankan (AES-256).
                </p>
              </div>

              <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;color:var(--indigo-600);font-weight:700;">
                  ${X.fileCheck}
                  <span>E-Meterai Resmi Peruri</span>
                </div>
                <h4 style="font-weight:700;color:var(--text-primary);margin:0;">Bea Meterai Digital Rp10.000</h4>
                <p style="color:var(--text-muted);font-size:11px;line-height:1.5;margin:0;">
                  Integrasi e-Meterai resmi Peruri otomatis dibubuhkan pada kontrak di atas Rp5.000.000 sesuai UU Bea Meterai No. 10/2020.
                </p>
              </div>
            </div>

            <!-- Audit Trail Log Table -->
            <div style="display:flex;flex-direction:column;gap:12px;padding-top:12px;">
              <h4 style="font-weight:700;font-size:12px;color:var(--text-primary);margin:0;">
                Log Audit Trail Penandatanganan Terkini:
              </h4>

              <div style="border-radius:var(--radius-xl);border:1px solid var(--border-light);overflow:hidden;font-size:12px;">
                <table style="width:100%;text-align:left;border-collapse:collapse;">
                  <thead style="background:var(--bg-surface);font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">
                    <tr>
                      <th style="padding:12px 14px;">Waktu (WIB)</th>
                      <th style="padding:12px 14px;">Nomor Kontrak</th>
                      <th style="padding:12px 14px;">Penandatangan</th>
                      <th style="padding:12px 14px;">Integritas Dokumen</th>
                      <th style="padding:12px 14px;text-align:right;">Status Sertifikat</th>
                    </tr>
                  </thead>
                  <tbody style="border-top:1px solid var(--border-light);color:var(--text-secondary);">
                    ${e.map((e,t)=>`
                      <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:12px 14px;font-family:var(--font-mono);font-size:11px;color:var(--text-muted);">
                          ${e.signed_at?new Date(e.signed_at).toLocaleString(`id-ID`):`15/10/2026, 14:30:00`}
                        </td>
                        <td style="padding:12px 14px;font-family:var(--font-mono);font-weight:700;color:var(--indigo-600);">
                          ${e.contract_number}
                        </td>
                        <td style="padding:12px 14px;">
                          <span style="font-weight:700;color:var(--text-primary);">${e.signed_by_company||`Asisyah Sarah Azzahra`}</span>
                          <span style="display:block;font-size:10px;color:var(--text-muted);">& ${e.signed_by_client||e.customer_name}</span>
                        </td>
                        <td style="padding:12px 14px;font-family:var(--font-mono);font-size:10px;color:var(--text-muted);">
                          SHA256: 9f8e7d6c5b4a...${t}8e7d
                        </td>
                        <td style="padding:12px 14px;text-align:right;">
                          <span style="padding:2px 8px;border-radius:var(--radius-full);font-size:10px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);">
                            Valid & Terverifikasi
                          </span>
                        </td>
                      </tr>
                    `).join(``)}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      `:``}

      <!-- MODAL 1: BUAT SPK / KONTRAK BARU -->
      ${Vn?`
        <div id="new-contract-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);overflow-y:auto;">
          <div style="width:100%;max-width:520px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Buat Surat Perjanjian Kerja (SPK) Baru
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">
                  PT ASTACODE Solusindo Teknologi • Penanggung Jawab: Asisyah Sarah Azzahra
                </p>
              </div>
              <button id="new-contract-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <form id="new-contract-form" style="display:flex;flex-direction:column;gap:14px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Pilih Klien / Customer:</label>
                <select
                  id="new-contract-cust-select"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-weight:600;"
                >
                  ${t.map(e=>`
                    <option value="${e.id}" ${e.id===Hn?`selected`:``}>
                      ${e.legal_name} (${e.code}) - ${e.industry}
                    </option>
                  `).join(``)}
                </select>
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Judul Kontrak / Perjanjian:</label>
                <input
                  id="new-contract-title-input"
                  type="text"
                  required
                  placeholder="Contoh: Perjanjian Pembuatan Sistem ERP & Mobile ESS"
                  value="${Un}"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                />
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Nilai Kontrak (IDR):</label>
                  <input
                    id="new-contract-val-input"
                    type="number"
                    min="0"
                    step="1000000"
                    required
                    value="${Wn}"
                    style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                  />
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Alokasi Mandays Scope:</label>
                  <input
                    id="new-contract-mandays-input"
                    type="number"
                    min="1"
                    required
                    value="${Gn}"
                    style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                  />
                </div>
              </div>

              <!-- Skema TOP with Lainnya -->
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Skema Termin Pembayaran (TOP):</label>
                <select
                  id="new-contract-top-select"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-weight:600;"
                >
                  <option value="30/40/30" ${Kn===`30/40/30`?`selected`:``}>30% DP / 40% UAT / 30% BAST (Standard ASTACODE)</option>
                  <option value="50/50" ${Kn===`50/50`?`selected`:``}>50% DP / 50% Pelunasan</option>
                  <option value="40/30/30" ${Kn===`40/30/30`?`selected`:``}>40% DP / 30% Progress / 30% BAST</option>
                  <option value="30/30/30/10" ${Kn===`30/30/30/10`?`selected`:``}>30% DP / 30% Dev / 30% UAT / 10% Retensi Garansi</option>
                  <option value="Lainnya" ${Kn===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                </select>

                ${Kn===`Lainnya`?`
                  <div style="margin-top:6px;padding:10px;border-radius:var(--radius-lg);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                    <label style="display:block;font-size:11px;font-weight:700;color:var(--indigo-600);margin-bottom:4px;">Ketik Skema TOP Kustom:</label>
                    <input
                      id="new-contract-custom-top-input"
                      type="text"
                      required
                      placeholder="Misal: 25% DP / 25% Sprint 2 / 25% UAT / 25% BAST..."
                      value="${qn}"
                      style="width:100%;padding:6px 10px;border-radius:var(--radius-md);background:var(--bg-card);border:1px solid var(--indigo-400);color:var(--text-primary);outline:none;"
                    />
                  </div>
                `:``}
              </div>

              <!-- Garansi & SLA -->
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Garansi Bug Pasca BAST:</label>
                  <select
                    id="new-contract-warranty-select"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  >
                    <option value="30 Hari" ${Jn===`30 Hari`?`selected`:``}>30 Hari Pasca BAST</option>
                    <option value="60 Hari" ${Jn===`60 Hari`?`selected`:``}>60 Hari Pasca BAST</option>
                    <option value="90 Hari" ${Jn===`90 Hari`?`selected`:``}>90 Hari Pasca BAST (Rekomendasi ASTACODE)</option>
                    <option value="180 Hari" ${Jn===`180 Hari`?`selected`:``}>180 Hari Pasca BAST</option>
                    <option value="Lainnya" ${Jn===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                  </select>

                  ${Jn===`Lainnya`?`
                    <input
                      id="new-contract-custom-warranty-input"
                      type="text"
                      required
                      placeholder="Durasi garansi kustom..."
                      value="${Yn}"
                      style="width:100%;margin-top:4px;padding:6px 8px;border-radius:var(--radius-md);background:var(--bg-surface);border:1px solid var(--indigo-300);color:var(--text-primary);font-size:11px;outline:none;"
                    />
                  `:``}
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Komitmen SLA Dukungan:</label>
                  <select
                    id="new-contract-sla-select"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  >
                    <option value="99.5% Uptime SLA" ${Xn===`99.5% Uptime SLA`?`selected`:``}>99.5% Uptime SLA</option>
                    <option value="99.9% High Availability SLA" ${Xn===`99.9% High Availability SLA`?`selected`:``}>99.9% High Availability SLA</option>
                    <option value="Response Time < 1 Jam" ${Xn===`Response Time < 1 Jam`?`selected`:``}>Response Time &lt; 1 Jam</option>
                    <option value="Lainnya" ${Xn===`Lainnya`?`selected`:``}>Lainnya (Kustom...)</option>
                  </select>

                  ${Xn===`Lainnya`?`
                    <input
                      id="new-contract-custom-sla-input"
                      type="text"
                      required
                      placeholder="Komitmen SLA kustom..."
                      value="${Zn}"
                      style="width:100%;margin-top:4px;padding:6px 8px;border-radius:var(--radius-md);background:var(--bg-surface);border:1px solid var(--indigo-300);color:var(--text-primary);font-size:11px;outline:none;"
                    />
                  `:``}
                </div>
              </div>

              <!-- Masa Berlaku Dates -->
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Tanggal Mulai Efektif:</label>
                  <input
                    id="new-contract-start-input"
                    type="date"
                    value="${Qn}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  />
                </div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                  <label style="font-weight:700;color:var(--text-secondary);">Target Serah Terima (BAST):</label>
                  <input
                    id="new-contract-end-input"
                    type="date"
                    value="${$n}"
                    style="width:100%;padding:8px 10px;border-radius:var(--radius-lg);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                  />
                </div>
              </div>

              <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="new-contract-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Terbitkan Kontrak SPK
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}

      <!-- MODAL 2: DETAIL SPK KONTRAK -->
      ${r?`
        <div id="detail-contract-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);overflow-y:auto;">
          <div style="width:100%;max-width:640px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;max-height:90vh;overflow-y:auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:var(--indigo-600);">
                  ${r.contract_number}
                </span>
                <h3 style="font-size:16px;font-weight:800;color:var(--text-primary);margin:2px 0 0 0;">
                  ${r.title}
                </h3>
              </div>
              <button id="detail-contract-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;font-size:12px;">
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:12px;padding:14px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);">
                <div>
                  <span style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;display:block;">Klien:</span>
                  <span style="font-weight:700;color:var(--text-primary);">${r.customer_name}</span>
                </div>
                <div>
                  <span style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;display:block;">Nilai Kontrak:</span>
                  <span style="font-family:var(--font-mono);font-weight:700;color:var(--emerald-600);">${a(r.contract_value)}</span>
                </div>
                <div>
                  <span style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;display:block;">Garansi:</span>
                  <span style="font-weight:600;color:var(--text-primary);">${r.warranty_period||`90 Hari`}</span>
                </div>
                <div>
                  <span style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;display:block;">E-Sign Status:</span>
                  <span style="font-weight:700;color:var(--emerald-600);">${r.esign_status||`Sah UU ITE`}</span>
                </div>
              </div>

              <div>
                <h4 style="font-weight:700;color:var(--text-primary);margin:0 0 8px 0;">Jadwal & Milestone Sprint:</h4>
                <div style="display:flex;flex-direction:column;gap:8px;">
                  ${(r.milestones||[]).map(e=>`
                    <div style="padding:10px 12px;border-radius:var(--radius-lg);border:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;font-size:11px;">
                      <div>
                        <span style="font-weight:700;color:var(--text-primary);">${e.name}</span>
                        <span style="display:block;font-size:10px;color:var(--text-muted);">Target: ${e.target_date} • ${e.mandays_budget} Mandays</span>
                      </div>
                      <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;${e.status===`Completed`?`background:rgba(16,185,129,.1);color:var(--emerald-600);`:`background:var(--bg-surface);color:var(--text-secondary);`}">
                        ${e.status}
                      </span>
                    </div>
                  `).join(``)}
                </div>
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
              <button
                id="detail-contract-cancel-btn"
                style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-secondary);background:var(--bg-surface);border:1px solid var(--border-light);font-semibold;font-size:12px;cursor:pointer;"
              >
                Tutup
              </button>
              <button
                id="detail-contract-open-doc-btn"
                style="padding:8px 16px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;"
              >
                Buka di Dokumen 4-Part
              </button>
            </div>
          </div>
        </div>
      `:``}

      <!-- MODAL 3: E-SIGNATURE INTERAKTIF -->
      ${o?`
        <div id="sign-contract-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
          <div style="width:100%;max-width:440px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--emerald-500);display:flex;">${X.penTool}</span>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Pengesahan Tanda Tangan Digital (E-Sign)
                </h3>
              </div>
              <button id="sign-contract-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
              <p style="color:var(--text-secondary);margin:0;">
                Anda akan menandatangani dokumen kontrak <strong>${o.contract_number}</strong> secara elektronik atas nama:
              </p>

              <div style="padding:12px;border-radius:var(--radius-xl);background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);display:flex;flex-direction:column;gap:2px;">
                <span style="font-size:10px;color:var(--indigo-600);text-transform:uppercase;font-weight:700;">Penandatangan Resmi ASTACODE:</span>
                <p style="font-weight:800;color:var(--text-primary);margin:0;">Asisyah Sarah Azzahra</p>
                <p style="font-size:11px;color:var(--text-muted);margin:0;">Super Admin & Project Manager • PT ASTACODE SOLUSINDO TEKNOLOGI</p>
              </div>

              <!-- Signature Canvas Mock -->
              <div style="height:100px;border-radius:var(--radius-xl);border:2px dashed var(--border-light);background:var(--bg-surface);display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-muted);font-family:var(--font-mono);font-size:11px;gap:4px;">
                <span style="font-style:italic;font-family:serif;font-size:20px;color:var(--indigo-600);">Asisyah Sarah Azzahra</span>
                <span style="font-size:9px;color:var(--emerald-600);font-weight:700;">✔ Hash Sertifikat X.509 Terintegrasi</span>
              </div>

              <div style="font-size:10px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                <span style="color:var(--emerald-500);display:flex;">${X.shieldCheck}</span>
                <span>Memenuhi Pasal 11 UU ITE No. 11/2008 & UU PDP No. 27/2022. Memiliki keabsahan hukum penuh.</span>
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
              <button
                id="sign-contract-cancel-btn"
                style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;font-size:12px;"
              >
                Batal
              </button>
              <button
                id="sign-contract-confirm-btn"
                style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--emerald-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,.3);display:flex;align-items:center;gap:6px;"
              >
                ${X.check}
                <span>Bubuhkan Tanda Tangan Digital</span>
              </button>
            </div>
          </div>
        </div>
      `:``}

      <!-- MODAL 4: BUAT ADDENDUM KONTRAK -->
      ${s?`
        <div id="addendum-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
          <div style="width:100%;max-width:440px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                  Buat Addendum Kontrak ${s.contract_number}
                </h3>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0 0;">${s.customer_name}</p>
              </div>
              <button id="addendum-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <form id="addendum-form" style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Deskripsi Perubahan Scope / Addendum:</label>
                <textarea
                  id="addendum-desc-input"
                  rows="3"
                  required
                  placeholder="Contoh: Penambahan integrasi Payment Gateway QRIS & Biometric Face Recognition pada modul kasir..."
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-sans;"
                >${zn}</textarea>
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Penyesuaian Nilai Tambah (IDR):</label>
                <input
                  id="addendum-val-input"
                  type="number"
                  min="0"
                  step="1000000"
                  value="${Bn}"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);font-family:var(--font-mono);color:var(--text-primary);outline:none;"
                />
              </div>

              <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="addendum-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;font-size:12px;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Simpan & Terbitkan Addendum
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}

      <!-- MODAL 5: EDIT CLAUSE -->
      ${c?`
        <div id="edit-clause-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
          <div style="width:100%;max-width:520px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
              Edit ${c.title}
            </h3>

            <div style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Judul Pasal:</label>
                <input
                  id="edit-clause-title-input"
                  type="text"
                  value="${c.title}"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-weight:700;"
                />
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Isi Redaksional Pasal MoU:</label>
                <textarea
                  id="edit-clause-body-input"
                  rows="6"
                  style="width:100%;padding:10px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;line-height:1.6;font-sans;"
                >${c.body}</textarea>
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
              <button
                id="edit-clause-cancel-btn"
                style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;font-size:12px;"
              >
                Batal
              </button>
              <button
                id="edit-clause-save-btn"
                style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
              >
                Simpan Klausul Legal
              </button>
            </div>
          </div>
        </div>
      `:``}

      <!-- MODAL 6: ADD NEW CLAUSE -->
      ${kn?`
        <div id="add-clause-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);">
          <div style="width:100%;max-width:520px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <h3 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                + Tambah Pasal Baru ke Library Legal MoU
              </h3>
              <button id="add-clause-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <form id="add-clause-form" style="display:flex;flex-direction:column;gap:12px;font-size:12px;">
              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Kategori Pasal:</label>
                <select
                  id="add-clause-cat-select"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                >
                  <option value="Ruang Lingkup & Deliverable">Ruang Lingkup & Deliverable</option>
                  <option value="Keuangan & Pajak">Keuangan & Pajak</option>
                  <option value="HAKI & Lisensi">HAKI & Lisensi</option>
                  <option value="Kerahasiaan & Keamanan">Kerahasiaan & Keamanan</option>
                  <option value="Garansi & SLA">Garansi & SLA</option>
                  <option value="Hukum & Sengketa">Hukum & Sengketa</option>
                  <option value="Operasional & Infrastruktur">Operasional & Infrastruktur</option>
                  <option value="Kebijakan AI & LLM Data">Kebijakan AI & LLM Data</option>
                </select>
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Judul Pasal:</label>
                <input
                  id="add-clause-title-input"
                  type="text"
                  required
                  placeholder="Contoh: Kebijakan Penggunaan Model AI & LLM"
                  value="${An}"
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;"
                />
              </div>

              <div style="display:flex;flex-direction:column;gap:4px;">
                <label style="font-weight:700;color:var(--text-secondary);">Isi Naskah Klausul:</label>
                <textarea
                  id="add-clause-body-input"
                  rows="5"
                  required
                  placeholder="Tuliskan isi redaksional klausul hukum yang mengikat para pihak..."
                  style="width:100%;padding:8px 12px;border-radius:var(--radius-xl);border:1px solid var(--border-light);background:var(--bg-surface);color:var(--text-primary);outline:none;font-sans;"
                >${jn}</textarea>
              </div>

              <div style="display:flex;align-items:center;gap:8px;padding-top:4px;">
                <input
                  type="checkbox"
                  id="add-clause-mandatory-chk"
                  checked
                  style="width:16px;height:16px;cursor:pointer;"
                />
                <label for="add-clause-mandatory-chk" style="color:var(--text-primary);cursor:pointer;">
                  Tandai sebagai <strong>Klausul Wajib (Mandatory)</strong> dalam perjanjian
                </label>
              </div>

              <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--border-light);">
                <button
                  type="button"
                  id="add-clause-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;font-size:12px;"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,.3);"
                >
                  Simpan Pasal Baru
                </button>
              </div>
            </form>
          </div>
        </div>
      `:``}

      <!-- MODAL 7: EXPORT KOMPILASI 22 PASAL -->
      ${tr?`
        <div id="export-clauses-modal-backdrop" style="position:fixed;inset:0;z-index:999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);overflow-y:auto;">
          <div style="width:100%;max-width:760px;background:var(--bg-card);border-radius:var(--radius-2xl);padding:24px;box-shadow:var(--shadow-xl);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:16px;max-height:90vh;overflow-y:auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);padding-bottom:12px;">
              <div>
                <h3 style="font-size:16px;font-weight:800;color:var(--text-primary);margin:0;">
                  Naskah Resmi Kompilasi 22 Pasal Standar MoU ASTACODE
                </h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0 0;">
                  Format teks resmi untuk lampiran perjanjian hukum software house.
                </p>
              </div>
              <button id="export-clauses-close-btn" style="padding:4px;border-radius:var(--radius-md);color:var(--text-muted);background:none;border:none;cursor:pointer;">
                ${X.x}
              </button>
            </div>

            <div style="padding:16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);display:flex;flex-direction:column;gap:14px;font-size:12px;max-height:55vh;overflow-y:auto;color:var(--text-primary);">
              <div style="text-align:center;font-weight:800;border-bottom:1px solid var(--border-light);padding-bottom:8px;">
                LAMPIRAN SYARAT & KETENTUAN HUKUM (MoU)<br />
                PT ASTACODE SOLUSINDO TEKNOLOGI
              </div>
              ${J.map(e=>`
                <div style="display:flex;flex-direction:column;gap:2px;">
                  <p style="font-weight:700;color:var(--indigo-600);margin:0;">${e.title}</p>
                  <p style="color:var(--text-secondary);line-height:1.6;text-align:justify;margin:0;">${e.body}</p>
                </div>
              `).join(``)}
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border-light);">
              <button
                id="export-clauses-copy-all-btn"
                style="padding:8px 16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);color:var(--text-primary);font-weight:700;font-size:12px;cursor:pointer;display:flex;align-items:center;gap:6px;"
              >
                ${X.copy}
                <span>Salin Semua Teks</span>
              </button>

              <div style="display:flex;align-items:center;gap:8px;">
                <button
                  id="export-clauses-cancel-btn"
                  style="padding:8px 16px;border-radius:var(--radius-xl);color:var(--text-muted);background:none;border:none;cursor:pointer;font-weight:600;font-size:12px;"
                >
                  Tutup
                </button>
                <button
                  id="export-clauses-print-btn"
                  style="padding:8px 20px;border-radius:var(--radius-xl);background:var(--indigo-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;"
                >
                  ${X.printer}
                  <span>Cetak / Save PDF</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      `:``}
    </div>
  `}function rr(e){e.querySelectorAll(`.legal-tab-btn[data-tab]`).forEach(t=>{t.addEventListener(`click`,()=>{q=t.dataset.tab,Z(e)})});let t=e.querySelector(`#legal-search-contract-input`);t&&t.addEventListener(`input`,t=>{Tn=t.target.value,Mn=1,Z(e)}),e.querySelectorAll(`.legal-status-filter-btn[data-status]`).forEach(t=>{t.addEventListener(`click`,()=>{En=t.dataset.status,Mn=1,Z(e)})});let n=e.querySelector(`#legal-open-new-contract-btn`);n&&n.addEventListener(`click`,()=>{Vn=!0,Z(e)});let r=e.querySelector(`#new-contract-close-btn`),i=e.querySelector(`#new-contract-cancel-btn`);r&&r.addEventListener(`click`,()=>{Vn=!1,Z(e)}),i&&i.addEventListener(`click`,()=>{Vn=!1,Z(e)});let o=e.querySelector(`#new-contract-cust-select`);o&&o.addEventListener(`change`,e=>Hn=e.target.value);let s=e.querySelector(`#new-contract-title-input`);s&&s.addEventListener(`input`,e=>Un=e.target.value);let c=e.querySelector(`#new-contract-val-input`);c&&c.addEventListener(`input`,e=>Wn=Number(e.target.value));let l=e.querySelector(`#new-contract-mandays-input`);l&&l.addEventListener(`input`,e=>Gn=Number(e.target.value));let d=e.querySelector(`#new-contract-top-select`);d&&d.addEventListener(`change`,t=>{Kn=t.target.value,Z(e)});let f=e.querySelector(`#new-contract-custom-top-input`);f&&f.addEventListener(`input`,e=>qn=e.target.value);let p=e.querySelector(`#new-contract-warranty-select`);p&&p.addEventListener(`change`,t=>{Jn=t.target.value,Z(e)});let m=e.querySelector(`#new-contract-custom-warranty-input`);m&&m.addEventListener(`input`,e=>Yn=e.target.value);let h=e.querySelector(`#new-contract-sla-select`);h&&h.addEventListener(`change`,t=>{Xn=t.target.value,Z(e)});let g=e.querySelector(`#new-contract-custom-sla-input`);g&&g.addEventListener(`input`,e=>Zn=e.target.value);let _=e.querySelector(`#new-contract-start-input`);_&&_.addEventListener(`input`,e=>Qn=e.target.value);let v=e.querySelector(`#new-contract-end-input`);v&&v.addEventListener(`input`,e=>$n=e.target.value);let y=e.querySelector(`#new-contract-form`);y&&y.addEventListener(`submit`,t=>{t.preventDefault();let{customers:n}=b.getState();Kn===`Lainnya`&&qn.trim();let r=Jn===`Lainnya`?Yn.trim()||`Garansi Kustom`:Jn,i=Xn===`Lainnya`?Zn.trim()||`SLA Kustom`:Xn,a=n.find(e=>e.id===Hn)||n[0],o=b.addNewProject({title:Un||`Perjanjian Proyek Baru`,customer_id:a.id,customer_name:a.legal_name,contract_value:Wn||1e8,budget_mandays:Gn||80,start_date:Qn,end_date:$n,warranty_period:r,sla_level:i,status:`Active`,pm_name:`Asisyah Sarah Azzahra`,esign_status:`Terverifikasi Sah UU ITE`,signed_by_client:a.contacts&&a.contacts[0]?.name||`Direktur Klien`,signed_by_company:`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,signed_at:new Date().toISOString()});Vn=!1,er=o.id,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Kontrak SPK "${o.contract_number}" untuk ${a.legal_name} berhasil diterbitkan!`,type:`success`}})),Z(e)}),e.querySelectorAll(`.legal-detail-btn[data-project-id]`).forEach(t=>{t.addEventListener(`click`,()=>{In=t.dataset.projectId,Z(e)})});let ee=e.querySelector(`#detail-contract-close-btn`),te=e.querySelector(`#detail-contract-cancel-btn`);ee&&ee.addEventListener(`click`,()=>{In=null,Z(e)}),te&&te.addEventListener(`click`,()=>{In=null,Z(e)});let ne=e.querySelector(`#detail-contract-open-doc-btn`);ne&&ne.addEventListener(`click`,()=>{er=In,In=null,q=`mou_preview`,Z(e)}),e.querySelectorAll(`.legal-open-doc-btn[data-project-id]`).forEach(t=>{t.addEventListener(`click`,()=>{er=t.dataset.projectId,q=`mou_preview`,Z(e)})}),e.querySelectorAll(`.legal-esign-btn[data-project-id]`).forEach(t=>{t.addEventListener(`click`,()=>{Ln=t.dataset.projectId,Z(e)})});let re=e.querySelector(`#sign-contract-close-btn`),ie=e.querySelector(`#sign-contract-cancel-btn`);re&&re.addEventListener(`click`,()=>{Ln=null,Z(e)}),ie&&ie.addEventListener(`click`,()=>{Ln=null,Z(e)});let ae=e.querySelector(`#sign-contract-confirm-btn`);ae&&ae.addEventListener(`click`,()=>{let{projects:t}=b.getState(),n=t.find(e=>e.id===Ln);n&&(n.esign_status=`Terverifikasi Sah UU ITE`,n.signed_at=new Date().toISOString(),n.signed_by_company=`Asisyah Sarah Azzahra (Super Admin & PM ASTACODE)`,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Dokumen kontrak ${n.contract_number} berhasil ditandatangani secara digital & bersertifikat UU ITE!`,type:`success`}}))),Ln=null,Z(e)}),e.querySelectorAll(`.legal-addendum-btn[data-project-id]`).forEach(t=>{t.addEventListener(`click`,()=>{Rn=t.dataset.projectId,Z(e)})});let oe=e.querySelector(`#addendum-close-btn`),se=e.querySelector(`#addendum-cancel-btn`);oe&&oe.addEventListener(`click`,()=>{Rn=null,Z(e)}),se&&se.addEventListener(`click`,()=>{Rn=null,Z(e)});let ce=e.querySelector(`#addendum-form`);ce&&ce.addEventListener(`submit`,t=>{t.preventDefault();let{projects:n}=b.getState(),r=n.find(e=>e.id===Rn);if(r){let e=`ADD/${r.contract_number.replace(`CT/`,``)}-01`;window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Addendum ${e} senilai ${a(Bn)} berhasil dibuat dan dilampirkan ke kontrak utama!`,type:`success`}}))}Rn=null,zn=``,Z(e)});let le=e.querySelector(`#addendum-desc-input`);le&&le.addEventListener(`input`,e=>zn=e.target.value);let ue=e.querySelector(`#addendum-val-input`);ue&&ue.addEventListener(`input`,e=>Bn=Number(e.target.value));let x=e.querySelector(`#legal-search-clause-input`);x&&x.addEventListener(`input`,t=>{Tn=t.target.value,Pn=1,Z(e)});let de=e.querySelector(`#legal-reset-clauses-btn`);de&&de.addEventListener(`click`,()=>{confirm(`Kembalikan seluruh 22 pasal ke redaksional standar baku ASTACODE?`)&&(J=[...u],window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Klausul berhasil direset ke standar baku 22 Pasal ASTACODE.`,type:`info`}})),Z(e))});let fe=e.querySelector(`#legal-export-clauses-btn`);fe&&fe.addEventListener(`click`,()=>{tr=!0,Z(e)});let pe=e.querySelector(`#export-clauses-close-btn`),me=e.querySelector(`#export-clauses-cancel-btn`);pe&&pe.addEventListener(`click`,()=>{tr=!1,Z(e)}),me&&me.addEventListener(`click`,()=>{tr=!1,Z(e)});let he=e.querySelector(`#export-clauses-copy-all-btn`);he&&he.addEventListener(`click`,()=>{let e=J.map(e=>`${e.title}\n${e.body}`).join(`

`);navigator.clipboard.writeText(e),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Seluruh naskah 22 pasal berhasil disalin ke clipboard!`,type:`success`}}))});let ge=e.querySelector(`#export-clauses-print-btn`);ge&&ge.addEventListener(`click`,()=>window.print()),e.querySelectorAll(`.legal-cat-btn[data-cat]`).forEach(t=>{t.addEventListener(`click`,()=>{Dn=t.dataset.cat,Pn=1,Z(e)})}),e.querySelectorAll(`.clause-copy-btn[data-clause-id]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=J.find(t=>t.id===e.dataset.clauseId);if(t){let e=`${t.title}\n\n${t.body}\n\n[Diterbitkan oleh Legal ASTACODE - PT ASTACODE Solusindo Teknologi]`;navigator.clipboard.writeText(e),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`✔ Teks ${t.title} berhasil disalin ke clipboard!`,type:`success`}}))}})}),e.querySelectorAll(`.clause-edit-btn[data-clause-id]`).forEach(t=>{t.addEventListener(`click`,()=>{On=t.dataset.clauseId,Z(e)})});let _e=e.querySelector(`#edit-clause-cancel-btn`);_e&&_e.addEventListener(`click`,()=>{On=null,Z(e)});let ve=e.querySelector(`#edit-clause-save-btn`);ve&&ve.addEventListener(`click`,()=>{let t=e.querySelector(`#edit-clause-title-input`),n=e.querySelector(`#edit-clause-body-input`);t&&n&&On&&(J=J.map(e=>e.id===On?{...e,title:t.value,body:n.value}:e),On=null,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Klausul hukum berhasil disimpan ke legal repository ASTACODE.`,type:`success`}})),Z(e))}),e.querySelectorAll(`.clause-toggle-mandatory-btn[data-clause-id]`).forEach(t=>{t.addEventListener(`click`,()=>{J=J.map(e=>e.id===t.dataset.clauseId?{...e,is_mandatory:!e.is_mandatory}:e),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Status wajib/fleksibel klausul berhasil diperbarui.`,type:`info`}})),Z(e)})});let S=e.querySelector(`#legal-add-clause-btn`);S&&S.addEventListener(`click`,()=>{kn=!0,Z(e)});let ye=e.querySelector(`#add-clause-close-btn`),be=e.querySelector(`#add-clause-cancel-btn`);ye&&ye.addEventListener(`click`,()=>{kn=!1,Z(e)}),be&&be.addEventListener(`click`,()=>{kn=!1,Z(e)});let xe=e.querySelector(`#add-clause-form`);xe&&xe.addEventListener(`submit`,t=>{t.preventDefault();let n=e.querySelector(`#add-clause-title-input`),r=e.querySelector(`#add-clause-body-input`),i=e.querySelector(`#add-clause-cat-select`),a=e.querySelector(`#add-clause-mandatory-chk`);if(!n||!r||!n.value.trim()||!r.value.trim())return;let o=J.length+1,s={id:`cl-${Date.now()}`,number:o,title:n.value.startsWith(`Pasal`)?n.value:`Pasal ${o}: ${n.value}`,body:r.value,is_active:!0,category:i?i.value:`Ruang Lingkup & Deliverable`,is_mandatory:!a||a.checked};J.push(s),kn=!1,An=``,jn=``,window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Pasal baru "${s.title}" berhasil ditambahkan ke library.`,type:`success`}})),Z(e)});let Se=e.querySelector(`#doc-project-select`);Se&&Se.addEventListener(`change`,t=>{er=t.target.value,Z(e)}),e.querySelectorAll(`.doc-part-btn[data-part]`).forEach(t=>{t.addEventListener(`click`,()=>{Y=t.dataset.part,Z(e)})});let Ce=e.querySelector(`#doc-print-btn`);Ce&&Ce.addEventListener(`click`,()=>window.print()),Be(e,t=>{Mn=t,Z(e)},t=>{Nn=t,Mn=1,Z(e)},`legal-contracts-pagination`),Be(e,t=>{Pn=t,Z(e)},t=>{Fn=t,Pn=1,Z(e)},`legal-clauses-pagination`)}function Z(e){e.innerHTML=nr(),rr(e)}function ir(e){return Z(e),b.subscribe(()=>{b.getState().activeView===`contracts_legal`&&Z(e)})}var ar=t({bindWhatsAppInboxEvents:()=>lr,mount:()=>fr,renderWhatsAppInbox:()=>cr}),Q=`all`,or=``,sr={messageSquare:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,sparkles:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>`,bot:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>`,user:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`,send:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>`,alertTriangle:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>`,shieldAlert:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>`};function cr(){let{waConversations:e,activeWaId:t}=b.getState(),n=e.find(e=>e.id===t)||e[0]||null,r=e.filter(e=>Q===`ai`?e.ai_mode===`auto`:Q!==`human`||e.needs_human||e.ai_mode===`off`);return`
    <div style="flex:1;display:flex;height:100%;overflow:hidden;background:var(--bg-canvas);">
      
      <!-- Left Chat List Column -->
      <div style="width:340px;display:flex;flex-direction:column;border-right:1px solid var(--border-light);background:var(--bg-card);flex-shrink:0;">
        <!-- Header -->
        <div style="padding:16px;border-bottom:1px solid var(--border-light);display:flex;flex-direction:column;gap:12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:32px;height:32px;border-radius:var(--radius-xl);background:var(--emerald-500);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(16,185,129,.2);">
                ${sr.messageSquare}
              </div>
              <h2 style="font-size:14px;font-weight:800;color:var(--text-primary);margin:0;">
                WhatsApp Client Care
              </h2>
            </div>
            <span style="padding:2px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(16,185,129,.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,.2);">
              Meta Cloud API
            </span>
          </div>

          <!-- Quick Filters -->
          <div style="display:flex;align-items:center;gap:4px;background:var(--bg-surface);padding:4px;border-radius:var(--radius-xl);font-size:11px;font-weight:600;">
            <button
              class="wa-filter-btn ${Q===`all`?`active`:``}"
              data-mode="all"
              style="flex:1;padding:6px;border-radius:var(--radius-lg);text-align:center;border:none;cursor:pointer;transition:all var(--transition);${Q===`all`?`background:var(--bg-card);color:var(--text-primary);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              Semua (${e.length})
            </button>
            <button
              class="wa-filter-btn ${Q===`ai`?`active`:``}"
              data-mode="ai"
              style="flex:1;padding:6px;border-radius:var(--radius-lg);text-align:center;border:none;cursor:pointer;transition:all var(--transition);${Q===`ai`?`background:var(--bg-card);color:var(--indigo-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              🤖 AI Mode
            </button>
            <button
              class="wa-filter-btn ${Q===`human`?`active`:``}"
              data-mode="human"
              style="flex:1;padding:6px;border-radius:var(--radius-lg);text-align:center;border:none;cursor:pointer;transition:all var(--transition);${Q===`human`?`background:var(--bg-card);color:var(--rose-600);font-weight:700;box-shadow:var(--shadow-xs);`:`background:none;color:var(--text-muted);`}"
            >
              ⚠ Butuh Human
            </button>
          </div>
        </div>

        <!-- Conversation List -->
        <div style="flex:1;overflow-y:auto;">
          ${r.map(e=>{let t=n&&e.id===n.id;return`
              <div
                class="wa-chat-item ${t?`selected`:``}"
                data-chat-id="${e.id}"
                style="padding:14px 16px;display:flex;align-items:flex-start;gap:12px;cursor:pointer;border-bottom:1px solid var(--border-light);transition:background var(--transition);position:relative;${t?`background:rgba(99,102,241,.08);border-left:4px solid var(--indigo-600);`:`background:transparent;`}"
              >
                <!-- Avatar -->
                <div
                  style="width:40px;height:40px;border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;background:${e.avatar_bg?e.avatar_bg.replace(`bg-`,``):`var(--indigo-600)`};"
                  class="${e.avatar_bg||`bg-indigo-600`}"
                >
                  ${(e.contact_name||`WA`).slice(0,2).toUpperCase()}
                </div>

                <!-- Chat Details -->
                <div style="flex:1;min-width:0;">
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-weight:700;font-size:12px;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                      ${e.contact_name}
                    </span>
                    <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">
                      ${e.last_message_time||``}
                    </span>
                  </div>

                  <p style="font-size:11px;font-weight:600;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:2px 0 0 0;">
                    ${e.company_name}
                  </p>

                  <p style="font-size:12px;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:4px 0 0 0;">
                    ${e.last_message||``}
                  </p>

                  <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                    <span style="padding:2px 6px;border-radius:var(--radius-sm);font-size:9px;font-weight:700;${e.ai_mode===`auto`?`background:rgba(99,102,241,.1);color:var(--indigo-600);`:e.ai_mode===`suggest`?`background:rgba(245,158,11,.1);color:var(--amber-600);`:`background:var(--bg-surface);color:var(--text-muted);`}">
                      AI: ${(e.ai_mode||`OFF`).toUpperCase()}
                    </span>

                    ${e.needs_human?`
                      <span style="padding:2px 6px;border-radius:var(--radius-sm);font-size:9px;font-weight:800;background:rgba(239,68,68,.1);color:var(--rose-600);border:1px solid rgba(239,68,68,.2);display:flex;align-items:center;gap:3px;">
                        ${sr.alertTriangle}
                        <span>Handoff</span>
                      </span>
                    `:``}
                  </div>
                </div>
              </div>
            `}).join(``)}
        </div>
      </div>

      <!-- Center Chat Area -->
      ${n?`
        <div style="flex:1;display:flex;flex-direction:column;height:100%;background:var(--bg-canvas);">
          <!-- Active Chat Header -->
          <div style="height:64px;padding:0 24px;background:var(--bg-card);border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow-xs);flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div
                style="width:36px;height:36px;border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;background:${n.avatar_bg?n.avatar_bg.replace(`bg-`,``):`var(--indigo-600)`};"
                class="${n.avatar_bg||`bg-indigo-600`}"
              >
                ${(n.contact_name||`WA`).slice(0,2).toUpperCase()}
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;">
                  <h3 style="font-weight:800;font-size:13px;color:var(--text-primary);margin:0;">
                    ${n.contact_name}
                  </h3>
                  <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-muted);">
                    ${n.contact_phone||``}
                  </span>
                </div>
                <p style="font-size:11px;color:var(--text-muted);font-weight:500;margin:2px 0 0 0;">
                  ${n.company_name}
                </p>
              </div>
            </div>

            <!-- AI Mode Selector & Lead Button -->
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
                <span style="color:var(--text-muted);font-weight:500;">Mode Respon AI:</span>
                <select
                  id="wa-ai-mode-select"
                  style="padding:6px 12px;border-radius:var(--radius-xl);background:var(--bg-surface);font-size:12px;font-weight:700;color:var(--text-primary);border:1px solid var(--border-light);outline:none;cursor:pointer;"
                >
                  <option value="auto" ${n.ai_mode===`auto`?`selected`:``}>🤖 Auto (AI Kirim Otomatis)</option>
                  <option value="suggest" ${n.ai_mode===`suggest`?`selected`:``}>✨ Suggest (AI Beri Draft)</option>
                  <option value="off" ${n.ai_mode===`off`?`selected`:``}>Off (Manual Human Saja)</option>
                </select>
              </div>

              ${n.lead_id?`
                <button
                  id="wa-open-lead-btn"
                  data-lead-id="${n.lead_id}"
                  style="padding:6px 14px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);color:var(--indigo-600);font-size:12px;font-weight:700;border:1px solid rgba(99,102,241,.2);cursor:pointer;"
                >
                  Buka Lead
                </button>
              `:``}
            </div>
          </div>

          <!-- Sensitive Guardrail Banner if Handoff -->
          ${n.needs_human?`
            <div style="padding:10px 24px;background:rgba(239,68,68,.08);border-bottom:1px solid rgba(239,68,68,.2);display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--rose-600);flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="display:flex;">${sr.shieldAlert}</span>
                <span style="font-weight:700;">
                  Human Handoff Aktif: Percakapan menyangkut negosiasi harga / diskon di luar kewenangan AI.
                </span>
              </div>
              <span id="wa-assign-sales-btn" style="font-weight:600;font-size:11px;text-decoration:underline;cursor:pointer;">
                Tugaskan Agen Sales
              </span>
            </div>
          `:``}

          <!-- Messages Stream -->
          <div id="wa-messages-container" style="flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:16px;">
            ${(n.messages||[]).map(e=>{let t=e.sender===`client`,r=e.sender===`ai`;return`
                <div style="display:flex;justify-content:${t?`flex-start`:`flex-end`};">
                  <div style="max-width:540px;border-radius:var(--radius-2xl);padding:14px 16px;display:flex;flex-direction:column;gap:6px;box-shadow:var(--shadow-xs);${t?`background:var(--bg-card);color:var(--text-primary);border:1px solid var(--border-light);border-top-left-radius:var(--radius-xs);`:r?`background:linear-gradient(135deg, var(--indigo-600), #7c3aed);color:#fff;border-top-right-radius:var(--radius-xs);`:`background:var(--emerald-600);color:#fff;border-top-right-radius:var(--radius-xs);`}">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:10px;opacity:0.85;padding-bottom:4px;border-bottom:${t?`1px solid var(--border-light)`:`1px solid rgba(255,255,255,.2)`};">
                      <span style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;display:flex;align-items:center;gap:4px;">
                        ${t?`${sr.user}<span>${n.contact_name}</span>`:r?`${sr.bot}<span>ASTACODE AI Assistant</span>`:`${sr.user}<span>Sales Executive</span>`}
                      </span>
                      <span style="font-family:var(--font-mono);">${e.timestamp||``}</span>
                    </div>

                    <p style="font-size:12px;line-height:1.5;margin:0;">
                      ${e.text}
                    </p>

                    ${r&&e.kb_source?`
                      <div style="padding-top:6px;font-size:10px;color:rgba(255,255,255,.8);display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,.15);">
                        <span>Sumber: ${e.kb_source}</span>
                        <span style="font-family:var(--font-mono);">Confidence: 94%</span>
                      </div>
                    `:``}
                  </div>
                </div>
              `}).join(``)}
          </div>

          <!-- AI Suggestion Bar if mode == suggest -->
          ${n.ai_mode===`suggest`?`
            <div style="padding:12px 16px;margin:0 24px 8px 24px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.25);font-size:12px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow-xs);flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--amber-500);display:flex;">${sr.sparkles}</span>
                <span style="font-weight:700;color:var(--indigo-600);">
                  Rekomendasi Balasan AI:
                </span>
                <span style="color:var(--text-secondary);font-style:italic;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:380px;">
                  "Halo Pak, untuk negosiasi diskon penawaran kami diskusikan dengan Head of Sales ya..."
                </span>
              </div>
              <button
                id="wa-apply-suggestion-btn"
                style="padding:5px 12px;border-radius:var(--radius-lg);background:var(--indigo-600);color:#fff;font-weight:700;font-size:11px;border:none;cursor:pointer;"
              >
                Gunakan Draft AI
              </button>
            </div>
          `:``}

          <!-- Chat Input Bar -->
          <form
            id="wa-send-form"
            style="padding:16px 24px;background:var(--bg-card);border-top:1px solid var(--border-light);display:flex;align-items:center;gap:12px;flex-shrink:0;"
          >
            <input
              id="wa-chat-input"
              type="text"
              placeholder="Ketik balasan WhatsApp resmi ke klien..."
              value="${or}"
              style="flex:1;padding:10px 16px;border-radius:var(--radius-xl);background:var(--bg-surface);border:1px solid var(--border-light);font-size:12px;color:var(--text-primary);outline:none;transition:border-color var(--transition);"
            />
            <button
              type="submit"
              style="padding:10px 20px;border-radius:var(--radius-xl);background:var(--emerald-600);color:#fff;font-weight:700;font-size:12px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,.3);display:flex;align-items:center;gap:6px;transition:all var(--transition);"
            >
              ${sr.send}
              <span>Kirim</span>
            </button>
          </form>
        </div>
      `:`
        <div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:13px;">
          Pilih percakapan WhatsApp dari daftar di sebelah kiri.
        </div>
      `}
    </div>
  `}function lr(e){e.querySelectorAll(`.wa-filter-btn[data-mode]`).forEach(t=>{t.addEventListener(`click`,()=>{Q=t.dataset.mode,dr(e)})}),e.querySelectorAll(`.wa-chat-item[data-chat-id]`).forEach(t=>{t.addEventListener(`click`,()=>{let n=t.dataset.chatId;b.setActiveWaId(n),dr(e),ur(e)})});let t=e.querySelector(`#wa-ai-mode-select`);t&&t.addEventListener(`change`,t=>{let{activeWaId:n}=b.getState();n&&(b.setWaAiMode(n,t.target.value),window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Mode AI WhatsApp diperbarui menjadi: ${t.target.value.toUpperCase()}`,type:`info`}})),dr(e))});let n=e.querySelector(`#wa-open-lead-btn`);n&&n.addEventListener(`click`,()=>{let e=n.dataset.leadId;e&&b.navigateToLeadDetail(e)});let r=e.querySelector(`#wa-assign-sales-btn`);r&&r.addEventListener(`click`,()=>{window.dispatchEvent(new CustomEvent(`show-toast`,{detail:{message:`Lead Sales Executive telah ditugaskan untuk mengambil alih percakapan ini.`,type:`success`}}))});let i=e.querySelector(`#wa-apply-suggestion-btn`);i&&i.addEventListener(`click`,()=>{or=`Halo Pak Kevin, mengenai permintaan penyesuaian budget 25%, kami perlu mengajukan approval khusus ke Sales Director. Apakah berkenan kita jadwalkan Google Meet besok jam 14:00?`;let t=e.querySelector(`#wa-chat-input`);t&&(t.value=or,t.focus())});let a=e.querySelector(`#wa-chat-input`);a&&a.addEventListener(`input`,e=>{or=e.target.value});let o=e.querySelector(`#wa-send-form`);o&&o.addEventListener(`submit`,t=>{t.preventDefault();let{activeWaId:n}=b.getState();or.trim()&&n&&(b.sendWaMessage(n,or.trim()),or=``,dr(e),ur(e))})}function ur(e){setTimeout(()=>{let t=e.querySelector(`#wa-messages-container`);t&&(t.scrollTop=t.scrollHeight)},50)}function dr(e){e.innerHTML=cr(),lr(e)}function fr(e){return dr(e),ur(e),b.subscribe(()=>{b.getState().activeView===`client_care`&&dr(e)})}var pr=t({mount:()=>mr}),$={barChart:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>`,trendingUp:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>`,target:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,dollar:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`,users:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,award:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>`,shieldCheck:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>`,printer:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>`,arrowUpRight:`<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>`};function mr(e){let t=null;function n(){let t=b.getState(),n=t.leads||[],r=t.projects||[],i=n.filter(e=>e.stage_id===`deal`).reduce((e,t)=>e+(t.est_value||0),0),o=n.filter(e=>e.stage_id!==`deal`&&e.stage_id!==`reject`).reduce((e,t)=>e+(t.est_value||0),0);n.filter(e=>e.stage_id===`reject`).reduce((e,t)=>e+(t.est_value||0),0);let s=n.filter(e=>e.stage_id===`deal`).length,c=n.filter(e=>e.stage_id!==`deal`&&e.stage_id!==`reject`).length,l=n.length>0?Math.round(s/n.length*100):0,u=r.filter(e=>e.status===`in_progress`).length,d=r.filter(e=>e.status===`completed`).length,f=r.reduce((e,t)=>e+(t.contract_value||0),0),p={};n.forEach(e=>{let t=e.source||`Lainnya`;p[t]||(p[t]={count:0,total:0,wonCount:0}),p[t].count++,p[t].total+=e.est_value||0,e.stage_id===`deal`&&p[t].wonCount++});let m=Math.max(1,n.length);e.innerHTML=`
      <div style="flex:1;display:flex;flex-direction:column;min-height:100%;background:var(--bg-main);padding:24px;gap:24px;overflow-y:auto;">
        
        <!-- Top Header Card -->
        <div style="background:var(--bg-card);padding:20px 24px;border-radius:var(--radius-xl);border:1px solid var(--border-color);box-shadow:var(--shadow-xs);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
          <div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
              <h1 style="font-size:18px;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;margin:0;">
                Laporan Eksekutif, Sales Funnel & ROI Kanal Akuisisi
              </h1>
              <span style="padding:3px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:800;background:rgba(99,102,241,0.1);color:var(--indigo-600);border:1px solid rgba(99,102,241,0.25);">
                PRD Section 20
              </span>
              <span style="padding:3px 8px;border-radius:var(--radius-sm);font-size:10px;font-weight:700;background:rgba(16,185,129,0.1);color:var(--emerald-600);border:1px solid rgba(16,185,129,0.25);">
                Real-Time Synced
              </span>
            </div>
            <p style="font-size:12px;color:var(--text-muted);margin:6px 0 0 0;">
              Visibilitas performa pipeline end-to-end dari lead capture hingga cash collection dan analisis efektivitas sumber lead.
            </p>
          </div>

          <div style="display:flex;align-items:center;gap:8px;">
            <button id="btn-print-report" class="btn btn-outline" style="font-size:12px;height:34px;padding:0 12px;gap:6px;">
              ${$.printer} Cetak Ringkasan
            </button>
            <button id="btn-goto-kanban" class="btn btn-primary" style="font-size:12px;height:34px;padding:0 14px;gap:6px;">
              Buka Pipeline Kanban ${$.arrowUpRight}
            </button>
          </div>
        </div>

        <!-- 4 Key Performance Indicators (KPIs) -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;">
          
          <!-- KPI 1: Won Revenue -->
          <div style="background:var(--bg-card);padding:20px;border-radius:var(--radius-xl);border:1px solid var(--border-color);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
                Total Revenue Won (Deal)
              </span>
              <span style="width:28px;height:28px;border-radius:var(--radius-md);background:rgba(16,185,129,0.1);color:var(--emerald-600);display:flex;align-items:center;justify-content:center;">
                ${$.award}
              </span>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:var(--font-mono);color:var(--emerald-600);letter-spacing:-0.02em;">
              ${a(i)}
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
              <span style="font-size:11px;font-weight:700;color:var(--emerald-600);">
                ${s} Kontrak Signed (${l}% Win Rate)
              </span>
            </div>
          </div>

          <!-- KPI 2: Active Pipeline -->
          <div style="background:var(--bg-card);padding:20px;border-radius:var(--radius-xl);border:1px solid var(--border-color);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
                Active Pipeline Value
              </span>
              <span style="width:28px;height:28px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                ${$.trendingUp}
              </span>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:var(--font-mono);color:var(--indigo-600);letter-spacing:-0.02em;">
              ${a(o)}
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
              <span style="font-size:11px;font-weight:700;color:var(--indigo-600);">
                ${c} Prospek Aktif Berjalan
              </span>
            </div>
          </div>

          <!-- KPI 3: Lead-to-Quote Velocity -->
          <div style="background:var(--bg-card);padding:20px;border-radius:var(--radius-xl);border:1px solid var(--border-color);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
                Waktu Lead → Quotation
              </span>
              <span style="width:28px;height:28px;border-radius:var(--radius-md);background:rgba(245,158,11,0.1);color:#d97706;display:flex;align-items:center;justify-content:center;">
                ${$.target}
              </span>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:var(--font-mono);color:var(--text-primary);letter-spacing:-0.02em;">
              ≤ 1 Hari (Target KPI)
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
              <span style="font-size:11px;color:var(--text-muted);">
                Didukung Generator AI Mandays & RAB
              </span>
            </div>
          </div>

          <!-- KPI 4: TOP Compliance & Gate Rules -->
          <div style="background:var(--bg-card);padding:20px;border-radius:var(--radius-xl);border:1px solid var(--border-color);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
                Adopsi Setting TOP Standar
              </span>
              <span style="width:28px;height:28px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);color:var(--indigo-600);display:flex;align-items:center;justify-content:center;">
                ${$.shieldCheck}
              </span>
            </div>
            <div style="font-size:20px;font-weight:800;font-family:var(--font-mono);color:var(--indigo-600);letter-spacing:-0.02em;">
              100% Balance
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
              <span style="font-size:11px;color:var(--text-muted);">
                Validasi termin 100% sebelum invoice terbit
              </span>
            </div>
          </div>

        </div>

        <!-- 2 Column Section: Sales Funnel & Lead Source Attribution -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(420px, 1fr));gap:24px;">

          <!-- Funnel Conversion Column -->
          <div style="background:var(--bg-card);border-radius:var(--radius-xl);border:1px solid var(--border-color);padding:22px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:18px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--indigo-600);">${$.barChart}</span>
                <h3 style="font-size:13px;font-weight:800;color:var(--text-primary);text-transform:uppercase;letter-spacing:0.04em;margin:0;">
                  Sales Funnel Conversion per Stage
                </h3>
              </div>
              <span style="font-size:11px;font-weight:700;color:var(--text-muted);">
                Total: ${n.length} Leads
              </span>
            </div>

            <!-- Stages List -->
            <div style="display:flex;flex-direction:column;gap:14px;">
              ${y.map(e=>{let t=n.filter(t=>t.stage_id===e.id),r=t.length,i=t.reduce((e,t)=>e+(t.est_value||0),0),o=Math.round(r/m*100);return`
                  <div style="display:flex;flex-direction:column;gap:5px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;">
                      <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:8px;height:8px;border-radius:50%;background-color:${e.color};display:inline-block;"></span>
                        <span style="font-weight:700;color:var(--text-primary);">${e.name}</span>
                      </div>
                      <div style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--text-secondary);">
                        ${r} Leads <span style="color:var(--text-muted);font-weight:500;">(${a(i)})</span>
                      </div>
                    </div>
                    
                    <div style="width:100%;height:10px;border-radius:var(--radius-full);background:var(--border-color);overflow:hidden;position:relative;">
                      <div style="height:100%;border-radius:var(--radius-full);background-color:${e.color};width:${Math.max(4,o)}%;transition:width 0.4s ease;"></div>
                    </div>
                  </div>
                `}).join(``)}
            </div>

            <div style="margin-top:auto;padding-top:14px;border-top:1px dashed var(--border-color);display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--text-muted);">
              <span>Stage Gate Rules: Mandatory RAB & TOP Validated</span>
              <span style="color:var(--emerald-600);font-weight:700;">Zero Data Loss</span>
            </div>
          </div>

          <!-- Lead Source Channel Attribution Column -->
          <div style="background:var(--bg-card);border-radius:var(--radius-xl);border:1px solid var(--border-color);padding:22px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:18px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--emerald-600);">${$.users}</span>
                <h3 style="font-size:13px;font-weight:800;color:var(--text-primary);text-transform:uppercase;letter-spacing:0.04em;margin:0;">
                  Performa Saluran Akuisisi (Attribution)
                </h3>
              </div>
              <span style="font-size:11px;font-weight:700;color:var(--text-muted);">
                ${Object.keys(p).length} Kanal Terpantau
              </span>
            </div>

            <!-- Source Cards List -->
            <div style="display:flex;flex-direction:column;gap:10px;">
              ${Object.entries(p).map(([e,t])=>`
                  <div style="padding:12px 14px;border-radius:var(--radius-lg);background:var(--bg-subtle);border:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div style="display:flex;flex-direction:column;gap:2px;">
                      <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:12px;font-weight:800;color:var(--text-primary);">${e}</span>
                        <span style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:var(--radius-full);background:rgba(99,102,241,0.1);color:var(--indigo-600);">
                          ${n.length>0?Math.round(t.count/n.length*100):0}%
                        </span>
                      </div>
                      <span style="font-size:11px;color:var(--text-muted);">
                        ${t.count} Peluang Masuk • ${t.wonCount} Menang
                      </span>
                    </div>

                    <div style="text-align:right;">
                      <div style="font-family:var(--font-mono);font-size:13px;font-weight:800;color:var(--emerald-600);">
                        ${a(t.total)}
                      </div>
                      <span style="font-size:10px;font-weight:700;color:var(--indigo-500);">
                        Attribution OK
                      </span>
                    </div>
                  </div>
                `).join(``)}
            </div>

            <!-- Meta Cloud API Attribution Tip -->
            <div style="margin-top:auto;padding:12px;border-radius:var(--radius-md);background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;gap:10px;">
              <span style="color:var(--indigo-600);">${$.shieldCheck}</span>
              <p style="font-size:11px;color:var(--text-secondary);margin:0;line-height:1.4;">
                Setiap lead WhatsApp secara otomatis diberi tag attribution <strong>Meta Cloud API</strong> dengan nomor pengirim terverifikasi.
              </p>
            </div>
          </div>

        </div>

        <!-- Row 3: Delivery Realization & Executive Insights -->
        <div style="background:var(--bg-card);border-radius:var(--radius-xl);border:1px solid var(--border-color);padding:22px;box-shadow:var(--shadow-xs);display:flex;flex-direction:column;gap:16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="color:var(--indigo-600);">${$.dollar}</span>
              <h3 style="font-size:13px;font-weight:800;color:var(--text-primary);text-transform:uppercase;letter-spacing:0.04em;margin:0;">
                Realisasi Kontrak ke Proyek & Cashflow Handover
              </h3>
            </div>
            <div style="display:flex;gap:12px;font-size:12px;color:var(--text-secondary);">
              <span>Proyek Berjalan: <strong style="color:var(--text-primary);">${u}</strong></span>
              <span>Proyek Selesai: <strong style="color:var(--emerald-600);">${d}</strong></span>
              <span>Total Nilai Delivery: <strong style="color:var(--indigo-600);font-family:var(--font-mono);">${a(f)}</strong></span>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:16px;">
            <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-subtle);border:1px solid var(--border-color);display:flex;flex-direction:column;gap:6px;">
              <div style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;">
                SLA Handover Sales ke PM
              </div>
              <div style="font-size:14px;font-weight:700;color:var(--text-primary);">
                Maksimal 1 Jam Pasca Deal
              </div>
              <p style="font-size:11px;color:var(--text-muted);margin:0;">
                Otomasi pembuatan workspace proyek saat status lead berubah menjadi 'Deal'.
              </p>
            </div>

            <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-subtle);border:1px solid var(--border-color);display:flex;flex-direction:column;gap:6px;">
              <div style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;">
                Integritas Dokumen Kontrak (22 Pasal MoU)
              </div>
              <div style="font-size:14px;font-weight:700;color:var(--emerald-600);">
                100% Klausul Standar KKI
              </div>
              <p style="font-size:11px;color:var(--text-muted);margin:0;">
                Pasal 10 (Force Majeure), Pasal 11 (Kerahasiaan/NDA), dan Pasal 14 (Hak Cipta) terkunci otomatis.
              </p>
            </div>

            <div style="padding:14px;border-radius:var(--radius-lg);background:var(--bg-subtle);border:1px solid var(--border-color);display:flex;flex-direction:column;gap:6px;">
              <div style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;">
                Pemberitahuan Otomatis Klien
              </div>
              <div style="font-size:14px;font-weight:700;color:var(--indigo-600);">
                WhatsApp Webhook Active
              </div>
              <p style="font-size:11px;color:var(--text-muted);margin:0;">
                Notifikasi milestone dan invoice terkirim langsung ke kontak penanggung jawab klien.
              </p>
            </div>
          </div>
        </div>

      </div>
    `;let h=e.querySelector(`#btn-print-report`);h&&h.addEventListener(`click`,()=>{window.print()});let g=e.querySelector(`#btn-goto-kanban`);g&&g.addEventListener(`click`,()=>{b.setState({activeView:`pipeline`,viewMode:`kanban`})})}return n(),t=b.subscribe(()=>{n()}),()=>{typeof t==`function`&&t()}}var hr={pipeline_kanban:ke,pipeline_table:Ve,pipeline_workflow:Qe,lead_detail:ht,customers:Nt,projects:qt,finance_top:mn,contracts_legal:wn,client_care:ar,reports:pr},gr={success:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`,error:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>`,warning:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>`,info:`<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>`};function _r(e,t=`info`,n=3500){let r=document.getElementById(`toast-container`);if(!r)return;let i=document.createElement(`div`);i.className=`toast toast-${t}`,i.innerHTML=`
    <span style="display:flex;align-items:center;flex-shrink:0;">
      ${gr[t]||gr.info}
    </span>
    <span style="flex:1;line-height:1.4;">${e}</span>
  `,r.appendChild(i),setTimeout(()=>{i.style.opacity=`0`,i.style.transform=`translateX(20px)`,i.style.transition=`all 0.25s ease`,setTimeout(()=>{i.parentNode===r&&r.removeChild(i)},250)},n)}window.addEventListener(`show-toast`,e=>{e.detail&&_r(e.detail.message,e.detail.type||`info`,e.detail.duration||3500)});function vr(e){e?document.documentElement.classList.add(`dark`):document.documentElement.classList.remove(`dark`)}var yr={},br=null,xr=null,Sr=null;function Cr(){let e=document.getElementById(`view-container`);if(!e)return;let{activeView:t,viewMode:n,selectedLeadId:r}=b.getState(),i=t===`pipeline`?`pipeline_${n}`:t;if(br===i&&xr===n&&(t!==`lead_detail`||t===`lead_detail`&&r===Sr))return;if(br=i,xr=n,Sr=r,e.innerHTML=``,typeof yr[i]==`function`)try{yr[i](e);return}catch(e){console.error(`[ASTACODE CRM] Error rendering registered view "${i}":`,e)}let a=hr[i];if(a&&typeof a.mount==`function`)try{a.mount(e);return}catch(t){console.error(`[ASTACODE CRM] Error mounting view "${i}":`,t),e.innerHTML=`
        <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;padding:32px;text-align:center;">
          <div style="width:54px;height:54px;border-radius:14px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:24px;">
            ⚠️
          </div>
          <div>
            <h2 style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">
              Gagal Membuka Tampilan: ${i}
            </h2>
            <p style="font-size:13px;color:var(--text-muted);max-width:440px;">
              ${t.message||String(t)}
            </p>
          </div>
        </div>
      `;return}e.innerHTML=`
    <div style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;padding:32px;text-align:center;">
      <div style="width:54px;height:54px;border-radius:var(--radius-xl);background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;color:var(--indigo-500);font-size:24px;">
        ✨
      </div>
      <div>
        <h2 style="font-size:16px;font-weight:800;color:var(--text-primary);margin-bottom:6px;">
          Workspace View: ${t.toUpperCase()} ${t===`pipeline`?`(${n.toUpperCase()})`:``}
        </h2>
        <p style="font-size:13px;color:var(--text-muted);max-width:440px;">
          Modul siap dihubungkan. State store reactive, filter, dan data seed telah aktif.
        </p>
      </div>
    </div>
  `}function wr(){let e=document.getElementById(`app`);if(!e){console.error(`Fatal: #app root element not found in DOM.`);return}vr(b.getState().isDarkMode),e.innerHTML=`
    <div class="app-shell">
      <!-- Left Sidebar Container -->
      <div id="sidebar-container" style="height:100%;"></div>

      <!-- Right Main Area -->
      <div class="main-content">
        <!-- Top Sticky Header Container -->
        <div id="header-container"></div>

        <!-- Dynamic Main View Container -->
        <main id="view-container" class="page-area"></main>
      </div>
    </div>
  `;let t=document.getElementById(`sidebar-container`);t&&ue(t);let n=document.getElementById(`header-container`);n&&me(n),xe(),Oe(),Cr(),window.__crmAppLoaded=!0,window.__crmLoadTimeout&&clearTimeout(window.__crmLoadTimeout),b.subscribe(e=>{vr(e.isDarkMode),Cr()}),window.addEventListener(`route-change`,()=>{br=null,Cr()}),console.log(`✅ ASTACODE CRM (Vanilla JS) initialized successfully.`)}function Tr(){try{wr()}catch(e){console.error(`[ASTACODE CRM Fatal Init Error]`,e),typeof window.__showCrmLoadError==`function`&&window.__showCrmLoadError(`init_error`,e.message||String(e))}}document.readyState===`loading`?document.addEventListener(`DOMContentLoaded`,Tr):Tr();
//# sourceMappingURL=index-Plvu2Qhf.js.map