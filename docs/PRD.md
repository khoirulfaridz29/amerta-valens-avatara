# PRD — Sistem Administrasi Rental Alat Berat (Excavator)

> Status: **Draft**
> Versi: 0.2 | Tanggal: 2026-10-02 | Owner: [isi]
> Catatan: **Living document.** Fitur yang belum jelas dari customer ditandai `TBD` dan dicatat di bagian 15 (Open Questions). Setiap perubahan requirement menaikkan versi.

---

## 1. Executive Summary

Sistem web untuk mencatat dan memantau operasional usaha rental alat berat (excavator) dengan dua peran: **Bos (pemilik)** dan **Operator**. Tujuan utamanya: Bos bisa mengetahui **posisi alat**, **arus kas masuk/keluar**, dan **pemakaian anggaran** tanpa harus bertanya manual ke operator. Operator mengirim **laporan harian** dari lapangan langsung dari HP. Fokus awal bukan menggantikan seluruh pembukuan, melainkan menghilangkan kebocoran informasi: laporan hilang, posisi alat tidak jelas, dan kas tidak tercatat. Target pemakaian kecil namun bertumbuh (1 Bos + beberapa operator, jumlah alat bertambah), banyak diakses dari HP, sebagian dari laptop. Dampak yang diharapkan: Bos punya gambaran operasional dan keuangan yang selalu terbarui dalam satu tempat.

---

## 2. Problem Statement

- **Masalah:** Pencatatan operasional alat berat saat ini manual/tidak terpusat. Bos tidak tahu posisi alat terkini, siapa yang memegang, berapa pemakaian solar, dan berapa kas tersisa, kecuali menanyakan langsung kepada operator.
- **Bukti masalah valid:**
  - Sistem sebelumnya berupa satu file HTML + Firebase yang belum dijalankan sungguhan (lihat `index.html` di repo ini) → menunjukkan kebutuhan pencatatan sudah nyata, tapi belum ada solusi yang dipakai.
  - `TBD` — perlu kutipan langsung dari Bos/operator: berapa kali laporan harian hilang/terlambat per bulan, dan berapa lama waktu untuk merekap kas saat ini.

---

## 3. Goals & Success Metrics

- **North star metric:** Persentase hari operasional yang laporan hariannya tercatat lengkap (laporan masuk / hari kerja alat). Target: ≥ 95%.
- **KPI:**
  - Waktu input laporan harian oleh operator: baseline `TBD` (manual) → target **< 1 menit** per laporan.
  - Waktu Bos merekap kas mingguan: baseline `TBD` → target **< 5 menit** (otomatis dari sistem).
  - Saldo kas & posisi alat terakhir selalu dapat dilihat < 10 detik setelah login.
- **Guardrail metrics:**
  - Tidak boleh ada data transaksi yang hilang (0 kehilangan data tercatat).
  - Aplikasi tidak boleh error (5xx) pada alur inti: login, input laporan, input kas.

---

## 4. Target Users

- **Persona 1 — Bos / Pemilik:** Pemilik usaha rental excavator. Konteks: memantau dari HP saat di luar, kadang dari laptop. Skill teknis: menengah-rendah (bisa WhatsApp, browser, tidak teknis). Kebutuhan: lihat posisi alat, saldo kas, pemakaian anggaran, laporan operator.
- **Persona 2 — Operator:** Pengguna alat di lapangan. Konteks: hampir selalu dari HP, kadang sinyal lemah. Skill teknis: rendah. Kebutuhan: kirim laporan harian dengan sedikit langkah, lihat riwayat laporan sendiri.
- **Segmen & prioritas:**
  1. Bos/admin (prioritas 1)
  2. Operator (prioritas 2)
- **Batasan user:** bukan untuk pelanggan umum, bukan POS, bukan multi-tenant banyak perusahaan. Jumlah pengguna kecil tapi **bertumbuh**: 1 Bos + N operator (operator bisa ditambah kapan saja), dan jumlah **alat juga bertambah** seiring waktu.

