# Catatan Perubahan Proyek KKN Desa Sesaot

Dokumen ini mencatat perubahan operasional dan teknis yang dilakukan setelah
rilis awal aplikasi Peta Wisata Digital Desa Sesaot.

## 20 Juli 2026 — Pembaruan jaringan jalan dan overlay hasil survei

### Perubahan lapangan dan data peta

- Rekaman GPS jalan aspal utama Desa Sesaot tersedia dalam file
  `survei-gps/2026-07-20_jalan-aspal-desa-pintu-masuk-keluar.gpx`.
- Jalur direkam dari pintu masuk desa menuju pintu keluar desa dan melewati
  Kantor Desa Sesaot.
- Pengguna telah menggambar atau memperbarui jaringan jalan pada editor
  OpenStreetMap berdasarkan hasil survei dan pemeriksaan visual.
- Perubahan sudah disimpan di OpenStreetMap, tetapi tile raster standar OSM
  belum tentu langsung menampilkan hasil penyuntingan tersebut.

### Penyebab perubahan OSM tidak langsung terlihat

Website menggunakan tile raster standar OpenStreetMap melalui Leaflet, bukan
membaca basis data jalan OpenStreetMap secara langsung. Editor OSM dapat
menampilkan data terbaru lebih dahulu, sedangkan tile standar harus menunggu
proses render ulang.

Untuk menghilangkan ketergantungan pada waktu render tile OSM, hasil survei GPX
sudah dikonversi menjadi GeoJSON dan ditampilkan sebagai overlay mandiri pada
peta aplikasi.

## Perubahan kode yang telah dilakukan

### Overlay GeoJSON jalur survei

Commit awal overlay:

- `6308241a96f6f3374d99e525b9b99f6125d4d789` — menambahkan lapisan jalur
  GeoJSON agar tidak perlu menunggu render tile OSM.

File yang ditambahkan:

- `public/jalur/jalan-aspal-desa.geojson` untuk website VPS;
- `docs/jalur/jalan-aspal-desa.geojson` untuk GitHub Pages.

Integrasi dilakukan pada:

- `resources/js/app.js`;
- `docs/js/app.js`.

### Perbaikan tampilan awal peta

Sebelumnya peta menjalankan `fitBounds()` hanya berdasarkan marker titik wisata.
Akibatnya, bagian jalur survei yang jauh dari koordinat marker dapat berada di
luar tampilan awal walaupun overlay sebenarnya berhasil dimuat.

Perbaikan:

- pemuatan jalur dan marker dilakukan secara paralel;
- bounds seluruh overlay jalur dan marker digabungkan;
- peta baru menjalankan `fitBounds()` setelah kedua jenis lapisan selesai
  dimuat;
- respons `fetch()` diperiksa melalui status HTTP;
- GeoJSON dan data titik wisata diminta dengan `cache: 'no-cache'`.

Commit terkait:

- `d58559234cc8c7ed4eca2ba4188a0b8ea9d95f29` — perbaikan pemuatan dan bounds
  overlay pada VPS;
- `089aff7496621e8d047f449b057764198f44322d` — perbaikan pemuatan dan bounds
  overlay pada GitHub Pages.

### Perbaikan cache service worker VPS

`public/sw.js` telah diperbarui:

- versi cache dinaikkan menjadi `wisata-sesaot-v3`;
- tile OSM, API, dan file `/jalur/*.geojson` menggunakan strategi
  **network-first**;
- cache tetap menjadi cadangan saat perangkat offline.

Commit:

- `d581ce8f58a1e0f4c9a65c299c22491fcfcf5fc8`.

### Perbaikan cache service worker GitHub Pages

`docs/sw.js` telah diperbarui:

- versi cache dinaikkan menjadi `wisata-sesaot-pages-v4`;
- `data*.json`, file `.geojson`, `js/app.js`, `js/i18n.js`, navigasi, dan tile
  OSM menggunakan strategi **network-first**;
- cache lama versi `v3` akan dihapus ketika service worker `v4` aktif;
- mode offline tetap dipertahankan.

Commit:

- `664735e35d7096927a088e8844a48526626aed94`.

### Perbaikan cache tile OSM sebelumnya

- `c738e6b0f0062d716cbde34307fc71f99f72608f` — mengubah cache tile OSM VPS
  menjadi network-first;
- `814d1720fc72278ba8f6be332312960a99a7708f` — mengubah cache tile OSM GitHub
  Pages menjadi network-first.

## Status saat ini

| Komponen | Status |
|---|---|
| Rekaman GPX survei | Selesai dan tersimpan di repo |
| Penyuntingan jalan di OpenStreetMap | Sudah dilakukan pengguna |
| Konversi GPX menjadi GeoJSON | Selesai |
| Overlay jalur GeoJSON pada VPS | Sudah diimplementasikan |
| Overlay jalur GeoJSON pada GitHub Pages | Sudah diimplementasikan |
| Bounds gabungan jalur dan marker | Sudah diperbaiki |
| Cache GeoJSON dan JavaScript inti | Sudah diperbaiki |
| Perubahan masuk branch `main` | Selesai |
| Deploy perubahan terbaru ke VPS | Belum, siap dilakukan |
| Koordinat titik wisata hasil survei | Belum seluruhnya diverifikasi |

## Perintah deploy VPS

```bash
cd /var/www/kkn-sesaot
git checkout main
git pull origin main
npm install
npm run build
php artisan optimize:clear
sudo systemctl restart kkn-sesaot-queue
sudo systemctl reload nginx
```

Setelah deploy, periksa commit terbaru:

```bash
git log -1 --oneline
```

Kemudian buka `https://wisatasesaot.my.id`, lakukan hard refresh, dan pastikan:

1. garis jalur survei berwarna merah terlihat;
2. tampilan awal memuat jalur dan marker;
3. popup jalur menampilkan nama **Jalan Aspal Desa Sesaot**;
4. tidak ada error `404` untuk `/jalur/jalan-aspal-desa.geojson` pada browser;
5. service worker aktif menggunakan cache `wisata-sesaot-v3`.

## Catatan mutu data

Overlay GeoJSON adalah lapisan informasi hasil survei dan melengkapi data resmi
OpenStreetMap, bukan menggantikannya. Koordinat sejumlah titik wisata pada data
aplikasi masih bersifat perkiraan dan harus diverifikasi melalui survei lapangan
sebelum pencetakan QR Code final atau serah terima kepada Pemerintah Desa dan
Pokdarwis.
