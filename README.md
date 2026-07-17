# Peta Wisata Digital Desa Sesaot

Aplikasi peta wisata digital untuk Desa Sesaot, Kecamatan Narmada, Kabupaten
Lombok Barat — dibangun dalam rangka Program Kerja 1 KKN UNIZAR 2026
("Peta Digital & QR Code Gerbang/Pos Wisata"), sebagai pengganti Google My
Maps agar source code-nya dapat didaftarkan sebagai Hak Cipta Program
Komputer (HKI) ke DJKI.

## Stack

- Laravel 12 (PHP 8.4)
- Filament v3 — admin panel untuk Pokdarwis/desa kelola data titik wisata
- Leaflet.js + OpenStreetMap — peta interaktif publik
- PWA (manifest + service worker) — mode offline untuk area sinyal lemah

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Struktur Data

Model utama: `TitikWisata` (tabel `titik_wisatas`) — nama, kategori, dusun,
deskripsi, cerita lokal, koordinat GPS, foto, link video YouTube.

> Koordinat GPS pada seeder saat ini masih **perkiraan/placeholder** —
> wajib diganti dengan hasil drop pin survei lapangan sebelum dipakai
> untuk cetak QR code final.
