# Architecture — Sistem Administrasi Rental Alat Berat (Excavator)

> Keputusan arsitektur + alasannya. Sumber: `docs/PRD.md` v0.2.

## 1. Stack
- **Laravel (PHP 8.2+)** — user menguasai; ekosistem lengkap (auth, ORM, validasi).
- **MySQL / MariaDB** — satu-satunya DB yang disediakan InfinityFree (bukan Postgres).
- **Blade + Alpine.js** — bundle kecil, cocok HP low-end, tanpa SPA.
- **Hosting: InfinityFree** — gratis, PHP 8.4, MySQL 8, 5 GB disk, SSL gratis.

## 2. Batas Modul & Invarian Arsitektur
Mengikuti alur satu arah (PROFESSIONAL_STANDARDS §2.7A), disederhanakan untuk monolith Laravel:

```
Route (web.php) → Middleware (auth + EnsureRole) → FormRequest (validasi)
   → Controller → Service (logika bisnis) → Model/Eloquent → MySQL
   → Blade view
```

Invarian yang ditegakkan:
- **Otorisasi sebelum logika**: semua route non-publik lewat `auth` + `EnsureRole`.
- **Validasi server-side wajib** via FormRequest (jangan percaya input client).
- **Hanya Service yang menulis data lintas-entitas** (perhitungan kas/anggaran/solar). Controller tipis; Blade tidak pernah query DB.
- **Jangan menyimpan role/aturan sebagai magic value tersebar** — pakai konstanta/enum.

## 3. Data Flow
- **Read:** Browser → Route → Middleware → Controller → Service/Model → Blade → HTML responsif.
- **Write:** Form → Route → Middleware (auth+role) → FormRequest (validasi) → Controller → Service → Model → MySQL → redirect + flash message.
- **Near-realtime (bila dipakai):** polling ringan ke endpoint JSON pada interval, diperbarui di UI (bukan WebSocket).

## 4. External Services
- Tidak ada API pihak ketiga. Tidak ada GPS, pembayaran, atau notifikasi (semua out-of-scope di PRD).
- Tidak ada secret pihak ketiga. Kredensial DB hanya di `.env` server (tidak di-commit).

## 5. Deployment Target
- **Platform:** InfinityFree (shared hosting, tanpa SSH, tanpa cron/queue di paket gratis).
- **Konstrain & solusi:**
  - Document root `htdocs` → isi `public/` diletakkan di `htdocs`, sisa Laravel di luar; sesuaikan path di `index.php`.
  - Tanpa CLI → `composer install --optimize-autoloader --no-dev` di lokal, unggah `vendor/` via FTP.
  - Tanpa cron/queue → hindari queue; scheduler (bila perlu) via cron eksternal.
  - Backup → ekspor DB berkala + simpan salinan rilis untuk rollback.
- **Env & secret:** `.env` terpisah dev/produksi; `.gitignore` menahan `.env` dan `vendor` bila perlu.

## 6. Architecture Decision Records (ADR)

| Tanggal | Keputusan | Alternatif ditolak | Alasan |
|--------|-----------|--------------------|--------|
| 2026-10-02 | Laravel (bukan Next.js) | Next.js + Vercel | User menguasai Laravel; kebutuhan CRUD internal, bukan SPA publik |
| 2026-10-02 | InfinityFree | Vercel, Railway, Neon | Gratis, PHP+MySQL cocok, disk asli; Vercel serverless menyulitkan upload/realtime & ToS non-komersial |
| 2026-10-02 | MySQL (DB milik hosting) | Neon/Postgres | InfinityFree hanya MySQL; menghindari ketergantungan DB eksternal |
| 2026-10-02 | Blade + Alpine | SPA (Inertia/Livewire penuh) | Ringan untuk HP low-end; cukup untuk app kecil |
| 2026-10-02 | Polling (bukan WebSocket) | Laravel Reverb/Pusher | WebSocket tidak didukung hosting gratis; realtime penuh out-of-scope |
| 2026-10-02 | Monolith | Split FE/BE | Skala kecil (≤10 user); split = over-engineering |