---

## 5. User Stories

- **US-01** — Sebagai **Bos**, saya ingin **melihat posisi/lokasi terakhir setiap alat**, supaya **saya tahu alat ada di mana tanpa bertanya**.
  - Acceptance Criteria:
    - [ ] Halaman daftar alat menampilkan nama alat, lokasi/kontrak terakhir, operator, dan tanggal update.
    - [ ] Update lokasi dari laporan operator terbaru (atau input manual oleh Bos).
  - Edge case: alat belum pernah dilaporkan → tampil "Belum ada lokasi".
- **US-02** — Sebagai **Operator**, saya ingin **mengirim laporan harian (tanggal, lokasi, jam kerja/HM, solar, keterangan)**, supaya **Bos tahu pekerjaan hari itu**.
  - Acceptance Criteria:
    - [ ] Form dapat diselesaikan < 1 menit di HP.
    - [ ] Validasi: tanggal & lokasi & HM wajib; solar opsional.
    - [ ] Konfirmasi sukses + laporan muncul di riwayat.
  - Edge case: gagal simpan (koneksi terputus) → pesan error jelas, data tidak hilang dari form.
- **US-03** — Sebagai **Bos**, saya ingin **mencatat kas masuk/keluar**, supaya **saldo selalu terbarui**.
  - Acceptance Criteria:
    - [ ] Input debit/kredit + nominal + keterangan.
    - [ ] Saldo total, total masuk, total keluar terhitung otomatis.
  - Edge case: nominal 0 / karakter non-angka ditolak.
- **US-04** — Sebagai **Bos**, saya ingin **menetapkan dan memantau anggaran per proyek/lokasi**, supaya **saya tahu apakah pengeluaran sebuah proyek melebihi rencana**.
  - Acceptance Criteria:
    - [ ] Bos membuat proyek/lokasi dan menetapkan nilai anggarannya.
    - [ ] Sistem menampilkan realisasi (pengeluaran) vs anggaran per proyek + sisa.
  - Edge case: proyek belum diberi anggaran → tampil "Belum ditetapkan".
- **US-05** — Sebagai **Bos**, saya ingin **mengunduh laporan (kas/laporan harian)**, supaya **bisa diarsipkan**.
  - Acceptance Criteria: `TBD` (format CSV/PDF belum diputuskan).
- **US-06** — Sebagai **Bos**, saya ingin **menambah dan mengubah data alat**, supaya **sistem mengikuti jumlah alat yang terus bertambah**.
  - Acceptance Criteria:
    - [ ] Tambah/ubah/nonaktifkan alat (nama, jenis, kode, status).
    - [ ] Alat dipilih saat operator mengirim laporan.
  - Edge case: alat dinonaktifkan → laporan lama tetap tampil, alat tidak muncul di pilihan baru.
- **US-07** — Sebagai **Bos**, saya ingin **menambah akun operator baru**, supaya **operator baru bisa langsung memakai sistem**.
  - Acceptance Criteria:
    - [ ] Bos membuat akun operator (email + password).
    - [ ] Operator baru bisa login dengan peran Operator.
  - Edge case: email sudah terdaftar → error jelas.

---

## 6. Functional Requirements

