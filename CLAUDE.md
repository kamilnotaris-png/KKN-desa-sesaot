# CLAUDE.md

Panduan untuk Claude Code saat bekerja di repo ini.

## Project Overview

**Peta Wisata Digital Desa Sesaot** — Laravel 12 + Filament v3, dibangun
untuk **Program 1 proposal KKN UNIZAR 2026** ("Peta Digital & QR Code
Gerbang/Pos Wisata"), Desa Sesaot, Kec. Narmada, Lombok Barat.

**Kenapa proyek ini ada:** proposal awal Program 1 memakai Google My Maps
sebagai platform peta. Itu tidak bisa didaftarkan HKI (Hak Cipta Program
Komputer di DJKI) karena bukan ciptaan orisinal — cuma konfigurasi
platform milik Google. Aplikasi ini dibangun dari nol (Leaflet.js
self-hosted, backend sendiri) supaya source code-nya bisa didaftarkan HKI.

## Status Saat Ini (per 2026-07-17)

- Semua pengembangan ada di branch **`claude/hki-registration-alternatives-bzcmwd`**
  (8 commit di depan `main`, 0 di belakang — merge dijamin bersih, sudah
  diverifikasi via `git merge-tree`, tidak ada konflik).
- **Belum di-merge ke `main`** — user sedang proses bikin PR lewat GitHub UI.
- **GitHub Pages belum diaktifkan** (terverifikasi via API: 0 workflow run).
  Begitu diaktifkan, source branch-nya harus `claude/hki-registration-alternatives-bzcmwd`
  (bukan `main`, karena folder `docs/` belum ada di `main`) sampai PR di-merge.
- **Belum di-deploy ke VPS** — `deploy/DEPLOY.md` belum dijalankan sama sekali.
- **Domain belum diputuskan.** Sempat dipertimbangkan subdomain
  `fhunizar.my.id` / `fh.unizar.ac.id`, tapi user memutuskan proyek ini
  harus punya domain independen (bukan menumpang FH Unizar), keputusan
  final ditunda. Config deploy untuk sementara pakai port `8091` via IP
  VPS langsung (`http://103.93.132.225:8091`), tanpa domain.

## Arsitektur

**Dua sisi yang saling melengkapi, sengaja didesain independen:**

1. **VPS (Laravel penuh)** — admin panel Filament untuk Pokdarwis/desa
   input data, plus peta publik yang dirender server-side.
2. **GitHub Pages (`docs/`)** — static site mandiri (HTML/CSS/JS polos,
   Leaflet di-self-host di `docs/vendor/leaflet/`, tanpa CDN eksternal),
   supaya peta publik tetap hidup walau VPS down/domain bermasalah.
   Disinkronkan otomatis dari admin panel lewat GitHub Contents API
   (lihat bagian "Sinkronisasi GitHub Pages" di bawah).

### Data Model

`TitikWisata` (tabel `titik_wisatas`): `nama`, `slug` (auto dari nama),
`kategori` (enum di `TitikWisata::KATEGORI`), `dusun`, `deskripsi`,
`cerita_lokal`, `latitude`/`longitude`, `foto`, `video_youtube_url`,
`urutan`, `is_active`.

### Routes

```
/                      → peta publik (Leaflet, HomeController: PetaWisataController@index)
/peta/{slug}           → halaman detail per titik (target QR code)
/api/titik-wisata      → GeoJSON, dikonsumsi peta (juga dipakai GithubPagesSync)
/admin                 → Filament admin panel (TitikWisataResource)
```

### Sinkronisasi GitHub Pages

`TitikWisataObserver` (didaftarkan di `AppServiceProvider::boot()`) →
dispatch job `SyncTitikWisataToGithubPages` tiap `TitikWisata` disimpan/
dihapus → job panggil `GithubPagesSync::push()` → commit `docs/data.json`
terbaru ke GitHub lewat Contents API.

- Nonaktif by default (`GITHUB_PAGES_SYNC_ENABLED=false`) sampai
  `GITHUB_TOKEN` diisi di `.env` — gagalnya selalu graceful (log warning,
  tidak pernah menjatuhkan alur simpan admin panel).
- **Butuh queue worker jalan** (`QUEUE_CONNECTION=database` by default) —
  tanpa ini job cuma numpuk di tabel `jobs`. Systemd service tersedia di
  `deploy/kkn-sesaot-queue.service`.
- Logika generate GeoJSON disatukan di `App\Support\TitikWisataGeoJsonExporter`
  — dipakai bareng oleh API controller, `php artisan export:github-pages`,
  dan `GithubPagesSync`. Jangan duplikasi logika ini di tempat lain.

### PWA / Mode Offline

`public/manifest.json` + `public/sw.js` (sisi VPS) dan `docs/manifest.json`
+ `docs/sw.js` (sisi GitHub Pages) — dua service worker terpisah karena
dua origin berbeda. **Service worker cuma aktif di HTTPS atau localhost**
— versi VPS di port 8091 tanpa domain TIDAK akan punya mode offline
sampai domain asli + `certbot` terpasang. Versi GitHub Pages otomatis
HTTPS jadi offline-nya langsung aktif begitu Pages di-enable.

Sudah diverifikasi via Playwright (bukan cuma ditulis) bahwa mode offline
beneran jalan di kedua sisi: peta, halaman detail, dan data API tetap
bisa diakses setelah kunjungan online pertama.

## Deploy

Runbook lengkap: `deploy/DEPLOY.md`. Ringkasan urutan:
1. Clone ke `/var/www/kkn-sesaot` di VPS `103.93.132.225` (VPS yang sama
   dengan LegalVerse & website FH — pool PHP-FPM & vhost Nginx dibuat
   terisolasi, lihat `deploy/php-fpm-pool-kkn-sesaot.conf` &
   `deploy/nginx-kkn-sesaot.conf`, pakai socket & port sendiri jadi tidak
   bentrok dengan 2 app lain).
2. `mysql -u root -p < deploy/mysql-setup.sql` — DB & user `kkn_sesaot`
   terpisah total dari `fhunizar`/`legalverse`.
3. `composer install --ignore-platform-reqs` (VPS mungkin masih PHP 8.3,
   composer.lock dibuat dengan PHP 8.4 lokal — cek `php -v` di VPS dulu).
4. Aktifkan `kkn-sesaot-queue.service` (wajib untuk sinkronisasi GitHub Pages).

VPS sudah dicek longgar (~28% RAM terpakai untuk 2 app lain saat sesi ini
berlangsung), app ketiga ini kecil (pool PHP-FPM `pm=ondemand`), aman
ditambahkan tanpa upgrade resource.

## Catatan Penting / Belum Beres

- **Koordinat GPS di `TitikWisataSeeder` masih perkiraan/placeholder**,
  bukan hasil survei lapangan asli. WAJIB diganti sebelum cetak QR final.
- **Ikon PWA** (`public/icons/`, `docs/icons/`) dibuat programatis via
  PHP GD (placeholder), bukan desain resmi — ganti sebelum rilis publik.
- **Akun admin dev**: `kamil.notaris@gmail.com` / `sesaot2026kkn` — WAJIB
  diganti/dihapus sebelum serah terima ke Pokdarwis/desa.
- Font Google/Bunny CDN sengaja dihapus dari `vite.config.js` (tidak
  reachable di sandbox dev, juga selaras prinsip offline-first) — pakai
  system font stack biasa.
- Kalau menguji lokal di sandbox/CI: OpenStreetMap tile & domain eksternal
  lain (github.io dst) sering diblokir oleh proxy sandbox — itu bukan bug
  kode, cek dulu `curl -sS "$HTTPS_PROXY/__agentproxy/status"` sebelum
  menyimpulkan sesuatu rusak.

## Commands

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve

php artisan export:github-pages          # tulis ulang docs/data.json dari DB
php artisan export:github-pages --push   # + langsung push ke GitHub (butuh GITHUB_TOKEN)
```

## Riwayat Perubahan

### Sesi 2026-07-17
- Scaffold project Laravel 12 dari nol, model/migration/seeder `TitikWisata`.
- Admin panel Filament (`TitikWisataResource`) untuk Pokdarwis/desa.
- API GeoJSON + peta publik Leaflet.js + halaman detail per titik (target QR).
- Lapisan PWA (manifest + service worker), diverifikasi offline via Playwright.
- Deploy config VPS: Nginx vhost, PHP-FPM pool terisolasi, MySQL setup,
  `DEPLOY.md` — awalnya pakai domain `kkn-sesaot.fhunizar.my.id`, lalu
  diubah ke port `8091` tanpa domain karena keputusan domain ditunda.
- Bridge GitHub Pages: static site mandiri di `docs/`, sinkronisasi
  otomatis dari admin panel via observer → job queue → GitHub Contents API.
- Sempat dibahas & diputuskan: domain final akan independen dari FH
  Unizar (bukan `fhunizar.my.id` maupun `fh.unizar.ac.id`), belum
  ditentukan nama domainnya.
- Diverifikasi: merge branch ini ke `main` dijamin tanpa konflik (main
  masih 0 commit sejak divergence, tinggal fast-forward).
