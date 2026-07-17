# Deploy ke VPS (bersama LegalVerse & website FH)

> File-file di folder `deploy/` ini disiapkan sebagai config siap pakai,
> tapi **belum dieksekusi ke VPS** — jalankan langkah-langkah di bawah
> lewat SSH kamu sendiri, saya tidak punya akses SSH ke server dari sini.

VPS sudah longgar (cek `htop` sebelumnya: ~28% RAM terpakai untuk
LegalVerse + website FH), jadi app ketiga ini aman ditambahkan tanpa
upgrade resource.

## 0. Prasyarat

- **Domain: `wisatasesaot.my.id`** (dibeli terpisah, independen dari FH
  Unizar). Sebelum lanjut ke langkah 7 (HTTPS), tambahkan **A record**
  `@` dan `www` di panel DNS domain ini, mengarah ke IP VPS
  (`103.93.132.225`). Propagasi DNS bisa makan waktu beberapa menit
  sampai ~1 jam — cek sudah mengarah dengan benar via
  `nslookup wisatasesaot.my.id` sebelum jalankan certbot.
- Cek versi PHP aktif di VPS: `php -v` (dokumentasi website-FH mencatat
  8.3 per Juli 2026 — mungkin sudah berubah, cek ulang).

## 1. Clone repo

```bash
ssh -i "legalverse-key.pem" legalverse@103.93.132.225
sudo -i
mkdir -p /var/www/kkn-sesaot
git clone -b main https://github.com/kamilnotaris-png/KKN-desa-sesaot.git /var/www/kkn-sesaot
cd /var/www/kkn-sesaot
```

## 2. Setup database

```bash
mysql -u root -p < deploy/mysql-setup.sql
# lalu edit file itu / .env agar password yang dipakai konsisten
```

## 3. Install dependency

```bash
# --ignore-platform-reqs cuma perlu kalau composer.lock dibuat dengan versi
# PHP lebih baru dari yang terpasang di VPS (sama seperti kasus website-FH)
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
npm install && npm run build
```

## 4. Konfigurasi .env

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wisatasesaot.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kkn_sesaot
DB_USERNAME=kkn_sesaot
DB_PASSWORD=<samakan dengan mysql-setup.sql>
```

## 5. Migrate, seed, optimize

```bash
php artisan migrate --force
php artisan db:seed --force   # opsional, isi data contoh — sebaiknya skip
                               # di production, isi data asli lewat /admin
php artisan storage:link
php artisan optimize
```

## 6. Pasang PHP-FPM pool & Nginx vhost

```bash
# sesuaikan {VERSI} dengan hasil `php -v`
cp deploy/php-fpm-pool-kkn-sesaot.conf /etc/php/{VERSI}/fpm/pool.d/kkn-sesaot.conf
systemctl restart php{VERSI}-fpm

cp deploy/nginx-kkn-sesaot.conf /etc/nginx/sites-available/kkn-sesaot
ln -s /etc/nginx/sites-available/kkn-sesaot /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# pastikan port 80/443 terbuka kalau firewall ufw aktif (biasanya sudah,
# karena LegalVerse/FH juga pakai port ini)
ufw status
ufw allow 80/tcp
ufw allow 443/tcp
```

Setelah ini, app sudah bisa diakses di `http://wisatasesaot.my.id`
(asal DNS sudah mengarah ke VPS ini — cek dengan `nslookup wisatasesaot.my.id`).

## 7. Aktifkan HTTPS

```bash
certbot --nginx -d wisatasesaot.my.id -d www.wisatasesaot.my.id
```

certbot otomatis menambahkan blok `server` untuk port 443 + redirect
dari port 80, tidak perlu edit `nginx-kkn-sesaot.conf` manual. Setelah
ini, situs bisa diakses lewat `https://wisatasesaot.my.id` dan **mode
offline PWA otomatis aktif** (service worker butuh HTTPS).

## 8. Permission

```bash
chown -R www-data:www-data /var/www/kkn-sesaot/storage /var/www/kkn-sesaot/bootstrap/cache
```

## 9. Buat akun admin production

```bash
php artisan tinker --execute="
App\Models\User::create([
    'name' => 'Admin Pokdarwis Sesaot',
    'email' => 'GANTI_EMAIL_DESA',
    'password' => bcrypt('GANTI_PASSWORD_KUAT'),
]);
"
```

Ganti/hapus akun dev (`kamil.notaris@gmail.com` / `sesaot2026kkn`) sebelum
serah terima ke desa.

## 10. Bridge ke GitHub Pages (opsional, tapi disarankan)

Peta publik statis di GitHub Pages (folder `docs/`) tidak bergantung
uptime VPS sama sekali — kalau VPS down, peta & data titik wisata tetap
bisa diakses lewat GitHub Pages. Supaya datanya otomatis ikut ter-update
tiap Pokdarwis edit lewat admin panel:

**a. Aktifkan GitHub Pages** (satu kali saja, lewat browser):
   `https://github.com/kamilnotaris-png/KKN-desa-sesaot/settings/pages`
   → Source: **Deploy from a branch** → Branch: **main** / folder **`/docs`** → Save.
   Setelah aktif, situsnya ada di `https://kamilnotaris-png.github.io/KKN-desa-sesaot/`.

**b. Buat GitHub Personal Access Token (fine-grained)**, khusus repo ini saja:
   `https://github.com/settings/personal-access-tokens/new`
   → Repository access: **Only select repositories** → pilih `KKN-desa-sesaot`
   → Permissions: **Contents: Read and write** (yang lain biarkan No access)
   → Generate, simpan tokennya.

**c. Isi `.env` di VPS:**
```
GITHUB_PAGES_SYNC_ENABLED=true
GITHUB_TOKEN=<token dari langkah b>
```

**d. Pasang queue worker** (wajib — tanpa ini job sync cuma numpuk di
tabel `jobs`, tidak pernah benar-benar jalan):
```bash
cp deploy/kkn-sesaot-queue.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable --now kkn-sesaot-queue
systemctl status kkn-sesaot-queue   # pastikan "active (running)"
```

**e. Tes sekali secara manual:**
```bash
php artisan export:github-pages --push
```
Kalau berhasil akan muncul "Berhasil push ke GitHub." dan ada commit baru
otomatis di repo (author: token yang dipakai) mengubah `docs/data.json`.

## Update kode di kemudian hari

```bash
cd /var/www/kkn-sesaot
git pull   # ambil dari branch yang sedang di-checkout di VPS ini
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan optimize   # WAJIB dua-duanya,
                                                       # bukan cuma optimize
npm run build
```

> Catatan: per hari ini VPS masih checkout branch
> `claude/hki-registration-alternatives-bzcmwd` (PR belum di-merge ke
> `main`). Setelah PR di-merge, jalankan `git checkout main && git pull`
> sekali saja untuk pindah, setelah itu `git pull` biasa cukup.
> `--ignore-platform-reqs` tidak diperlukan lagi sejak composer.lock
> di-regenerate untuk PHP 8.3 (lihat riwayat commit).
