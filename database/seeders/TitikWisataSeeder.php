<?php

namespace Database\Seeders;

use App\Models\TitikWisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TitikWisataSeeder extends Seeder
{
    public function run(): void
    {
        // Sengaja hanya berisi titik wisata yang koordinatnya sudah
        // diverifikasi presisi (decode Plus Code dari alamat Google Maps
        // yang dikonfirmasi user), bukan tebakan kasar - lihat riwayat
        // perubahan di CLAUDE.md. Titik lain (air terjun, kuliner, dst)
        // yang koordinatnya masih perkiraan sengaja dihapus dari sini
        // supaya tidak menampilkan lokasi yang salah di peta publik;
        // tambahkan kembali lewat admin panel setelah ada data GPS pasti.
        $titikWisata = [
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
                // dari Kantor Desa. Bukan hasil survei GPS lapangan langsung.
                'latitude' => -8.5402625,
                'longitude' => 116.2442344,
                'urutan' => 1,
            ],
            [
                'nama' => [
                    'id' => 'Berugak Elen Sesaot',
                    'en' => 'Berugak Elen Sesaot',
                    'ar' => 'بيروغاك إيلين سيساوت',
                    'zh' => '贝鲁加克埃伦赛索特',
                    'ms' => 'Berugak Elen Sesaot',
                ],
                'kategori' => 'pemandian',
                'dusun' => 'Sesaot',
                'deskripsi' => [
                    'id' => 'Wisata pemandian sungai dengan berugaq (gazebo tradisional Sasak) tertata rapi di tepi sungai, kolam anak, dan akses jalan yang mudah.',
                    'en' => 'A river bathing spot with neatly arranged traditional Sasak berugaq (gazebos) along the riverbank, a children\'s pool, and easy road access.',
                    'ar' => 'موقع استحمام على النهر مع أكواخ ساساك التقليدية (بيروغاك) مرتبة بعناية على ضفة النهر، وبركة للأطفال، وطريق وصول سهل.',
                    'zh' => '河边浴场，河岸整齐排列着传统萨萨克族凉亭（berugaq），设有儿童泳池，交通便利。',
                    'ms' => 'Tempat mandi sungai dengan berugaq (pondok tradisional Sasak) tersusun kemas di tepi sungai, kolam kanak-kanak, dan akses jalan yang mudah.',
                ],
                // Koordinat dari decode Plus Code F64V+X5 (alamat Google Maps
                // "Sesaot, Kabupaten Lombok Barat" - dikonfirmasi user), ~185m
                // dari Kantor Desa. Bukan hasil survei GPS lapangan langsung.
                'latitude' => -8.542562500000003,
                'longitude' => 116.24293750000004,
                'urutan' => 2,
            ],
            [
                'nama' => [
                    'id' => 'Wisata Bawak Are Sesaot',
                    'en' => 'Bawak Are Sesaot',
                    'ar' => 'باواك آري سيساوت',
                    'zh' => '巴瓦克阿雷赛索特',
                    'ms' => 'Wisata Bawak Are Sesaot',
                ],
                'kategori' => 'pemandian',
                'dusun' => 'Sesaot',
                'deskripsi' => [
                    'id' => 'Sungai dengan air jernih biru kehijauan mengalir di atas bebatuan unik, dijuluki "Sungai Aare-nya Lombok". Ada gazebo, warung, area parkir, wahana tubing, dan kolam renang. Akses lewat jalan di samping Masjid Nurul Ikhlas, tiket masuk Rp 2.000.',
                    'en' => 'A river with clear turquoise-blue water flowing over uniquely textured rocks, nicknamed "Lombok\'s Aare River". Facilities include gazebos, food stalls, parking, tubing, and a swimming pool. Access via the road beside Masjid Nurul Ikhlas, entrance fee Rp 2,000.',
                    'ar' => 'نهر بمياه فيروزية صافية يتدفق فوق صخور ذات ملمس فريد، يُلقَّب بـ "نهر آري لومبوك". تتوفر أكواخ استراحة وأكشاك طعام ومواقف سيارات وأنبوب تعويم وحمام سباحة. الوصول عبر الطريق بجانب مسجد نور الإخلاص، رسوم الدخول 2000 روبية.',
                    'zh' => '河水清澈碧绿，流经纹理独特的岩石，被誉为"龙目岛的阿勒河"。设有凉亭、小吃摊、停车场、漂流圈项目及游泳池。经努鲁伊赫拉斯清真寺旁的小路进入，门票2000印尼盾。',
                    'ms' => 'Sungai dengan air jernih biru kehijauan mengalir di atas batu bertekstur unik, digelar "Sungai Aare Lombok". Terdapat berugaq, gerai makanan, tempat letak kereta, aktiviti tubing, dan kolam renang. Akses melalui jalan di sebelah Masjid Nurul Ikhlas, tiket masuk Rp 2.000.',
                ],
                // Koordinat dari decode Plus Code F65R+3X (alamat Google Maps
                // "Sesaot, Kabupaten Lombok Barat" - dikonfirmasi user), ~219m
                // dari Kantor Desa. Bukan hasil survei GPS lapangan langsung.
                // Diverifikasi via web search (RRI, detik.com, Lombok Post,
                // Kompasiana) sebagai objek wisata nyata di Desa Sesaot.
                'latitude' => -8.5423125,
                'longitude' => 116.2424375,
                'urutan' => 3,
            ],
            [
                'nama' => [
                    'id' => 'Bawak Goak Rivercamp',
                    'en' => 'Bawak Goak Rivercamp',
                    'ar' => 'باواك غواك ريفركامب',
                    'zh' => '巴瓦克戈亚河营',
                    'ms' => 'Bawak Goak Rivercamp',
                ],
                'kategori' => 'pemandian',
                'dusun' => 'Sesaot',
                'deskripsi' => [
                    'id' => 'Sungai jernih dan sejuk dari mata air pegunungan, mengalir bertingkat-tingkat di atas bebatuan alami. Aktivitas utama river tubing (susur sungai pakai ban) dikelola pemuda desa, ditambah kolam renang alami. Tiket masuk sekitar Rp 2.000-3.000, sewa ban tubing Rp 10.000-20.000.',
                    'en' => 'Clear, cool river fed by mountain springs, cascading over natural tiered rock formations. Main activity is river tubing managed by local youth, plus a natural swimming spot. Entrance fee around Rp 2,000-3,000, tube rental Rp 10,000-20,000.',
                    'ar' => 'نهر صافٍ وبارد من ينابيع جبلية، يتدفق على شكل مدرجات فوق صخور طبيعية. النشاط الرئيسي هو أنبوب النهر الذي يديره شباب القرية، بالإضافة إلى مكان سباحة طبيعي. رسوم الدخول حوالي 2000-3000 روبية، وإيجار الأنبوب 10000-20000 روبية.',
                    'zh' => '清澈凉爽的山泉河流，层层叠叠流经天然岩石。主要活动是由村里青年管理的漂流圈项目，另有天然游泳区。门票约2000-3000印尼盾，漂流圈租金10000-20000印尼盾。',
                    'ms' => 'Sungai jernih dan sejuk dari mata air gunung, mengalir berperingkat di atas batu semula jadi. Aktiviti utama river tubing (menyusur sungai guna tiub) diuruskan pemuda kampung, ditambah kolam renang semula jadi. Tiket masuk sekitar Rp 2.000-3.000, sewa tiub Rp 10.000-20.000.',
                ],
                // Koordinat dari decode Plus Code F65V+98 (alamat Google Maps
                // "Sesaot, Kabupaten Lombok Barat" - dikonfirmasi user), ~105m
                // dari Kantor Desa. Bukan hasil survei GPS lapangan langsung.
                // Diverifikasi via web search (Tribunlombok, kumparan, Timenews,
                // Lombok Post) sebagai objek wisata nyata di Desa Sesaot, terpisah
                // dari Bawak Are (beberapa sumber menyebut keduanya berdampingan).
                'latitude' => -8.541562500000012,
                'longitude' => 116.2433125,
                'urutan' => 4,
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
