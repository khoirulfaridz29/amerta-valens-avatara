# Task List — Sistem Administrasi Rental Alat Berat (Excavator)

Sumber: `docs/PRD.md` (v0.2) | Update terakhir: 2026-10-02

> **Status build:** TASK-001–010 (MVP) **selesai & terverifikasi lokal** (Pint bersih, 17 test hijau, app jalan di `http://localhost:8000`). Deploy ke hosting **ditunda atas permintaan user**.

Task = **vertical slice** (menembus DB → API/Controller → UI), bukan per-lapisan.
Urutan: **surface unknown lebih dulu** (deploy InfinityFree & auth dikerjakan paling awal).

**Status:** `Todo` | `In Progress` | `Review` | `Done` | `Blocked`

---

### TASK-001 — Scaffold Laravel + deploy skeleton ke InfinityFree
- **Deskripsi**: Buat project Laravel, set `.env`, halaman awal. Lalu deploy ke InfinityFree (trik document root + unggah `vendor`) sampai bisa dibuka lewat link HTTPS. Ini paling awal karena deploy = risiko terbesar.
- **Referensi FR/US**: PRD §10 (Technical)
- **Dependensi**: none
- **Estimasi**: 4 jam
- **Status**: Done
- **File terpengaruh**: seluruh scaffold, `.env.example`, `.gitignore`, `docs/deploy-infinityfree.md`
- **Acceptance Criteria**:
  - [ ] `php artisan serve` menampilkan halaman awal di lokal (Laragon)
  - [ ] Halaman yang sama terbuka di subdomain InfinityFree via HTTPS
  - [ ] `vendor/` terunggah dan app berjalan (tanpa SSH)
  - [ ] `.env` tidak ikut ter-commit; kredensial DB hanya di server
  - [ ] Panduan deploy tertulis di `docs/deploy-infinityfree.md`

### TASK-002 — Autentikasi + peran (Bos / Operator)
- **Deskripsi**: Login email+password, 2 peran, middleware otorisasi. Redirect sesuai peran.
- **Referensi FR/US**: FR-01 / US-02
- **Dependensi**: TASK-001
- **Estimasi**: 4 jam
- **Status**: Done
- **File terpengaruh**: `app/Http/Controllers/Auth`, `app/Http/Middleware/EnsureRole.php`, `routes/web.php`, `resources/views/auth`
- **Acceptance Criteria**:
  - [ ] Login/logout berfungsi; password ter-hash
  - [ ] Bos diarahkan ke dashboard Bos, Operator ke dashboard Operator
  - [ ] Operator mengakses URL Bos → ditolak (403/redirect)
  - [ ] Edge: kredensial salah → pesan error jelas, bukan stack trace
  - [ ] Feature test: login tiap peran + akses ditolak (hijau)

### TASK-003 — Manajemen pengguna (Bos menambah operator)
- **Deskripsi**: Bos membuat/ubah/nonaktifkan akun operator.
- **Referensi FR/US**: FR-02 / US-07
- **Dependensi**: TASK-002
- **Estimasi**: 3 jam
- **Status**: Done
- **File terpengaruh**: `UserController`, `resources/views/users`, `StoreUserRequest`
- **Acceptance Criteria**:
  - [ ] Bos dapat menambah akun operator (email + password)
  - [ ] Ubah/nonaktifkan akun; user nonaktif tidak bisa login
  - [ ] Edge: email duplikat → error validasi jelas
  - [ ] Empty state "Belum ada operator"
  - [ ] Feature test CRUD + validasi (hijau)

### TASK-004 — Manajemen proyek/lokasi
- **Deskripsi**: CRUD proyek (nama, lokasi, anggaran awal).
- **Referensi FR/US**: FR-13 / US-04
- **Dependensi**: TASK-002
- **Estimasi**: 3 jam
- **Status**: Done
- **File terpengaruh**: `ProyekController`, `Proyek` model + migrasi, `resources/views/proyek`
- **Acceptance Criteria**:
  - [ ] Tambah/ubah/hapus proyek; anggaran boleh kosong
  - [ ] Edge: hapus proyek yang punya data terkait → dicegah/dikonfirmasi
  - [ ] Empty state & loading state
  - [ ] Feature test CRUD (hijau)

### TASK-005 — Manajemen alat (multi-alat)
- **Deskripsi**: CRUD alat; alat terkait proyek penempatan.
- **Referensi FR/US**: FR-03, FR-04 / US-01, US-06
- **Dependensi**: TASK-004
- **Estimasi**: 3 jam
- **Status**: Done
- **File terpengaruh**: `AlatController`, `Alat` model + migrasi, `resources/views/alat`
- **Acceptance Criteria**:
  - [ ] Tambah/ubah/nonaktifkan alat (nama, jenis, kode, status)
  - [ ] Alat nonaktif tidak muncul di pilihan laporan, data lama tetap tampil
  - [ ] Edge: kode alat duplikat → dicegah
  - [ ] Empty state "Belum ada alat"
  - [ ] Feature test CRUD (hijau)

