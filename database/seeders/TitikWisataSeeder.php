<?php

namespace Database\Seeders;

use App\Models\TitikWisata;
use Illuminate\Database\Seeder;

class TitikWisataSeeder extends Seeder
{
    public function run(): void
    {
        $titikWisata = [
            [
                'nama' => 'Air Terjun Tibu Sendalem',
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Lauk',
                'deskripsi' => 'Air terjun dengan kolam alami di tepi Hutan Lindung Sesaot.',
                'latitude' => -8.5230,
                'longitude' => 116.2650,
                'urutan' => 1,
            ],
            [
                'nama' => 'Air Terjun Tembiras',
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Timur',
                'deskripsi' => 'Salah satu air terjun favorit wisatawan di jalur trekking Sesaot.',
                'latitude' => -8.5245,
                'longitude' => 116.2668,
                'urutan' => 2,
            ],
            [
                'nama' => 'Tibu Goa',
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Lauk',
                'deskripsi' => 'Pemandian alami di sekitar area goa.',
                'latitude' => -8.5219,
                'longitude' => 116.2637,
                'urutan' => 3,
            ],
            [
                'nama' => 'Aik Nyet',
                'kategori' => 'pemandian',
                'dusun' => 'Gontoran',
                'deskripsi' => 'Pemandian air dingin alami, salah satu titik favorit keluarga.',
                'latitude' => -8.5262,
                'longitude' => 116.2611,
                'urutan' => 4,
            ],
            [
                'nama' => 'Sate Bulayak',
                'kategori' => 'kuliner',
                'dusun' => 'Sesaot Timur',
                'deskripsi' => 'Kuliner khas Sesaot, sate dengan lontong bulayak.',
                'latitude' => -8.5241,
                'longitude' => 116.2655,
                'urutan' => 5,
            ],
        ];

        foreach ($titikWisata as $data) {
            TitikWisata::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['nama'])],
                $data
            );
        }
    }
}
