<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class TitikWisata extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['nama', 'deskripsi', 'cerita_lokal'];

    public const KATEGORI = [
        'air_terjun' => 'Air Terjun',
        'pemandian' => 'Pemandian',
        'jalur_tracking' => 'Jalur Tracking',
        'kuliner' => 'Kuliner',
        'homestay' => 'Homestay',
        'budaya' => 'Budaya',
        'fasilitas_umum' => 'Fasilitas Umum',
    ];

    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'dusun',
        'deskripsi',
        'cerita_lokal',
        'latitude',
        'longitude',
        'foto',
        'video_youtube_url',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (TitikWisata $titikWisata) {
            if (empty($titikWisata->slug)) {
                $namaIndonesia = $titikWisata->getTranslation('nama', 'id', false) ?: $titikWisata->nama;
                $titikWisata->slug = Str::slug($namaIndonesia);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_youtube_url) {
            return null;
        }

        preg_match('/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{11})/', $this->video_youtube_url, $matches);

        return isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : null;
    }
}
