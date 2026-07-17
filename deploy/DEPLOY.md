# Deploy ke VPS (bersama LegalVerse & website FH)

> File-file di folder `deploy/` ini disiapkan sebagai config siap pakai,
> tapi **belum dieksekusi ke VPS** — jalankan langkah-langkah di bawah
> lewat SSH kamu sendiri, saya tidak punya akses SSH ke server dari sini.

VPS sudah longgar (cek `htop` sebelumnya: ~28% RAM terpakai untuk
LegalVerse + website FH), jadi app ketiga ini aman ditambahkan tanpa
upgrade resource.

## 0. Prasyarat

- Domain/subdomain sudah diarahkan ke IP VPS (A record). Contoh dipakai
  di bawah: `kkn-sesaot.fhunizar.my.id` — ganti sesuai subdomain yang
  benar-benar kamu daftarkan di DomaiNesia.
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
APP_URL=https://kkn-sesaot.fhunizar.my.id

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
```

## 7. Aktifkan HTTPS

```bash
certbot --nginx -d kkn-sesaot.fhunizar.my.id
```

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