- **FR-01** Autentikasi: login dengan email + password; 2 peran (Bos/admin, Operator). Akses halaman dibatasi per peran.
- **FR-02** Manajemen pengguna oleh Bos: **tambah**/ubah/nonaktifkan akun operator (operator bisa bertambah kapan saja).
- **FR-03** Manajemen data alat (**multi-alat**): tambah/ubah/nonaktifkan alat; field: nama, jenis, kode/nomor lambung, status (aktif/perbaikan/idle) `TBD`.
- **FR-04** Pencatatan posisi/lokasi alat: proyek/lokasi terkini + operator penanggung jawab + waktu update.
- **FR-05** Laporan harian operator: tanggal, alat, proyek/lokasi, HM (jam kerja), jumlah solar (jerigen/liter), keterangan/kendala.
- **FR-06** Riwayat laporan: operator melihat laporannya sendiri; Bos melihat semua.
- **FR-07** Buku kas: transaksi debit (masuk) & kredit (keluar), nominal, keterangan, tanggal.
- **FR-08** Ringkasan keuangan: total masuk, total keluar, saldo/laba per periode.
- **FR-09** Anggaran **per proyek/lokasi**: Bos menetapkan nilai anggaran per proyek; sistem menampilkan realisasi (pengeluaran terkait proyek) vs anggaran + sisa.
- **FR-10** Dashboard Bos: ringkasan posisi semua alat + saldo kas + pemakaian anggaran per proyek dalam satu layar.
- **FR-11** Ekspor laporan (CSV) `TBD`.
- **FR-12** Update tampilan data terbaru tanpa reload penuh (near-realtime via polling interval) `TBD`.
- **FR-13** Manajemen proyek/lokasi: tambah/ubah proyek (nama, lokasi, anggaran).

---

## 7. Non-Functional Requirements

- **Performa:** LCP ≤ 2.5s pada 4G; respons halaman inti < 1s; ukuran halaman awal wajar untuk HP mid/low-end. Hindari aset berat.
- **Responsif (WAJIB):** mobile-first; nyaman dipakai dari **320px** (HP kecil) sampai **≥ 1440px** (laptop). Tanpa scroll horizontal. Target sentuh ≥ 44×44px. Tabel data berubah jadi kartu/list di layar kecil.
- **Aksesibilitas:** WCAG 2.2 AA; kontras memenuhi AA; navigasi keyboard + focus terlihat; label form eksplisit; teks ≥ 16px di HP.
- **Security:** password hashed (bcrypt/argon2 — bawaan Laravel); proteksi CSRF (bawaan Laravel); validasi & otorisasi server-side; HTTPS wajib; tidak ada secret di kode; akses data dibatasi per peran (operator tidak bisa lihat data Bos / pengaturan).
- **Reliability:** alur inti bebas 5xx; pesan error non-teknis yang jelas; backup/ekspor data berkala (mengingat hosting shared).
- **Scalability:** desain untuk ≤ 10 pengguna dan ribuan baris data; belum perlu skala besar.
- **Privacy/Compliance:** data keuangan & lokasi bersifat internal; jangan log data sensitif.
- **Maintainability:** ikuti konvensi Laravel; file non-test < 300 baris; fungsi kompleksitas rendah; coverage ≥ 80% pada file yang berubah.
- **UI modern & anti-AI-slop (WAJIB):** pakai design token (warna, spacing, radius, tipografi), bukan hardcode; hindari template generik/khas-AI; keputusan desain punya alasan; konsisten di semua halaman.
- **Tanpa bug pada alur inti (WAJIB):** login, input laporan, input kas, lihat dashboard harus teruji (unit + alur utama) sebelum dianggap selesai.

---

## 8. Scope

**In-scope (Fase 1 — MVP):**
- Login + 2 peran (Bos, Operator).
- Manajemen **multi-alat** (tambah/ubah/nonaktifkan) + posisi/lokasi terkini.
- Manajemen **proyek/lokasi**.
- Manajemen pengguna: Bos bisa **menambah akun operator**.
- Laporan harian operator (per alat) + riwayat.
- Buku kas (debit/kredit) + ringkasan saldo.
- **Anggaran per proyek/lokasi** + realisasi vs anggaran.
- Dashboard Bos ringkas.
- Responsif penuh (HP + laptop), UI modern.

**In-scope (Fase 2 — setelah MVP dipakai):**
- Ekspor laporan (CSV/PDF).
- Near-realtime (polling) dashboard.
- Log aktivitas & audit.
- Rekap laporan per proyek/per alat.

