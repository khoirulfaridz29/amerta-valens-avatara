# Deploy ke InfinityFree — Amerta Valens Avatara

Panduan langkah demi langkah. Bahan yang sudah disiapkan:
- `deploy/database.sql` — dump DB (struktur + data) untuk diimpor ke phpMyAdmin.
- APP_KEY: `base64:WaT+7ExARxU5zLoapZZ7ZjNGC7l4ux4Z0k6N2bq0MJw=`

> InfinityFree: **PHP + MySQL, tanpa SSH/Composer/cron**, document root = `htdocs`.
> Jadi: file aplikasi ke folder `laravel/` (sejajar `htdocs`), isi `public/` ke `htdocs/`.
> DB **diimpor** via phpMyAdmin (remote MySQL diblokir, jadi tidak bisa `migrate` dari lokal).

---

## A. Siapkan di komputer (sebelum upload)

```bash
composer install --optimize-autoloader --no-dev
npm ci && npm run build
```

Pastikan folder `deploy/` (berisi `database.sql`) ikut — ini yang diimpor.

---

## B. Buat hosting & database (InfinityFree)

1. Daftar di infinityfree.com → **Create Account** → pilih subdomain gratis (mis. `avatara.rf.gd`) atau domain sendiri.
2. **Control Panel → MySQL Databases → Create Database.** Catat:
   - Host: `sqlXXX.infinityfree.com`
   - Database: `if0_xxxx_avatara`
   - Username: `if0_xxxx`
   - Password: (yang kamu set)
3. **phpMyAdmin** → pilih database → tab **Import** → pilih `deploy/database.sql` → **Go**.

> Masuk dengan akun demo: `admin@avatara.id / admin123` (atau ubah dulu). Data awal bisa dihapus lewat phpMyAdmin bila mau kosong.

---

## C. Upload via FTP (FileZilla)

Kredensial FTP: **Control Panel → FTP Accounts**. Struktur di server:

```
/ (root FTP)
├── htdocs/     ← isi dari folder public/
└── laravel/    ← SELURUH project (termasuk vendor/ dan public/)
```

Langkah:
1. Buat folder **`laravel`** (sejajar `htdocs`), upload **seluruh isi project** ke situ.
   - **Sertakan** `vendor/` dan `public/build/` (server tidak punya Composer/Node).
   - Boleh kecualikan `.git`, `node_modules`.
2. Upload **isi folder `public/`** ke **`htdocs/`** (index.php, .htaccess, build/, images/, dsb).
3. Ganti isi **`htdocs/index.php`** dengan versi ini (path diarahkan ke `../laravel/`):

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

4. Buat folder **`htdocs/uploads/laporan/`** dan upload **`public/uploads/.htaccess`** ke **`htdocs/uploads/.htaccess`** (mencegah eksekusi PHP di folder upload).
5. Upload file **`.env`** ke **`laravel/.env`** (lihat bagian E).

> Kenapa `public/` diduplikasi? Karena Laravel membaca `public_path()` = `laravel/public` (untuk asset & manifest), sedangkan browser mengakses file dari `htdocs/`. Keduanya diisi agar aman.

---

## D. Izin tulis

Set permission **755** (FileZilla: klik kanan → File Permissions) untuk:
- `laravel/storage` (dan subfolder `framework/cache`, `framework/sessions`, `framework/views`)
- `laravel/bootstrap/cache`
- `htdocs/uploads/laporan`

Jika folder `laravel/storage/framework/{cache,sessions,views}` belum ada, buat (kosong).

---

## E. Isi `.env` (produksi)

```
APP_NAME="Amerta Valens Avatara"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:WaT+7ExARxU5zLoapZZ7ZjNGC7l4ux4Z0k6N2bq0MJw=
APP_URL=https://avatara.rf.gd

# WAJIB di shared hosting: path absolut ke web root (htdocs).
# Tanpa ini, foto yang diupload tidak akan tampil.
APP_PUBLIC_PATH=/path/absolut/ke/htdocs

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_xxxx_avatara
DB_USERNAME=if0_xxxx
DB_PASSWORD=PASSWORD_DB_KAMU

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=warning

# Google OAuth (opsional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

---

## F. Uji

- Buka `https://avatara.rf.gd` → login (email/password).
- Cek: Dashboard, Data Alat, Kontrak, Kas, Bon, Laporan (upload foto → pastikan tersimpan & terkompres), Riwayat Service.
- Kalau ada error: set `APP_DEBUG=true` sementara → buka lagi → lihat pesan → **kembalikan ke false**.

---

## G. Catatan penting InfinityFree

- **Jangan** `php artisan migrate` dari lokal (remote MySQL diblokir). Pakai **import SQL** (bagian B).
- **Tanpa SSH/Composer/cron**: semua dependency (`vendor/`) & aset (`build/`) diupload manual.
- Ada batas **jumlah file & hit harian**; untuk 2–5 pengguna aman. Kalau kena limit inode, kurangi paket `composer` yang tidak terpakai atau minta hosting lain.
- Pastikan PHP ≥ 8.2 dan ekstensi: **pdo_mysql, mbstring, openssl, tokenizer, ctype, fileinfo, gd, exif**.
- **Google login**: butuh `GOOGLE_CLIENT_ID/SECRET` + redirect `https://avatara.rf.gd/auth/google/callback`; email Google pengguna harus sudah terdaftar sebagai akun (menu Operator / Pengaturan).

---

## H. Re-deploy (setelah revisi) — bisa berkali-kali

Ya, aman diulang. Prinsipnya: **unggah hanya yang berubah.**

1. **Di lokal**:
   - Ubah kode.
   - Kalau UI/CSS/JS berubah → `npm run build`.
   - Kalau dependency berubah (`composer.json`) → `composer install --optimize-autoloader --no-dev` lalu upload `vendor/`.
2. **Upload via FTP**:
   - File aplikasi yang berubah → ke `/laravel/...`
   - Aset baru (`public/build`, `public/images`, dll) → ke `/htdocs/...` (dan biarkan juga di `/laravel/public/...`).
   - `htdocs/index.php` biasanya tidak perlu diubah lagi.
3. **JANGAN disentuh** (biar data aman):
   - `htdocs/uploads/` (foto laporan) dan `laravel/storage/`
   - **Jangan impor ulang `database.sql`** saat update — itu menimpa data asli.
4. **Kalau ada perubahan skema DB** (kolom/tabel baru):
   - Buat dump baru dari lokal, atau jalankan `ALTER TABLE ...` via phpMyAdmin (jangan full re-import).
5. **Kalau ubah `.env`** → upload ulang `/laravel/.env`.
6. **Kalau bingung ada cache config lama**: hapus isi `laravel/bootstrap/cache/*.php` via FTP.

**Ganti total (full replace):** upload ulang semua **kecuali** `.env`, `laravel/storage/`, dan `htdocs/uploads/`.

> Tip: pakai FileZilla, aktifkan "overwrite if newer" supaya cepat. Simpan ZIP rilis terakhir untuk rollback.

## I. Ringkasan perintah lokal

```bash
# 1. dependency produksi
composer install --optimize-autoloader --no-dev
# 2. aset frontend
npm ci && npm run build
# 3. (bila perlu) dump DB terbaru
"D:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe" -u root --no-tablespaces dapa > deploy/database.sql
```
