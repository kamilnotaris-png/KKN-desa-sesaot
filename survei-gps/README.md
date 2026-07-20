# Arsip Hasil Survei GPS Lapangan

File `.gpx` mentah hasil rekaman OsmAnd di lapangan diarsipkan di folder ini
agar tidak hilang dan dapat dipakai ulang atau diverifikasi. Data dapat dipakai
untuk memperbarui OpenStreetMap, memeriksa jalur lapangan, dan mengoreksi
koordinat titik wisata di `database/seeders/TitikWisataSeeder.php`.

Folder ini belum dibaca langsung oleh aplikasi. File GPX berfungsi sebagai
arsip sumber dan bukti survei lapangan.

## Daftar Rekaman

| File | Tanggal | Keterangan | Status |
|---|---|---|---|
| `2026-07-20_jalan-aspal-desa-pintu-masuk-keluar.gpx` | 2026-07-20 | Jalan aspal utama Desa Sesaot dari pintu masuk desa `(-8.5575661, 116.2385974)` sampai pintu keluar desa `(-8.5394807, 116.2558324)`, melewati Kantor Desa Sesaot di tengah jalur. Rekaman terdiri dari dua segmen karena terdapat jeda di Kantor Desa. | Sudah dipakai sebagai acuan untuk menggambar atau memperbarui jaringan jalan di OpenStreetMap pada 20 Juli 2026. |

## Pembaruan OpenStreetMap 20 Juli 2026

Pengguna telah melakukan penyuntingan jaringan jalan Desa Sesaot melalui editor
OpenStreetMap. Perubahan mengikuti hasil survei lapangan dan mencakup jalur jalan
di sekitar Desa Sesaot yang terlihat pada editor OSM, termasuk koridor menuju dan
melewati kawasan Kantor Desa.

Perubahan sudah disimpan di basis data OpenStreetMap, tetapi pada saat pemeriksaan
terakhir belum tampil pada peta `wisatasesaot.my.id`.

### Penyebab belum terlihat di website

Website tidak mengambil data jalan mentah atau perubahan OSM secara langsung.
Leaflet menampilkan gambar raster dari:

```text
https://tile.openstreetmap.org/{z}/{x}/{y}.png
```

Karena itu, perubahan yang sudah terlihat di editor OpenStreetMap belum tentu
langsung terlihat pada tile peta standar. Tile harus dirender ulang oleh server
OpenStreetMap. Proses tersebut berada di luar aplikasi dan waktunya tidak dapat
dipaksa dari server `wisatasesaot.my.id`.

### Perbaikan yang sudah dilakukan pada aplikasi

Pada 20 Juli 2026 dilakukan perubahan berikut:

1. `public/sw.js` diubah agar tile OpenStreetMap menggunakan strategi
   **network-first**, bukan terus mengambil tile lama dari cache.
2. Versi cache VPS dinaikkan dari `wisata-sesaot-v1` menjadi
   `wisata-sesaot-v2` agar cache lama dihapus ketika service worker aktif.
3. `docs/sw.js` untuk GitHub Pages juga diubah menjadi **network-first** untuk
   tile OpenStreetMap.
4. Versi cache GitHub Pages dinaikkan dari `wisata-sesaot-pages-v2` menjadi
   `wisata-sesaot-pages-v3`.
5. Mode offline tetap dipertahankan: jika jaringan gagal, aplikasi memakai tile
   terakhir yang tersedia di cache.

Commit terkait:

- `c738e6b0f0062d716cbde34307fc71f99f72608f` — perbaikan cache tile pada VPS.
- `814d1720fc72278ba8f6be332312960a99a7708f` — perbaikan cache tile pada GitHub Pages.

### Status tindak lanjut

- Perubahan OpenStreetMap: **sudah disimpan oleh pengguna**.
- Perubahan service worker di GitHub: **sudah masuk ke branch `main`**.
- Deploy VPS: perlu memastikan VPS sudah menarik commit terbaru dan menjalankan
  build/clear cache.
- Render tile OpenStreetMap: **masih menunggu pembaruan dari server tile OSM**.
- Solusi agar jalur langsung terlihat tanpa menunggu render OSM: ekspor GPX
  menjadi GeoJSON dan tampilkan sebagai overlay jalur mandiri di Leaflet.

## Prosedur penyuntingan OpenStreetMap

1. Buka OpenStreetMap dan masuk ke akun pengguna.
2. Unggah file GPX melalui menu **GPS Traces → Upload a Trace**.
3. Buka editor **iD** dan aktifkan layer trace yang telah diunggah.
4. Gambar atau koreksi garis jalan mengikuti jejak GPX dan citra yang tersedia.
5. Untuk jalan aspal desa, gunakan klasifikasi yang sesuai kondisi faktual,
   misalnya **Residential Road** atau **Unclassified Road**, lalu isi
   `surface=asphalt` atau `surface=paved`.
6. Periksa sambungan antarruas agar tidak ada garis terputus, duplikasi, atau
   persimpangan yang tidak terhubung.
7. Simpan changeset dengan uraian perubahan yang jelas dan tunggu proses render
   tile OpenStreetMap.
