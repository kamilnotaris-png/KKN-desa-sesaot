# Arsip Hasil Survei GPS Lapangan

File `.gpx` mentah hasil rekaman OsmAnd di lapangan, diarsipkan di sini
supaya tidak hilang dan bisa dipakai ulang/diverifikasi kapan saja —
baik untuk digambar ke OpenStreetMap maupun untuk update koordinat titik
wisata di `database/seeders/TitikWisataSeeder.php`.

Folder ini bukan bagian dari aplikasi yang jalan (tidak dibaca kode apa
pun) — murni arsip data mentah survei.

## Daftar Rekaman

| File | Tanggal | Keterangan |
|---|---|---|
| `2026-07-20_jalan-aspal-desa-pintu-masuk-keluar.gpx` | 2026-07-20 | Jalan aspal utama Desa Sesaot, dari pintu masuk desa (-8.5575661, 116.2385974) sampai pintu keluar desa (-8.5394807, 116.2558324), melewati Kantor Desa Sesaot di tengah jalur (~-8.5416, 116.2442 — cocok dengan koordinat Kantor Desa hasil decode Plus Code F65V+9PJ, selisih ±15m). 2 segmen (ada jeda ±26 menit di Kantor Desa). Belum digambar ke OpenStreetMap — lihat panduan di CLAUDE.md bagian "Multi-Bahasa" / modul mahasiswa untuk cara upload & tag jalur.

## Cara Pakai untuk Update OpenStreetMap

1. Buka [openstreetmap.org](https://www.openstreetmap.org), login.
2. Menu **GPS Traces → Upload a Trace** → pilih file `.gpx` dari folder ini.
3. Buka editor **iD** (tombol Edit), aktifkan layer trace yang baru diupload.
4. Gambar garis mengikuti jejak, tandai sebagai jalan (preset **Residential
   Road**/**Unclassified Road**, field Surface: **Paved/Asphalt** karena ini
   jalan aspal — bukan jalur setapak).