**Out-of-scope (eksplisit, TIDAK dikerjakan sekarang):**
- POS / penjualan barang / stok retail.
- Pembayaran online (QRIS/gateway), invoice ke pelanggan.
- Integrasi GPS pelacak alat sungguhan.
- Aplikasi mobile native (Android/iOS).
- WebSocket realtime penuh, chat, notifikasi push.
- Multi-perusahaan / multi-tenant.

---

## 9. Design & UX

- **User flow (ringkas):**
  - Operator: Login → Dashboard sederhana → Form laporan harian → Simpan → Riwayat.
  - Bos: Login → Dashboard (posisi alat + saldo + anggaran) → Buku kas → Anggaran per proyek → Laporan operator.
- **Konsep & interaksi:**
  - Mobile-first: navigasi bawah (bottom nav) di HP, sidebar di laptop.
  - Aksen warna hangat/industrial (nuansa alat berat) — dipertajam di `styleguide.md`.
  - Semua state wajib dirancang: **empty** ("Belum ada laporan"), **loading** (skeleton), **error** (pesan jelas + cara ulang), **success** (konfirmasi).
- **Kebutuhan a11y:** kontras AA, ukuran target sentuh, urutan tab logis, form punya label, tidak mengandalkan warna saja untuk status.

---

## 10. Technical Considerations

- **Stack:** Laravel (versi terbaru) + MySQL/MariaDB. Blade + sedikit JS (Alpine/vanilla) — bukan SPA penuh, demi ringan & sederhana.
- **Hosting target:** **InfinityFree** (PHP 8.4, MySQL 8 / MariaDB 11.4, 5 GB disk, SSL gratis). Implikasi:
  - Tidak ada SSH/CLI → `composer install` di lokal, unggah `vendor` via FTP.
  - Tidak ada cron & queue → scheduler dijalankan via layanan cron eksternal bila perlu; hindari queue.
  - Document root `htdocs` → seluruh Laravel di luar `htdocs`, isi `public/` di `htdocs`.
  - Tanpa WebSocket → realtime diganti polling.
- **Dependencies/integrasi:** tidak ada API eksternal wajib. Notifikasi/ekspor ditunda ke Fase 2.
- **Data model (entitas utama):** `users` (role: bos/operator), `proyek` (nama, lokasi, anggaran), `alat` (multi, milik/berada di proyek), `laporan_harian` (terkait alat + proyek + operator), `transaksi_kas` (terkait proyek, opsional).
- **Risiko teknis:**
  - Deploy manual (FTP + `public/`) rawan salah konfigurasi → perlu panduan langkah demi langkah.
  - Batas hit/CPU InfinityFree → pantau; untuk 2 user seharusnya aman.
  - Koneksi DB dari shared hosting → pastikan host MySQL memakai kredensial milik hosting yang sama (bukan DB eksternal).

---

## 11. Success Metrics (pengukuran)

- **Cara mengukur:**
  - Laporan harian tercatat: hitung dari tabel `laporan_harian` per hari kerja.
  - Waktu input: uji manual stopwatch oleh Bos/operator.
  - Error sistem: pantau log Laravel + tangkapan error.
- **Alat ukur:** log aplikasi (Laravel log), ekspor data periodik, feedback langsung dari 2 pengguna. (Analytics pihak ketiga belum diperlukan.)

---

## 12. Risks & Mitigations

| Risiko | Probabilitas | Dampak | Mitigasi |
|---|---|---|---|
| Fitur dari customer belum pasti → scope melebar | Tinggi | Tinggi | PRD living + scope per fase; perubahan lewat revisi PRD |
| Deploy InfinityFree (document root, FTP) gagal | Sedang | Tinggi | Panduan deploy rinci + uji di staging subdomain dulu |
| Laporan hilang saat koneksi putus | Sedang | Sedang | Simpan draft di sisi klien + pesan error jelas |
| Data hilang karena hosting shared | Rendah | Tinggi | Ekspor/backup berkala ke file |
| UI terasa "AI slop" / generik | Sedang | Sedang | Ikuti styleguide + anti-slop checklist; desain punya alasan |
| Tidak ada cron/queue di hosting gratis | Tinggi | Rendah | Hindari queue; scheduler via cron eksternal bila perlu |
| Kebutuhan realtime ternyata wajib | Sedang | Sedang | Polling near-realtime; WebSocket = di luar InfinityFree → tinjau ulang |

