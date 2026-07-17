<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TitikWisata;
use Illuminate\Http\JsonResponse;

class TitikWisataController extends Controller
{
    public function index(): JsonResponse
    {
        $titikWisata = TitikWisata::active()->ordered()->get();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $titikWisata->map(fn (TitikWisata $titik) => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $titik->longitude, (float) $titik->latitude],
                ],
                'properties' => [
                    'nama' => $titik->nama,
                    'slug' => $titik->slug,
                    'kategori' => $titik->kategori,
                    'kategori_label' => TitikWisata::KATEGORI[$titik->kategori] ?? $titik->kategori,
                    'dusun' => $titik->dusun,
                    'deskripsi' => $titik->deskripsi,
                    'foto' => $titik->foto ? asset('storage/'.$titik->foto) : null,
                    'detail_url' => route('titik-wisata.show', $titik->slug),
                ],
            ]),
        ]);
    }
}
