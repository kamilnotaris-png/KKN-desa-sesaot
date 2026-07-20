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
            // Bukit Vetong: koordinat masih perkiraan kasar (belum hasil
            // survei GPS lapangan) - lihat catatan "Belum Beres" di CLAUDE.md.
            // Aman untuk tampil di peta, belum aman dipakai untuk navigasi presisi.
            [
                'nama' => [
                    'id' => 'Purekmas',
                    'en' => 'Purekmas',
                    'ar' => 'بوريكماس',
                    'zh' => '普雷克马斯',
                    'ms' => 'Purekmas',
                ],
                'kategori' => 'pemandian',
                'dusun' => 'Penangke',
                'deskripsi' => [
                    'id' => 'Pusat Rekreasi Masyarakat (Purekmas) - mata air alami dari Gunung Rinjani yang dikelola warga, sungai jernih toska dan kolam anak.',
                    'en' => 'Purekmas (Community Recreation Center) - natural springs from Mount Rinjani managed by locals, with a turquoise river and children\'s pool.',
                    'ar' => 'بوريكماس (مركز الترفيه المجتمعي) - ينابيع طبيعية من جبل رينجاني يديرها السكان المحليون، مع نهر فيروزي وبركة للأطفال.',
                    'zh' => 'Purekmas（社区休闲中心）- 由当地居民管理的林贾尼火山天然泉水，配有碧绿的河流和儿童泳池。',
                    'ms' => 'Purekmas (Pusat Rekreasi Masyarakat) - mata air semula jadi dari Gunung Rinjani yang diurus penduduk tempatan, sungai jernih dan kolam kanak-kanak.',
                ],
                // Koordinat dari decode Plus Code F65V+VMX (alamat Google Maps
                // "Jl. Sesaot, Sesaot, Kec. Narmada" - dikonfirmasi user), ~142m
                // dari Kantor Desa. Lebih akurat dari placeholder lama, tapi
                // tetap bukan hasil survei GPS lapangan langsung di lokasi.
                'latitude' => -8.5402625,
                'longitude' => 116.2442344,
                'urutan' => 6,
            ],
            [
                'nama' => [
                    'id' => 'Bukit Vetong',
                    'en' => 'Vetong Hill',
                    'ar' => 'تلة فيتونغ',
                    'zh' => '维通山',
                    'ms' => 'Bukit Vetong',
                ],
                'kategori' => 'homestay',
                'dusun' => 'Sesaot',
                'deskripsi' => [
                    'id' => 'Area camping ground dengan rumah Sasak tradisional, lumbung padi, dan Tree House untuk menikmati sunrise dengan latar Gunung Rinjani.',
                    'en' => 'A camping ground with traditional Sasak houses, rice barns, and a Tree House for watching the sunrise with a Mount Rinjani backdrop.',
                    'ar' => 'أرض تخييم فيها منازل ساساك التقليدية، ومخازن الأرز، وبيت على الشجرة لمشاهدة شروق الشمس مع إطلالة على جبل رينجاني.',
                    'zh' => '露营地设有传统萨萨克族房屋、稻谷仓，以及可眺望林贾尼火山日出的树屋。',
                    'ms' => 'Kawasan berkhemah dengan rumah tradisional Sasak, jelapang padi, dan Tree House untuk menikmati matahari terbit dengan latar Gunung Rinjani.',
                ],
                'latitude' => -8.5215,
                'longitude' => 116.2675,
                'urutan' => 7,
            ],
            // Bukit Mangga: koordinat juga masih perkiraan kasar, belum survei
            // GPS lapangan - lihat catatan di atas.
            [
                'nama' => [
                    'id' => 'Bukit Mangga',
                    'en' => 'Bukit Mangga Hill',
                    'ar' => 'تلة بوكيت مانغا',
                    'zh' => '芒果山',
                    'ms' => 'Bukit Mangga',
                ],
                'kategori' => 'jalur_tracking',
                'dusun' => 'Sesaot Timuk',
                'deskripsi' => [
                    'id' => 'Bukit dengan jalur trekking ringan cocok pemula, pemandangan hijau perbukitan dan Gunung Rinjani, spot foto bintang besar, dan kopi khas Sesaot. Favorit untuk menikmati sunset.',
                    'en' => 'A hill with an easy, beginner-friendly trekking trail, green hillside and Mount Rinjani views, a large star-shaped photo spot, and local Sesaot coffee. A favourite for watching the sunset.',
                    'ar' => 'تلة بمسار مشي سهل يناسب المبتدئين، بإطلالات خضراء على التلال وجبل رينجاني، ومكان تصوير على شكل نجمة كبيرة، وقهوة سيساوت المحلية. مكان مفضل لمشاهدة غروب الشمس.',
                    'zh' => '一座适合初学者的轻松徒步山丘，可眺望绿意盎然的山丘与林贾尼火山，设有大型星形拍照点，并供应赛索特当地咖啡，是欣赏日落的热门去处。',
                    'ms' => 'Bukit dengan laluan trekking mudah sesuai pemula, pemandangan hijau perbukitan dan Gunung Rinjani, tempat foto berbentuk bintang besar, dan kopi khas Sesaot. Kegemaran untuk menikmati matahari terbenam.',
                ],
                'latitude' => -8.5198,
                'longitude' => 116.2690,
                'urutan' => 8,
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
