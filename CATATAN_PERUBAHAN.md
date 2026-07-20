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

## Marker aplikasi dan petunjuk arah Google Maps

### Pengelolaan marker aplikasi

Marker resmi aplikasi tidak diambil otomatis dari OpenStreetMap. Marker dikelola
melalui panel admin pada menu **Titik Wisata**. Data utama marker meliputi:

- nama dan slug;
- kategori;
- dusun;
- deskripsi dan cerita lokal;
- latitude dan longitude;
- foto dan video;
- urutan tampilan;
- status aktif atau tidak aktif.

Perubahan marker melalui panel admin langsung tersimpan di database VPS dan
langsung digunakan oleh API `/api/titik-wisata`. Perubahan nama, deskripsi,
koordinat, foto, dan status aktif tidak membutuhkan `git pull`, build, atau deploy
ulang.

Setiap data titik wisata disimpan atau dihapus, observer aplikasi mengirim job
sinkronisasi ke GitHub Pages. Karena itu, queue worker
`kkn-sesaot-queue.service` harus tetap aktif agar `docs/data*.json` ikut
terbarui.

### Cara kerja tombol Petunjuk Arah

Tombol **Petunjuk Arah** hanya mengirim koordinat tujuan marker kepada Google
Maps dalam bentuk parameter:

```text
https://www.google.com/maps/dir/?api=1&destination={latitude},{longitude}
```

Google Maps kemudian:

1. menggunakan lokasi pengguna sebagai titik awal apabila izin lokasi tersedia;
2. membaca latitude dan longitude marker sebagai tujuan;
3. menghitung rute berdasarkan jaringan jalan milik Google Maps.

### Batas integrasi Google Maps

Perlu dibedakan empat sumber data berikut:

| Komponen | Sumber data | Otomatis memengaruhi Google Maps? |
|---|---|---|
| Marker aplikasi | Database `titik_wisatas` | Ya, sebagai koordinat tujuan setelah marker disimpan |
| Jalan pada basemap website | Tile OpenStreetMap | Tidak |
| Edit jalan di OpenStreetMap | Basis data OSM | Tidak otomatis masuk ke database jalan Google |
| Garis merah GeoJSON | File `public/jalur/*.geojson` | Tidak |

Dengan demikian, memperbarui jalan pada OpenStreetMap atau memperbaiki garis
GeoJSON tidak memaksa Google Maps mengikuti jalur tersebut. Google Maps tetap
menghitung rute menggunakan data jalannya sendiri.

### Standar penempatan marker tujuan

Untuk tempat wisata, marker yang dipakai tombol petunjuk arah sebaiknya ditempatkan
pada titik yang benar-benar dapat dicapai pengunjung, seperti:

- area parkir;
- loket;
- pintu masuk;
- titik kumpul;
- ujung jalan kendaraan sebelum jalur berjalan kaki.

Marker tidak disarankan ditempatkan tepat di tengah air terjun, sungai, bukit,
atau kawasan hutan apabila kendaraan tidak dapat mencapainya. Penempatan yang
salah dapat membuat Google Maps berhenti di jalan terdekat, memilih jalur memutar,
atau menyatakan rute tidak tersedia.

### Pembaruan jalur menuju tempat wisata

Ada tiga skenario:

1. **Hanya memperbaiki tujuan navigasi**  
   Perbarui latitude dan longitude marker melalui panel admin. Tidak perlu deploy.

2. **Memperbaiki jaringan jalan pada basemap**  
   Edit OpenStreetMap dan tunggu tile OSM dirender ulang. Tidak perlu deploy.

3. **Menampilkan jalur survei sebagai garis pada aplikasi**  
   Rekam GPX, konversi menjadi GeoJSON, simpan pada `public/jalur/` dan
   `docs/jalur/`, tambahkan ke `JALUR_FILES`, lalu lakukan build dan deploy.

## Status saat ini

| Komponen | Status |
|---|---|
| Rekaman GPX survei | Selesai dan tersimpan di repo |
| Penyuntingan jalan di OpenStreetMap | Sudah dilakukan pengguna |
| Konversi GPX menjadi GeoJSON | Selesai |
| Overlay jalur GeoJSON pada VPS | Sudah diimplementasikan dan dideploy |
| Overlay jalur GeoJSON pada GitHub Pages | Sudah diimplementasikan |
| Bounds gabungan jalur dan marker | Sudah diperbaiki |
| Cache GeoJSON dan JavaScript inti | Sudah diperbaiki |
| Perubahan masuk branch `main` | Selesai |
| Deploy perubahan terbaru ke VPS | Selesai |
| File GeoJSON dapat diakses dari domain | Terverifikasi `HTTP 200` |
| Service worker VPS | Terverifikasi memakai `wisata-sesaot-v3` |
| Koordinat titik wisata hasil survei | Belum seluruhnya diverifikasi |
| Rute Google Maps | Mengikuti koordinat marker dan data jalan Google Maps |

## Perintah pembaruan VPS berikutnya

Untuk perubahan kode atau file GeoJSON berikutnya:

```bash
cd /var/www/kkn-sesaot
git checkout main
git pull --ff-only origin main
npm install
npm run build
php artisan optimize:clear
sudo systemctl restart kkn-sesaot-queue.service
sudo systemctl reload nginx
```

Setelah deploy, periksa:

```bash
git branch --show-current
git log -1 --oneline
curl -I https://wisatasesaot.my.id/jalur/jalan-aspal-desa.geojson
curl -s https://wisatasesaot.my.id/sw.js | grep CACHE_NAME
```

## Catatan mutu data

Overlay GeoJSON adalah lapisan informasi hasil survei dan melengkapi data resmi
OpenStreetMap, bukan menggantikannya. Koordinat sejumlah titik wisata pada data
aplikasi masih bersifat perkiraan dan harus diverifikasi melalui survei lapangan.

Sebelum pencetakan QR Code final atau serah terima kepada Pemerintah Desa dan
Pokdarwis, setiap marker perlu melalui pemeriksaan minimal:

1. koordinat sesuai titik akses nyata;
2. nama dan kategori benar;
3. deskripsi singkat tersedia;
4. foto sesuai lokasi;
5. tombol petunjuk arah diuji dari perangkat seluler;
6. status aktif hanya diberikan kepada titik yang sudah diverifikasi.