---

## 13. Launch & Rollout

- **Fase rilis:**
  1. Dev lokal (Laragon).
  2. Staging di subdomain InfinityFree gratis.
  3. Produksi (subdomain `.rf.gd`/`.great-site.net` atau domain sendiri) setelah uji alur inti.
- **Feature flag:** tidak perlu (app kecil).
- **Rollback plan:** simpan salinan folder rilis terakhir + backup DB sebelum update; bila gagal, unggah ulang versi sebelumnya.

---

## 14. Open Questions

Hal-hal yang belum dipastikan customer. Ini yang menentukan bentuk akhir:

**Sudah dipastikan (v0.2):**
- Alat **lebih dari satu**, dapat ditambah → multi-alat masuk MVP.
- Anggaran **per proyek/lokasi** → entitas `proyek` masuk MVP.
- Operator **dapat bertambah** → manajemen akun operator masuk MVP.

**Masih TBD:**
1. **Realtime:** Bos cukup buka halaman / refresh, atau butuh update otomatis (polling) / instan?
2. **Foto lampiran:** operator perlu unggah foto (alat, kondisi, nota) atau tidak?
3. **Excel/Export:** perlu unduh laporan (CSV/PDF) atau cukup lihat di layar?
4. **Notifikasi:** Bos perlu notifikasi (mis. laporan masuk) atau tidak?
5. **Lokasi alat:** cukup dari laporan harian operator, atau Bos juga input manual?
6. **Status alat:** perlu status (aktif/idle/perbaikan) atau tidak?
7. **Pembayaran sewa:** perlu catat tagihan/terima pembayaran ke pelanggan, atau hanya kas masuk/keluar?
8. **Siapa yang mengisi anggaran** proyek: hanya Bos, atau operator juga bisa menginput pengeluaran terkait proyek?
9. **Domain:** pakai subdomain gratis atau punya domain sendiri?
10. **Deadline & budget** pengerjaan.

---

## 15. Traceability

| FR | User Story | Test | Status |
|----|-----------|------|--------|
| FR-01 | US-01, US-02, US-03 | tests/Feature/AuthTest | Done |
| FR-02 | US-07 | tests/Feature/ProyekAlatTest | Done |
| FR-03 | US-01, US-06 | tests/Feature/ProyekAlatTest | Done |
| FR-04 | US-01 | tests/Feature/LaporanHarianTest | Done |
| FR-05 | US-02 | tests/Feature/LaporanHarianTest | Done |
| FR-06 | US-02 | tests/Feature/LaporanHarianTest | Done |
| FR-07 | US-03 | tests/Feature/KeuanganTest | Done |
| FR-08 | US-03 | tests/Feature/KeuanganTest | Done |
| FR-09 | US-04 | tests/Feature/KeuanganTest | Done |
| FR-10 | US-01, US-03, US-04 | (dashboard) | Done |
| FR-11 | US-05 | [belum] | Fase 2 |
| FR-12 | US-01 | [belum] | Fase 2 |
| FR-13 | US-04 | tests/Feature/ProyekAlatTest | Done |

---

**Catatan perubahan versi:**
- v0.2 (2026-10-02) — Multi-alat, anggaran per proyek/lokasi, dan operator dapat ditambah masuk MVP. Tambah entitas `proyek`, FR-13, US-06, US-07. Open Questions 1–3 selesai.
- v0.1 (2026-10-02) — Draft awal. Fitur inti dari deskripsi customer; detail yang belum jelas → Open Questions.