### TASK-006 — Laporan harian operator + update posisi alat
- **Deskripsi**: Operator input laporan (tanggal, alat, proyek, HM, solar, keterangan); posisi alat terbarui otomatis dari laporan terakhir.
- **Referensi FR/US**: FR-05, FR-06 / US-01, US-02
- **Dependensi**: TASK-005
- **Estimasi**: 5 jam
- **Status**: Done
- **File terpengaruh**: `LaporanHarianController`, `LaporanHarian` model, `LaporanService`, `resources/views/laporan`
- **Acceptance Criteria**:
  - [ ] Form bisa selesai < 1 menit di HP; validasi server-side (tanggal/lokasi/HM wajib)
  - [ ] Operator lihat riwayat sendiri; Bos lihat semua
  - [ ] Posisi alat di dashboard ikut laporan terbaru
  - [ ] Edge: gagal simpan → data form tidak hilang + pesan error
  - [ ] Solar jerigen→liter otomatis benar (1 jrg = 35 L)
  - [ ] Feature test simpan + otorisasi riwayat (hijau)

### TASK-007 — Buku kas (debit/kredit) + ringkasan
- **Deskripsi**: Bos mencatat kas masuk/keluar; ringkasan total masuk, keluar, saldo per periode. Opsional terkait proyek.
- **Referensi FR/US**: FR-07, FR-08 / US-03
- **Dependensi**: TASK-004
- **Estimasi**: 4 jam
- **Status**: Done
- **File terpengaruh**: `TransaksiKasController`, `TransaksiKas` model, `KasService`, `resources/views/kas`
- **Acceptance Criteria**:
  - [ ] Input debit/kredit + nominal + keterangan + tanggal
  - [ ] Total masuk/keluar/saldo terhitung benar (uji angka)
  - [ ] Edge: nominal ≤ 0 / non-angka ditolak
  - [ ] Filter periode berfungsi
  - [ ] Unit test perhitungan saldo (hijau)

### TASK-008 — Anggaran per proyek + realisasi
- **Deskripsi**: Tetapkan anggaran per proyek; tampilkan realisasi pengeluaran vs anggaran + sisa.
- **Referensi FR/US**: FR-09 / US-04
- **Dependensi**: TASK-004, TASK-007
- **Estimasi**: 4 jam
- **Status**: Done
- **File terpengaruh**: `AnggaranService`, `resources/views/proyek/show`, `ProyekController`
- **Acceptance Criteria**:
  - [ ] Anggaran per proyek dapat ditetapkan/diubah
  - [ ] Realisasi dihitung dari transaksi kredit terkait proyek
  - [ ] Tampilan sisa + indikator over-budget (tanpa mengandalkan warna saja)
  - [ ] Edge: proyek tanpa anggaran → "Belum ditetapkan"
  - [ ] Unit test perhitungan realisasi (hijau)

### TASK-009 — Dashboard Bos
- **Deskripsi**: Satu layar: posisi semua alat, saldo kas, ringkasan anggaran per proyek.
- **Referensi FR/US**: FR-10 / US-01, US-03, US-04
- **Dependensi**: TASK-006, TASK-007, TASK-008
- **Estimasi**: 4 jam
- **Status**: Done
- **File terpengaruh**: `DashboardController`, `resources/views/dashboard`
- **Acceptance Criteria**:
  - [ ] Menampilkan daftar alat + lokasi/operator/tanggal update
  - [ ] Menampilkan saldo kas & ringkasan anggaran
  - [ ] Empty/loading state rapi saat data kosong
  - [ ] Feature test render dashboard per peran (hijau)

### TASK-010 — Polish responsif, a11y, dan state
- **Deskripsi**: Audit mobile-first (320px–1440px+), target sentuh, kontras AA, keyboard/focus, semua state (empty/loading/error/success).
- **Referensi FR/US**: PRD §7 NFR, §9
- **Dependensi**: TASK-009
- **Estimasi**: 5 jam
- **Status**: Done
- **File terpengaruh**: layout, komponen Blade, CSS/token
- **Acceptance Criteria**:
  - [ ] Tidak ada scroll horizontal di 320px
  - [ ] Tabel berubah jadi kartu/list di layar kecil
  - [ ] Kontras AA terverifikasi; fokus terlihat saat tab
  - [ ] `prefers-reduced-motion` dihormati
  - [ ] Lint/formatter bersih

### TASK-011 — Backup/ekspor + rilis produksi
- **Deskripsi**: Rilis penuh ke InfinityFree, panduan backup data, uji alur inti di produksi.
- **Referensi FR/US**: PRD §13
- **Dependensi**: TASK-010
- **Estimasi**: 3 jam
- **Status**: Pending (deploy ditunda — build lokal dulu sesuai permintaan user)
- **File terpengaruh**: `docs/deploy-infinityfree.md`, `docs/backup.md`
- **Acceptance Criteria**:
  - [ ] Alur inti (login, laporan, kas, dashboard) berjalan di produksi
  - [ ] Cara backup DB terdokumentasi
  - [ ] Rollback plan tertulis (simpan rilis sebelumnya)

### TASK-012 — (Fase 2) Ekspor laporan CSV/PDF
- **Deskripsi**: Unduh laporan kas/laporan harian.
- **Referensi FR/US**: FR-11 / US-05
- **Dependensi**: TASK-011
- **Estimasi**: 4 jam
- **Status**: Blocked (menunggu keputusan format)
- **File terpengaruh**: `ReportController`
- **Acceptance Criteria**:
  - [ ] Format dikonfirmasi customer dulu (Open Question #3)

---

## Catatan
- **Unknown pertama**: deploy InfinityFree (TASK-001) dan auth (TASK-002) — dikerjakan paling awal.
- Kalau requirement berubah saat kerja → update `docs/PRD.md` dulu, baru task ini.
- Test pakai database test terpisah (SQLite in-memory) supaya tidak menyentuh DB dev.
