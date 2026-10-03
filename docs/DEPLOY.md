# Deploy & Setup — Amerta Valens Avatara

> Panduan apa yang harus kamu siapkan. Stack: Laravel 13 + MySQL, upload foto ke `public/uploads`.

## Ringkasan yang perlu kamu siapkan

| # | Item | Untuk apa | Wajib? |
|---|------|-----------|--------|
| 1 | Hosting PHP + MySQL online | Sinkron data antar device | ✅ |
| 2 | Kredensial DB (host, nama DB, user, password) | Sambungkan app ke DB online | ✅ |
| 3 | Domain / subdomain | Link akses | ⬜ (bisa subdomain gratis) |
| 4 | Google OAuth Client ID & Secret | Login via Google | ⬜ (opsional, PIN tetap jalan) |

---

## A. Database Online (sinkron antar device)

App membaca koneksi dari `.env`, jadi tinggal arahkan ke MySQL online.

**Pilih salah satu:**
- **InfinityFree** (gratis) — MySQL 8 tersedia di paket gratis.
- **Aiven / Railway / PlanetScale / cPanel hosting** — MySQL terkelola.

**Langkah (contoh InfinityFree):**
1. Daftar → buat akun hosting → buat **MySQL Database**.
2. Catat dari panel: **host** (mis. `sql123.infinityfree.com`), **nama DB** (mis. `if0_xxxx_avatara`), **user**, **password**.
3. Isi di `.env` produksi:
   ```
   DB_CONNECTION=mysql
   DB_HOST=sql123.infinityfree.com
   DB_PORT=3306
   DB_DATABASE=if0_xxxx_avatara
   DB_USERNAME=if0_xxxx
   DB_PASSWORD=passwordmu
   ```
4. Impor struktur + data awal:
   - Cara mudah: dari lokal jalankan `php artisan migrate --seed` setelah `.env` lokal diarahkan ke DB online (hosting mengizinkan koneksi dari luar biasanya), **atau**
   - Ekspor DB lokal (phpMyAdmin → Export) lalu impor ke DB online.

> Semua perangkat (HP operator, laptop admin) yang membuka link yang sama akan membaca DB yang sama → data tertaut otomatis.

---

## B. Google OAuth (opsional)

Login Google hanya muncul di halaman login **setelah** kredensial diisi.

1. Buka **console.cloud.google.com** → buat Project.
2. **APIs & Services → OAuth consent screen** → pilih **External** → isi nama app, email support, dsb. Tambahkan scope `email` + `profile`.
3. **APIs & Services → Credentials → Create Credentials → OAuth client ID** → tipe **Web application**.
4. **Authorized redirect URIs** (isi keduanya):
   - Lokal: `http://localhost:8000/auth/google/callback`
   - Produksi: `https://domainmu.com/auth/google/callback`
5. Salin **Client ID** & **Client secret** ke `.env`:
   ```
   GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=xxxxxxxx
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```
6. Bersihkan cache config: `php artisan config:clear`.
7. **Penting:** user Google harus **emailnya sudah terdaftar** oleh Admin (menu Operator). Google tidak auto-daftar akun baru — ini demi keamanan.

---

## C. Build & Deploy (InfinityFree / shared hosting)

1. **Di lokal**, siapkan aset & dependency produksi:
   ```
   npm ci && npm run build
   composer install --optimize-autoloader --no-dev
   ```
2. **Set `.env` produksi**:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domainmu.com
   APP_KEY=            # jalankan: php artisan key:generate (lokal) lalu salin
   ```
3. **Upload**: karena document root hosting (`htdocs`) tidak bisa diarahkan ke `public/`, unggah:
   - Seluruh isi folder **`public/`** → ke `htdocs/`.
   - Sisa project (app, vendor, bootstrap, config, dst) → satu level **di atas** `htdocs` (di luar web root).
   - Sesuaikan `htdocs/index.php` agar `require` mengarah ke `../vendor/autoload.php` dan `../bootstrap/app.php`.
4. **Izin tulis**: pastikan folder `storage/` dan `bootstrap/cache/` bisa ditulis, dan folder `uploads/` (di dalam `htdocs`) dibuat oleh app saat upload (atau buat manual `htdocs/uploads/laporan` dengan izin tulis).
5. **Storage tidak perlu `storage:link`** — foto disimpan langsung di `public/uploads` (aman untuk shared hosting tanpa symlink).
6. **Cron (opsional)**: tidak ada queue/scheduler wajib untuk app ini.

> Alternatif lebih mudah: hosting yang bisa set document root ke `public/` (mis. VPS/Laragon Cloud/Forge) menghilangkan trik di langkah 3.

---

## D. Checklist singkat untuk kamu

- [ ] Buat **MySQL online**, catat host/nama DB/user/password.
- [ ] (Opsional) Dapatkan **domain** atau subdomain.
- [ ] (Opsional) Buat **Google OAuth Client ID & Secret** + redirect URI.
- [ ] Tentukan **hosting PHP** (InfinityFree/VPS/cPanel) untuk jalankan app.
- [ ] (Opsional) Tentukan **URL produksi** (untuk `APP_URL`).

Setelah 4 item di atas siap, kirim nilainya dan aku bantu sambungkan/isi `.env` dan langkah upload.
