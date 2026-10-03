# Styleguide — Sistem Administrasi Rental Alat Berat (Excavator)

> Sumber kebenaran konvensi desain & kode. Update saat ada keputusan baru.
> Referensi: `~/.config/opencode/PROFESSIONAL_STANDARDS.md` Part 4.2.

## Bagian A — Design

### A1. Brand
- **Tone & personality:** bersih, korporat-modern, terpercaya — putih/very-light-blue dengan **deep royal blue**, kartu rounded + soft shadow. Referensi: app **MANDAU** (deep blue + Plus Jakarta Sans).
- **Boleh:** latar putih/`#EFF4FF`; aksen deep blue (`#1E429F`) & bright blue (`#2D6FF2`); font Plus Jakarta Sans + JetBrains Mono untuk angka; border tipis; shadow lembut; bahasa Indonesia lugas ("Kirim Laporan", "Saldo Kas").
- **Tidak boleh:** warna warni berlebihan, gradient ungu-pink klise, emoji sebagai ikon fungsional, kartu bertumpuk di dalam kartu, border tebal/berat, teks pemasaran berbunga-bunga.

### A2. Color Tokens (light & dark)
Gunakan token, bukan hardcode.

Sumber resmi: `docs/design ui/archive.zip` → `design_guidelines.json` (spec MANDAU).

| Token | Nilai | Kegunaan |
|-------|-------|----------|
| brand | `#2D6FF2` | aksi utama (tombol) |
| brand-hover | `#1E56D9` | hover tombol |
| brand-soft | `#EFF4FF` | tint (nav aktif, badge) |
| sidebar | `#1E429F` | sidebar desktop |
| sidebar-active | `#1C3A82` | menu sidebar aktif |
| page | `#F8FAFC` (desktop) → `#E9EEF6` (mobile) | latar halaman |
| surface | `#FFFFFF` | kartu/panel |
| surface-2 | `#F1F5F9` | area sekunder |
| line | `#E2E8F0` | border 1px |
| ink | `#0F172A` | teks utama |
| muted | `#64748B` | teks sekunder |
| active | `#10B981` | alat beroperasi |
| maintenance | `#F59E0B` | alat perbaikan |
| standby | `#6366F1` | alat idle |
| breakdown | `#EF4444` | alat rusak |
| debit | `#059669` | kas masuk |
| kredit | `#DC2626` | kas keluar |

**Font:** Plus Jakarta Sans (judul) + Inter (body) + JetBrains Mono (angka/uang vs `.mono`/`.font-num`).
**Bentuk:** tombol & input `rounded-full` (pill); kartu `rounded-3xl` (24px); kartu unggulan gradien `#1E429F → #0B1E4B`; bottom nav mobile pill mengambang (`bottom-3 left-3 right-3`, blur).

- Kontras minimal **AA** (teks 4.5:1, UI 3:1). Token `brand` (`#2563EB`) + teks putih ≥ 4.5:1.
- Dark mode via `prefers-color-scheme` + token (bukan inversi mentah).

### A3. Typography
- **Font:** system UI stack (`-apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`). Opsional Inter bila ingin konsisten lintas device.
- **Type scale (px / line-height):**
  - 12 / 16 — label kecil, keterangan
  - 14 / 20 — teks tabel, sekunder
  - 16 / 24 — **base** (body, input) — minimum di HP
  - 18 / 26 — subjudul
  - 24 / 32 — judul halaman
  - 30 / 38 — angka besar (saldo)
- **Weight:** 400 body, 500 label, 600 judul, 700 angka penting. Dark mode: naikkan sedikit weight untuk keterbacaan.

### A4. Spacing, Radius, Shadow
- **Spacing (4pt grid):** 4, 8, 12, 16, 24, 32, 48.
- **Radius:** input & tombol `0.85rem`, kartu `1.25rem`, pill `999px` untuk badge.
- **Shadow:** kartu `0 6px 24px rgba(37,99,235,.08)`; tombol primer `0 8px 20px rgba(37,99,235,.25)`. Tidak lebih dari 2 tingkat.

