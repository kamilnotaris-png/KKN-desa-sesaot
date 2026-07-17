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

- **Sudah di-merge ke `main`** (PR #1, merge commit `793fe4c1`). Branch
  fitur `claude/hki-registration-alternatives-bzcmwd` sudah selesai
  tugasnya; pekerjaan lanjutan (termasuk fitur multi-bahasa) langsung
  dikerjakan & di-push ke `main`.
- **GitHub Pages sudah aktif** — repo dibuat public (perlu untuk Pages
  gratis), source `main` folder `docs/`, sudah dites merender peta.
- **Fitur multi-bahasa (5 bahasa: id/en/ar/zh/ms) sudah selesai** baik di
  sisi VPS (Laravel, model `TitikWisata` pakai `spatie/laravel-translatable`,
  admin Filament translatable + locale switcher) maupun sisi GitHub Pages
  (`docs/js/i18n.js` kamus mandiri + `docs/data.{locale}.json` per bahasa).
  Lihat bagian "Multi-Bahasa" di bawah untuk detail arsitektur.
- **Belum di-deploy ke VPS** — `deploy/DEPLOY.md` belum dijalankan sama
  sekali. VPS masih perlu: `git pull origin main`, migrate (kolom
  `nama`/`deskripsi`/`cerita_lokal` sekarang JSON, ada migrasi baru),
  `npm run build`, `php artisan optimize:clear && php artisan optimize`.
- **Domain final: `wisatasesaot.my.id`** (independen, bukan menumpang FH
  Unizar). Deploy config sudah dipindah dari port `8091` ke port standar
  80/443 + HTTPS via certbot (lihat `deploy/nginx-kkn-sesaot.conf` &
  `deploy/DEPLOY.md`) — port non-standar sempat diduga jadi penyebab
  masalah 405 berulang saat testing (request tidak pernah sampai ke
  Laravel/PHP-FPM di log). Port 80/443+HTTPS juga syarat mode offline PWA
  aktif di sisi VPS.

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
— begitu domain `wisatasesaot.my.id` + `certbot` terpasang (langkah 7 di
`deploy/DEPLOY.md`), mode offline di sisi VPS otomatis aktif. Versi
GitHub Pages otomatis HTTPS jadi offline-nya langsung aktif begitu Pages
di-enable.

Sudah diverifikasi via Playwright (bukan cuma ditulis) bahwa mode offline
beneran jalan di kedua sisi: peta, halaman detail, dan data API tetap
bisa diakses setelah kunjungan online pertama.

### Multi-Bahasa

5 bahasa: **Indonesia (default), English, Arab, Mandarin, Melayu** —
daftar & label/flag ada di `config/languages.php`. Hanya halaman publik
yang diterjemahkan; **admin panel Filament tetap Bahasa Indonesia**
(dipakai staf Pokdarwis lokal, tidak perlu multi-bahasa).

**Sisi VPS (Laravel):**
- Kolom `nama`, `deskripsi`, `cerita_lokal` di `TitikWisata` adalah JSON
  (per-locale) lewat `spatie/laravel-translatable` (`HasTranslations` +
  `$translatable`). Migrasi konversi ada di
  `database/migrations/2026_07_17_094238_add_translatable_columns_to_titik_wisatas_table.php`.
- Locale halaman publik ditentukan `App\Http\Middleware\SetPublicLocale`
  (alias `public-locale`, hanya dipasang di route publik di `routes/web.php`
  — **sengaja tidak** didaftarkan global di grup `web` supaya tidak bocor
  ke `/admin`). Switch bahasa lewat query `?lang=xx`, disimpan di session.
- String UI (bukan data) ada di `resources/lang/{id,en,ar,zh,ms}/peta.php`,
  dipanggil via `__('peta.xxx')`.
- Admin panel: `SpatieLaravelTranslatablePlugin` terpasang di
  `AdminPanelProvider`, `TitikWisataResource` implement `Translatable`
  concern → ada locale switcher di form admin. **Tidak ada auto-translate
  API** (sempat dibangun pakai Google Cloud Translation API lalu dibatalkan
  karena butuh API key + billing) — sebagai gantinya tiap field
  nama/deskripsi/cerita_lokal punya hint-action "Buka Google Translate"
  yang buka `translate.google.com` di tab baru dengan teks Indonesia
  sudah ter-prefill, staf tinggal salin-tempel hasil terjemahan manual.
  Zero-cost, tidak butuh API key.

**Sisi GitHub Pages (`docs/`, static, tanpa Laravel):**
- `docs/js/i18n.js` — kamus JS mandiri (duplikat isi dari
  `resources/lang/*/peta.php`, harus disinkronkan manual kalau ada
  perubahan string) + fungsi `getCurrentLocale()`/`t()`/`renderLanguageSwitcher()`.
  Locale disimpan di `localStorage`, override lewat `?lang=xx`.
- Data titik wisata: `docs/data.json` (id, default) + `docs/data.en.json`,
  `docs/data.ar.json`, `docs/data.zh.json`, `docs/data.ms.json` — semua
  di-generate `php artisan export:github-pages` (loop semua locale di
  `config('languages.supported')`) dan di-push otomatis oleh
  `GithubPagesSync::push()` lewat GitHub Contents API (juga di-loop per
  locale, path lain dari `docs/data.json` diturunkan otomatis).
- Service worker (`docs/sw.js`) network-first regex-nya diperluas jadi
  `/\/data(\.[a-z]{2})?\.json$/` supaya cocok ke semua file data per-bahasa,
  bukan cuma `data.json` — versi cache dinaikkan ke `v2`.
- **Kalau nambah string UI baru:** WAJIB update dua tempat —
  `resources/lang/*/peta.php` (Laravel) DAN `docs/js/i18n.js` (statis) —
  tidak ada mekanisme sync otomatis antara keduanya.

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
- **Akun admin dev** (kredensial dicatat terpisah, tidak di file ini) —
  WAJIB diganti/dihapus sebelum serah terima ke Pokdarwis/desa. Sudah
  diganti passwordnya per sesi 2026-07-17 setelah deploy VPS pertama.
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

### Sesi 2026-07-17 (lanjutan — deploy VPS, fix sinkronisasi, tambah titik wisata)
- **Deploy VPS pertama untuk fitur multi-bahasa selesai** — `git pull`,
  migrate, `npm run build`, optimize, GitHub Pages sync (`GITHUB_TOKEN`)
  + queue worker (`kkn-sesaot-queue.service`) semua aktif.
- **Fix bug `GithubPagesSync`**: push 5 file `docs/data*.json` dari VPS
  cuma 1 (`data.json`, locale default) yang benar-benar ter-update, 4
  lainnya (en/ar/zh/ms) diam-diam gagal - ditemukan lewat perbandingan
  konten langsung di GitHub API, root cause diduga secondary rate limit
  GitHub Contents API untuk write beruntun. Fix: `pushAll()` return status
  per-locale (bukan satu boolean agregat) + jeda 700ms antar file push.
- **Tambah 2 titik wisata baru** ke `TitikWisataSeeder`: **Purekmas**
  (Dusun Penangke - mata air Rinjani dikelola warga) dan **Bukit Vetong**
  (camping ground, rumah Sasak, Tree House). Hasil riset web search,
  terverifikasi sumber resmi (Jadesta Kemenparekraf, media lokal).
  **Sengaja TIDAK menambahkan** beberapa nama air terjun yang sempat
  muncul di Google Maps dekat kawasan ini (Air Terjun Segenter, Titian
  Batu Kawangan, Sesere, Bunut Ngengkang) karena setelah dicek,
  semuanya secara administratif berada di desa/kecamatan/kabupaten LAIN
  (Desa Pakuan, Desa Buwun Sejati, bahkan Lombok Tengah) - bukan Desa
  Sesaot, meski berdekatan secara geografis di kawasan hutan yang sama.
- **Koordinat 2 titik baru masih perkiraan kasar** (belum survei GPS
  lapangan), sama seperti 5 titik sebelumnya - lihat "Belum Beres".

### Sesi 2026-07-17 (lanjutan — merge, GitHub Pages, multi-bahasa)
- **Merge ke `main`** — PR #1 di-merge (merge commit `793fe4c1`).
- **GitHub Pages diaktifkan** — repo dibuat public (syarat Pages gratis di
  akun non-Pro), source `main`/`docs`, dicek "GitHub Pages source saved."
- **Fitur multi-bahasa (5 bahasa) selesai**, sisi VPS maupun GitHub Pages
  — lihat bagian "Multi-Bahasa" di atas untuk arsitektur lengkap.
  Keputusan penting yang diambil bareng user: tanpa API berbayar/API key
  untuk auto-translate — dicoba dulu pakai Google Cloud Translation API
  lalu dibatalkan (user: "tidak usah pake api"), diganti hint-action
  "Buka Google Translate" manual di admin panel (zero-cost).
- Fix bug `APP_LOCALE=en` di `.env` (harusnya `id`) — sisa stub default
  Laravel yang bikin semua konten publik render Bahasa Inggris.

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
