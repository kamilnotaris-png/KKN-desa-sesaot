# Deploy ke VPS (bersama LegalVerse & website FH)

> File-file di folder `deploy/` ini disiapkan sebagai config siap pakai,
> tapi **belum dieksekusi ke VPS** — jalankan langkah-langkah di bawah
> lewat SSH kamu sendiri, saya tidak punya akses SSH ke server dari sini.

VPS sudah longgar (cek `htop` sebelumnya: ~28% RAM terpakai untuk
LegalVerse + website FH), jadi app ketiga ini aman ditambahkan tanpa
upgrade resource.

## 0. Prasyarat

- **Domain belum ditentukan** — untuk sementara app ini diakses langsung
  lewat IP VPS di port khusus: `http://103.93.132.225:8091` (lihat
  komentar di `nginx-kkn-sesaot.conf`). Tidak perlu setup DNS dulu untuk
  mulai deploy & testing.
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
APP_URL=http://103.93.132.225:8091

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

# buka port 8091 kalau firewall ufw aktif
ufw status   # cek dulu apa ufw aktif
ufw allow 8091/tcp
```

Setelah ini, app sudah bisa diakses di `http://103.93.132.225:8091`.

## 7. Aktifkan HTTPS (belakangan, setelah domain dipilih)

**Lewati langkah ini dulu.** Nanti setelah domain final dipilih & DNS-nya
mengarah ke VPS ini:

1. Edit `nginx-kkn-sesaot.conf`: ganti `listen 8091;` → `listen 80;`,
   isi `server_name _;` dengan domain aslinya.
2. `nginx -t && systemctl reload nginx`
3. Jalankan:
   ```bash
   certbot --nginx -d domain-aslinya.tld
   ```
4. Update `APP_URL` di `.env` ke `https://domain-aslinya.tld`, lalu
   `php artisan optimize:clear && php artisan optimize`.

> Ingat: mode offline PWA (service worker) baru aktif setelah HTTPS
> terpasang — di port 8091 tanpa HTTPS, semua fitur lain normal kecuali
> caching offline-nya.

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
git pull origin main
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan optimize   # WAJIB dua-duanya,
                                                       # bukan cuma optimize
npm run build
```
