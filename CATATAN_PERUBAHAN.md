# Catatan Perubahan Proyek KKN Desa Sesaot

Dokumen ini mencatat perubahan operasional dan teknis yang dilakukan setelah
rilis awal aplikasi Peta Wisata Digital Desa Sesaot.

## 20 Juli 2026 — Pembaruan jaringan jalan OpenStreetMap

### Perubahan lapangan dan data peta

- Rekaman GPS jalan aspal utama Desa Sesaot telah tersedia dalam file
  `survei-gps/2026-07-20_jalan-aspal-desa-pintu-masuk-keluar.gpx`.
- Jalur direkam dari pintu masuk desa menuju pintu keluar desa dan melewati
  Kantor Desa Sesaot.
- Pengguna telah menggambar atau memperbarui jaringan jalan pada editor
  OpenStreetMap berdasarkan hasil survei dan pemeriksaan visual.
- Perubahan sudah disimpan di OpenStreetMap, sebagaimana terlihat pada tampilan
  editor OSM yang menunjukkan ruas jalan baru atau ruas yang telah dikoreksi.

### Temuan setelah pembaruan

Perubahan belum langsung terlihat pada `wisatasesaot.my.id`. Hal tersebut tidak
berarti perubahan OSM gagal disimpan. Website menggunakan tile raster standar
OpenStreetMap melalui Leaflet, bukan membaca basis data jalan OSM atau file GPX
secara langsung. Editor OSM dapat menampilkan data terbaru lebih dahulu,
sedangkan tile standar masih menunggu proses render ulang.

### Perubahan kode yang dilakukan

#### `public/sw.js`

- Strategi pemuatan tile OpenStreetMap diubah dari **cache-first** menjadi
  **network-first**.
- Versi cache dinaikkan dari `wisata-sesaot-v1` menjadi
  `wisata-sesaot-v2`.
- Tile lama tetap dapat dipakai sebagai cadangan ketika perangkat offline.
- Commit: `c738e6b0f0062d716cbde34307fc71f99f72608f`.

#### `docs/sw.js`

- Strategi pemuatan tile OpenStreetMap pada GitHub Pages diubah menjadi
  **network-first**.
- Versi cache dinaikkan dari `wisata-sesaot-pages-v2` menjadi
  `wisata-sesaot-pages-v3`.
- Commit: `814d1720fc72278ba8f6be332312960a99a7708f`.

#### `survei-gps/README.md`

- Status GPX diperbarui dari “belum digambar ke OpenStreetMap” menjadi sudah
  digunakan sebagai acuan pembaruan OSM.
- Ditambahkan penjelasan mengenai perbedaan data editor OSM dan tile raster.
- Ditambahkan status deploy, render tile, dan opsi overlay GeoJSON.
- Commit: `bfe92e61b24b154daeac9d38a7172ba2951942a0`.

### Status saat ini

| Komponen | Status |
|---|---|
| Rekaman GPX survei | Selesai dan tersimpan di repo |
| Penyuntingan jalan di OpenStreetMap | Sudah dilakukan pengguna |
| Perbaikan cache service worker | Sudah masuk branch `main` |
| Deploy perubahan service worker ke VPS | Perlu dipastikan melalui `git pull` dan build |
| Render tile OpenStreetMap terbaru | Belum terlihat pada saat pemeriksaan |
| Overlay jalur GPX/GeoJSON di website | Belum dibuat |

### Perintah deploy VPS

```bash
cd /var/www/kkn-sesaot
git checkout main
git pull origin main
npm install
npm run build
php artisan optimize:clear
```

Setelah deploy, browser harus memuat service worker versi terbaru. Namun,
kemunculan bentuk jalan terbaru tetap bergantung pada proses render tile
OpenStreetMap.

### Rekomendasi teknis

Untuk memastikan jalur survei langsung terlihat di website tanpa menunggu tile
OSM, file GPX perlu dikonversi menjadi GeoJSON dan ditambahkan sebagai overlay
Leaflet. Overlay tersebut harus diperlakukan sebagai lapisan informasi hasil
survei, bukan sebagai pengganti data resmi OpenStreetMap.