### A5. Komponen & State
- **Button:** primary (accent), secondary (surface + border), ghost (teks), danger (hapus). Tinggi min 44px di HP.
- **Form:** label di atas input; pesan error di bawah field (bukan toast saja); input tinggi ≥ 44px.
- **Card:** dipakai untuk grup konten; jangan bersarang kartu di dalam kartu.
- **Tabel → Kartu:** di HP, tabel berubah jadi daftar kartu (label: nilai). Tidak ada scroll horizontal.
- **Navigasi:** bottom nav di HP (maks 4 item), sidebar di ≥ 1024px.
- **Badge:** DEBIT (success-soft) / KREDIT (danger-soft); teks, bukan hanya warna.
- **State wajib:** hover, focus, active, disabled, error, **empty**, **loading** (skeleton), success.

### A6. Motion
- Durasi 150–200ms, easing `ease-out`. Animasi hanya untuk makna (transisi panel, feedback simpan).
- `prefers-reduced-motion`: animasi diminimalkan/dimatikan.

### A7. Accessibility Contract
- Kontras AA (4.5:1 teks, 3:1 UI). Touch target ≥ 44×44px (`≥24px` adalah batas absolut WCAG). Keyboard nav penuh + focus indicator terlihat. Form selalu berlabel. Status tidak hanya lewat warna.

## Bagian B — Code

### B1. Stack
- **Laravel (versi terbaru) + MySQL/MariaDB, PHP 8.2+.** Blade + Alpine.js (ringan) + sedikit vanilla JS.
- **Alasan:** user menguasai Laravel; Blade membuat bundle kecil (cocok HP low-end); tidak perlu SPA untuk app CRUD kecil. Hosting InfinityFree mendukung PHP+MySQL.

### B2. Struktur Folder
- Ikuti konvensi Laravel standar:
  - `app/Models` — model Eloquent
  - `app/Http/Controllers` — controller (resource)
  - `app/Http/Requests` — validasi (FormRequest)
  - `app/Services` — logika bisnis (perhitungan kas/anggaran/solar)
  - `app/Http/Middleware` — `EnsureRole`
  - `resources/views` — Blade (per fitur: `alat/`, `proyek/`, `laporan/`, `kas/`, `dashboard/`)
  - `database/migrations`, `database/seeders`
  - `tests/Feature`, `tests/Unit`
- **Aturan impor:** view tidak mengakses DB langsung; controller memanggil Service untuk logika non-trivial.

### B3. Naming
- Model: `PascalCase` tunggal (`Alat`, `Proyek`, `LaporanHarian`, `TransaksiKas`).
- Tabel & kolom: `snake_case` jamak (`laporan_harian`, `transaksi_kas`).
- Controller: `AlatController` (resource). Route name: `alat.index`, `kas.store`.
- Variabel/fungsi: `camelCase` deskriptif; hindari singkatan tak jelas.

### B4. Error Handling
- Validasi via FormRequest → pesan Indonesia yang jelas.
- Aksi simpan: flash message sukses/gagal; jangan menelan exception.
- Halaman error kustom (404/500/403) yang ramah, bukan stack trace.
- Jangan `dd()`/`dump()` di kode yang di-commit.

### B5. Testing
- **Pest** (atau PHPUnit). Struktur: `it('...')` / `test('...')` deskriptif perilaku.
- Feature test untuk alur (login, laporan, kas); unit test untuk Service (perhitungan).
- DB test: SQLite in-memory (`phpunit.xml`).
- Coverage target: **≥ 80% pada file yang berubah**.

### B6. Lint & Format
- **Laravel Pint** untuk PHP (`vendor/bin/pint`).
- **ESLint + Prettier** untuk JS bila ada.
- Jalankan sebelum dianggap selesai.

### B7. Definition of Done
Rujuk `~/.config/opencode/PROFESSIONAL_STANDARDS.md` Part 5. Ringkasnya: lint bersih, test hijau, state lengkap, a11y AA, tanpa secret, build/rilis sukses, `task.md` diperbarui.
