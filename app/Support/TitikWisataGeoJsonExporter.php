<?php

namespace App\Support;

use App\Models\TitikWisata;

class TitikWisataGeoJsonExporter
{
    public static function toArray(bool $withGeneratedAt = false): array
    {
        $titikWisata = TitikWisata::active()->ordered()->get();

        return [
            'type' => 'FeatureCollection',
            ...($withGeneratedAt ? ['generated_at' => now()->toIso8601String()] : []),
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
                    'cerita_lokal' => $titik->cerita_lokal,
                    'foto' => $titik->foto ? asset('storage/'.$titik->foto) : null,
                    'video_embed_url' => $titik->video_embed_url,
                    'latitude' => (float) $titik->latitude,
                    'longitude' => (float) $titik->longitude,
                    'detail_url' => route('titik-wisata.show', $titik->slug),
                ],
            ])->values(),
        ];
    }

    public static function toJson(bool $withGeneratedAt = false): string
    {
        return json_encode(
            self::toArray($withGeneratedAt),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
