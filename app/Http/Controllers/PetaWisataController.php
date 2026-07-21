<?php

namespace App\Http\Controllers;

use App\Models\TitikWisata;
use Illuminate\Support\Collection;

class PetaWisataController extends Controller
{
    public function index()
    {
        $titikWisata = TitikWisata::active()->ordered()->get();

        return view('peta.index', [
            'titikWisata' => $titikWisata,
            'structuredData' => $this->encodeJsonLd([
                '@context' => 'https://schema.org',
                '@type' => 'TouristDestination',
                'name' => __('peta.judul_situs'),
                'description' => __('peta.meta_deskripsi_default'),
                'url' => route('peta.index'),
                'includesAttraction' => $titikWisata->map(fn (TitikWisata $titik) => [
                    '@type' => 'TouristAttraction',
                    'name' => $titik->nama,
                    'url' => route('titik-wisata.show', $titik),
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $titik->latitude,
                        'longitude' => $titik->longitude,
                    ],
                ])->values(),
            ]),
        ]);
    }

    public function show(TitikWisata $titikWisata)
    {
        abort_unless($titikWisata->is_active, 404);

        $lainnya = TitikWisata::active()
            ->where('id', '!=', $titikWisata->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $detailUrl = route('titik-wisata.show', $titikWisata);

        return view('peta.show', [
            'titikWisata' => $titikWisata,
            'lainnya' => $lainnya,
            'structuredData' => $this->encodeJsonLd([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'TouristAttraction',
                        '@id' => $detailUrl.'#attraction',
                        'name' => $titikWisata->nama,
                        'description' => $titikWisata->deskripsi ?: __('peta.meta_deskripsi_default'),
                        'url' => $detailUrl,
                        'mainEntityOfPage' => $detailUrl,
                        'image' => $titikWisata->foto
                            ? asset('storage/'.$titikWisata->foto)
                            : asset('icons/icon-512.png'),
                        'geo' => [
                            '@type' => 'GeoCoordinates',
                            'latitude' => $titikWisata->latitude,
                            'longitude' => $titikWisata->longitude,
                        ],
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => 'Dusun '.$titikWisata->dusun.', Desa Sesaot',
                            'addressRegion' => 'Nusa Tenggara Barat',
                            'addressCountry' => 'ID',
                        ],
                        'isAccessibleForFree' => true,
                    ],
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            [
                                '@type' => 'ListItem',
                                'position' => 1,
                                'name' => __('peta.judul_situs'),
                                'item' => route('peta.index'),
                            ],
                            [
                                '@type' => 'ListItem',
                                'position' => 2,
                                'name' => $titikWisata->nama,
                                'item' => $detailUrl,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
    }

    public function sitemap()
    {
        $titikWisata = TitikWisata::active()->ordered()->get();
        $lastModified = $titikWisata->max('updated_at');

        return response()
            ->view('sitemap', [
                'titikWisata' => $titikWisata,
                'lastModified' => $lastModified,
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * json_encode() dijalankan di controller, bukan di Blade, supaya literal
     * "@context"/"@type" di dalam JSON tidak salah dikenali sebagai directive
     * Blade (mis. @context bawaan Laravel untuk Context facade).
     */
    private function encodeJsonLd(array|Collection $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
