<?php

namespace App\Support;

use App\Models\TitikWisata;
use Illuminate\Support\Facades\App;

class TitikWisataGeoJsonExporter
{
    /**
     * @param  string|null  $locale  Kalau diisi, snapshot dibuat untuk locale itu
     *                               (dipakai export/generate multi-bahasa untuk
     *                               docs/ GitHub Pages) - tanpa mengubah locale
     *                               request yang sedang berjalan.
     */
    public static function toArray(bool $withGeneratedAt = false, ?string $locale = null): array
    {
        $build = function () use ($withGeneratedAt) {
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
                        'kategori_label' => __('peta.kategori.'.$titik->kategori),
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
        };

        if ($locale === null) {
            return $build();
        }

        return App::runningInConsole() || App::getLocale() !== $locale
            ? App::make(self::class)::withLocale($locale, $build)
            : $build();
    }

    public static function toJson(bool $withGeneratedAt = false, ?string $locale = null): string
    {
        return json_encode(
            self::toArray($withGeneratedAt, $locale),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    private static function withLocale(string $locale, callable $callback): array
    {
        $original = App::getLocale();
        App::setLocale($locale);

        try {
            return $callback();
        } finally {
            App::setLocale($original);
        }
    }
}
