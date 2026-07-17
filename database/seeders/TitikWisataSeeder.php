<?php

namespace Database\Seeders;

use App\Models\TitikWisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TitikWisataSeeder extends Seeder
{
    public function run(): void
    {
        $titikWisata = [
            [
                'nama' => [
                    'id' => 'Air Terjun Tibu Sendalem',
                    'en' => 'Tibu Sendalem Waterfall',
                    'ar' => 'شلال تيبو سيندالم',
                    'zh' => '蒂布森达伦瀑布',
                    'ms' => 'Air Terjun Tibu Sendalem',
                ],
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Lauk',
                'deskripsi' => [
                    'id' => 'Air terjun dengan kolam alami di tepi Hutan Lindung Sesaot.',
                    'en' => 'A waterfall with a natural pool on the edge of Sesaot Protected Forest.',
                    'ar' => 'شلال به بركة طبيعية على حافة غابة سيساوت المحمية.',
                    'zh' => '瀑布伴有天然水池，位于赛索特保护林边缘。',
                    'ms' => 'Air terjun dengan kolam semula jadi di tepi Hutan Lindung Sesaot.',
                ],
                'latitude' => -8.5230,
                'longitude' => 116.2650,
                'urutan' => 1,
            ],
            [
                'nama' => [
                    'id' => 'Air Terjun Tembiras',
                    'en' => 'Tembiras Waterfall',
                    'ar' => 'شلال تيمبيراس',
                    'zh' => '腾比拉斯瀑布',
                    'ms' => 'Air Terjun Tembiras',
                ],
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Timur',
                'deskripsi' => [
                    'id' => 'Salah satu air terjun favorit wisatawan di jalur trekking Sesaot.',
                    'en' => 'One of the most popular waterfalls along the Sesaot trekking trail.',
                    'ar' => 'أحد أكثر الشلالات شعبية بين الزوار على مسار سيساوت للمشي.',
                    'zh' => '赛索特徒步路线上最受游客欢迎的瀑布之一。',
                    'ms' => 'Salah satu air terjun kegemaran pelancong di laluan trekking Sesaot.',
                ],
                'latitude' => -8.5245,
                'longitude' => 116.2668,
                'urutan' => 2,
            ],
            [
                'nama' => [
                    'id' => 'Tibu Goa',
                    'en' => 'Tibu Goa',
                    'ar' => 'تيبو غوا',
                    'zh' => '蒂布戈瓦',
                    'ms' => 'Tibu Goa',
                ],
                'kategori' => 'air_terjun',
                'dusun' => 'Sesaot Lauk',
                'deskripsi' => [
                    'id' => 'Pemandian alami di sekitar area goa.',
                    'en' => 'A natural bathing spot near a cave area.',
                    'ar' => 'مكان استحمام طبيعي بالقرب من منطقة الكهف.',
                    'zh' => '洞穴附近的天然浴场。',
                    'ms' => 'Tempat mandi semula jadi berhampiran kawasan gua.',
                ],
                'latitude' => -8.5219,
                'longitude' => 116.2637,
                'urutan' => 3,
            ],
            [
                'nama' => [
                    'id' => 'Aik Nyet',
                    'en' => 'Aik Nyet',
                    'ar' => 'آيك نيت',
                    'zh' => '艾克涅特',
                    'ms' => 'Aik Nyet',
                ],
                'kategori' => 'pemandian',
                'dusun' => 'Gontoran',
                'deskripsi' => [
                    'id' => 'Pemandian air dingin alami, salah satu titik favorit keluarga.',
                    'en' => 'A natural cold-water bathing spot, a family favourite.',
                    'ar' => 'مكان استحمام طبيعي بمياه باردة، من الأماكن المفضلة للعائلات.',
                    'zh' => '天然冷泉浴场，是家庭出游的热门地点。',
                    'ms' => 'Tempat mandi air sejuk semula jadi, kegemaran keluarga.',
                ],
                'latitude' => -8.5262,
                'longitude' => 116.2611,
                'urutan' => 4,
            ],
            [
                'nama' => [
                    'id' => 'Sate Bulayak',
                    'en' => 'Sate Bulayak',
                    'ar' => 'ساتيه بولاياك',
                    'zh' => '布拉亚克沙爹',
                    'ms' => 'Sate Bulayak',
                ],
                'kategori' => 'kuliner',
                'dusun' => 'Sesaot Timur',
                'deskripsi' => [
                    'id' => 'Kuliner khas Sesaot, sate dengan lontong bulayak.',
                    'en' => 'A Sesaot speciality: satay served with bulayak rice cake.',
                    'ar' => 'طبق مميز من سيساوت: ساتيه يُقدَّم مع كعكة الأرز بولاياك.',
                    'zh' => '赛索特特色美食：沙爹配布拉亚克糯米卷。',
                    'ms' => 'Kuliner khas Sesaot, sate bersama lontong bulayak.',
                ],
                'latitude' => -8.5241,
                'longitude' => 116.2655,
                'urutan' => 5,
            ],
        ];

        foreach ($titikWisata as $data) {
            TitikWisata::updateOrCreate(
                ['slug' => Str::slug($data['nama']['id'])],
                $data
            );
        }
    }
}
